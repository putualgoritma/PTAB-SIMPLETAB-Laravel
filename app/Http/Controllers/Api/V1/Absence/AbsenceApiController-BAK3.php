<?php

namespace App\Http\Controllers\Api\V1\Absence;

use App\Absence;
use App\Absence_categories;
use App\AbsenceLog;
use App\Holiday;
use App\Http\Controllers\Controller;
use App\Requests;
use App\Shift;
use App\ShiftGroups;
use App\ShiftPlannerStaff;
use App\ShiftPlannerStaffs;
use App\ShiftStaff;
use App\WorkTypeDays;
use App\WorkUnit;
use Illuminate\Http\Request;

class AbsenceApiController extends Controller
{

    // ketika sudah dibuatkan absence oleh sistem
    public function index(Request $request)
    {

        $coordinat = WorkUnit::join('staffs', 'staffs.work_unit_id', '=', 'work_units.id')
            ->join('work_types', 'staffs.work_type_id', '=', 'work_types.id')
            ->where('staffs.id', '404')->first();
        $menu = "";

        $holiday = Holiday::whereDate('start', '<=', date('Y-m-d'))->whereDate('end', '>=', date('Y-m-d'))->first();
        // cek hari libur
        if ($holiday) {
            $menu = 'OFF';
            return response()->json([
                'message' => 'Success',
                'menu' => $menu,
                'date' => date('Y-m-d h:i:s')
            ]);
        }
        // cek jadwal biasa
        else {
            if (date('w') == '0') {
                $day = '7';
            } else {
                $day = date('w');
            }

            // $cek = Absence::whereDate('created_at', '=', date('Y-m-d H:i:s'))->first();

            // if ($cek) {

            // cek absen apa ada absen hari ini
            $absence = Absence::selectRaw('absences.id, absence_logs.expired_date,shift_planner_id, queue, absences.day_id , status_active, absence_categories.id as absence_category_id')
                ->rightJoin('absence_logs', 'absence_logs.absence_id', '=', 'absences.id')
                ->leftJoin('absence_categories', 'absence_logs.absence_category_id', '=', 'absence_categories.id')
                ->where('staff_id', '323')
                ->where('absence_logs.expired_date', '>=', date('Y-m-d H:i:s'))
                ->where('absence_logs.status', '=', 1)
                ->where('absence_categories.type', '!=', 'break')
                ->orderBy('absence_logs.id', 'DESC')
                ->first();
            $a1 = "1";
            // jika ada absen hari ini
            if ($absence) {
                if ($absence->shift_planner_id === 0) {
                    $absen = AbsenceLog::selectRaw('absence_categories.*, work_type_days.start, work_type_days.end')
                        ->join('work_type_days', 'work_type_days.absence_category_id', '=', 'absence_categories.id')
                        ->join('work_types', 'work_type_days.work_type_id', '=', 'work_types.id')
                        ->where('work_types.id', '1')
                        ->where('work_type_days.absence_category_id', $absence->id)
                        ->where('work_type_days.day_id', $absence->day_id)
                        ->where('absence_logs.status', '=', 1)
                        // ->where('queue', $absence->queue)
                        ->first();
                    $a1 = "2";
                } else {
                    $absen = AbsenceLog::selectRaw('absence_logs.*, shift_group_timesheets.start, shift_group_timesheets.end')->leftJoin('absences', 'absences.id', '=', 'absence_logs.absence_id')
                        ->join('absence_categories', 'absence_categories.id', '=', 'absence_logs.absence_category_id')
                        ->join('shift_group_timesheets', 'absence_categories.id', '=', 'shift_group_timesheets.absence_category_id')
                        // ->where('work_types.id', '1')
                        // ->where('work_type_days.absence_category_id', $absence[0]->id)
                        ->where('absence_logs.status', '=', 1)
                        ->where('absence_id', $absence->id)
                        ->where('absence_categories.type', '!=', 'break')
                        ->orderBy('absence_logs.id', 'DESC')
                        // ->where('queue', $absence->queue)
                        ->first();

                    $a1 = "2";
                }
                // if ($absen == null) {
                //     $absen = "Absen Hari Ini Sudah Selesai";
                // }
            }
            // ketika tidak ada absen di tanggal tersebut
            else {

                // cek apa ada shift di tanggal ini
                if ($coordinat->type == "shift") {
                    $a1 = "3";
                    // buat baru start
                    // cek apa sudah ada group absen di tanggal ini
                    $c = Absence::selectRaw('shift_planner_staffs.id as shift_planner_id, shift_planner_staffs.shift_group_id')->rightJoin('shift_groups', 'shift_groups.id', '=', 'absences.shift_group_id')
                        ->join('shift_planner_staffs', 'shift_planner_staffs.shift_group_id', '=', 'shift_groups.id')
                        ->where('shift_planner_staffs.staff_id', '=', '323')
                        ->where('absences.id', '=', null)
                        ->whereDate('shift_planner_staffs.start', '=', '2023-02-11')
                        ->orderBy('shift_groups.queue', 'DESC')
                        ->get();

                    if (count($c) > 0) {
                        foreach ($c as $item) {

                            $data = [
                                'day_id' => $day,
                                'shift_group_id' => $item->shift_group_id,
                                'staff_id' => '323',
                                'created_at' => date('Y-m-d')
                            ];
                            $absence = Absence::create($data);
                            $list_absence = ShiftGroups::join('shift_group_timesheets', 'shift_group_timesheets.shift_group_id', '=', 'shift_groups.id')
                                ->join('absence_categories', 'shift_group_timesheets.absence_category_id', '=', 'absence_categories.id')
                                ->where('shift_groups.id', $item->shift_group_id)
                                ->orderBy('absence_categories.queue', 'DESC')
                                ->get();

                            $expired_date = date('Y-m-d H:i:s');
                            try {
                                for ($n = 0; $n < count($list_absence); $n++) {
                                    $expired_date = date("Y-m-d H:i:s", strtotime('+' . $list_absence[$n]->duration . ' hours', strtotime(date('Y-m-d ' . $list_absence[0]->time))));
                                    $status = 0;
                                    if ($n === (count($list_absence) - 1)) {
                                        $status =  1;
                                    } else if ($n === 2 && $list_absence[$n]->type == "break") {
                                        $status =  1;
                                    } else {
                                        $status =  0;
                                    }

                                    if ($list_absence[$n]->start == "0000-00-00") {
                                        $start_date =  null;
                                    } else {
                                        $start_date = date("Y-m-d H:i:s", strtotime(date('Y-m-d ' . $list_absence[$n]->start)));
                                    }

                                    $upload_image = new AbsenceLog;
                                    // sementara start
                                    $upload_image->absence_id = $absence->id;
                                    $upload_image->shift_planner_id = $item->shift_planner_id;

                                    $upload_image->start_date = $start_date;
                                    $upload_image->expired_date = $expired_date;
                                    // sementara end
                                    $upload_image->created_at = date('Y-m-d H:i:s');
                                    $upload_image->updated_at = date('Y-m-d H:i:s');
                                    $upload_image->status = 1;
                                    $upload_image->absence_category_id =  $list_absence[$n]->absence_category_id;
                                    // $upload_image->shift_id = $request->shift_id;

                                    $upload_image->save();
                                }
                            } catch (QueryException $ex) {
                                return response()->json([
                                    'message' => 'gagal',
                                ]);
                            }
                        }
                    }
                    return response()->json([
                        'message' => 'Absen Terkirim',
                        'message' => 'ssss',
                        'data' =>   $c,
                    ]);
                    // buat baru end
                }
                // jika tidak ada shift, dinas keluar kota, libur ataupun cuti, izin, atau sakit(mungkin dipisah untuk pengecekan)
                else {
                    $absen = Absence_categories::selectRaw('absence_categories.*, work_type_days.start, work_type_days.end')
                        ->join('work_type_days', 'work_type_days.absence_category_id', '=', 'absence_categories.id')
                        ->join('work_types', 'work_type_days.work_type_id', '=', 'work_types.id')
                        ->where('work_types.id', '1')
                        ->where('day_id', '4')
                        ->first();

                    // buat baru start
                    // cek apa sudah ada group absen di tanggal ini
                    $c = Absence::whereDate('created_at', '=', date('Y-m-d'))->first();
                    if (!$c) {
                        $data = [
                            'day_id' => $day,
                            'shift_group_id' => $request->shift_group_id,
                            'staff_id' => '323',
                            'created_at' => date('Y-m-d')
                        ];
                        $absence = Absence::create($data);
                        $list_absence = WorkTypeDays::join('absence_categories', 'work_type_days.absence_category_id', '=', 'absence_categories.id')
                            ->where('work_type_id', $coordinat->work_type_id)
                            ->where('day_id', $day)
                            ->orderBy('queue', 'DESC')
                            ->get();
                        $expired_date = date('Y-m-d H:i:s');
                        try {
                            for ($n = 0; $n < count($list_absence); $n++) {
                                $expired_date = date("Y-m-d H:i:s", strtotime('+' . $list_absence[$n]->duration . ' hours', strtotime(date('Y-m-d ' . $list_absence[0]->time))));
                                $status = 0;
                                if ($n === (count($list_absence) - 1)) {
                                    $status =  1;
                                } else if ($n === 2 && $list_absence[$n]->type == "break") {
                                    $status =  1;
                                } else {
                                    $status =  0;
                                }
                                $upload_image = new AbsenceLog;
                                // sementara start
                                $upload_image->absence_id = $absence->id;

                                $upload_image->expired_date = $expired_date;
                                // sementara end
                                $upload_image->created_at = date('Y-m-d H:i:s');
                                $upload_image->updated_at = date('Y-m-d H:i:s');
                                $upload_image->status = $status;
                                $upload_image->absence_category_id =  $list_absence[$n]->absence_category_id;
                                // $upload_image->shift_id = $request->shift_id;

                                $upload_image->save();
                            }

                            return response()->json([
                                'message' => 'Absen Terkirim',
                                'data' => $list_absence,
                                'data1' => $coordinat->work_type_id
                            ]);
                        } catch (QueryException $ex) {
                            return response()->json([
                                'message' => 'gagal',
                            ]);
                        }
                    }
                    // buat baru end

                    $a1 = "4";
                }
            }
            // untuk update otomatis jam register(jika lupa absen) start
            // if ("1" == "1") {
            //     $uAbsence = AbsenceLog::select('absence_logs.id')->join('absence_categories', 'absence_logs.absence_category_id', '=', 'absence_categories.id')
            //         ->where('absence_id', '66')
            //         ->where('type', 'break')
            //         ->get();
            //     $out = AbsenceLog::select('absence_logs.id')->join('absence_categories', 'absence_logs.absence_category_id', '=', 'absence_categories.id')
            //         ->where('absence_id', '66')
            //         ->where('type', 'presence')
            //         ->orderBy('queue', 'DESC')
            //         ->first();
            //     foreach ($uAbsence as $item) {
            //         AbsenceLog::where('id', $item->id)->update(['register' => date('Y-m-d H:i:s')]);
            //     }
            //     AbsenceLog::where('id', $out->id)->update(['register' => date('Y-m-d H:i:s')]);
            // } else {
            // }
            // untuk update otomatis jam register(jika lupa absen) end

            return response()->json([
                'lat' => $coordinat->lat,
                'lng' => $coordinat->lng,
                'work_type' => $coordinat->work_type_id,
                'menu' => $menu,
                'date' => $coordinat->type,
                'absence' => $absence,
                'tesss' => $absen,
                'a1' => $a1,
            ]);
        }
    }

