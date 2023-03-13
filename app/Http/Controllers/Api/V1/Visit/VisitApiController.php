<?php

namespace App\Http\Controllers\Api\V1\Visit;

use App\CtmStatussmPelanggan;
use App\Customer;
use App\Http\Controllers\Controller;
use App\Staff;
use App\Visit;
use App\VisitImage;
use Illuminate\Http\Request;

class VisitApiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $staff = Staff::where('id', $request->staff_id)->first();

            $data =
                CtmStatussmPelanggan::selectRaw('tblpelanggan.nomorrekening,tblstatuswm.NamaStatus,tblstatussmpelanggan.nomorrekening, tblpelanggan.namapelanggan, tblpelanggan.idareal as idareal, tblstatussmpelanggan.bulan, tblopp.operator, tblstatussmpelanggan.tahun')
                ->join('tblpelanggan', 'tblpelanggan.nomorrekening', '=', 'tblstatussmpelanggan.nomorrekening')
                ->join('tblwilayah', 'tblwilayah.id', '=', 'tblpelanggan.idareal')
                ->join('tblstatuswm', 'tblstatuswm.id', '=', 'tblstatussmpelanggan.statussm')
                ->join('tblopp', 'tblopp.nomorrekening', '=', 'tblpelanggan.nomorrekening')
                // ->where('tblwilayah.group_unit', $group_unit)
                ->where(function ($query) {
                    //$query->where('tblpelanggan.idareal', $data[0]->area_id);
                    $query->where('statussm', '=', 107);
                    $query->orWhere('statussm', '=', 105);
                })
                ->where('tblopp.operator', 'like', $staff->pbk)
                ->where('tahun', date('Y', strtotime('0 month', strtotime(date('Y-m-d')))))->where('bulan', date('m', strtotime('0 month', strtotime(date('Y-m-d')))))->FilterStatusWM($request->statussm)->FilterAreas($request->areas)
                ->FilterStatusWM($request->statussm)->FilterAreas($request->areas)
                ->paginate(10, ['*'], 'page', $request->page);
            return response()->json([
                'message' => 'success',
                'data' => $data,
            ]);
        } catch (QueryException $ex) {
            return response()->json([
                'message' => 'Gagal Mengambil data',
                'err' => $ex,
            ]);
        }
    }


    public function store(Request $request)
    {

        // $last_code = $this->get_last_code('lock_action');

        // $code = acc_code_generate($last_code, 8, 3);
        // $img_path = "/images/segelMeter";
        // $basepath = str_replace("laravel-simpletab", "public_html/simpletabadmin/", \base_path());
        // $dataForm = json_decode($request->form);

        try {
            //     $visit = new Visit;
            //     $visit->staff_id = $dataForm->$staff_id;
            //     $visit->customer_id = $dataForm->customer_id;
            //     $visit->status_wm = $dataForm->status_wm;
            //     $visit->memo = $dataForm->memo;
            //     $visit->lat = $dataForm->lat;
            //     $visit->lng = $dataForm->lng;

            //     $visit->save();
            $tes = [];
            for ($i = 0; $i < count($request->image); $i++) {
                # code...
                $tes[] = ['sssss' => $request->image];
            }
            // if ($request->file('image')) {
            //     $image = $request->file('imagePng');
            //     $resourceImage = $image;
            //     $nameImage = 'image' . date('Y-m-d h:i:s') . '.' . $image->extension();
            //     $file_extImage = $image->extension();
            //     $folder_upload = 'images/Visit';
            //     $resourceImage->move($folder_upload, $nameImage);


            //     $data = [
            //         'image' =>  $nameImage,
            //         'visit_id' => $visit_id
            //     ];
            //     $data = VisitImage::create($data);
            // }


            return response()->json([
                'message' => 'Segel Meter Terkirim',
                'data' => $visit,
            ]);
        } catch (QueryException $ex) {
            return response()->json([
                'message' => 'gagal',
            ]);
        }
    }
    public function show($id)
    {
        $customer = Customer::selectRaw('tblpelanggan.*, map_koordinatpelanggan.lat, map_koordinatpelanggan.lng')
            ->leftJoin('map_koordinatpelanggan', 'map_koordinatpelanggan.nomorrekening', '=', 'tblpelanggan.nomorrekening')
            ->where('tblpelanggan.nomorrekening', $id)
            ->first();
        return response()->json([
            'message' => 'Show',
            'data' => $customer,
        ]);
    }
}
