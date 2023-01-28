<?php

namespace App\Http\Controllers\Api\V1\Absence;

use App\Http\Controllers\Controller;
use App\Shift;
use App\ShiftStaff;
use Illuminate\Http\Request;

class ShiftApiController extends Controller
{
    public function index(Request $request)
    {
        $data = [];
        $data2 = [];
        $staff_list = ShiftStaff::selectRaw('shift_staff.id ,user.name as user_name, user_change.name as userc_name')
            ->join('users as user', 'user.id', '=', 'shift_staff.staff_id')
            ->leftJoin('users as user_change', 'user_change.id', '=', 'shift_staff.change_staff_id')
            ->where('shift_id', $request->shift_id)
            ->whereDate('date', '=', $request->date)
            ->get();

        $shift_list = Shift::get();

        foreach ($staff_list as $key => $value) {
            $data[] = ['id' => $value->id, 'name' => $value->user_name, 'checked' => false];
        }

        foreach ($shift_list as $key => $value) {
            $data2[] = ['id' => $value->id, 'name' => $value->title, 'checked' => false];
        }
        return response()->json([
            'message' => 'success',
            'data' => $data,
            'data2' => $data2,
        ]);
    }

    public function listChange(Request $request)
    {
        $staff_list = ShiftStaff::selectRaw('shift_staff.id ,user.name as user_name, user_change.name as userc_name, shifts.*, shift_staff.*')
            ->join('shifts', 'shifts.id', '=', 'shift_staff.shift_id')
            ->join('users as user', 'user.id', '=', 'shift_staff.staff_id')
            ->join('users as user_change', 'user_change.id', '=', 'shift_staff.change_staff_id')
            ->where('change_staff_id', $request->staff_id)
            ->get();

        return response()->json([
            'message' => 'pengajuan berhasil',
            'data' => $staff_list,
        ]);
    }

    public function update(Request $request)
    {
        $dataForm = json_decode($request->form);
        $change = [];
        $staff_list = ShiftStaff::selectRaw('shift_staff.id ,user.name as user_name, user_change.name as userc_name')
            ->join('users as user', 'user.id', '=', 'shift_staff.staff_id')
            ->join('users as user_change', 'user_change.id', '=', 'shift_staff.change_staff_id')
            ->where('shift_staff.id', $dataForm->id)
            ->first();
        if ($staff_list) {
            return response()->json([
                'message' => 'pengajuan berhasil',
                'data' => $change,
            ]);
        } else {
            $change = ShiftStaff::where('id', $dataForm->id)->update(['change_staff_id' => $dataForm->staff_id, 'description' => $dataForm->description]);
            return response()->json([
                'message' => 'pengajuan berhasil',
                'data' => $change,
            ]);
        }
    }

    public function approve(Request $request)
    {
        $change = ShiftStaff::where('id', $request->id)->update(['staff_id' => $request->staff_id, 'change_staff_id' => '']);
        return response()->json([
            'message' => 'pengajuan berhasil',
            'data' => $change,
        ]);
    }

    public function myShift(Request $request)
    {
        $staff_list = ShiftStaff::selectRaw('shifts.time_in, shifts.time_out, shifts.title, shift_staff.id, shift_staff.date, shift_staff.id ,user.name as user_name')
            ->join('users as user', 'user.id', '=', 'shift_staff.staff_id')
            ->join('shifts', 'shifts.id', '=', 'shift_staff.shift_id')
            ->where('shift_staff.staff_id', $request->staff_id)
            ->get();
        return response()->json([
            'message' => 'success',
            'data' =>  $staff_list,
        ]);
    }
}
