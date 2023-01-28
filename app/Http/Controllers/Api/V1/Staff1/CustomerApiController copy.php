<?php

namespace App\Http\Controllers\Api\V1\Staff;

use App\AreaStaff;
use App\CtmPelanggan;
use App\CtmWilayah;
use App\CustomerMaps;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class CustomerApiController extends Controller
{
    public function getCtmarealgroup(Request $request, $id)
    {
        $user = User::where('id', $id)->first();
        if ($user->staff_id != 0 || $user->staff_id != null) {
            $data = AreaStaff::select('area_id')->where('staff_id', $user->staff_id)->get();
            if ($request->search == "") {
                // $data1 = "2";
                if (count($data) > 0) {
                    $wilayah = CustomerMaps::join('tblpelanggan', 'tblpelanggan.nomorrekening', '=', 'map_koordinatpelanggan.nomorrekening')->where('lat', '!=', "")->where('lng', '!=', "");
                    for ($i = 0; $i < count($data); $i++) {
                        if ($i < 1) {


                            $wilayah->where('tblpelanggan.idareal', $data[$i]->area_id);


                            // $data2 = $data2 . ' where idareal = ' . $data[$i]->area_id;
                        } else {
                            $wilayah->orWhere('tblpelanggan.idareal', $data[$i]->area_id);
                        }
                    }
                } else {
                    $wilayah->where('tblpelanggan.nomorrekening', null);
                }
            } else {
                $data = AreaStaff::select('area_id')->where('staff_id', $user->staff_id)->get();
                if (count($data) > 0) {
                    $wilayah = CustomerMaps::join('tblpelanggan', 'tblpelanggan.nomorrekening', '=', 'map_koordinatpelanggan.nomorrekening')->where('lat', '!=', "");
                    for ($i = 0; $i < count($data); $i++) {
                        if ($i < 1) {
                            // $data1 = "1";

                            $wilayah->where('tblpelanggan.idareal', $data[$i]->area_id)->where('map_koordinatpelanggan.nomorrekening', $request->search);


                            // $data2 = $data2 . ' where idareal = ' . $data[$i]->area_id;
                        } else {
                            $wilayah->orWhere('tblpelanggan.idareal', $data[$i]->area_id)->where('map_koordinatpelanggan.nomorrekening', $request->search);
                        }
                    }
                } else {
                    $wilayah->where('tblpelanggan.nomorrekening', null);
                }
            }

            $wilayah = $wilayah->paginate(10, ['*'], 'page', $request->page);
        } else {
            if ($request->search != "") {
                $wilayah = CustomerMaps::join('tblpelanggan', 'tblpelanggan.nomorrekening', '=', 'map_koordinatpelanggan.nomorrekening')->where('lat', '!=', "")->where('lng', '!=', "")->where('map_koordinatpelanggan.nomorrekening', $request->search)
                    ->paginate(10, ['*'], 'page', $request->page);
            } else {
                $wilayah = CustomerMaps::join('tblpelanggan', 'tblpelanggan.nomorrekening', '=', 'map_koordinatpelanggan.nomorrekening')->where('lat', '!=', "")->where('lng', '!=', "")->paginate(10, ['*'], 'page', $request->page);
            }
        }
        try {
            if (!empty($wilayah)) {
                return response()->json([
                    'message' => 'Sukses',
                    'data' => $wilayah,
                ]);
            }
        } catch (QueryException $ex) {
            return response()->json([
                'message' => 'Gagal',
                'data' => $ex,
            ]);
        }
    }

    public function ctmCustomer($id)
    {
        try {
            $ctm = CtmPelanggan::where('nomorrekening', $id)
                ->first();
            $ctm->year = date('Y');
            return response()->json([
                'message' => 'Data CTM',
                'data' => $ctm,
            ]);
        } catch (QueryException $ex) {
            return response()->json([
                'message' => 'Gagal Mengambil data',
                'err' => $ex,
            ]);
        }
    }

    public function ctmPay($id)
    {
        try {
            $date_now = date("Y-m-d");
            $date_comp = date("Y-m") . "-20";
            $month_now = date('n');
            $month_next = date('n', strtotime('+1 month')) - 1;
            if ($month_now > $month_next) {
                $month_next = $month_next + 12;
            }

            $data = array(
                'nomorrekening' => $id,
            );

            $url = 'https://yndvck.perumdatab.com/akademi-pelawak-tpi/tgh.api.php';

            //open connection
            $ch = curl_init();

            //set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, count($data));
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            //execute post
            $ctm = curl_exec($ch);
            $ctm = json_decode($ctm);

            //close connection
            curl_close($ch);

            if ($date_now > $date_comp) {
                $ctm_lock = 0;
            } else {
                $ctm_lock = 1;
            }

            $ctm_num_row = count($ctm) - 1;
            foreach ($ctm as $key => $item) {
                //get sudah dibayar
                $item->sudahdibayar = 0;
                if ($item->statuslunas == 2) {
                    $item->sudahdibayar = $item->wajibdibayar;
                }
                $sisa = $item->wajibdibayar - $item->sudahdibayar;
                //if not paid
                if ($sisa > 0) {
                    $ctm[$key]->tglbayarterakhir = "";
                }
                //denda & $item->sudahdibayar=$item->wajibdibayar;
                $ctm[$key]->denda = 0;
                $ctm[$key]->sudahdibayar = $item->sudahdibayar;
                //set to prev
                $ctm[$key]->tahunrekening = date('Y', strtotime(date($item->tahunrekening . '-' . $item->bulanrekening . '-01') . " -1 month"));
                $ctm[$key]->bulanrekening = date('m', strtotime(date($item->tahunrekening . '-' . $item->bulanrekening . '-01') . " -1 month"));
                //if status 0
                if ($ctm[$key]->status == 0 && $key == $ctm_num_row) {
                    unset($ctm[$key]);
                }
            }
            return response()->json([
                'message' => 'Data CTM',
                'data' => $ctm,
                'month_next' => $month_next,
                'ctm_lock' => $ctm_lock,
            ]);
        } catch (QueryException $ex) {
            return response()->json([
                'message' => 'Gagal Mengambil data',
                'err' => $ex,
            ]);
        }
    }

    public function scanBarcode(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);

        $code = $request->code;

        try {
            $customerMaps = CustomerMaps::where('qrcode', $code)->first();
            if (empty($customerMaps)) {
                return response()->json([
                    'status' => '0',
                    'message' => 'Data Kosong',
                ]);
            } else {
                $customer = CustomerApi::WhereMaps('code', $customerMaps->nomorrekening)->first();

                if (isset($customer)) {
                    $pass_set = 0;
                    if ($customer->password != '') {
                        $pass_set = 1;
                    }
                    return response()->json([
                        'status' => '1',
                        'message' => 'Anda terdaftar sebagai pelanggan',
                        'data' => $customer,
                        'pass_set' => $pass_set,
                    ]);
                } else {
                    return response()->json([
                        'status' => '0',
                        'message' => 'Data Kosong',
                    ]);
                }
            }
        } catch (QueryException $ex) {
            return response()->json([
                'status' => '0',
                'message' => $ex,
            ]);
        }
    }
}
