<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Imports\CustomWaImport;
use App\wa_history;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\WablasTrait;

class CustomWaController extends Controller
{
    public function index()
    {
        return view('admin.whatsapp.custom.index');
    }

    //untuk ganti format nomorHp
    public function gantiformat($nomorhp)
    {
        //Terlebih dahulu kita trim dl
        $nomorhp = trim($nomorhp);
        //bersihkan dari karakter yang tidak perlu
        $nomorhp = strip_tags($nomorhp);
        // Berishkan dari spasi
        $nomorhp = str_replace(" ", "", $nomorhp);
        // bersihkan dari bentuk seperti  (022) 66677788
        $nomorhp = str_replace("(", "", $nomorhp);
        // bersihkan dari format yang ada titik seperti 0811.222.333.4
        $nomorhp = str_replace(".", "", $nomorhp);

        //cek apakah mengandung karakter + dan 0-9
        if (!preg_match('/[^+0-9]/', trim($nomorhp))) {
            // cek apakah no hp karakter 1-3 adalah +62
            if (substr(trim($nomorhp), 0, 3) == '+62') {
                $nomorhp = trim($nomorhp);
            }
            // cek apakah no hp karakter 1 adalah 0
            elseif (substr($nomorhp, 0, 1) == '0') {
                $nomorhp = '62' . substr($nomorhp, 1);
            }
        }
        return $nomorhp;
    }

    public function import(Request $request)
    {
        $import = new CustomWaImport;
        $test =  Excel::import($import, $request->file('file'));

        $array = $import->getArray();

        abort_unless(\Gate::allows('wablast_access'), 403);
        $limit = env('LIMIT_SEND');
        $jam = date('H');
        if ($jam > 0 && $jam < 11) {
            $waktu = "pagi";
        } else if ($jam > 10 && $jam < 15) {
            $waktu = "siang";
        } else if ($jam > 14 && $jam < 19) {
            $waktu = "sore";
        } else if ($jam > 18 && $jam < 23) {
            $waktu = "malam";
        } else {
            $waktu = "";
        }
        $kumpulan_data = [];
        $date_now = date('Y-m-d');
        $date_comp = date('Y-m') . '-20';
        $last_4_month = date("Y-n-d", strtotime('-4 month', strtotime(date('Y-m-01'))));

        $customers = $import->getArray();

        ini_set("memory_limit", -1);
        set_time_limit(0);
        $code = date('y') . date('m') . date('d') . date('H') . date('i') . date('s');
        $data2 = [];
        //ini test

        // dd($customers[2]['name']);
        for ($i = 0; $i < count($customers); $i++) {
            $message = str_replace("@nama", $customers[$i]['name'], $request->message);
            // $message = str_replace("@sbg", $customers[$i][''], $message);
            $message = str_replace("@alamat", $customers[$i]['adress'], $message);
            $message = str_replace("@waktu", $waktu, $message);


            //Terlebih dahulu kita trim dl
            $nomorhp = trim($customers[$i]['phone']);
            //bersihkan dari karakter yang tidak perlu
            $nomorhp = strip_tags($nomorhp);
            // Berishkan dari spasi
            $nomorhp = str_replace(" ", "", $nomorhp);
            // bersihkan dari bentuk seperti  (022) 66677788
            $nomorhp = str_replace("(", "", $nomorhp);
            // bersihkan dari format yang ada titik seperti 0811.222.333.4
            $nomorhp = str_replace(".", "", $nomorhp);

            //cek apakah mengandung karakter + dan 0-9
            if (!preg_match('/[^+0-9]/', trim($nomorhp))) {
                // cek apakah no hp karakter 1-3 adalah +62
                if (substr(trim($nomorhp), 0, 3) == '+62') {
                    $nomorhp = trim($nomorhp);
                }
                // cek apakah no hp karakter 1 adalah 0
                elseif (substr($nomorhp, 0, 1) == '0') {
                    $nomorhp = '62' . substr($nomorhp, 1);
                }
            }

            $data = [
                'phone' => $nomorhp,
                // test
                // 'phone' => 'x',
                'customer_id' => '',
                'message' => $message,
                // 'id_wa' => 'empty',
                // 'template_id' => $request[$i]->template_id,
                'status' => 'gagal',
                'ref_id' => $code . $customers[$i]['id']
            ];

            $kumpulan_data[] = $data;
        }
        // dd($kumpulan_data);
        $i = 0;
        $array_merg = [];
        $temp = [];
        foreach (array_chunk($kumpulan_data, 5000) as $key => $smallerArray) {
            foreach ($smallerArray as $index => $value) {
                // count($kumpulan_data/5000);
                $temp[] = array_merge($value, ["created_at" => date('Y-m-d h:i:sa'), "updated_at" => date('Y-m-d h:i:sa')]);
                // $i = $i + 1;

            }

            // $array_merg = array_merge($temp, $array_merg);
            DB::table('wa_histories')->insert($temp);
            $temp = [];
        }
        // dd($temp);
        // dd($kumpulan_data);
        // dd($kumpulan_data[65892]);
        $data2 = [];

        $array_merg = [];
        // send WA
        foreach (array_chunk($kumpulan_data, $limit) as $key => $smallerArray) {
            foreach ($smallerArray as $index => $value) {
                $temp[] = $value;
            }
            $test1 = WablasTrait::sendText($temp);
            $temp = [];
            // dd($test1);
            if (!empty(json_decode($test1)->data->messages)) {
                $array_merg = array_merge(json_decode($test1)->data->messages, $array_merg);
            }
        }

        $countSend = 0;
        foreach ($array_merg as $key => $value) {
            if (!empty($value->ref_id)) {
                wa_history::where('ref_id', $value->ref_id)->update(['id_wa' => $value->id, 'status' => ($value->status === false) ? "gagal" : $value->status]);
                $countSend = $countSend + 1;
            }
        }

        return redirect()->route('admin.historywa.index')->withInfo('Pesan Diproses Sebanyak ' . $countSend)->withInput();
    }
}
