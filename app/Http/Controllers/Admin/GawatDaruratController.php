<?php

namespace App\Http\Controllers\Admin;

use App\Absence;
use App\CtmGambarmetersms;
use DB;
use App\CtmPelanggan;
use App\Exports\TestExport;
use App\Holiday;
use App\Http\Controllers\Controller;
use App\Imports\StaffImport;
use App\Staff;
use App\wa_history;
use App\WorkTypeDays;
use App\WorkTypes;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class GawatDaruratController extends Controller
{
    public function index(Request $request)
    {

        // $jumlahWA = wa_history::selectRaw('count(id_wa)')->orderBy('id_wa')->get();
        // return $jumlahWA;

        if (date('d') > 20) {
            $awal1 = strtotime('-1 month', strtotime(date('Y-m') . "-21"));
            $akhir1 = strtotime('0 month', strtotime(date('Y-m') . "-20"));
            $namaB1 = date("F", strtotime('-1 month', strtotime(date('Y-m') . "-21")));

            $awal2 = strtotime('-2 month', strtotime(date('Y-m') . "-21"));
            $akhir2 = strtotime('-1 month', strtotime(date('Y-m') . "-20"));

            $namaB2 = date("F", strtotime('-2 month', strtotime(date('Y-m') . "-21")));

            $awal3 = strtotime('-3 month', strtotime(date('Y-m') . "-21"));
            $akhir3 = strtotime('-2 month', strtotime(date('Y-m') . "-20"));

            $namaB3 = date("F", strtotime('-3 month', strtotime(date('Y-m') . "-21")));
        } else {
            $awal1 = strtotime('-2 month', strtotime(date('Y-m') . "-21"));
            $akhir1 = strtotime('-1 month', strtotime(date('Y-m') . "-20"));
            $namaB1 = date("F", strtotime('-2 month', strtotime(date('Y-m') . "-21")));

            $awal2 = strtotime('-3 month', strtotime(date('Y-m') . "-21"));
            $akhir2 = strtotime('-2 month', strtotime(date('Y-m') . "-20"));
            $namaB2 = date("F", strtotime('-3 month', strtotime(date('Y-m') . "-21")));

            $awal3 = strtotime('-4 month', strtotime(date('Y-m') . "-21"));
            $akhir3 = strtotime('-3 month', strtotime(date('Y-m') . "-20"));
            $namaB3 = date("F", strtotime('-4 month', strtotime(date('Y-m') . "-21")));
        }

        $hari_effective = [];
        $sabtuminggu = [];

        $work_type_day = [];
        $work_type = WorkTypes::where('type', 'reguler')->get();


        foreach ($work_type as $key => $value) {
            $work_type_day[$value->id] = [
                WorkTypeDays::where('work_type_id', $value->id)->get()->keyBy('day_id')->toArray()
            ];
        }


        // mulai mencari persentase bulan 1
        $jumlah_hadir = 0;
        $absence = Absence::selectRaw('count(absence_logs.id) as jmlh_masuk, staffs.work_type_id')
            ->rightJoin('absence_logs', 'absences.id', '=', 'absence_logs.absence_id')
            ->leftJoin('staffs', 'staffs.id', '=', 'absences.staff_id')
            ->where('absence_category_id', '1')
            ->where('absence_logs.status', '0')
            ->where('absences.staff_id', '404')
            ->whereBetween(DB::raw('DATE(absences.created_at)'), [$awal1, $akhir1])
            ->first();
        if ($absence) {
            $jumlah_hadir = $absence->jmlh_masuk;
        } else {
            $jumlah_hadir = 0;
        }
        for ($i = $awal1; $i <= $akhir1; $i += (60 * 60 * 24)) {
            if (!empty($work_type_day[$absence->work_type_id][0][date('w', $i)])) {
                $hari_effective[] = $i;
            }
            if (date('w', $i) === 0 && $work_type_day[1][0]['7']) {
                $hari_effective[] = $i;
            } else {
                $sabtuminggu[] = $i;
            }
        }


        // libur nasional
        $holidays = Holiday::selectRaw('count(holidays.id) as holiday_total')
            ->whereBetween(DB::raw('DATE(holidays.start)'), [$awal1, $akhir1])
            ->first();

        $jumlah_effective = count($hari_effective);
        // dd($jumlah_effective);
        $jumlah_sabtuminggu = count($sabtuminggu);

        $hari_setelah_libur = $jumlah_effective - $holidays->holyday_total;

        if ($hari_setelah_libur > 0) {
            $persentase =  $jumlah_hadir / $jumlah_effective - $holidays->holyday_total;
        } else {
            $persentase = 0;
        }
        dd($jumlah_sabtuminggu, $persentase);
        return $abtotal;

        ini_set("memory_limit", -1);
        set_time_limit(0);
        $month = date("m", strtotime('-1 month', strtotime(date('Y-m-01'))));
        $year =  date("Y", strtotime('-1 month', strtotime(date('Y-m-01'))));
        $data12 = [];
        $mapping = CtmGambarmetersms::selectRaw('gambarmetersms.nomorrekening,
        gambarmetersms.tanggal,
        gambarmeter.filegambar,
        gambarmeter.infowaktu,
        tblpelanggan.nomorrekening,
        tblpelanggan.namapelanggan,
        tblpelanggan.namapelanggan,
        tblpelanggan.idgol,
        tblpelanggan.idareal,
        tblpelanggan.alamat,
        map_koordinatpelanggan.lat,
        map_koordinatpelanggan.lng,
        gambarmetersms.bulanrekening,
        gambarmetersms.tahunrekening,
        tblopp.operator,
        Elt(gambarmetersms.bulanrekening, tblpemakaianair.pencatatanmeter1, tblpemakaianair.pencatatanmeter2, tblpemakaianair.pencatatanmeter3, tblpemakaianair.pencatatanmeter4, tblpemakaianair.pencatatanmeter5, tblpemakaianair.pencatatanmeter6, tblpemakaianair.pencatatanmeter7, tblpemakaianair.pencatatanmeter8, tblpemakaianair.pencatatanmeter9, tblpemakaianair.pencatatanmeter10, tblpemakaianair.pencatatanmeter11, tblpemakaianair.pencatatanmeter12) pencatatanmeter, Elt(gambarmetersms.bulanrekening, tblpemakaianair.pemakaianair1, tblpemakaianair.pemakaianair2, tblpemakaianair.pemakaianair3, tblpemakaianair.pemakaianair4, tblpemakaianair.pemakaianair5, tblpemakaianair.pemakaianair6, tblpemakaianair.pemakaianair7, tblpemakaianair.pemakaianair8, tblpemakaianair.pemakaianair9, tblpemakaianair.pemakaianair10, tblpemakaianair.pemakaianair11, tblpemakaianair.pemakaianair12) pemakaianair')
            ->join('tblpemakaianair', 'tblpemakaianair.nomorrekening', '=', 'gambarmetersms.nomorrekening')

            ->join('gambarmeter', 'gambarmeter.idgambar', '=', 'gambarmetersms.idgambar')
            ->join('tblpelanggan', 'tblpelanggan.nomorrekening', '=', 'gambarmetersms.nomorrekening')
            ->join('map_koordinatpelanggan', 'map_koordinatpelanggan.nomorrekening', '=', 'tblpelanggan.nomorrekening')
            ->join('tblopp', 'tblopp.nomorrekening', '=', 'gambarmetersms.nomorrekening')
            // ->groupBy('tblpelanggan.nomorrekening')
            ->where('gambarmetersms.bulanrekening', $month)
            ->where('gambarmetersms.tahunrekening', $year)
            ->where('tblpemakaianair.tahunrekening', $year)
            // ->FilterOperator($operator)
            // ->FilterSbg($request->nomorrekening)
            ->where('tblopp.status', '1')
            // ->groupBy('tblpelanggan.nomorrekening')
            ->orderByRaw('tblpelanggan.nomorrekening * 1')
            // ->skip(0)
            // ->take(10)
            ->get();
        // dd('selesai');
        foreach ($mapping as $data) {
            $data12[] =   [
                'Nomor Rekening' => $data->nomorrekening,
                'Nama'  => $data->namapelanggan,
                'Alamat'  => $data->alamat,
                'Golongan'  => $data->idgol,
                'Area'  => $data->idareal,
                'X'  => $data->lat,
                'Y'  => $data->lng,
                'Periode'  => $data->tanggal,
                'Kubikasi' => $data->pemakaianair,

            ];
        }

        // dd($data12, $mapping);
        return Excel::download(new TestExport($data12), 'data_pelanggan.xlsx');
        // dd($mapping);


        // $date_now = date('Y-m-d');
        // $date_comp = date('Y-m') . '-20';
        // $last_4_month = date("Y-n-d", strtotime('-4 month', strtotime(date('Y-m-01'))));
        // // return view('admin.gawatdarurat.editImport');
        // if ($date_now > $date_comp) {
        //     $qry = CtmPelanggan::selectRaw('tblpelanggan.nomorrekening,tblpelanggan.idareal,tblwilayah.group_unit,tblpembayaran.bulanrekening, (((count(tblpembayaran.statuslunas) * 2) - sum(tblpembayaran.statuslunas)) DIV 2) as jumlahtunggakan,  (case when( (((count(tblpembayaran.statuslunas) * 2) - sum(tblpembayaran.statuslunas)) DIV 2) > 1 ) THEN 1 ELSE 0 END) as statusnunggak, tblpelanggan.status')
        //         ->join('tblwilayah', 'tblpelanggan.idareal', '=', 'tblwilayah.id')
        //         ->join('tblpembayaran', 'tblpelanggan.nomorrekening', '=', 'tblpembayaran.nomorrekening')

        //         ->whereDate(DB::raw('concat(tblpembayaran.tahunrekening,"-",tblpembayaran.bulanrekening,"-01")'), '<=', date('Y-n-01'))
        //         ->whereDate(DB::raw('concat(tblpembayaran.tahunrekening,"-",tblpembayaran.bulanrekening,"-01")'), '>=', $last_4_month)
        //         ->having('jumlahtunggakan', '>', 3)
        //         ->where('tblpelanggan.nomorrekening', '54172')
        //         // ->orHaving('jumlahtunggakan', '<', 3)
        //         ->orWhere('tblpelanggan.status', 1)
        //         ->whereDate(DB::raw('concat(tblpembayaran.tahunrekening,"-",tblpembayaran.bulanrekening,"-01")'), '<=', date('Y-n-01'))
        //         ->whereDate(DB::raw('concat(tblpembayaran.tahunrekening,"-",tblpembayaran.bulanrekening,"-01")'), '>=', $last_4_month)
        //         ->having('jumlahtunggakan', '<', 3)
        //         // ->where('tblpelanggan.nomorrekening', '54172')
        //         ->groupBy('tblpembayaran.nomorrekening')->paginate(10, ['*'], 'page', 1);
        //     dd('1', $qry);
        // } else {
        //     $qry = CtmPelanggan::selectRaw('tblpelanggan.nomorrekening,tblpelanggan.idareal,tblwilayah.group_unit,tblpembayaran.bulanrekening, (((count(tblpembayaran.statuslunas) * 2) - sum(tblpembayaran.statuslunas)) DIV 2) as jumlahtunggakan,  (case when( (((count(tblpembayaran.statuslunas) * 2) - sum(tblpembayaran.statuslunas)) DIV 2) > 1 ) THEN 1 ELSE 0 END) as statusnunggak')
        //         ->join('tblwilayah', 'tblpelanggan.idareal', '=', 'tblwilayah.id')
        //         ->join('tblpembayaran', 'tblpelanggan.nomorrekening', '=', 'tblpembayaran.nomorrekening')
        //         ->where('tblpelanggan.status', 1)
        //         ->whereDate(DB::raw('concat(tblpembayaran.tahunrekening,"-",tblpembayaran.bulanrekening,"-01")'), '<', date('Y-n-01'))
        //         ->whereDate(DB::raw('concat(tblpembayaran.tahunrekening,"-",tblpembayaran.bulanrekening,"-01")'), '>=', $last_4_month)
        //         // ->having('jumlahtunggakan', 2)
        //         ->where('tblpelanggan.nomorrekening', '54172')
        //         ->groupBy('tblpembayaran.nomorrekening');
        //     dd('2', $qry->get());
        // }
    }
    public function store(Request $request)
    {
        // Staff::where('id', $id)->first();

    }

    public function import(Request $request)
    {
        abort_unless(\Gate::allows('staff_edit'), 403);
        $import = new StaffImport;
        $test =  Excel::import($import, $request->file('file'));
        // dd($test);
        $array = $import->getArray();
        // dd($array);
        abort_unless(\Gate::allows('wablast_access'), 403);

        $staffs = $import->getArray();

        // dd($staffs);
        ini_set("memory_limit", -1);
        set_time_limit(0);
        //ini test

        // dd($staffs[2]['id']);
        for ($i = 0; $i < (count($staffs) - 1); $i++) {
            if ($staffs[$i]['work_unit_id'] != null && $staffs[$i]['id'] != null) {
                $staff = Staff::where('id', $staffs[$i]['id'])->update([
                    'work_unit_id' => $staffs[$i]['work_unit_id']
                ]);
                // dd($staff);
                // $staff->work_unit_id = $staffs[$i]['work_unit_id'];
                // $staff->nomorhp = $staffs[$i]['nomorhp'];
                // $staff->_synced = 0;
                // $staff->save();
                // dd($staff);
            }
        }
        dd($staffs);

        // return redirect()->route('admin.staffs.index');

        dd($request->file('file'));
        Staff::where('id', $id)->first();
    }
}
