<?php

namespace App\Http\Controllers\Api\V1\Absence;

use App\Absence;
use App\Http\Controllers\Controller;
use App\Requests;
use App\Requests_file;
use Illuminate\Http\Request;

class RequestApiController extends Controller
{

    public function index(Request $request)
    {
        $workPermit = Absence::where('user_id', $request->id)->where('register', $request->date)->get();
        $absenOut = Absence::where('user_id', $request->id)->where('absen_category_id', $request->absen_category_id)->get();
        $wP = '0';
        $aO = '0';
        if (count($workPermit) > 0) {
            $wP = '0';
        } else {
            $wP = '1';
        }
        if (count($absenOut) > 0) {
            $aO = '1';
        } else {
            $wP = '0';
        }
        return response()->json([
            'message' => 'Pengajuan Terkirim',
            'absenOut' => $aO,
            'workPermit' => $wP,
        ]);
    }

    public function store(Request $request)
    {

        // $last_code = $this->get_last_code('lock_action');

        // $code = acc_code_generate($last_code, 8, 3);
        $dataForm = json_decode($request->form);


        $requests = new Requests();
        $requests->user_id = $dataForm->user_id;
        $requests->description = $dataForm->description;
        $requests->date = $dataForm->date == "" ? $dataForm->date : date('Y-m-d');
        $requests->end = $dataForm->end;
        $requests->type = $dataForm->type;
        $requests->start = $dataForm->start;
        $requests->status = $dataForm->status;
        $requests->category = $dataForm->category;

        $requests->save();
        $requests_id = $requests->id;


        if ($request->file('imageP')) {
            $image = $request->file('imageP');
            $resourceImage = $image;
            $nameImage = 'imageP' . date('Y-m-d h:i:s') . '.' . $image->extension();
            $file_extImage = $image->extension();
            $folder_upload = 'images/RequestFile';
            $resourceImage->move($folder_upload, $nameImage);

            // dd($request->file('old_image')->move($folder_upload, $img_name));

            // if ($actionWm->old_image != '') {
            //     foreach (json_decode($actionWm->old_image) as $n) {
            //         if (file_exists($n)) {

            //             unlink($basepath . $n);
            //         }
            //     }
            // }
            $data = [
                'file' => $nameImage,
                'requests_id' => $requests_id,
                'type' => 'persetujuan'
            ];
            $data = Requests_file::create($data);
        }

        if ($request->file('imagePng')) {
            $image = $request->file('imagePng');
            $resourceImage = $image;
            $nameImage = 'imagePng' . date('Y-m-d h:i:s') . '.' . $image->extension();
            $file_extImage = $image->extension();
            $folder_upload = 'images/RequestFile';
            $resourceImage->move($folder_upload, $nameImage);


            $data = [
                'file' =>  $nameImage,
                'requests_id' => $requests_id,
                'type' => 'pengajuan'
            ];
            $data = Requests_file::create($data);
        }

        return response()->json([
            'message' => 'Pengajuan Terkirim',
            'data' => $requests,
        ]);
    }
    public function history(Request $request)
    {
        $requests = Requests::where('user_id', $request->user_id)->get();
        return response()->json([
            'message' => 'Pengajuan Terkirim',
            'data' => $requests,
        ]);
    }

    public function getPermissionCat(Request $request)
    {
        $cat = [
            ['id' => '1', 'name' => 'sakit', 'checked' => false],
            ['id' => '2', 'name' => 'Izin', 'checked' => false],
            ['id' => '3', 'name' => 'Lain-Lain', 'checked' => false],
        ];
        return response()->json([
            'message' => 'Pengajuan Terkirim',
            'data' => $cat,
        ]);
    }
}