    public function store(Request $request)
    {

        // $last_code = $this->get_last_code('lock_action');

        // $code = acc_code_generate($last_code, 8, 3);
        $img_path = "/images/absence";
        $basepath = str_replace("laravel-simpletab", "public_html/simpletabadmin/", \base_path());
        // $dataForm = json_decode($request->form);
        $responseImage = '';

        if (date('w') == '0') {
            $day = '7';
        } else {
            $day = date('w');
        }
        // untuk update otomatis jam register(jika lupa absen) start
        if ($request->queue == "1") {
            $uAbsence = AbsenceLog::join('absence_categories', 'absence_logs.absence_category_id', '=', 'absence_categories.id')
                ->where('absence_id', $request->absence_id)
                ->where('type', $request->type)
                ->get();
            $out = AbsenceLog::join('absence_categories', 'absence_logs.absence_category_id', '=', 'absence_categories.id')
                ->where('absence_id', $request->absence_id)
                ->where('type', 'presence')
                ->orderBy('queue',)
                ->get();
            foreach ($uAbsence as $item) {
                AbsenceLog::where('id', $item->id)->update(['register' => date('Y-m-d H:i:s')]);
            }
            AbsenceLog::where('id', $out->id)->update(['register' => date('Y-m-d H:i:s')]);
        } else {
        }
        // untuk update otomatis jam register(jika lupa absen) end
        if ($request->file('image')) {
            $resource_image = $request->file('image');
            $name_image = $request->file('image')->getClientOriginalName();
            $file_ext_image = $request->file('image')->extension();
            // $id_name_image = str_replace(' ', '-', $id_image);

            $name_image = $img_path . '/' . $name_image . '-' . date('Y-m-d h:i:s') . '-absence.' . $file_ext_image;

            $resource_image->move($basepath . $img_path, $name_image);
            $data_image = $name_image;
        }


        if ($responseImage != '') {
            return response()->json([
                'message' => $responseImage,
            ]);
        }

        try {
            $upload_image = AbsenceLog::where('id', $request->id)->first();
            $upload_image->image = $data_image;
            // sementara start
            $upload_image->created_by_staff_id = $request->user_id;
            $upload_image->updated_by_staff_id = $request->user_id;
            $upload_image->register = date('Y-m-d H:i:s');
            $upload_image->late = $late;
            $upload_image->early = $early;
            $upload_image->duration = $duration;
            // sementara end
            $upload_image->created_at = date('Y-m-d H:i:s');
            $upload_image->updated_at = date('Y-m-d H:i:s');
            $upload_image->lat = $request->lat;
            $upload_image->lng = $request->lng;
            $upload_image->status =  0;
            $upload_image->shift_id = $request->shift_id;

            $upload_image->save();


            return response()->json([
                'message' => 'Absen Terkirim',
                'data' => $upload_image,
            ]);
        } catch (QueryException $ex) {
            return response()->json([
                'message' => 'gagal',
            ]);
        }
    }

