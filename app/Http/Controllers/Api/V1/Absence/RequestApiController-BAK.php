<?php

namespace App\Http\Controllers\Api\V1\Absence;

use App\Http\Controllers\Controller;
use App\Requests;
use Illuminate\Http\Request;

class RequestApiController extends Controller
{
    public function store(Request $request)
    {

        // $last_code = $this->get_last_code('lock_action');

        // $code = acc_code_generate($last_code, 8, 3);
        $dataForm = json_decode($request->form);


        $requests = new Requests();
        $requests->user_id = $dataForm->user_id;
        $requests->description = $dataForm->description;
        $requests->date = $dataForm->category == "permit" ? $dataForm->date : date('Y-m-d');
        $requests->end = $dataForm->end;
        $requests->type = $dataForm->type;
        $requests->start = $dataForm->start;
        $requests->status = $dataForm->status;
        $requests->category = $dataForm->category;

        $requests->save();

        return response()->json([
            'message' => 'Pengajuan Terkirim',
            'data' => $requests,
        ]);
    }
}
