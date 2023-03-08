<?php

namespace App\Http\Controllers\Api\V1\Absence;

use App\Http\Controllers\Controller;
use App\MessageLog;
use Illuminate\Http\Request;

class MessageApiController extends Controller
{
    public function index(Request $request)
    {
        $message_log =  MessageLog::where('staff_id', $request->staff_id)
            ->paginate(3, ['*'], 'page', $request->page);
        return response()->json([
            'message' => 'Pengajuan Terkirim',
            'data' => $message_log,
        ]);
    }
    public function read(Request $request)
    {
        $message_log = MessageLog::where('id', $request->id)->first();
        // return response()->json([
        //     'message' => 'Pengajuan Terkirim',
        //     'data' => $message_log,
        // ]);
        $message_log->status = "read";
        $message_log->save();
        return response()->json([
            'message' => 'Pengajuan Terkirim',
            'data' => $message_log,
        ]);
    }

    public function check(Request $request)
    {
        $message_log = MessageLog::where('id', $request->id)->first();
        $message_log->update([
            'status' => 'read',
            'lat' => $request->lat,
            'lng' => $request->lng
        ]);
        return response()->json([
            'message' => 'Pengajuan Terkirim',
            'data' => $message_log,
        ]);
    }
}
