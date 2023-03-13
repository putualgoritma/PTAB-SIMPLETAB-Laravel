<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\MessageLog;
use App\Staff;
use Illuminate\Http\Request;

class CronJobController extends Controller
{
    public function index()
    {
        $data = [];
        if (date('w') == '0') {
            $day = '7';
        } else {
            $day = date('w');
        }
        // reguler
        // $message = MessageLog::get();
        // if (!$message) {
        //     Staff::join('work_types', 'work_types.id', '=', 'staffs.work_type_id')
        //         ->join('work_type_days', '=', 'work_type_days.work_type_id', '=', 'work_types.id')
        //         ->join('absence_categories', 'absence_categories.id', '=', 'work_type_days')
        //         ->where('day_id', $day)
        //         ->where('categories.type', 'presence')
        //         ->where('categories.queue', '1')
        //         ->get();
        // }
        $reguler =  Staff::join('work_types', 'work_types.id', '=', 'staffs.work_type_id')
            ->join('work_type_days', 'work_type_days.work_type_id', '=', 'work_types.id')
            ->join('absence_categories', 'absence_categories.id', '=', 'work_type_days.absence_category_id')
            ->where('day_id', $day)
            ->where('absence_categories.type', 'presence')
            ->where('absence_categories.queue', '1')
            ->get();

        for ($i = 0; $i < count($reguler); $i++) {
            $time = date("Y-m-d H:i:s",  strtotime(date('Y-m-d ' . $reguler[0]->time)));

            $start = date("Y-m-d H:i:s", strtotime('- ' . 2 . ' minutes', strtotime(date('Y-m-d H:i:s'))));
            $end = date("Y-m-d H:i:s", strtotime('+ ' . 1 . ' minutes', strtotime(date('Y-m-d H:i:s'))));

            if ($start < $time && $time < $end) {
                $data[] = [$reguler[0]->name, $reguler[0]->time];
            }
        }

        // dd($time, $start, $end, $data);
        $shift = Staff::join('shift_planner_staffs', 'shift_planner_staffs.staff_id', '=', 'staffs.id')
            ->join('shift_groups', 'shift_planner_staffs.shift_group_id', '=', 'shift_groups.id')
            ->join('shift_group_timesheets', 'shift_groups.id', '=', 'shift_group_timesheets.shift_group_id')
            ->join('absence_categories', 'absence_categories.id', '=', 'shift_group_timesheets.absence_category_id')
            // ->where('shift_planner')
            ->whereDate('shift_planner_staffs.start', '=', date('Y-m-d'))
            ->where('absence_categories.type', 'presence')
            ->where('absence_categories.queue', '1')
            ->get();
        $data2 = [];

        for ($i = 0; $i < count($reguler); $i++) {
            $time = date("Y-m-d H:i:s",  strtotime(date('Y-m-d ' . $reguler[0]->time)));

            $start = date("Y-m-d H:i:s", strtotime('- ' . 2 . ' minutes', strtotime(date('Y-m-d H:i:s'))));
            $end = date("Y-m-d H:i:s", strtotime('+ ' . 1 . ' minutes', strtotime(date('Y-m-d H:i:s'))));

            if ($start < $time && $time < $end) {
                $data2[] = [$reguler[0]->name, $reguler[0]->time];
            }
        }
        // dd($shift);
        dd($data2);
    }
}