    public function storeLocation(Request $request)
    {

        // $last_code = $this->get_last_code('lock_action');

        // $code = acc_code_generate($last_code, 8, 3);
        $img_path = "/images/absence";
        $basepath = str_replace("laravel-simpletab", "public_html/simpletabadmin/", \base_path());
        // $dataForm = json_decode($request->form);
        $responseImage = '';

        if (date('w') == '0') {
            $day = '7';
        } else {
            $day = date('w');
        }

        if ($request->file('image')) {
            $resource_image = $request->file('image');
            $name_image = $request->file('image')->getClientOriginalName();
            $file_ext_image = $request->file('image')->extension();
            // $id_name_image = str_replace(' ', '-', $id_image);

            $name_image = $img_path . '/' . $name_image . '-' . date('Y-m-d h:i:s') . '-absence.' . $file_ext_image;

            $resource_image->move($basepath . $img_path, $name_image);
            $data_image = $name_image;
        }


        if ($responseImage != '') {
            return response()->json([
                'message' => $responseImage,
            ]);
        }



        try {
            $upload_image = new Absence;
            $upload_image->image = $data_image;
            // $upload_image->user_id = $dataForm->user_id;
            // $upload_image->register = $dataForm->register;
            // $upload_image->late = $dataForm->late;
            // $upload_image->onesignal_id = $dataForm->onesignal_id;
            // $upload_image->value = $dataForm->value;

            // sementara start
            $upload_image->user_id = $request->user_id;
            $upload_image->register = date('Y-m-d H:i:s');
            $upload_image->late = 0;
            $upload_image->onesignal_id = "dddddwdwdww";
            $upload_image->value = 0;
            // sementara end
            $upload_image->created_at = date('Y-m-d H:i:s');
            $upload_image->updated_at = date('Y-m-d H:i:s');
            $upload_image->lat = $request->lat;
            $upload_image->lng = $request->lng;
            $upload_image->requests_id = $request->id;
            $upload_image->absence_category_id = $request->absence_category_id;
            $upload_image->shift_id = $request->shift_id;
            $upload_image->day_id = $day;

            $upload_image->save();

            $requests = Requests::where('id', $request->id)->update(['status' => $request->status]);

            return response()->json([
                'message' => 'Absen Terkirim',
                'data' => $upload_image,
            ]);
        } catch (QueryException $ex) {
            return response()->json([
                'message' => 'gagal',
            ]);
        }
    }

    public function checkAbsenceLocation(Request $request)
    {
        $absence = Absence::where('user_id', $request->user_id)->where('requests_id', $request->requests_id)->whereDate('register', '=', date('Y-m-d'))->first();

        if ($absence != null) {
            $cek = "1";
        } else {
            $cek = "0";
        }
        return response()->json([
            'message' => 'success',
            'data' => $cek,
        ]);
    }

    public function history(Request $request)
    {
        $data = [];
        $absence = Absence::join('days', 'days.id', '=', 'absences.day_id')->selectRaw('DATE(register) as register, days.name as day_name')->where('user_id', $request->user_id)->groupByRaw('DATE(register)')->get();

        foreach ($absence as $d) {
            $data[] = ['date' => $d->register, 'day_name' => $d->day_name, 'list' => Absence::join('absence_categories', 'absences.absence_category_id', '=', 'absence_categories.id')->selectRaw('absences.*, absence_categories.title as category_title')->where('user_id', $request->user_id)->whereDate('register', '=', $d->register)->get()];
        }
        return response()->json([
            'message' => 'success',
            'data' => $data,
        ]);
    }
}
