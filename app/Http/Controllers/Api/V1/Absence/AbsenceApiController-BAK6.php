<?php

namespace App\Http\Controllers\Api\V1\Absence;

use App\Absence;
use App\Absence_categories;
use App\AbsenceLog;
use App\AbsenceRequest;
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
        // untuk menampung data menu
        $reguler = "";
        $holiday = "";
        $break = "";
        $duty = "";
        $finish = "";
        $excuse_id = "";

        // mematikan menu
        $menuReguler = "OFF";
        $menuHoliday = "OFF";
        $menuBreak = "OFF";
        $menuExcuse = "OFF";
        $menuDuty = "OFF";
        $menuFinish = "OFF";

        // mematikan batas radius di absence
        $geofence_off = "OFF";

        $coordinat = WorkUnit::join('staffs', 'staffs.work_unit_id', '=', 'work_units.id')
            ->join('work_types', 'staffs.work_type_id', '=', 'work_types.id')
            ->where('staffs.id', '404')->first();
        $menu = "";
        $leave = AbsenceRequest::where('start', '<=', date('Y-m-d H:i:s'))
            ->where('end', '>=', date('Y-m-d H:i:s'))
            ->where('category', 'leave')->first();
        $permission = AbsenceRequest::where('start', '<=', date('Y-m-d H:i:s'))
            ->where('end', '>=', date('Y-m-d H:i:s'))
            ->where('category', 'permission')->first();
        $duty = AbsenceRequest::where('start', '<=', date('Y-m-d H:i:s'))
            ->where('end', '>=', date('Y-m-d H:i:s'))
            ->where('category', 'duty')->first();

        // return response()->json([
        //     'message' => 'Success',
        //     'leave' => $leave,
        //     'permission' => $permission,
        //     'duty' => $duty,
        //     'date' => date('Y-m-d h:i:s')
        // ]);

        // cek hari libur
        if ($leave) {
            $menu = 'OFF';
            return response()->json([
                'message' => 'Success',
                'menu' => $menu,
                'date' => date('Y-m-d h:i:s')
            ]);
        } else if ($permission) {
            $menu = 'OFF';
            return response()->json([
                'message' => 'Success',
                'menu' => $menu,
                'date' => date('Y-m-d h:i:s')
            ]);
        } else if ($duty) {
            $menu = 'OFF';
            return response()->json([
                'message' => 'Success',
                'menu' => $menu,
                'date' => date('Y-m-d h:i:s')
            ]);
        }
        // cek jadwal kerja
        else {
            if (date('w') == '0') {
                $day = '7';
            } else {
                $day = date('w');
            }

            // cek absen, apa ada absen hari ini
            $absence = AbsenceLog::selectRaw('absence_logs.expired_date,shift_planner_id, queue, status_active, absence_categories.id as absence_category_id, absences.id as absence_id, absence_logs.id as id')
                ->leftJoin('absences', 'absence_logs.absence_id', '=', 'absences.id')
                ->leftJoin('absence_categories', 'absence_logs.absence_category_id', '=', 'absence_categories.id')
                ->where('staff_id', '323')
                ->where('absence_logs.start_date', '<=', date('Y-m-d H:i:s'))
                ->where('absence_logs.expired_date', '>=', date('Y-m-d H:i:s'))
                ->where('absence_logs.status', '=', 1)
                ->where('absence_categories.type', '!=', 'break')
                ->orderBy('absence_logs.start_date', 'ASC')
                ->first();
            $a1 = "1";

            // jika ada absen hari ini
            if ($absence) {
                if ($absence->shift_planner_id === 0) {
                    $absen = AbsenceLog::selectRaw('absence_categories.*, work_type_days.start, work_type_days.end')
                        ->join('work_type_days', 'work_type_days.absence_category_id', '=', 'absence_categories.id')
                        ->join('work_types', 'work_type_days.work_type_id', '=', 'work_types.id')
                        ->where('work_types.id', '1')
                        ->where('absence_logs.id', $absence->id)
                        ->where('absence_categories.type', '!=', 'break')
                        ->where('absence_logs.status', '=', 1)
                        ->first();
                    $a1 = "2";
                } else {
                    $absen = AbsenceLog::selectRaw('absence_logs.*, shift_group_timesheets.start, shift_group_timesheets.end')->leftJoin('absences', 'absences.id', '=', 'absence_logs.absence_id')
                        ->join('absence_categories', 'absence_categories.id', '=', 'absence_logs.absence_category_id')
                        ->join('shift_group_timesheets', 'absence_categories.id', '=', 'shift_group_timesheets.absence_category_id')
                        ->where('absence_logs.status', '=', 1)
                        ->where('absence_logs.id', $absence->id)
                        ->where('absence_categories.type', '!=', 'break')
                        ->orderBy('absence_logs.id', 'DESC')
                        ->first();

                    $a1 = "2";
                }
                $open = "Close";
                if ($absen) {
                    if ($absen->start_date <= date('Y-m-d H:i:s')) {
                        $open = "Open";
                    } else {
                        $open = "Close";
                    }
                }




                return response()->json([
                    'message' => 'Absen Terkirim',
                    'data' => $absen,
                    'open' => $open,
                    'absence' =>  $absence,
                    'break' =>  $break,
                    'excuse_id' => $excuse_id,
                    'menu' => [
                        'menuBreak' => $menuBreak,
                        'menuExcuse' => $menuExcuse,
                        'menuReguler' => $menuReguler,
                        'menuHoliday' => $menuHoliday,
                        'menuDuty' => $menuDuty,
                        'menuFinish' => $menuFinish,
                    ],
                    // 'absencea' =>  $absenceOut,
                    // 'absenceaw' => $braeakCheck
                ]);
            }
            // ketika tidak ada absen di tanggal tersebut
            else {
                // buat istirahat start
                // cek absen masuk saat ini
                $absenceBreak = AbsenceLog::selectRaw('absence_id, absence_logs.status, absence_logs.expired_date,shift_planner_id, queue, status_active, absence_categories.id as absence_category_id')
                    ->leftJoin('absences', 'absence_logs.absence_id', '=', 'absences.id')
                    ->leftJoin('absence_categories', 'absence_logs.absence_category_id', '=', 'absence_categories.id')
                    ->where('staff_id', '323')
                    // ->where('absence_logs.start_date', '<=', date('Y-m-d H:i:s'))
                    ->where('absence_logs.expired_date', '>=', date('Y-m-d H:i:s'))
                    ->where('absence_logs.status', '=', 0)
                    ->where('absence_logs.absence_category_id', '=', 1)
                    ->orderBy('absence_logs.id', 'DESC')
                    ->first();

                // return response()->json([
                //     'message' => 'Absen Terkirim',
                //     'message' => 'ssssssa',
                //     'data' =>    $absenceBreak,
                // ]);

                $braeakCheck = null;
                // cek apa sudah melakukan absen masuk
                if ($absenceBreak) {
                    // cek apa ada permisi di tanggal ini
                    $absence_excuse = AbsenceRequest::where('start', '<=', date('Y-m-d H:i:s'))
                        ->where('end', '>=', date('Y-m-d H:i:s'))
                        ->where('category', 'excuse')->first();
                    if ($absence_excuse) {
                        $excuse = AbsenceLog::selectRaw('absence_logs.status, absence_logs.expired_date,shift_planner_id, queue, status_active, absence_categories.id as absence_category_id, absence_logs.id as id')
                            ->leftJoin('absences', 'absence_logs.absence_id', '=', 'absences.id')
                            ->leftJoin('absence_categories', 'absence_logs.absence_category_id', '=', 'absence_categories.id')
                            ->where('staff_id', '323')
                            ->where('absence_logs.expired_date', '>=', date('Y-m-d H:i:s'))
                            ->where('absence_logs.status', '=', 1)
                            ->where('absence_categories.type', '=', 'excuse')
                            ->where('absence_categories.queue', '=', '2')
                            ->orderBy('absence_logs.id', 'DESC')
                            ->first();
                        if ($excuse) {
                            $excuse_id = $excuse->id;
                        }
                        $menuExcuse = "ON";
                    }

                    // cek apa ada data absen istirahat dengan expired_date waktu saat ini
                    $braeakCheck = AbsenceLog::selectRaw('absence_logs.status, absence_logs.expired_date,shift_planner_id, queue, status_active, absence_categories.id as absence_category_id, absence_logs.absence_id as absence_id')
                        ->leftJoin('absences', 'absence_logs.absence_id', '=', 'absences.id')
                        ->leftJoin('absence_categories', 'absence_logs.absence_category_id', '=', 'absence_categories.id')
                        ->where('staff_id', '323')
                        ->where('absence_logs.start_date', '<=', date('Y-m-d H:i:s'))
                        ->where('absence_logs.expired_date', '>=', date('Y-m-d H:i:s'))
                        // ->where('absence_logs.status', '=', 1)
                        ->where('absence_categories.type', '=', 'break')
                        ->orderBy('absence_logs.id', 'DESC')
                        ->first();
                    // cari absen out expired hari ini untuk mengambil expired date
                    $absenceOut = AbsenceLog::selectRaw('absence_logs.expired_date,shift_planner_id, queue, status_active, absence_categories.id as absence_category_id, absences.id as absence_id')
                        ->leftJoin('absences', 'absence_logs.absence_id', '=', 'absences.id')
                        ->leftJoin('absence_categories', 'absence_logs.absence_category_id', '=', 'absence_categories.id')
                        ->where('staff_id', '323')
                        // ->where('absence_logs.start_date', '<=', date('Y-m-d H:i:s'))
                        ->where('absence_logs.expired_date', '>=', date('Y-m-d H:i:s'))
                        ->where('absence_logs.status', '=', 1)
                        ->where('absence_logs.absence_id', $absenceBreak->absence_id)
                        ->where('absence_categories.id', '=', '2')
                        ->where('absence_categories.type', '!=', 'break')
                        ->orderBy('absence_logs.start_date', 'ASC')
                        ->first();

                    // return response()->json([
                    //     'message' => 'Absen Terkirim',
                    //     'message' => 'ssssssa',
                    //     'data' =>     $absenceOut->absence_id,
                    // ]);
                    // jika belum ada absen istirahat
                    if (!$braeakCheck) {
                        // buat absen istirahat
                        AbsenceLog::create([

                            'absence_id' => $absenceOut->absence_id,
                            'absence_category_id' => 3,
                            'status' => '1',
                            'expired_date' => $absenceOut->expired_date,
                            'start_date' => date('Y-m-d H:i:10'),

                        ]);
                        AbsenceLog::create([

                            'absence_id' => $absenceOut->absence_id,
                            'absence_category_id' => 4,
                            'status' => '1',
                            'expired_date' => $absenceOut->expired_date,
                            'start_date' => date('Y-m-d H:i:11'),

                        ]);
                    }
                    $break = AbsenceLog::selectRaw('absence_logs.status, absence_logs.expired_date,shift_planner_id, queue, status_active, absence_categories.id as absence_category_id')
                        ->leftJoin('absences', 'absence_logs.absence_id', '=', 'absences.id')
                        ->leftJoin('absence_categories', 'absence_logs.absence_category_id', '=', 'absence_categories.id')
                        ->where('staff_id', '323')
                        ->where('absence_logs.expired_date', '>=', date('Y-m-d H:i:s'))
                        ->where('absence_logs.status', '=', 1)
                        ->where('absence_categories.type', '=', 'break')
                        ->orderBy('absence_logs.start_date', 'ASC')
                        ->first();
                    $menuBreak = "ON";
                }
                // }
                // buat istirahat end

                // cek apa ada shift di tanggal ini
                if ($coordinat->type == "shift") {
                    $a1 = "3";
                    // buat baru start
                    // cek apa sudah ada group absen di tanggal ini
                    $c = ShiftPlannerStaffs::selectRaw('shift_planner_staffs.id as shift_planner_id, shift_planner_staffs.shift_group_id')
                        ->join('shift_groups', 'shift_planner_staffs.shift_group_id', '=', 'shift_groups.id')
                        ->leftJoin('absence_logs', 'shift_planner_staffs.id', '=', 'absence_logs.shift_planner_id')
                        ->where('shift_planner_staffs.staff_id', '=', '323')
                        ->whereDate('shift_planner_staffs.start', '=', '2023-02-12')
                        ->where('absence_logs.id', '=', null)
                        ->orderBy('shift_groups.queue', 'ASC')
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
                                ->where('absence_categories.type', '!=', "break")
                                ->orderBy('absence_categories.queue', 'ASC')
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
                        index();
                    } else {


                        if (!$absenceOut) {
                            return response()->json([
                                'message' => 'Absen Terkirim',
                                'message' => 'sudah pulang',
                                'data' =>   $c,
                                'menu' => [
                                    'menuBreak' => $menuBreak,
                                    'menuExcuse' => $menuExcuse,
                                    'menuReguler' => $menuReguler,
                                    'menuHoliday' => $menuHoliday,
                                    'menuDuty' => $menuDuty,
                                    'menuFinish' => $menuFinish,
                                ],
                            ]);
                        } else {
                            return response()->json([
                                'message' => 'Absen Terkirim',
                                'message' => 'ssssssa',
                                'data' =>   $c,
                                'menu' => [
                                    'menuBreak' => $menuBreak,
                                    'menuExcuse' => $menuExcuse,
                                    'menuReguler' => $menuReguler,
                                    'menuHoliday' => $menuHoliday,
                                    'menuDuty' => $menuDuty,
                                    'menuFinish' => $menuFinish,
                                ],
                            ]);
                        }
                    }

                    // buat baru end
                }
                // jika tidak ada shift, dinas keluar kota, libur ataupun cuti, izin, atau sakit(mungkin dipisah untuk pengecekan)
                else {

                    $holiday = Holiday::whereDate('start', '<=', date('Y-m-d'))->whereDate('end', '>=', date('Y-m-d'))->first();
                    // cek hari libur
                    if ($holiday) {
                        $menu = 'OFF';
                        return response()->json([
                            'message' => 'Success',
                            'menu' => $menu,
                            'date' => date('Y-m-d h:i:s')
                        ]);
                    } else {
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
            }

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


        // untuk update otomatis jam register(jika lupa absen) start
        if ($request->queue == "1") {
            $uAbsence = AbsenceLog::select('absence_logs.id')->join('absence_categories', 'absence_logs.absence_category_id', '=', 'absence_categories.id')
                ->where('absence_id', $request->absence_id)
                ->where('type', $request->type)
                ->get();
            $out = AbsenceLog::select('absence_logs.id')->join('absence_categories', 'absence_logs.absence_category_id', '=', 'absence_categories.id')
                ->where('absence_id', $request->absence_id)
                ->where('type', 'presence')
                ->orderBy('queue', 'DESC')
                ->first();
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
