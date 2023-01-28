<?php

namespace App\Http\Controllers\Api\V1\Absence;

use App\Http\Controllers\Controller;
use App\ShiftChange;
use Illuminate\Http\Request;

class ShiftChangeApiController extends Controller
{
    public function index(Request $request)
    {
        $shiftChange = ShiftChange::selectRaw('shiftsA.date, shifts.title, shifts.time_in, shifts.time_out, usersA.name, shift_changes.status, shiftsC.date as c_date, shiftsCC.title as c_title, shiftsCC.time_in as C_time_in, shiftsCC.time_out as C_time_out')
            ->leftJoin('shift_staff as shiftsA', 'shiftsA.id', '=', 'shift_changes.shift_id')
            ->leftJoin('shift_staff as shiftsC', 'shiftsC.id', '=', 'shift_changes.shift_change_id')
            ->leftJoin('shifts', 'shifts.id', '=', 'shiftsA.shift_id')
            ->leftJoin('shifts as shiftsCC', 'shiftsCC.id', '=', 'shiftsC.shift_id')
            ->leftJoin('users as usersA', 'usersA.id', '=', 'shiftsA.staff_id')
            ->where('shiftsC.staff_id', $request->id)
            ->get();
        return response()->json([
            'message' => 'pengajuan berhasil',
            'data' =>  $shiftChange,
        ]);
    }

    public function store(Request $request)
    {
        $dataForm = json_decode($request->form);
        if ($dataForm->date > date('Y-m-d')) {
            $data = [
                'shift_id' => $dataForm->id,
                'shift_change_id' => $dataForm->shift_change_id,
                'description' => $dataForm->description,
                'status' => 'pending',
            ];
            $shiftChange = ShiftChange::create($data);
            return response()->json([
                'message' => 'pengajuan berhasil',
                'data' =>  $shiftChange,
            ]);
        } else {
            return response()->json([
                'message' => 'failed',
            ]);
        }
    }
}
