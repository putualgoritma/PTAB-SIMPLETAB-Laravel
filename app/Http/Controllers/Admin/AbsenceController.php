<?php

namespace App\Http\Controllers\Admin;

use App\Absence;
use App\Exports\AbsenceExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Absence_categories;
use App\AbsenceLog;
use App\AbsenceProblem;
use App\AbsenceRequest;
use App\Dapertement;
use App\Day;
use App\Exports\AbsenceAccuracy;
use App\Exports\AbsenceReport;
use App\Holiday;
use App\Http\Controllers\Controller;
use App\Imports\AbsenceImport;
use App\Imports\AbsenceShiftImport;
use App\Requests;
use App\Shift;
use App\ShiftGroups;
use App\ShiftGroupTimesheets;
use App\ShiftParent;
use App\ShiftPlannerStaff;
use App\ShiftPlannerStaffs;
use App\ShiftStaff;
use App\Staff;
use App\User;
use App\WorkTypeDays;
use App\WorkTypes;
use App\WorkUnit;
use Carbon\CarbonPeriod;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class AbsenceController extends Controller
{

    public function getShiftPlanner(Request $request)
    {
        $shift_group = ShiftGroups::where('shift_parent_id', $request->shift_parent_id)
            ->pluck('title', 'id');

        return response()->json($shift_group);
    }

    public function index(Request $request)
    {
        // $encrypted = Crypt::encrypt(['sksksks'=>'sjsjsjsj']);

        // $decrypted = Crypt::decrypt($encrypted);
        // dd($encrypted, $decrypted);
        // 244
        // $test = AbsenceLog::whereDate('created_at', '>', '2023-07-13')->where('created_by_staff_id', 244)->get();
        // $test = Absence::whereDate('created_at', '>', '2023-07-13')->where('staff_id', 244)->get();

        // $test = AbsenceLog::where('absence_id', '10460')->get();
        // $test = AbsenceLog::join('absences', 'absence_logs.absence_id', '=', 'absences.id')->first();
        // dd($test);

        // $request_date = "2023-07-28";
        // $day_id = date('w', strtotime($request_date)) == "0" ? '7' : date('w', strtotime($request_date));
        // // dd($day_id);
        // $message_err = 'anda hanya bisa mengajukan di jam kerja';
        // $staff = Staff::where('id', 453)->first();
        // $absen_now = Absence::join('absence_logs', 'absence_logs.absence_id', '=', 'absences.id')
        //     ->where('staff_id', 404)
        //     ->where('absence_category_id', 2)
        //     ->where('absence_logs.status', 1)
        //     ->whereDate('absences.created_at', date('Y-m-d'))
        //     ->first();



        // if (date('Y-m-d') == $request_date) {


        //     if (!$absen_now) {
        //         return json_encode($message_err);
        //     } else {

        //         if ($staff->work_type_id === 1) {
        //             $schedule = WorkTypeDays::where('day_id', $day_id)->where('absence_category_id', 1)->first();
        //             $schedule_end = WorkTypeDays::where('day_id', $day_id)->where('absence_category_id', 2)->first();
        //         } else {
        //             $shift_staff = ShiftPlannerStaffs::where('staff_id', $staff->id)->first();
        //             // dd($shift_staff);
        //             if ($shift_staff) {
        //                 $schedule = ShiftGroupTimesheets::where('id', $shift_staff->id)->where('absence_category_id', 1)->first();
        //                 $schedule_end = ShiftGroupTimesheets::where('id', $id)->where('absence_category_id', 2)->first();
        //             } else {
        //                 return json_encode($message_err);
        //             }
        //         }
        //         dd($absen_now);
        //         $time_end = date("Y-m-d H:i:s", strtotime('+' . $schedule->duration . ' hours', strtotime(date('Y-m-d ' . $schedule->time))));
        //         // dd($schedule);
        //         if ($schedule->time < date('Y-m-d H:i:s') && date('Y-m-d H:i:s') < $time_end) {
        //             return json_encode('pengajuan berhasil');
        //         } else {
        //             return json_encode($message_err);
        //         }
        //     }
        // } else {
        //     if ($staff->work_type_id === 1) {
        //         $schedule = WorkTypeDays::where('day_id', $day_id)->where('absence_category_id', 1)->first();
        //         $schedule_end = WorkTypeDays::where('day_id', $day_id)->where('absence_category_id', 2)->first();
        //     } else {
        //         $shift_staff = ShiftPlannerStaffs::where('staff_id', $staff->id)->first();
        //         // dd($shift_staff);
        //         if ($shift_staff) {
        //             $schedule = ShiftGroupTimesheets::where('id', $shift_staff->id)->where('absence_category_id', 1)->first();
        //             $schedule_end = ShiftGroupTimesheets::where('id', $id)->where('absence_category_id', 2)->first();
        //         } else {
        //             return json_encode($message_err);
        //         }
        //     }
        //     $time_end = date("Y-m-d H:i:s", strtotime('+' . $schedule->duration . ' hours', strtotime(date('Y-m-d ' . $schedule->time))));
        //     // dd($schedule);
        //     if ($schedule->time < date('Y-m-d H:i:s') && date('Y-m-d H:i:s') < $time_end) {
        //         return json_encode('pengajuan berhasil');
        //     } else {
        //         return json_encode($message_err);
        //     }
        // }
        // dd(, $schedule_end->time);

        // $staff = Staff::selectRaw('work_units.name as work_unit, work_units.id as work_unit_id, staffs.*')
        //     ->join('work_units', 'work_units.id', '=', 'staffs.work_unit_id')->get();

        // foreach ($staff as $data) {
        //     $r[] = [
        //         'id' => $data->id,
        //         'name' => $data->name,
        //         'work_unit_id' => $data->work_unit_id,
        //         'work_unit' => $data->work_unit
        //     ];
        // }

        // return Excel::download(new AbsenceAccuracy($r), 'report_accuracy.xlsx');

        // dd($r);

        // $start1 = date("Y-m-d H:i:s", strtotime('12-04-2023' . '14:00:00'));
        // $day = date("w", strtotime('12-04-2023'));
        // $staff = Staff::selectRaw('staffs.*,work_types.type as work_type, work_types.id as work_type_id ')->join('work_types', 'work_types.id', '=', 'staffs.work_type_id')
        //     ->where('staffs.id', '407')->first();
        // // dd($staff);
        // if ($staff->work_type == "shift") {
        //     $jumShift = ShiftPlannerStaffs::whereDate('shift_planner_staffs.start', '=', '2023-04-12')
        //         ->where('staff_id', $staff->id)
        //         ->get();
        //     foreach ($jumShift as $data) {
        //         $absence = ShiftPlannerStaffs::join('shift_groups', 'shift_groups.id', '=', 'shift_planner_staffs.shift_group_id')
        //             ->join('shift_group_timesheets', 'shift_groups.id', '=', 'shift_group_timesheets.shift_group_id')
        //             ->where('shift_group_timesheets.absence_category_id', '1')
        //             ->where('shift_groups.id', $data->shift_group_id)
        //             ->where('staff_id', $staff->id)
        //             ->whereDate('shift_planner_staffs.start', '=', '2023-04-12')
        //             ->orWhere('shift_group_timesheets.absence_category_id', '2')
        //             ->where('shift_groups.id', $data->shift_group_id)
        //             ->where('staff_id', $staff->id)
        //             ->whereDate('shift_planner_staffs.start', '=', '2023-04-12')
        //             ->orderBy('shift_group_timesheets.absence_category_id', 'ASC')
        //             ->get();

        //         if ($absence[0]->time > $absence[1]->time) {
        //             $masuk = date("Y-m-d H:i:s", strtotime('12-04-2023' . $absence[0]->time));
        //             $pulang = date("Y-m-d H:i:s", strtotime('+ ' . '1' . ' days', strtotime('12-04-2023' . $absence[1]->time)));
        //             //  date("Y-m-d H:i:s", strtotime());
        //         } else {
        //             $masuk = date("Y-m-d H:i:s", strtotime('12-04-2023' . $absence[0]->time));
        //             $pulang = date("Y-m-d H:i:s", strtotime('12-04-2023' . $absence[1]->time));
        //         }


        //         // dd($masuk, $start1, $pulang, $absence);
        //         if ($start1 > $masuk && $start1 < $pulang) {
        //             return json_encode(
        //                 [
        //                     'message' => 'anda tidak bisa melakukan lembur di jam kerja'
        //                 ]
        //             );
        //         }
        //     }
        // } else {
        //     $absence = WorkTypeDays::selectRaw('time')
        //         ->where('work_type_id', $staff->work_type_id)
        //         ->where('day_id', $day != "0" ? $day : "7")
        //         ->where('work_type_days.absence_category_id', '2')
        //         ->orWhere('work_type_days.absence_category_id', '1')
        //         ->where('work_type_id', $staff->work_type_id)
        //         ->where('day_id', $day != "0" ? $day : "7")
        //         ->orderBy('work_type_days.absence_category_id', 'ASC')
        //         ->get();
        //     $masuk = date("Y-m-d H:i:s", strtotime('12-04-2023' . $absence[0]->time));
        //     $pulang = date("Y-m-d H:i:s", strtotime('12-04-2023' . $absence[1]->time));
        //     // dd($masuk, $start1, $pulang);
        //     if ($start1 > $masuk && $start1 < $pulang) {
        //         return json_encode(
        //             [
        //                 'message' => 'anda tidak bisa melakukan lembur di jam kerja'
        //             ]
        //         );
        //     }
        // }

        // dd($absence);

        // $str_date = date("Y-m-d H:i:s", strtotime(date('Y-m-d ' . '02:00:00')));
        // $exp_date = date("Y-m-d H:i:s", strtotime(date('Y-m-d ' . '10:00:00')));
        // $i = 0;
        // while ($str_date < $exp_date) {

        //     $str_date = date("Y-m-d H:i:s", strtotime('+ ' . $i * 60 . ' minutes', strtotime($str_date)));
        //     $d[] = [$str_date];
        //     $i = +1;
        // }
        // dd($d);
        // $t = AbsenceProblem::get();
        // dd($t);
        // $test = AbsenceLog::select(
        //     'staffs.name',
        //     'users.device',
        //     DB::raw('AVG(accuracy) as avg_accuracy'),
        //     DB::raw('MAX(accuracy) as max_accuracy'),
        //     DB::raw('MIN(accuracy) as min_accuracy'),
        //     DB::raw('AVG(distance) as avg_distance'),
        //     DB::raw('MIN(distance) as max_distance'),
        //     DB::raw('MAX(distance) as min_distance')
        // )
        //     ->join('absences', 'absences.id', '=', 'absence_logs.absence_id')
        //     ->join('staffs', 'staffs.id', '=', 'absences.staff_id')
        //     ->join('users', 'users.staff_id', '=', 'staffs.id')
        //     ->groupBy('staffs.id')
        //     ->get();
        // // dd($test);
        // $data12 = [];
        // foreach ($test as $t) {
        //     $data12[] = [
        //         'name' => $t->name,
        //         'device' => $t->device,
        //         'avg_accuracy' => $t->avg_accuracy,
        //         'max_accuracy' => $t->max_accuracy,
        //         'min_accuracy' => $t->min_accuracy,
        //         'avg_distance' => $t->avg_distance,
        //         'max_distance' => $t->max_distance,
        //         'min_distance' => $t->min_distance,
        //     ];
        // }
        // dd($test12);

        // $accuracy = AbsenceLog::select('accuracy', 'distance', 'absence_logs.id', 'status_active', 'staff_id', 'staffs.name')
        //     ->join('absences', 'absences.id', '=', 'absence_logs.absence_id')
        //     ->join('staffs', 'staffs.id', '=', 'absences.staff_id')
        //     // ->where('staffs.id', '437')
        //     ->whereDate('absences.created_at', '2023-04-17')
        //     ->groupBy('absences.id')
        //     ->orderBy('accuracy', 'DESC')
        //     ->get();
        // $r = [];
        // foreach ($accuracy as $acc) {
        //     $r[] = [
        //         'id' => $acc->id,
        //         'accuracy' => $acc->accuracy,
        //         'distance' => $acc->distance,
        //         'status_active' => $acc->status_active,
        //         'staff_id' => $acc->staff_id,
        //         'name' => $acc->name
        //     ];
        // }

        // return Excel::download(new AbsenceAccuracy($r), 'report_accuracy.xlsx');
        // echo "<pre>";
        // print_r($r);
        // dd($r);


        // echo "<pre>";
        // print_r($test);
        // dd($test);

        abort_unless(\Gate::allows('absence_access'), 403);
        $checker = [];
        $users = user::with(['roles'])
            ->where('id', Auth::user()->id)
            ->first();
        foreach ($users->roles as $data) {
            foreach ($data->permissions as $data2) {
                $checker[] = $data2->title;
            }
        }
        if (in_array('absence_all_access', $checker)) {
            $qry = AbsenceLog::selectRaw('absence_logs.*, work_types.type as work_type,  NIK, days.name as day, staffs.name as staff, staffs.image as staff_image, absence_categories.title as absence_category')
                ->leftJoin('absences', 'absence_logs.absence_id', '=', 'absences.id')
                ->leftJoin('days', 'days.id', '=', 'absences.day_id')
                ->leftJoin('staffs', 'absences.staff_id', '=', 'staffs.id')
                ->leftJoin('work_types', 'work_types.id', '=', 'staffs.work_type_id')
                ->leftJoin('absence_categories', 'absence_logs.absence_category_id', '=', 'absence_categories.id')
                ->FilterStaff($request->staff_id)
                ->FilterAbsenceCategory($request->absence_category_id)
                ->FilterDateWeb($request->from, $request->to)
                ->FilterDapertement($request->dapertement)
                ->where('absence_logs.register', '!=', '')
                ->orderBy('staffs.NIK')
                ->orderBy('register', 'DESC');
        } else {
            $qry = AbsenceLog::selectRaw('absence_logs.*, work_types.type as work_type,  NIK, days.name as day, staffs.name as staff, staffs.image as staff_image, absence_categories.title as absence_category')
                ->leftJoin('absences', 'absence_logs.absence_id', '=', 'absences.id')
                ->leftJoin('days', 'days.id', '=', 'absences.day_id')
                ->leftJoin('staffs', 'absences.staff_id', '=', 'staffs.id')
                ->leftJoin('work_types', 'work_types.id', '=', 'staffs.work_type_id')
                ->leftJoin('absence_categories', 'absence_logs.absence_category_id', '=', 'absence_categories.id')
                ->FilterStaff($request->staff_id)
                ->FilterAbsenceCategory($request->absence_category_id)
                ->FilterDateWeb($request->from, $request->to)
                ->where('dapertement_id', Auth::user()->dapertement_id)
                ->where('absence_logs.register', '!=', '')
                ->orderBy('staffs.NIK')
                ->orderBy('register', 'DESC');
        }
        // ->orderBy('nik', 'ASC');
        // dd($qry->get());
        // $qry = TestModel::Filter($request)->Order('id', 'desc')->skip(0)->take(10)->get();
        // return $qry;
        if ($request->ajax()) {
            //set query
            $table = Datatables::of($qry);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = '';
                $editGate = '';
                $deleteGate = '';
                // $act = 'absence_edit';
                $act = '';
                $crudRoutePart = 'absence';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'act',
                    'crudRoutePart',
                    'row'
                ));
            });
            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : "";
            });

            $table->editColumn('user', function ($row) {
                return $row->user ? $row->user : "";
            });

            $table->editColumn('image', function ($row) {
                return $row->image ? $row->image : "";
            });

            $table->editColumn('user_image', function ($row) {
                return $row->image ? $row->user_image : "";
            });

            $table->editColumn('lat', function ($row) {
                return $row->lat ? $row->lat : "";
            });
            $table->editColumn('lng', function ($row) {
                return $row->lng ? $row->lng : "";
            });
            $table->editColumn('register', function ($row) {
                return $row->register ? $row->register : "";
            });
            $table->editColumn('absen_category', function ($row) {
                return $row->absen_category ? $row->absen_category : "";
            });
            $table->editColumn('day', function ($row) {
                return $row->day ? $row->day : "";
            });
            $table->editColumn('late', function ($row) {
                return $row->late ? $row->late : "";
            });
            $table->editColumn('NIK', function ($row) {
                return $row->NIK ? $row->NIK : "";
            });
            $table->editColumn('late', function ($row) {
                if ($row->late > 0) {
                    return "Lambat";
                } else {
                    return "Tepat";
                }
            });
            $table->editColumn('updated_at', function ($row) {
                return $row->updated_at ? $row->updated_at : "";
            });

            $table->editColumn('work_type', function ($row) {
                return $row->work_type ? $row->work_type : "";
            });

            $table->rawColumns(['actions', 'placeholder']);

            $table->addIndexColumn();
            return $table->make(true);
        }
        // default view
        // return view('admin.schedule.index');
        $staffs = Staff::orderBy('name', 'ASC')->get();
        $dapertements = Dapertement::get();
        $absence_categories = Absence_categories::get();

        return view('admin.absence.index', compact('staffs', 'dapertements', 'absence_categories'));
    }
    public function createImportShift()
    {
        abort_unless(\Gate::allows('absenceOffline_access'), 403);
        return view('admin.absence.addImportShift');
    }

    public function storeImportShift(Request $request)
    {
        abort_unless(\Gate::allows('absenceOffline_access'), 403);
        $import = new AbsenceShiftImport;
        $test =  Excel::import($import, $request->file('file'));
        // dd($test);
        $array = $import->getArray();
        // dd($array);

        $absences =  collect($import->getArray());
        $staff =  $absences->groupBy('nik');
        $checker = [];
        $users = user::with(['roles'])
            ->where('id', Auth::user()->id)
            ->first();
        foreach ($users->roles as $data) {
            foreach ($data->permissions as $data2) {
                $checker[] = $data2->title;
            }
        }

        // dd($absences);
        foreach ($staff as $key => $value) {

            if (in_array('absence_all_access', $checker)) {
                $staff_id = Staff::selectRaw('staffs.*, work_units.import as import_status')
                    ->join('work_units', 'work_units.id', '=', 'staffs.work_unit_id')
                    ->where('work_type_id', '2')
                    ->where('NIK', $key)
                    ->first();
            } else {
                $staff_id = Staff::selectRaw('staffs.*, work_units.import as import_status')
                    ->join('work_units', 'work_units.id', '=', 'staffs.work_unit_id')
                    ->where('NIK', $key)
                    ->where('work_type_id', '2')
                    ->where('dapertement_id', Auth::user()->dapertement_id)->first();
            }
            if ($staff_id) {
                if ($staff_id->import_status == "ON") {
                    $absence_staff =  collect($import->getArray())->where('nik', $key);

                    $get_check_inR = collect($import->getArray())->where('nik', $key)->where('category_id', 'in')->first();
                    $get_check_outR = collect($import->getArray())->where('nik', $key)->where('category_id', 'out')->first();
                    $check_inR =  $get_check_inR['date'];
                    $check_outR = $get_check_outR['date'];

                    $get_check_inB = collect($import->getArray())->where('nik', $key)->where('category_id', 'break_in')->first();
                    $get_check_outB = collect($import->getArray())->where('nik', $key)->where('category_id', 'break_out')->first();
                    $check_inB =  $get_check_inB['date'];
                    $check_outB = $get_check_outB['date'];

                    $get_check_inV = collect($import->getArray())->where('nik', $key)->where('category_id', 'visit_in')->first();
                    $get_check_inV = collect($import->getArray())->where('nik', $key)->where('category_id', 'visit_out')->first();

                    $get_check_inE = collect($import->getArray())->where('nik', $key)->where('category_id', 'excuse_in')->first();
                    $get_check_outE = collect($import->getArray())->where('nik', $key)->where('category_id', 'excuse_out')->first();

                    // dd((date('Y-m-d', strtotime('-3 days', strtotime(date('Y-m-d'))))),
                    //     date('Y-m-d', strtotime($check_inR)),
                    //     date('Y-m-d', strtotime('+1 days', strtotime(date('Y-m-d'))))
                    // );
                    // Pengecekan
                    if (date('Y-m-d', strtotime('-3 days', strtotime(date('Y-m-d')))) > date('Y-m-d', strtotime($check_inR)) || date('Y-m-d', strtotime('+1 days', strtotime(date('Y-m-d')))) < date('Y-m-d', strtotime($check_inR))) {
                        // return dd('Tanggal Lebih besar dari hari ini atau lewat dari 3 hari');
                    } else if (Absence::where('staff_id', $staff_id->id)->whereDate('created_at', '=', date('Y-m-d', strtotime($check_inR)))->first()) {
                        // return dd('Absen sudah ada di tanggal tersebut');
                    }
                    // dd($check_inR, $check_outR);
                    // input absence start
                    // dd($request->all());
                    else {
                        $shift_staff_old = ShiftPlannerStaffs::where('staff_id', $staff_id->id)
                            ->where('shift_group_id', $get_check_inR['shiftGroup'])
                            ->whereDate('start', date('Y-m-d'))
                            ->first();

                        if ($shift_staff_old) {
                            ShiftPlannerStaffs::where('id', $shift_staff_old->id)->delete();
                        }

                        $shift_group = ShiftGroups::where('shift_groups.id', $get_check_inR['shiftGroup'])
                            ->where('shift_groups.id', $get_check_inR['shiftGroup'])
                            ->first();

                        $shift_group_timesheets = ShiftGroupTimesheets::where('shift_group_id', $get_check_inR['shiftGroup'])
                            ->orderBy('absence_category_id')
                            ->get();


                        // dd($shift_staff_old->id);
                        // dd($shift_group_timesheets);
                        $shift_staff = ShiftPlannerStaffs::create([
                            'staff_id'        =>    $staff_id->id,
                            'shift_group_id' => $get_check_inR['shiftGroup'],
                            'start'        =>     date('Y-m-d H:i:s', strtotime($check_inR)),
                            'end'        =>     date('Y-m-d H:i:s', strtotime($check_outR))
                        ]);

                        $data = [];
                        $data2 = [];
                        $data3 = [];

                        $data = [
                            'day_id' => date('w', strtotime($check_inR)),
                            'staff_id' => $staff_id->id,
                            'created_at' =>  date('Y-m-d 00:00:00', strtotime($check_inR)),
                            'shift_group_id' => $shift_group->id
                        ];

                        $absence = Absence::create($data);


                        $data1 = [
                            'register' => date('Y-m-d H:i:s', strtotime($check_inR)),
                            'absence_category_id' => 1,
                            'absence_id' => $absence->id,
                            'duration' => 0,
                            'created_by_staff_id' => 0,
                            'status' => 0,
                            'timein' => date('Y-m-d H:i:s', strtotime($check_inR)),
                            'timeout' => date('Y-m-d H:i:s', strtotime($check_outR)),
                            'shift_planner_id' => $shift_staff->id,
                            'shift_group_timesheet_id' => $shift_group_timesheets[0]->id
                        ];
                        AbsenceLog::create($data1);
                        $datetime_1 = date('Y-m-d H:i:s', strtotime($check_inR));
                        $datetime_2 = date('Y-m-d H:i:s', strtotime($check_outR));

                        $from_time = strtotime($datetime_1);
                        $to_time = strtotime($datetime_2);
                        $diff_minutes = round(abs($from_time - $to_time) / 3600, 2) . " minutes";
                        $data1end = [
                            'register' => date('Y-m-d H:i:s', strtotime($check_outR)),
                            'absence_category_id' => 2,
                            'absence_id' => $absence->id,
                            'duration' => $diff_minutes,
                            'created_by_staff_id' => 0,
                            'status' => 0,
                            'timein' => date('Y-m-d H:i:s', strtotime($check_inR)),
                            'timeout' => date('Y-m-d H:i:s', strtotime($check_outR)),
                            'shift_planner_id' => $shift_staff->id,
                            'shift_group_timesheet_id' => $shift_group_timesheets[1]->id
                        ];
                        AbsenceLog::create($data1end);

                        $data5 = [
                            'register' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $check_inB)),
                            'absence_category_id' => 3,
                            // 'absence_request_id' => $break->id,
                            'absence_id' => $absence->id,
                            'duration' => 0,
                            'created_by_staff_id' => 0,
                            'status' => 0,
                            'timein' => date('Y-m-d H:i:s', strtotime($check_inR)),
                            'timeout' => date('Y-m-d H:i:s', strtotime($check_outR)),
                            'shift_planner_id' => $shift_staff->id,
                            'shift_group_timesheet_id' => $shift_group_timesheets[2]->id
                        ];
                        AbsenceLog::create($data5);

                        $datetime_1 = date('Y-m-d H:i:s', strtotime($check_inB));
                        $datetime_2 = date('Y-m-d H:i:s', strtotime($check_outB));

                        $from_time = strtotime($datetime_1);
                        $to_time = strtotime($datetime_2);
                        $diff_minutes = round(abs($from_time - $to_time) / 3600, 2) . " minutes";
                        $data5end = [
                            'register' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $check_outB)),
                            'absence_category_id' => 4,
                            // 'absence_request_id' => $break->id,
                            'absence_id' => $absence->id,
                            'duration' => $diff_minutes,
                            'created_by_staff_id' => 0,
                            'status' => 0,
                            'timein' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->break)),
                            'timeout' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->break_end)),
                            'shift_planner_id' => $shift_staff->id,
                            'shift_group_timesheet_id' => $shift_group_timesheets[3]->id
                        ];
                        AbsenceLog::create($data5end);

                        if ($get_check_inV) {
                            // if ($request->duty_end) {
                            foreach ($absences->where('category_id', 'visit_in') as $dty) {
                                $check_inV = $dty['date'];
                                $check_outV =  collect($import->getArray())->where('nik', $key)->where('group_id', $dty['group_id'])->where('category_id', 'visit_out')->first()['date'];
                                $duty = AbsenceRequest::create([
                                    'staff_id' => $staff_id->id,
                                    'start' => date('Y-m-d H:i:s', strtotime($check_inV)),
                                    'end' => date('Y-m-d H:i:s', strtotime($check_outV)),
                                    'type' => 'other',
                                    'time' =>   date('H:i:s', strtotime($check_inV)),
                                    'status' => 'approve',
                                    'category' => 'visit',
                                    'description' => $dty['description'],
                                ]);
                                $data2 = [
                                    'register' => date('Y-m-d H:i:s', strtotime($check_inV)),
                                    'absence_category_id' => 5,
                                    'absence_request_id' => $duty->id,
                                    'absence_id' => $absence->id,
                                    'duration' => 0,
                                    'created_by_staff_id' => 0,
                                    'status' => 0,
                                    'timein' => date('Y-m-d H:i:s', strtotime($check_inR)),
                                    'timeout' => date('Y-m-d H:i:s', strtotime($check_outR))
                                ];
                                AbsenceLog::create($data2);

                                $datetime_1 = date('Y-m-d H:i:s', strtotime($check_inV));
                                $datetime_2 = date('Y-m-d H:i:s', strtotime($check_outV));

                                $from_time = strtotime($datetime_1);
                                $to_time = strtotime($datetime_2);
                                $diff_minutes = round(abs($from_time - $to_time) / 3600, 2) . " minutes";
                                $data2end = [
                                    'register' => date('Y-m-d H:i:s', strtotime($check_outV)),
                                    'absence_category_id' => 6,
                                    'absence_request_id' => $duty->id,
                                    'absence_id' => $absence->id,
                                    'duration' =>  $diff_minutes,
                                    'created_by_staff_id' => 0,
                                    'status' => 0,
                                    'timein' => date('Y-m-d H:i:s', strtotime($check_inR)),
                                    'timeout' => date('Y-m-d H:i:s', strtotime($check_outR))
                                ];
                                AbsenceLog::create($data2end);
                            }
                            // }
                        }
                        if ($get_check_inE) {
                            $check_inE = $get_check_inE['date'];
                            $check_outE = $get_check_outE['date'];
                            $excuse = AbsenceRequest::create([
                                'staff_id' => $staff_id->id,
                                'start' => date('Y-m-d H:i:s', strtotime($check_inE)),
                                'end' => date('Y-m-d H:i:s', strtotime($check_outE)),
                                'type' => 'other',
                                'time' => $request->excuse,
                                'status' => 'approve',
                                'category' => 'visit',
                                'description' => $request->description_excuse,
                            ]);
                            $data3 = [
                                'register' => date('Y-m-d H:i:s', strtotime($check_inE)),
                                'absence_category_id' => 11,
                                'absence_request_id' => $excuse->id,
                                'absence_id' => $absence->id,
                                'duration' => 0,
                                'created_by_staff_id' => 0,
                                'status' => 0,
                                'timein' => date('Y-m-d H:i:s', strtotime($check_inR)),
                                'timeout' => date('Y-m-d H:i:s', strtotime($check_outR))
                            ];
                            AbsenceLog::create($data3);


                            $datetime_1 = date('Y-m-d H:i:s', strtotime($check_inE));
                            $datetime_2 = date('Y-m-d H:i:s', strtotime($check_outE));

                            $from_time = strtotime($datetime_1);
                            $to_time = strtotime($datetime_2);
                            $diff_minutes = round(abs($from_time - $to_time) / 3600, 2) . " minutes";
                            $data3end = [
                                'register' => date('Y-m-d H:i:s', strtotime($check_outE)),
                                'absence_category_id' => $excuse->id,
                                'absence_request_id' => 12,
                                'absence_id' => $absence->id,
                                'duration' => $diff_minutes,
                                'created_by_staff_id' => 0,
                                'status' => 0,
                                'timein' => date('Y-m-d H:i:s', strtotime($check_inR)),
                                'timeout' => date('Y-m-d H:i:s', strtotime($check_outR))
                            ];
                            AbsenceLog::create($data3end);
                        }
                    }
                }

                // input absence end
                // dd($absence_staff);
            }
        }

        // dd($staff_id);
        ini_set("memory_limit", -1);
        set_time_limit(0);
        //ini test

        // $records = [
        //     ['id' => 3, 'nama' => 'budi'],
        //     ['id' => 4, 'nama' => 'udin'],
        //     ['id' => 6, 'nama' => 'udin'],
        //     ['id' => 5, 'nama' => 'yasa']
        // ];

        // $collect =  collect($records);
        // $search = $collect->groupBy('id');

        // $collect->where('id', '3');
        return redirect()->route('admin.absence.index');
        // dd($search);
    }

    // import absen reguler start
    public function createImport()
    {
        abort_unless(\Gate::allows('absenceOffline_access'), 403);
        return view('admin.absence.addImport');
    }

    public function storeImport(Request $request)
    {
        abort_unless(\Gate::allows('absenceOffline_access'), 403);
        ini_set("memory_limit", -1);
        set_time_limit(0);
        $import = new AbsenceImport;
        $test =  Excel::import($import, $request->file('file'));
        // dd($test);
        $array = $import->getArray();
        // dd($array);
        $absences =  collect($import->getArray());
        $staff =  $absences->groupBy('nik');
        // dd($absences->where('category_id', 'in'));
        $users = user::with(['roles'])
            ->where('id', Auth::user()->id)
            ->first();
        foreach ($users->roles as $data) {
            foreach ($data->permissions as $data2) {
                $checker[] = $data2->title;
            }
        }

        // dd($absences);

        foreach ($staff as $key => $value) {
            if (in_array('absence_all_access', $checker)) {
                $staff_id = Staff::selectRaw('staffs.*, work_units.import as import_status')
                    ->join('work_units', 'work_units.id', '=', 'staffs.work_unit_id')
                    ->where('NIK', $key)
                    ->where('work_type_id', '1')
                    ->first();
            } else {
                $staff_id = Staff::selectRaw('staffs.*, work_units.import as import_status')
                    ->join('work_units', 'work_units.id', '=', 'staffs.work_unit_id')
                    ->where('NIK', $key)
                    ->where('work_type_id', '1')
                    ->where('dapertement_id', Auth::user()->dapertement_id)->first();
            }
            // dd('ini departemen id : ', Auth::user()->dapertement_id);

            // dd($staff_id, $key, Auth::user()->dapertement_id);
            if ($staff_id) {

                if ($staff_id->import_status == "ON") {
                    $absence_staff =  collect($import->getArray())->where('nik', $key);

                    $get_check_inR = collect($import->getArray())->where('nik', $key)->where('category_id', 'in')->first();
                    $get_check_outR = collect($import->getArray())->where('nik', $key)->where('category_id', 'out')->first();
                    $check_inR =  $get_check_inR['date'];
                    $check_outR = $get_check_outR['date'];

                    $get_check_inB = collect($import->getArray())->where('nik', $key)->where('category_id', 'break_in')->first();
                    $get_check_outB = collect($import->getArray())->where('nik', $key)->where('category_id', 'break_out')->first();
                    $check_inB =  $get_check_inB['date'];
                    $check_outB = $get_check_outB['date'];

                    $get_check_inV = collect($import->getArray())->where('nik', $key)->where('category_id', 'visit_in')->first();
                    $get_check_outV = collect($import->getArray())->where('nik', $key)->where('category_id', 'visin_out')->first();

                    $get_check_inE = collect($import->getArray())->where('nik', $key)->where('category_id', 'excuse_in')->first();
                    $get_check_outE = collect($import->getArray())->where('nik', $key)->where('category_id', 'excuse_out')->first();


                    // Pengecekan
                    if (date('Y-m-d', strtotime('-3 days', strtotime(date('Y-m-d')))) > date('Y-m-d', strtotime($check_inR)) || date('Y-m-d', strtotime('+1 days', strtotime(date('Y-m-d')))) < date('Y-m-d', strtotime($check_inR))) {
                        // return dd('Tanggal Lebih besar dari hari ini atau lewat dari 3 hari');
                    } else if (Absence::where('staff_id', $staff_id->id)->whereDate('created_at', '=', date('Y-m-d', strtotime($check_inR)))->first()) {
                        // return dd('Absen sudah ada di tanggal tersebut');
                    } else {



                        // dd($shift_staff_old->id);
                        // dd($shift_group_timesheets);

                        $data = [];
                        $data2 = [];
                        $data3 = [];

                        $data = [
                            'day_id' => date('w', strtotime($check_inR)),
                            'staff_id' => $staff_id->id,
                            'created_at' =>  date('Y-m-d 00:00:00', strtotime($check_inR)),
                        ];

                        $absence = Absence::create($data);


                        $data1 = [
                            'register' => date('Y-m-d H:i:s', strtotime($check_inR)),
                            'absence_category_id' => 1,
                            'absence_id' => $absence->id,
                            'duration' => 0,
                            'created_by_staff_id' => 0,
                            'status' => 0,
                            'timein' => date('Y-m-d H:i:s', strtotime($check_inR)),
                            'timeout' => date('Y-m-d H:i:s', strtotime($check_outR)),
                        ];
                        AbsenceLog::create($data1);
                        $datetime_1 = date('Y-m-d H:i:s', strtotime($check_inR));
                        $datetime_2 = date('Y-m-d H:i:s', strtotime($check_outR));

                        $from_time = strtotime($datetime_1);
                        $to_time = strtotime($datetime_2);
                        $diff_minutes = round(abs($from_time - $to_time) / 3600, 2) . " minutes";
                        $data1end = [
                            'register' => date('Y-m-d H:i:s', strtotime($check_outR)),
                            'absence_category_id' => 2,
                            'absence_id' => $absence->id,
                            'duration' => $diff_minutes,
                            'created_by_staff_id' => 0,
                            'status' => 0,
                            'timein' => date('Y-m-d H:i:s', strtotime($check_inR)),
                            'timeout' => date('Y-m-d H:i:s', strtotime($check_outR)),
                        ];
                        AbsenceLog::create($data1end);

                        $data5 = [
                            'register' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $check_inB)),
                            'absence_category_id' => 3,
                            // 'absence_request_id' => $break->id,
                            'absence_id' => $absence->id,
                            'duration' => 0,
                            'created_by_staff_id' => 0,
                            'status' => 0,
                            'timein' => date('Y-m-d H:i:s', strtotime($check_inR)),
                            'timeout' => date('Y-m-d H:i:s', strtotime($check_outR)),
                        ];
                        AbsenceLog::create($data5);

                        $datetime_1 = date('Y-m-d H:i:s', strtotime($check_inB));
                        $datetime_2 = date('Y-m-d H:i:s', strtotime($check_outB));

                        $from_time = strtotime($datetime_1);
                        $to_time = strtotime($datetime_2);
                        $diff_minutes = round(abs($from_time - $to_time) / 3600, 2) . " minutes";
                        $data5end = [
                            'register' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $check_outB)),
                            'absence_category_id' => 4,
                            // 'absence_request_id' => $break->id,
                            'absence_id' => $absence->id,
                            'duration' => $diff_minutes,
                            'created_by_staff_id' => 0,
                            'status' => 0,
                            'timein' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->break)),
                            'timeout' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->break_end)),

                        ];
                        AbsenceLog::create($data5end);

                        if ($get_check_inV) {
                            // if ($request->duty_end) {
                            foreach ($absences->where('category_id', '5') as $dty) {
                                $check_inV = $dty['date'];
                                $check_outV =  collect($import->getArray())->where('nik', $key)->where('category_id', 'visit_out')->first()['date'];
                                $duty = AbsenceRequest::create([
                                    'staff_id' => $staff_id->id,
                                    'start' => date('Y-m-d H:i:s', strtotime($check_inV)),
                                    'end' => date('Y-m-d H:i:s', strtotime($check_outV)),
                                    'type' => 'other',
                                    'time' =>   date('H:i:s', strtotime($check_inV)),
                                    'status' => 'approve',
                                    'category' => 'visit',
                                    'description' => $dty['description'],
                                ]);
                                $data2 = [
                                    'register' => date('Y-m-d H:i:s', strtotime($check_inV)),
                                    'absence_category_id' => 5,
                                    'absence_request_id' => $duty->id,
                                    'absence_id' => $absence->id,
                                    'duration' => 0,
                                    'created_by_staff_id' => 0,
                                    'status' => 0,
                                    'timein' => date('Y-m-d H:i:s', strtotime($check_inR)),
                                    'timeout' => date('Y-m-d H:i:s', strtotime($check_outR))
                                ];
                                AbsenceLog::create($data2);

                                $datetime_1 = date('Y-m-d H:i:s', strtotime($check_inV));
                                $datetime_2 = date('Y-m-d H:i:s', strtotime($check_outV));

                                $from_time = strtotime($datetime_1);
                                $to_time = strtotime($datetime_2);
                                $diff_minutes = round(abs($from_time - $to_time) / 3600, 2) . " minutes";
                                $data2end = [
                                    'register' => date('Y-m-d H:i:s', strtotime($check_outV)),
                                    'absence_category_id' => 6,
                                    'absence_request_id' => $duty->id,
                                    'absence_id' => $absence->id,
                                    'duration' =>  $diff_minutes,
                                    'created_by_staff_id' => 0,
                                    'status' => 0,
                                    'timein' => date('Y-m-d H:i:s', strtotime($check_inR)),
                                    'timeout' => date('Y-m-d H:i:s', strtotime($check_outR))
                                ];
                                AbsenceLog::create($data2end);
                            }
                            // }
                        }
                        if ($get_check_inE) {
                            $check_inE = $get_check_inE['date'];
                            $check_outE = $get_check_outE['date'];
                            $excuse = AbsenceRequest::create([
                                'staff_id' => $staff_id->id,
                                'start' => date('Y-m-d H:i:s', strtotime($check_inE)),
                                'end' => date('Y-m-d H:i:s', strtotime($check_outE)),
                                'type' => 'other',
                                'time' => $request->excuse,
                                'status' => 'approve',
                                'category' => 'visit',
                                'description' => $request->description_excuse,
                            ]);
                            $data3 = [
                                'register' => date('Y-m-d H:i:s', strtotime($check_inE)),
                                'absence_category_id' => 11,
                                'absence_request_id' => $excuse->id,
                                'absence_id' => $absence->id,
                                'duration' => 0,
                                'created_by_staff_id' => 0,
                                'status' => 0,
                                'timein' => date('Y-m-d H:i:s', strtotime($check_inR)),
                                'timeout' => date('Y-m-d H:i:s', strtotime($check_outR))
                            ];
                            AbsenceLog::create($data3);


                            $datetime_1 = date('Y-m-d H:i:s', strtotime($check_inE));
                            $datetime_2 = date('Y-m-d H:i:s', strtotime($check_outE));

                            $from_time = strtotime($datetime_1);
                            $to_time = strtotime($datetime_2);
                            $diff_minutes = round(abs($from_time - $to_time) / 3600, 2) . " minutes";
                            $data3end = [
                                'register' => date('Y-m-d H:i:s', strtotime($check_outE)),
                                'absence_category_id' => $excuse->id,
                                'absence_request_id' => 12,
                                'absence_id' => $absence->id,
                                'duration' => $diff_minutes,
                                'created_by_staff_id' => 0,
                                'status' => 0,
                                'timein' => date('Y-m-d H:i:s', strtotime($check_inR)),
                                'timeout' => date('Y-m-d H:i:s', strtotime($check_outR))
                            ];
                            AbsenceLog::create($data3end);
                        }

                        // input absence end
                        // dd($absence_staff);
                    }
                }
            }
        }

        // dd($staff_id);

        //ini test
        // dd($data3end);
        return redirect()->route('admin.absence.index');
        // $records = [
        //     ['id' => 3, 'nama' => 'budi'],
        //     ['id' => 4, 'nama' => 'udin'],
        //     ['id' => 6, 'nama' => 'udin'],
        //     ['id' => 5, 'nama' => 'yasa']
        // ];

        // $collect =  collect($records);
        // $search = $collect->groupBy('id');

        // $collect->where('id', '3');

        // dd($search);
    }
    // import absen reguler end

    public function createShift()
    {
        abort_unless(\Gate::allows('absenceOffline_access'), 403);
        $shift_parents = ShiftParent::get();
        // $absence_categories = Absence_categories::where('day_id', null)->get();
        // $day = Day::get();
        $staffs = Staff::where('work_type_id', '2')->orderBy('name')->get();
        return view('admin.absence.createShift', compact('staffs', 'shift_parents'));
    }

    public function storeShift(Request $request)
    {
        abort_unless(\Gate::allows('absenceOffline_access'), 403);
        // dd($request->all());
        $shift_staff_old = ShiftPlannerStaffs::where('staff_id', $request->staff_id)
            ->where('shift_group_id', $request->shift_group_id)
            ->whereDate('start', date('Y-m-d'))
            ->first();

        if ($shift_staff_old) {
            ShiftPlannerStaffs::where('id', $shift_staff_old->id)->delete();
        }

        $shift_group = ShiftGroups::where('shift_groups.id', $request->shift_group_id)
            ->where('shift_groups.id', $request->shift_group_id)
            ->first();

        $shift_group_timesheets = ShiftGroupTimesheets::where('shift_group_id', $request->shift_group_id)
            ->orderBy('absence_category_id')
            ->get();
        // dd($shift_group_timesheets);

        // dd($shift_staff_old->id);

        $shift_staff = ShiftPlannerStaffs::create([
            'staff_id'        =>    $request->staff_id,
            'shift_group_id' => $request->shift_group_id,
            'start'        =>     date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time)),
            'end'        =>     date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time_end))
        ]);


        // dd(
        //     $shift_staff_old,
        //     $shift_group,
        //     $shift_staff
        // );

        // dd($diff_minutes);

        $data = [];
        $data2 = [];
        $data3 = [];

        $data = [
            'day_id' => date('w', strtotime($request->date)),
            'staff_id' => $request->staff_id,
            'created_at' =>  date('Y-m-d 00:00:00', strtotime($request->date)),
            'shift_group_id' => $shift_group->id
        ];

        $absence = Absence::create($data);


        $data1 = [
            'register' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time)),
            'absence_category_id' => 1,
            'absence_id' => $absence->id,
            'duration' => 0,
            'created_by_staff_id' => 0,
            'status' => 0,
            'timein' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time)),
            'timeout' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time_end)),
            'shift_planner_id' => $shift_staff->id,
            'shift_group_timesheet_id' => $shift_group_timesheets[0]->id
        ];
        AbsenceLog::create($data1);
        $datetime_1 = date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time));
        $datetime_2 = date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time_end));

        $from_time = strtotime($datetime_1);
        $to_time = strtotime($datetime_2);
        $diff_minutes = round(abs($from_time - $to_time) / 3600, 2) . " minutes";
        $data1end = [
            'register' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time_end)),
            'absence_category_id' => 2,
            'absence_id' => $absence->id,
            'duration' => $diff_minutes,
            'created_by_staff_id' => 0,
            'status' => 0,
            'timein' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time)),
            'timeout' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time_end)),
            'shift_planner_id' => $shift_staff->id,
            'shift_group_timesheet_id' => $shift_group_timesheets[1]->id
        ];
        AbsenceLog::create($data1end);

        $data5 = [
            'register' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->break)),
            'absence_category_id' => 3,
            // 'absence_request_id' => $break->id,
            'absence_id' => $absence->id,
            'duration' => 0,
            'created_by_staff_id' => 0,
            'status' => 0,
            'timein' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->break)),
            'timeout' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->break_end)),
            'shift_planner_id' => $shift_staff->id,
            'shift_group_timesheet_id' => $shift_group_timesheets[2]->id
        ];
        AbsenceLog::create($data5);

        $datetime_1 = date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time));
        $datetime_2 = date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time_end));

        $from_time = strtotime($datetime_1);
        $to_time = strtotime($datetime_2);
        $diff_minutes = round(abs($from_time - $to_time) / 3600, 2) . " minutes";
        $data5end = [
            'register' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->break_end)),
            'absence_category_id' => 4,
            // 'absence_request_id' => $break->id,
            'absence_id' => $absence->id,
            'duration' => $diff_minutes,
            'created_by_staff_id' => 0,
            'status' => 0,
            'timein' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->break)),
            'timeout' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->break_end)),
            'shift_planner_id' => $shift_staff->id,
            'shift_group_timesheet_id' => $shift_group_timesheets[3]->id
        ];
        AbsenceLog::create($data5end);

        // if ($request->duty_end) {
        //     foreach ($request->duty_end as $dty) {
        //         $duty = AbsenceRequest::create([
        //             'staff_id' => $request->staff_id,
        //             'start' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $dty->duty)),
        //             'end' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $dty->duty_end)),
        //             'type' => 'other',
        //             'time' => $dty->duty,
        //             'status' => 'approve',
        //             'category' => 'visit',
        //             'description' => $dty->description_duty,
        //         ]);
        //         $data2 = [
        //             'register' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $dty->duty)),
        //             'absence_category_id' => 5,
        //             'absence_request_id' => $duty->id,
        //             'absence_id' => $absence->id,
        //             'duration' => 0,
        //             'created_by_staff_id' => 0,
        //             'status' => 0,
        //             'timein' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $dty->duty)),
        //             'timeout' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $dty->duty_end))
        //         ];
        //         AbsenceLog::create($data2);

        //         $datetime_1 = date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time));
        //         $datetime_2 = date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time_end));

        //         $from_time = strtotime($datetime_1);
        //         $to_time = strtotime($datetime_2);
        //         $diff_minutes = round(abs($from_time - $to_time) / 3600, 2) . " minutes";
        //         $data2end = [
        //             'register' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $dty->duty_end)),
        //             'absence_category_id' => 5,
        //             'absence_request_id' => $duty->id,
        //             'absence_id' => $absence->id,
        //             'duration' =>  $diff_minutes,
        //             'created_by_staff_id' => 0,
        //             'status' => 0,
        //             'timein' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $dty->duty)),
        //             'timeout' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $dty->duty_end))
        //         ];
        //         AbsenceLog::create($data2end);
        //     }
        // }
        if ($request->excuse_end) {
            $excuse = AbsenceRequest::create([
                'staff_id' => $request->staff_id,
                'start' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->excuse)),
                'end' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->excuse_end)),
                'type' => 'other',
                'time' => $request->excuse,
                'status' => 'approve',
                'category' => 'visit',
                'description' => $request->description_excuse,
            ]);
            $data3 = [
                'register' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->excuse)),
                'absence_category_id' => $excuse->id,
                'absence_request_id' => $excuse->id,
                'absence_id' => $absence->id,
                'duration' => 0,
                'created_by_staff_id' => 0,
                'status' => 0,
                'timein' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->excuse)),
                'timeout' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->excuse_end))
            ];
            AbsenceLog::create($data3);


            $datetime_1 = date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time));
            $datetime_2 = date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time_end));

            $from_time = strtotime($datetime_1);
            $to_time = strtotime($datetime_2);
            $diff_minutes = round(abs($from_time - $to_time) / 3600, 2) . " minutes";
            $data3end = [
                'register' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->excuse_end)),
                'absence_category_id' => $excuse->id,
                'absence_request_id' => $excuse->id,
                'absence_id' => $absence->id,
                'duration' => $diff_minutes,
                'created_by_staff_id' => 0,
                'status' => 0,
                'timein' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->excuse)),
                'timeout' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->excuse_end))
            ];
            AbsenceLog::create($data3end);
        }



        // dd($data, $data2, $data3);

        return redirect()->route('admin.absence.index');
    }

    // absen reguler start
    public function create()
    {
        abort_unless(\Gate::allows('absenceOffline_access'), 403);
        $shift_parents = ShiftParent::get();
        // $absence_categories = Absence_categories::where('day_id', null)->get();
        // $day = Day::get();
        $staffs = Staff::where('work_type_id', '1')->orderBy('name')->get();
        return view('admin.absence.create', compact('staffs', 'shift_parents'));
    }

    public function store(Request $request)
    {
        abort_unless(\Gate::allows('absenceOffline_access'), 403);
        // dd(Absence::where('staff_id', $request->staff_id)->whereDate('created_at', '=', date('Y-m-d', strtotime($request->date)))->first());
        if (date('Y-m-d', strtotime('-3 days', strtotime(date('Y-m-d')))) > date('Y-m-d', strtotime($request->date)) || date('Y-m-d', strtotime('+1 days', strtotime(date('Y-m-d')))) < date('Y-m-d', strtotime($request->date))) {
            return dd('Tanggal Lebih besar dari hari ini atau lewat dari 3 hari');
        } else if (Absence::where('staff_id', $request->staff_id)->whereDate('created_at', '=', date('Y-m-d', strtotime($request->date)))->first()) {
            return dd('Absen sudah ada di tanggal tersebut');
        }
        $data = [];
        $data2 = [];
        $data3 = [];

        $data = [
            'day_id' => date('w', strtotime($request->date)),
            'staff_id' => $request->staff_id,
            'created_at' =>  date('Y-m-d 00:00:00', strtotime($request->date))
        ];

        $absence = Absence::create($data);

        $data1 = [
            'register' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time)),
            'absence_category_id' => 1,
            'absence_id' => $absence->id,
            'duration' => 0,
            'created_by_staff_id' => 0,
            'status' => 0,
            'timein' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time)),
            'timeout' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time_end)),
        ];
        AbsenceLog::create($data1);
        $datetime_1 = date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time));
        $datetime_2 = date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time_end));

        $from_time = strtotime($datetime_1);
        $to_time = strtotime($datetime_2);
        $diff_minutes = round(abs($from_time - $to_time) / 3600, 2) . " minutes";
        $data1end = [
            'register' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time_end)),
            'absence_category_id' => 2,
            'absence_id' => $absence->id,
            'duration' => $diff_minutes,
            'created_by_staff_id' => 0,
            'status' => 0,
            'timein' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time)),
            'timeout' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time_end)),
        ];
        AbsenceLog::create($data1end);

        $data5 = [
            'register' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->break)),
            'absence_category_id' => 3,
            // 'absence_request_id' => $break->id,
            'absence_id' => $absence->id,
            'duration' => 0,
            'created_by_staff_id' => 0,
            'status' => 0,
            'timein' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time)),
            'timeout' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time_end)),
        ];
        AbsenceLog::create($data5);

        $datetime_1 = date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->break));
        $datetime_2 = date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->break_end));

        $from_time = strtotime($datetime_1);
        $to_time = strtotime($datetime_2);
        $diff_minutes = round(abs($from_time - $to_time) / 3600, 2) . " minutes";
        $data5end = [
            'register' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->break_end)),
            'absence_category_id' => 4,
            // 'absence_request_id' => $break->id,
            'absence_id' => $absence->id,
            'duration' => $diff_minutes,
            'created_by_staff_id' => 0,
            'status' => 0,
            'timein' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time)),
            'timeout' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time_end)),
        ];
        AbsenceLog::create($data5end);


        if ($request->duty_end) {
            $i = 0;
            foreach ($request->duty_end as $dty) {
                if ($request->duty[$i]) {
                    // dd($dty);
                    $duty = AbsenceRequest::create([
                        'staff_id' => $request->staff_id,
                        'start' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->duty[$i])),
                        'end' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->duty_end[$i])),
                        'type' => 'other',
                        'time' => $request->duty[$i],
                        'status' => 'approve',
                        'category' => 'visit',
                        'description' => $request->description_duty,
                    ]);
                    $data2 = [
                        'register' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->duty[$i])),
                        'absence_category_id' => 5,
                        'absence_request_id' => $duty->id,
                        'absence_id' => $absence->id,
                        'duration' => 0,
                        'created_by_staff_id' => 0,
                        'status' => 0,
                        'timein' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->duty[$i])),
                        'timeout' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->duty_end[$i]))
                    ];
                    AbsenceLog::create($data2);

                    $datetime_1 = date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time));
                    $datetime_2 = date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time_end));

                    $from_time = strtotime($datetime_1);
                    $to_time = strtotime($datetime_2);
                    $diff_minutes = round(abs($from_time - $to_time) / 3600, 2) . " minutes";
                    $data2end = [
                        'register' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->duty_end[$i])),
                        'absence_category_id' => 6,
                        'absence_request_id' => $duty->id,
                        'absence_id' => $absence->id,
                        'duration' =>  $diff_minutes,
                        'created_by_staff_id' => 0,
                        'status' => 0,
                        'timein' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->duty[$i])),
                        'timeout' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->duty_end[$i]))
                    ];
                    AbsenceLog::create($data2end);
                }
                // $i++;
            }
            // dd($i, $request->duty);
        }

        if ($request->excuse_end) {
            $excuse = AbsenceRequest::create([
                'staff_id' => $request->staff_id,
                'start' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->excuse)),
                'end' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->excuse_end)),
                'type' => 'other',
                'time' => $request->excuse,
                'status' => 'approve',
                'category' => 'excuse',
                'description' => $request->description_excuse,
            ]);
            $data3 = [
                'register' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->excuse)),
                'absence_category_id' => 11,
                'absence_request_id' => $excuse->id,
                'absence_id' => $absence->id,
                'duration' => 0,
                'created_by_staff_id' => 0,
                'status' => 0,
                'timein' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time)),
                'timeout' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time_end))
            ];
            AbsenceLog::create($data3);


            $datetime_1 = date('Y-m-d H:i:s', strtotime($request->date . ' ' .  $request->excuse));
            $datetime_2 = date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->excuse_end));

            $from_time = strtotime($datetime_1);
            $to_time = strtotime($datetime_2);
            $diff_minutes = round(abs($from_time - $to_time) / 3600, 2) . " minutes";
            $data3end = [
                'register' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->excuse_end)),
                'absence_category_id' => 12,
                'absence_request_id' => $excuse->id,
                'absence_id' => $absence->id,
                'duration' => $diff_minutes,
                'created_by_staff_id' => 0,
                'status' => 0,
                'timein' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time)),
                'timeout' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time_end))
            ];
            AbsenceLog::create($data3end);
        }
        // dd($data, $data2, $data3);
        return redirect()->route('admin.absence.index');
    }

    // absen reguler end



    // absen ektra start
    public function createExtra()
    {
        abort_unless(\Gate::allows('absenceOffline_access'), 403);
        // $absence_categories = Absence_categories::where('day_id', null)->get();
        // $day = Day::get();
        $staffs = Staff::orderBy('name')->get();
        return view('admin.absence.createExtra', compact('staffs'));
    }

    public function storeExtra(Request $request)
    {
        abort_unless(\Gate::allows('absenceOffline_access'), 403);

        $data = [];
        $data2 = [];
        $data3 = [];

        $data = [
            'day_id' => date('w', strtotime($request->date)),
            'staff_id' => $request->staff_id,
            'created_at' =>  date('Y-m-d 00:00:00', strtotime($request->date))
        ];

        $absence = Absence::create($data);

        $extra = AbsenceRequest::create([
            'staff_id' => $request->staff_id,
            'start' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time)),
            'end' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time_end)),
            'type' => 'other',
            'time' => $request->time,
            'status' => 'approve',
            'category' => 'extra',
            'description' => $request->description,
        ]);

        $data1 = [
            'register' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time)),
            'absence_category_id' => 9,
            'absence_request_id' => $extra->id,
            'absence_id' => $absence->id,
            'duration' => 0,
            'created_by_staff_id' => 0,
            'status' => 0,
            'timein' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time)),
            'timeout' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time_end)),
        ];
        AbsenceLog::create($data1);


        $datetime_1 = date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time));
        $datetime_2 = date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time_end));

        $from_time = strtotime($datetime_1);
        $to_time = strtotime($datetime_2);
        $diff_minutes = round(abs($from_time - $to_time) / 3600, 2) . " minutes";
        $data1end = [
            'register' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time_end)),
            'absence_category_id' => 10,
            'absence_id' => $absence->id,
            'duration' => $diff_minutes,
            'created_by_staff_id' => 0,
            'status' => 0,
            'timein' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time)),
            'timeout' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time_end)),
        ];
        AbsenceLog::create($data1end);


        // dd($data, $data2, $data3);
        return redirect()->route('admin.absence.index');
    }

    // absen ektra end

    // absen tidak hadir
    public function createPermit()
    {
        abort_unless(\Gate::allows('absenceOffline_access'), 403);
        // $absence_categories = Absence_categories::where('day_id', null)->get();
        // $day = Day::get();
        $staffs = Staff::orderBy('name')->get();
        return view('admin.absence.createPermit', compact('staffs'));
    }


    public function storePermit(Request $request)
    {
        abort_unless(\Gate::allows('absenceOffline_access'), 403);

        $staff = Staff::where('id', $request->staff_id)->first();
        if ($staff->work_type_id === 2) {
            $permit = AbsenceRequest::create([
                'staff_id' => $request->staff_id,
                'start' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . '00:00:00')),
                'end' => date('Y-m-d H:i:s', strtotime($request->date2 . ' ' . '23:59:59')),
                'type' => $request->type,
                'time' => '',
                'status' => 'approve',
                'category' => 'permission',
                'description' => $request->description,
            ]);

            $d = AbsenceRequest::where('id', $permit->id)->first();

            // buat absence log start

            $absenceRequest =  AbsenceRequest::where('id', $permit->id)->first();
            // $message = "Cuti anda tanggal " . $d->start . " sampai dengan " . $d->end . " diterima";
            // if ($absenceRequest->end > $absenceRequest->start) {
            //     dd("shshsh");
            // } else {
            //     dd(date('Y-m-d'), $absenceRequest->start);
            // }
            // dd($absenceRequest->start);
            // if (date('Y-m-d') > $absenceRequest->start) {
            if ($absenceRequest->end > $absenceRequest->start) {
                $begin = strtotime($absenceRequest->start);
                $end   = strtotime($absenceRequest->end);

                for ($i = $begin; $i <= $end; $i = $i + 86400) {
                    // dd(date('Y-m-d', $i));
                    $check_empty = Absence::where('staff_id', $absenceRequest->staff_id)->whereDate('created_at', '=', date('Y-m-d', $i))->first();
                    // dd($check_empty);
                    if (!$check_empty) {
                        $shift_staff = ShiftPlannerStaffs::whereDate('start', '=', date('Y-m-d', $i))->first();
                        if (!$shift_staff) {
                            if (date("w", strtotime(date('Y-m-d', $i))) != 0 && date("w", strtotime(date('Y-m-d', $i))) != 6) {

                                $ab_id = Absence::create([
                                    'day_id' => date("w", strtotime(date('Y-m-d', $i))),
                                    'staff_id' => $absenceRequest->staff_id,
                                    'created_at' => date('Y-m-d H:i:s', $i),
                                    'updated_at' => date('Y-m-d H:i:s')
                                ]);
                                AbsenceLog::create([
                                    'absence_category_id' => 13,
                                    'lat' => '',
                                    'lng' => '',
                                    'absence_request_id' => $absenceRequest->id,
                                    'register' => date('Y-m-d', $i),
                                    'absence_id' => $ab_id->id,
                                    'duration' => '',
                                    'status' => ''
                                ]);
                            }
                        }
                    }
                }
            }
        } else {
            $permit = AbsenceRequest::create([
                'staff_id' => $request->staff_id,
                'start' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . '00:00:00')),
                'end' => date('Y-m-d H:i:s', strtotime($request->date2 . ' ' . '23:59:59')),
                'type' => $request->type,
                'time' => '',
                'status' => 'approve',
                'category' => 'permission',
                'description' => $request->description,
            ]);

            $d = AbsenceRequest::where('id', $permit->id)->first();

            // buat absence log start

            $absenceRequest =  AbsenceRequest::where('id', $permit->id)->first();
            // $message = "Cuti anda tanggal " . $d->start . " sampai dengan " . $d->end . " diterima";
            // if ($absenceRequest->end > $absenceRequest->start) {
            //     dd("shshsh");
            // } else {
            //     dd(date('Y-m-d'), $absenceRequest->start);
            // }
            // dd($absenceRequest->start);
            // if (date('Y-m-d') > $absenceRequest->start) {
            if ($absenceRequest->end > $absenceRequest->start) {
                $begin = strtotime($absenceRequest->start);
                $end   = strtotime($absenceRequest->end);

                for ($i = $begin; $i <= $end; $i = $i + 86400) {
                    // dd(date('Y-m-d', $i));
                    $check_empty = Absence::where('staff_id', $absenceRequest->staff_id)->whereDate('created_at', '=', date('Y-m-d', $i))->first();
                    // dd($check_empty);
                    if (!$check_empty) {
                        $holiday = Holiday::whereDate('start', '<=', date('Y-m-d', $i))->whereDate('end', '>=', date('Y-m-d', $i))->first();
                        if (!$holiday) {
                            if (date("w", strtotime(date('Y-m-d', $i))) != 0 && date("w", strtotime(date('Y-m-d', $i))) != 6) {

                                $ab_id = Absence::create([
                                    'day_id' => date("w", strtotime(date('Y-m-d', $i))),
                                    'staff_id' => $absenceRequest->staff_id,
                                    'created_at' => date('Y-m-d H:i:s', $i),
                                    'updated_at' => date('Y-m-d H:i:s')
                                ]);
                                AbsenceLog::create([
                                    'absence_category_id' => 13,
                                    'lat' => '',
                                    'lng' => '',
                                    'absence_request_id' => $absenceRequest->id,
                                    'register' => date('Y-m-d', $i),
                                    'absence_id' => $ab_id->id,
                                    'duration' => '',
                                    'status' => ''
                                ]);
                            }
                        }
                    }
                }
            }
        }
        // buat absence log end



        return redirect()->route('admin.absence.index');
    }
    // absen tidak hadir end

    // absen cuti start
    public function createLeave()
    {
        abort_unless(\Gate::allows('absenceOffline_access'), 403);
        // $absence_categories = Absence_categories::where('day_id', null)->get();
        // $day = Day::get();
        $staffs = Staff::orderBy('name')->get();
        return view('admin.absence.createLeave', compact('staffs'));
    }


    public function storeLeave(Request $request)
    {
        abort_unless(\Gate::allows('absenceOffline_access'), 403);

        $staff = Staff::where('id', $request->staff_id)->first();
        if ($staff->work_type_id === 2) {
            $permit = AbsenceRequest::create([
                'staff_id' => $request->staff_id,
                'start' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . '00:00:00')),
                'end' => date('Y-m-d H:i:s', strtotime($request->date2 . ' ' . '23:59:59')),
                'type' => 'other',
                'time' => '',
                'status' => 'approve',
                'category' => 'leave',
                'description' => $request->description,
            ]);

            $d = AbsenceRequest::where('id', $permit->id)->first();

            // buat absence log start

            $absenceRequest =  AbsenceRequest::where('id', $permit->id)->first();
            // $message = "Cuti anda tanggal " . $d->start . " sampai dengan " . $d->end . " diterima";
            // if ($absenceRequest->end > $absenceRequest->start) {
            //     dd("shshsh");
            // } else {
            //     dd(date('Y-m-d'), $absenceRequest->start);
            // }
            // dd($absenceRequest->start);
            // if (date('Y-m-d') > $absenceRequest->start) {
            if ($absenceRequest->end > $absenceRequest->start) {
                $begin = strtotime($absenceRequest->start);
                $end   = strtotime($absenceRequest->end);

                for ($i = $begin; $i <= $end; $i = $i + 86400) {
                    // dd(date('Y-m-d', $i));
                    $check_empty = Absence::where('staff_id', $absenceRequest->staff_id)->whereDate('created_at', '=', date('Y-m-d', $i))->first();
                    // dd($check_empty);
                    if (!$check_empty) {
                        $shift_staff = ShiftPlannerStaffs::whereDate('start', '=', date('Y-m-d', $i))->first();
                        if (!$shift_staff) {
                            if (date("w", strtotime(date('Y-m-d', $i))) != 0 && date("w", strtotime(date('Y-m-d', $i))) != 6) {

                                $ab_id = Absence::create([
                                    'day_id' => date("w", strtotime(date('Y-m-d', $i))),
                                    'staff_id' => $absenceRequest->staff_id,
                                    'created_at' => date('Y-m-d H:i:s', $i),
                                    'updated_at' => date('Y-m-d H:i:s')
                                ]);
                                AbsenceLog::create([
                                    'absence_category_id' => 8,
                                    'lat' => '',
                                    'lng' => '',
                                    'absence_request_id' => $absenceRequest->id,
                                    'register' => date('Y-m-d', $i),
                                    'absence_id' => $ab_id->id,
                                    'duration' => '',
                                    'status' => ''
                                ]);
                            }
                        }
                    }
                }
            }
        } else {
            $permit = AbsenceRequest::create([
                'staff_id' => $request->staff_id,
                'start' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . '00:00:00')),
                'end' => date('Y-m-d H:i:s', strtotime($request->date2 . ' ' . '23:59:59')),
                'type' => $request->type,
                'time' => '',
                'status' => 'approve',
                'category' => 'permission',
                'description' => $request->description,
            ]);

            $d = AbsenceRequest::where('id', $permit->id)->first();

            // buat absence log start

            $absenceRequest =  AbsenceRequest::where('id', $permit->id)->first();
            // $message = "Cuti anda tanggal " . $d->start . " sampai dengan " . $d->end . " diterima";
            // if ($absenceRequest->end > $absenceRequest->start) {
            //     dd("shshsh");
            // } else {
            //     dd(date('Y-m-d'), $absenceRequest->start);
            // }
            // dd($absenceRequest->start);
            // if (date('Y-m-d') > $absenceRequest->start) {
            if ($absenceRequest->end > $absenceRequest->start) {
                $begin = strtotime($absenceRequest->start);
                $end   = strtotime($absenceRequest->end);

                for ($i = $begin; $i <= $end; $i = $i + 86400) {
                    // dd(date('Y-m-d', $i));
                    $check_empty = Absence::where('staff_id', $absenceRequest->staff_id)->whereDate('created_at', '=', date('Y-m-d', $i))->first();
                    // dd($check_empty);
                    if (!$check_empty) {
                        $holiday = Holiday::whereDate('start', '<=', date('Y-m-d', $i))->whereDate('end', '>=', date('Y-m-d', $i))->first();
                        if (!$holiday) {
                            if (date("w", strtotime(date('Y-m-d', $i))) != 0 && date("w", strtotime(date('Y-m-d', $i))) != 6) {

                                $ab_id = Absence::create([
                                    'day_id' => date("w", strtotime(date('Y-m-d', $i))),
                                    'staff_id' => $absenceRequest->staff_id,
                                    'created_at' => date('Y-m-d H:i:s', $i),
                                    'updated_at' => date('Y-m-d H:i:s')
                                ]);
                                AbsenceLog::create([
                                    'absence_category_id' => 8,
                                    'lat' => '',
                                    'lng' => '',
                                    'absence_request_id' => $absenceRequest->id,
                                    'register' => date('Y-m-d', $i),
                                    'absence_id' => $ab_id->id,
                                    'duration' => '',
                                    'status' => ''
                                ]);
                            }
                        }
                    }
                }
            }
        }
        // buat absence log end



        return redirect()->route('admin.absence.index');
    }
    // absen cuti end


    // absen Dinas Luar Start
    public function createDuty()
    {
        abort_unless(\Gate::allows('absenceOffline_access'), 403);
        // $absence_categories = Absence_categories::where('day_id', null)->get();
        // $day = Day::get();
        $staffs = Staff::orderBy('name')->get();
        return view('admin.absence.createDuty', compact('staffs'));
    }


    public function storeDuty(Request $request)
    {
        abort_unless(\Gate::allows('absenceOffline_access'), 403);

        $staff = Staff::where('id', $request->staff_id)->first();
        if ($staff->work_type_id === 2) {
            $permit = AbsenceRequest::create([
                'staff_id' => $request->staff_id,
                'start' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . '00:00:00')),
                'end' => date('Y-m-d H:i:s', strtotime($request->date2 . ' ' . '23:59:59')),
                'type' => 'other',
                'time' => '',
                'status' => 'approve',
                'category' => 'leave',
                'description' => $request->description,
            ]);

            $d = AbsenceRequest::where('id', $permit->id)->first();

            // buat absence log start

            $absenceRequest =  AbsenceRequest::where('id', $permit->id)->first();
            // $message = "Cuti anda tanggal " . $d->start . " sampai dengan " . $d->end . " diterima";
            // if ($absenceRequest->end > $absenceRequest->start) {
            //     dd("shshsh");
            // } else {
            //     dd(date('Y-m-d'), $absenceRequest->start);
            // }
            // dd($absenceRequest->start);
            // if (date('Y-m-d') > $absenceRequest->start) {
            if ($absenceRequest->end > $absenceRequest->start) {
                $begin = strtotime($absenceRequest->start);
                $end   = strtotime($absenceRequest->end);

                for ($i = $begin; $i <= $end; $i = $i + 86400) {
                    // dd(date('Y-m-d', $i));
                    $check_empty = Absence::where('staff_id', $absenceRequest->staff_id)->whereDate('created_at', '=', date('Y-m-d', $i))->first();
                    // dd($check_empty);
                    if (!$check_empty) {
                        $shift_staff = ShiftPlannerStaffs::whereDate('start', '=', date('Y-m-d', $i))->first();
                        if (!$shift_staff) {
                            if (date("w", strtotime(date('Y-m-d', $i))) != 0 && date("w", strtotime(date('Y-m-d', $i))) != 6) {

                                $ab_id = Absence::create([
                                    'day_id' => date("w", strtotime(date('Y-m-d', $i))),
                                    'staff_id' => $absenceRequest->staff_id,
                                    'created_at' => date('Y-m-d H:i:s', $i),
                                    'updated_at' => date('Y-m-d H:i:s')
                                ]);
                                AbsenceLog::create([
                                    'absence_category_id' => 8,
                                    'lat' => '',
                                    'lng' => '',
                                    'absence_request_id' => $absenceRequest->id,
                                    'register' => date('Y-m-d', $i),
                                    'absence_id' => $ab_id->id,
                                    'duration' => '',
                                    'status' => ''
                                ]);
                            }
                        }
                    }
                }
            }
        } else {
            $permit = AbsenceRequest::create([
                'staff_id' => $request->staff_id,
                'start' => date('Y-m-d H:i:s', strtotime($request->date . ' ' . '00:00:00')),
                'end' => date('Y-m-d H:i:s', strtotime($request->date2 . ' ' . '23:59:59')),
                'type' => $request->type,
                'time' => '',
                'status' => 'approve',
                'category' => 'permission',
                'description' => $request->description,
            ]);

            $d = AbsenceRequest::where('id', $permit->id)->first();

            // buat absence log start

            $absenceRequest =  AbsenceRequest::where('id', $permit->id)->first();
            // $message = "Cuti anda tanggal " . $d->start . " sampai dengan " . $d->end . " diterima";
            // if ($absenceRequest->end > $absenceRequest->start) {
            //     dd("shshsh");
            // } else {
            //     dd(date('Y-m-d'), $absenceRequest->start);
            // }
            // dd($absenceRequest->start);
            // if (date('Y-m-d') > $absenceRequest->start) {
            if ($absenceRequest->end > $absenceRequest->start) {
                $begin = strtotime($absenceRequest->start);
                $end   = strtotime($absenceRequest->end);

                for ($i = $begin; $i <= $end; $i = $i + 86400) {
                    // dd(date('Y-m-d', $i));
                    $check_empty = Absence::where('staff_id', $absenceRequest->staff_id)->whereDate('created_at', '=', date('Y-m-d', $i))->first();
                    // dd($check_empty);
                    if (!$check_empty) {
                        $holiday = Holiday::whereDate('start', '<=', date('Y-m-d', $i))->whereDate('end', '>=', date('Y-m-d', $i))->first();
                        if (!$holiday) {
                            if (date("w", strtotime(date('Y-m-d', $i))) != 0 && date("w", strtotime(date('Y-m-d', $i))) != 6) {

                                $ab_id = Absence::create([
                                    'day_id' => date("w", strtotime(date('Y-m-d', $i))),
                                    'staff_id' => $absenceRequest->staff_id,
                                    'created_at' => date('Y-m-d H:i:s', $i),
                                    'updated_at' => date('Y-m-d H:i:s')
                                ]);
                                AbsenceLog::create([
                                    'absence_category_id' => 7,
                                    'lat' => '',
                                    'lng' => '',
                                    'absence_request_id' => $absenceRequest->id,
                                    'register' => date('Y-m-d', $i),
                                    'absence_id' => $ab_id->id,
                                    'duration' => '',
                                    'status' => ''
                                ]);
                            }
                        }
                    }
                }
            }
        }
        // buat absence log end



        return redirect()->route('admin.absence.index');
    }
    // absen dinas end

    public function show($id)
    {
        abort_unless(\Gate::allows('absence_show'), 403);
        $absence = Absence::selectRaw('absences.*, users.name as user, days.name as day, absence_categories.title as absence_category')->leftJoin('users', 'absences.user_id', '=', 'users.id')
            ->leftJoin('days', 'absences.day_id', '=', 'days.id')
            ->leftJoin('absence_categories', 'absences.absence_category_id', '=', 'absence_categories.id')
            ->where('absences.id', $id)->first();

        return view('admin.absence.show', compact('absence'));
    }

    public function edit($id)
    {
        abort_unless(\Gate::allows('absence_edit'), 403);
        $absence = Absence::selectRaw('absences.*, users.name as user, days.name as day, absence_categories.title as absence_category')->leftJoin('users', 'absences.user_id', '=', 'users.id')
            ->leftJoin('days', 'absences.day_id', '=', 'days.id')
            ->leftJoin('absence_categories', 'absences.absence_category_id', '=', 'absence_categories.id')
            ->where('absences.id', $id)->first();
        return view('admin.absence.edit', compact('absence'));
    }

    public function update(Request $request, $id)
    {
        abort_unless(\Gate::allows('absence_edit'), 403);
        $absence = Absence::where('id', $id)->first();
        $img_path = "/images/absence";
        $basepath = str_replace("laravel-simpletab", "public_html/simpletabadmin", \base_path());
        if ($absence->image != "") {
            unlink($basepath . $absence->image);
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
        $data = [
            'image' =>  $name_image
        ];
        $data = Absence::where('id', $id)->update($data);
        return redirect()->route('admin.absence.index');
    }

    // untuk nanti di API
    function countDays($year, $month, $ignore)
    {
        $count = 0;
        $counter = mktime(0, 0, 0, $month, 1, $year);
        while (date("n", $counter) == $month) {
            if (in_array(date("w", $counter), $ignore) == false) {
                $count++;
            }
            $counter = strtotime("+1 day", $counter);
        }
        return  $count;
    }
    public function test()
    {
        echo $this->countDays(2022, 12, array(0, 6)); // 23
    }

    public function absenceMenu()
    {

        $menu = "";
        $shift = ShiftStaff::join('shifts', 'shifts.id', '=', 'shift_staff.shift_id')
            ->where('date', date('Y-m-d'))
            ->where('staff_id', 2)
            ->first();
        $request = Requests::where('date', '<=', date('Y-m-d'))
            ->where('end', '>=', date('Y-m-d'))
            ->where('user_id', '=', '2')
            ->where('status', '=', 'approve')
            ->where('category', 'cuti')
            ->first();

        $holiday = Holiday::where('date', date('Y-m-d'))->first();
        if ($request) {
            dd('Anda Sedang Cuti');
        } else if ($holiday) {
            echo "hari ini libur " . $holiday->title;
        } else if ($shift) {
            $data1 = date('H:i:s', strtotime($shift->start_in));
            $data2 = date('H:i:s', strtotime($shift->end_in));

            $data3 = date('H:i:s', strtotime($shift->start_breakin));
            $data4 = date('H:i:s', strtotime($shift->end_breakin));

            $data5 = date('H:i:s', strtotime($shift->start_breakout));
            $data6 = date('H:i:s', strtotime($shift->end_breakout));

            $data7 = date('H:i:s', strtotime($shift->start_out));
            $data8 = date('H:i:s', strtotime($shift->end_out));
            // $r[] = $data1;
            if ($data1 < date('H:i:s') && $data2 > date('H:i:s')) {
                $menu = "IS";
                echo "absen data1";
            }
            if ($data3 < date('H:i:s') && $data4 > date('H:i:s')) {
                $menu = "BIS";
                echo "absen data2";
            }
            if ($data5 < date('H:i:s') && $data6 > date('H:i:s')) {
                $menu = "BOS";
                echo "absen data3";
            }
            if ($data7 < date('H:i:s') && $data8 > date('H:i:s')) {
                $menu = "IO";
                echo "absen data4";
            }
        } else {
            if (date('w') == '0') {
                $day = '7';
            } else {
                $day = date('w');
            }

            $absen = Absence_categories::selectRaw('absence_categories.*')
                ->where('day_id', 1)
                ->get();

            foreach ($absen as $data) {

                $data1 = date('H:i:s', strtotime($data->start));
                $data2 = date('H:i:s', strtotime($data->end));
                // $r[] = $data1;
                if ($data1 < date('H:i:s') && $data2 > date('H:i:s')) {
                    $menu = $data->title;
                    echo $menu;
                }
            }
        }
    }

    public function destroy($id)
    {
        abort_unless(\Gate::allows('absence_delete'), 403);
        $absence = Absence::where('id', $id)->first();
        $basepath = str_replace("laravel-simpletab", "public_html/simpletabadmin", \base_path());
        unlink($basepath . "images/absence/" . $absence->image);
        Absence::where('id', $id)->delete();
        return redirect()->back();
    }

    // nanti dikembalikan
    public function reportAbsenceExcelView()
    {
        $staffs = Staff::get();
        $work_units = WorkUnit::get();
        return view('admin.absence.reportExcel', compact('staffs', 'work_units'));
    }

    public function reportAbsenceExcel(Request $request)
    {
        $staffs = Staff::FilterWorkUnit($request->work_unit)
            ->FilterId($request->staff_id)
            ->get();
        $list_absen_excel = [];
        $date_from = $request->from;
        $date_to = $request->to;
        foreach ($staffs as $stf) {
            $staff = Staff::where('id', $stf->id)->first();

            if ($staff->work_type_id != 2) {
                // untuk reguler
                $absence =  Absence::with(['absence_logs', 'absence_logs.workTypeDays', 'staffs'])
                    ->where('staff_id', $staff->id)
                    ->whereBetween(DB::raw('DATE(absences.created_at)'), [$date_from, $date_to])
                    ->get();
            } else {
                // untuk shift
                $absence =  Absence::with(['absence_logs', 'absence_logs.shiftGroupTimeSheets', 'staffs'])
                    ->where('staff_id', $staff->id)
                    ->whereBetween(DB::raw('DATE(absences.created_at)'), [$date_from, $date_to])
                    ->get();
            }
            $list_absen = [];
            if ($staff->work_type_id != 2) {
                $holidays = Holiday::get();
                foreach ($holidays as $data) {
                    $list_absen[] = [
                        'Emp No' => '',
                        'AC-No' => '',
                        'No' => $staff->NIK,
                        'Name' => $staff->name,
                        'Auto-Asign' => '',
                        'Date' => date('Y-m-d', strtotime($data->start)),
                        'TimeTable' => '',
                        'On_Duty' => '',
                        'Off_Duty' => '',
                        'Clock_in' => '',
                        'Clock_out' => '',
                        'keterangan' => $data->title,
                        'deskripsi' => $data->description,
                    ];
                }
            }
            $get_jadwal_libur = Day::select('days.*')->leftJoin(
                'work_type_days',
                function ($join) use ($staff) {
                    $join->on('days.id', '=', 'work_type_days.day_id')
                        ->where('work_type_id', $staff->work_type_id);
                }
            )->where('work_type_days.day_id', '=', null)->get();;
            $jadwal_libur = [];
            foreach ($get_jadwal_libur as $data) {
                $jadwal_libur[] = $data->id;
            }
            // in_array('1', $jadwal_libur);
            // dd($jadwal_libur);

            foreach ($absence as $data) {
                $shift = "";
                $deskripsi = "";

                $get_duty_in = $data->absence_logs->where('absence_category_id', 1)->first();
                $get_duty_out = $data->absence_logs->where('absence_category_id', 2)->first();
                $get_clock_in = $data->absence_logs->where('absence_category_id', 1)->first();
                $get_clock_out = $data->absence_logs->where('absence_category_id', 2)->first();

                $get_lembur_in = $data->absence_logs->where('absence_category_id', 9)->first();
                $get_lembur_out = $data->absence_logs->where('absence_category_id', 10)->first();

                $get_dinasLuar = $data->absence_logs->where('absence_category_id', 7)->first();
                $get_cuti = $data->absence_logs->where('absence_category_id', 8)->first();
                $get_izin = $data->absence_logs->where('absence_category_id', 13)->first();

                if ($staff->work_type_id != 2) {

                    if ($get_duty_in) {
                        if ($get_duty_in->workTypeDays) {
                            $duty_in = $get_duty_in->workTypeDays->time;
                        } else {
                            $duty_in = '';
                        }
                    } else {
                        $duty_in = '';
                    }

                    if ($get_duty_out) {
                        if ($get_duty_out->workTypeDays) {
                            $duty_out = $get_duty_out->workTypeDays->time;
                        } else {
                            $duty_out = '';
                        }
                    } else {
                        $duty_out = '';
                    }

                    $clock_in = $get_clock_in ? $get_clock_in->register : '';
                    $clock_out = $get_clock_out ? $get_clock_out->register : '';
                } else {
                    if ($get_duty_in) {
                        if ($get_duty_in->shiftGroupTimeSheets) {
                            $duty_in = $get_duty_in->shiftGroupTimeSheets->time;
                        } else {
                            $duty_in = '';
                        }
                    } else {
                        $duty_in = '';
                    }

                    if ($get_duty_out) {
                        if ($get_duty_out->shiftGroupTimeSheets) {
                            $duty_out = $get_duty_out->shiftGroupTimeSheets->time;
                        } else {
                            $duty_out = '';
                        }
                    } else {
                        $duty_out = '';
                    }

                    $clock_in = $get_clock_in ? $get_clock_in->register : '';
                    $clock_out = $get_clock_out ? $get_clock_out->register : '';
                    $shift = $get_clock_out ? $get_clock_out->shift_planner_id : '';
                }


                if ($clock_in != '') {
                    $keterangan = "Masuk";
                }
                // ini dilanjutkan besok
                else if ($get_lembur_in) {
                    $clock_in = $get_lembur_in->register;
                    $clock_out = $get_lembur_out->register;
                    $keterangan = "Lembur";
                } else if ($get_dinasLuar) {
                    $clock_in = "00:00:00";
                    $clock_out = "00:00:00";
                    $keterangan = "Dinas Luar";
                } else if ($get_cuti) {
                    $clock_in = "00:00:00";
                    $clock_out = "00:00:00";
                    $keterangan = "Cuti";
                } else if ($get_izin) {
                    $clock_in = "00:00:00";
                    $clock_out = "00:00:00";
                    $keterangan = "Izin";
                } else {
                    $keterangan = "Alpha";
                }
                if ($get_clock_out && $get_clock_out->status == '1' && $keterangan != "Alpha") {
                    $deskripsi = "Cek Mungkin Lupa Absen";
                    // dd($get_clock_out);
                }
                if (date('H:i:s', strtotime($clock_in)) >  date('H:i:s', strtotime($clock_out))) {


                    if ($clock_out) {
                        $deskripsi = "Cek Mungkin Terhitung 2 Hari (" . $clock_in . ' - ' . $clock_out . ')';
                    } else {
                        $deskripsi = "Cek Mungkin Lupa Absen";
                    }
                    // dd('tesss');
                }



                $list_absen[] = [
                    'Emp No' => '',
                    'AC-No' => '',
                    'No' => $data->staffs->NIK,
                    'Name' => $data->staffs->name,
                    'Auto-Asign' => '',
                    'Date' => date('Y-m-d', strtotime($data->created_at)),
                    'TimeTable' => '',
                    'On_Duty' => $duty_in,
                    'Off_Duty' => $duty_out,
                    'Clock_in' => $clock_in ? date('H:i:s', strtotime($clock_in)) : '',
                    'Clock_out' => $clock_out ? date('H:i:s', strtotime($clock_out)) : '',
                    'keterangan' => $keterangan,
                    'deskripsi' => $deskripsi,
                    'shift' => $shift,
                ];
            }


            $list_absen = collect($list_absen);

            if ($staff->work_type_id === 2) {
                // untuk shift start
                $shifts = ShiftPlannerStaffs::where('staff_id', $staff->id)
                    ->whereBetween(DB::raw('DATE(shift_planner_staffs.start)'), [$date_from, $date_to])
                    ->get();
                foreach ($list_absen as $data) {
                    $list_absen_excel[] = $data;
                }

                foreach ($shifts as $data) {
                    $cek = $list_absen->where('shift', $data->id)->first();
                    if (!$cek) {
                        $cek_keterangan = $list_absen->where('Date', date('Y-m-d', strtotime($data->start)))->first();
                        if ($cek_keterangan) {
                            $list_absen_excel[] = [
                                'Emp No' => '',
                                'AC-No' => '',
                                'No' => $staff->NIK,
                                'Name' => $staff->name,
                                'Auto-Asign' => '',
                                'Date' => date('Y-m-d', strtotime($data->start)),
                                'TimeTable' => '',
                                'On_Duty' => '',
                                'Off_Duty' => '',
                                'Clock_in' => '',
                                'Clock_out' => '',
                                'keterangan' => 'Alpha',
                                'deskripsi' => $data->id,
                                'shift' => '',
                            ];
                        }
                    }
                }
                // dd($shifts);
                // dd($shifts);
                // dd($list_absen_excel);
                // untuk shift end
            } else {

                $dateRange = CarbonPeriod::create($date_from, $date_to);
                $dates = $dateRange->toArray();

                $i = 0;

                foreach ($dates as $dt) {
                    $day_id = date('w', strtotime($dt->format('Y-m-d'))) == "0" ? '7' : date('w', strtotime($dt->format('Y-m-d')));

                    $list = $list_absen->where('Date', $dt->format('Y-m-d'))->first();

                    if ($list != null) {
                        $cek_masuk = $list_absen->where('Date', $dt->format('Y-m-d'))->where('keterangan', 'Masuk')->first();

                        $cek_lembur = $list_absen->where('Date', $dt->format('Y-m-d'))->where('keterangan', 'Lembur')->first();
                        if ($cek_masuk && $cek_lembur) {
                            $deskripsi = "";
                            if ($cek_lembur['deskripsi'] != "") {
                                $deskripsi = $cek_lembur['deskripsi'];
                            } else {
                                $deskripsi = $cek_masuk['deskripsi'];
                            }
                            $list_absen_excel[]  = [
                                'Emp No' => '',
                                'AC-No' => '',
                                'No' => $cek_masuk['No'],
                                'Name' => $cek_masuk['Name'],
                                'Auto-Asign' => '',
                                'Date' => $cek_masuk['Date'],
                                'TimeTable' => '',
                                'On_Duty' => $cek_masuk['On_Duty'],
                                'Off_Duty' => $cek_masuk['Off_Duty'],
                                'Clock_in' => $cek_masuk['Clock_in'],
                                'Clock_out' => $cek_lembur['Clock_out'],
                                'keterangan' => 'Masuk dan Lembur',
                                'deskripsi' => $deskripsi,
                            ];
                        } else if ($cek_lembur) {
                            // untuk absen lembur
                            $list_absen_excel[]  = $cek_lembur;
                        } else if ($cek_lembur) {
                            // untuk absen masuk
                            $list_absen_excel[] = $cek_masuk;
                        } else {
                            $list_absen_excel[] = $list;
                        }
                    } else if (in_array($day_id, $jadwal_libur)) {
                        $list_absen_excel[] = [
                            'Emp No' => '',
                            'AC-No' => '',
                            'No' => $staff->NIK,
                            'Name' => $staff->name,
                            'Auto-Asign' => '',
                            'Date' => $dt->format('Y-m-d'),
                            'TimeTable' => '',
                            'On_Duty' => '',
                            'Off_Duty' => '',
                            'Clock_in' => '',
                            'Clock_out' => '',
                            'keterangan' => 'Libur',
                            'deskripsi' => '',
                        ];
                    } else {
                        $list_absen_excel[] = [
                            'Emp No' => '',
                            'AC-No' => '',
                            'No' => $staff->NIK,
                            'Name' => $staff->name,
                            'Auto-Asign' => '',
                            'Date' => $dt->format('Y-m-d'),
                            'TimeTable' => '',
                            'On_Duty' => '',
                            'Off_Duty' => '',
                            'Clock_in' => '',
                            'Clock_out' => '',
                            'keterangan' => 'Alpa',
                            'deskripsi' => '',
                        ];
                    }
                }
            }
        }

        // $absence[0]->absence_logs->where('absence_category_id', 2);

        return Excel::download(new AbsenceExport($list_absen_excel), 'report_excel.xlsx');
        dd($i, $list_absen_excel);


        return view('admin.absence.reportExcel');
    }

    public function reportAbsenceView()
    {
        return view('admin.absence.report');
    }


    public function reportAbsence(Request $request)
    {

        try {


            if (!empty(request()->input('from')) || !empty(request()->input('to'))) {
                $awal = date("Y-m-d", strtotime($request->from));
                $akhir   = date("Y-m-d", strtotime($request->to));
            } else {

                $awal = date("Y-m-d", strtotime('-1 month', strtotime(date('Y-m') . '-21')));
                $akhir  = date("Y-m-d", strtotime(date('Y-m') . '-20'));
            }


            $from = $awal;
            $to = $akhir;


            // tanggalnya diubah formatnya ke Y-m-d 
            $awal = date_create_from_format('Y-m-d', $awal);
            $awal = date_format($awal, 'Y-m-d');
            $awal = strtotime($awal);

            $akhir = date_create_from_format('Y-m-d', $akhir);
            $akhir = date_format($akhir, 'Y-m-d');
            $akhir = strtotime($akhir);

            $haricuti = array();
            $sabtuminggu = array();

            for ($i = $awal; $i <= $akhir; $i += (60 * 60 * 24)) {
                if (date('w', $i) !== '0' && date('w', $i) !== '6') {
                    $haricuti[] = $i;
                } else {
                    $sabtuminggu[] = $i;
                }
            }
            $jumlah_cuti = count($haricuti);
            $jumlah_sabtuminggu = count($sabtuminggu);
            $abtotal = $jumlah_cuti + $jumlah_sabtuminggu;


            $hariefective = array();
            $harilibur = array();
            $sabtuminggu = array();
            $tglLibur = array();

            $work_types = WorkTypes::get();

            foreach ($work_types as $work_type) {
                $work_type_days = Day::select('days.*')->leftJoin(
                    'work_type_days',
                    function ($join) use ($work_type) {
                        $join->on('days.id', '=', 'work_type_days.day_id')
                            ->where('work_type_id', $work_type->id);
                    }
                )
                    ->where('work_type_days.day_id', '=', null)->get();
                // dd($work_type_days);
                // dd($work_type_days);
                $jadwallibur = [];
                foreach ($work_type_days as $work_type_day) {
                    $jadwallibur = array_merge($jadwallibur, [$work_type_day->id != "7" ? '' . $work_type_day->id : '0']);
                }

                // libur nasional
                $holidays = Holiday::select(DB::raw('DATE(holidays.start) AS start'), DB::raw('DATE(holidays.end) AS end'))
                    ->whereBetween(DB::raw('DATE(holidays.start)'), [$from, $to])
                    ->orWhereBetween(DB::raw('DATE(holidays.end)'), [$from, $to])
                    ->get();
                // dd($holidays);
                foreach ($holidays as $holiday) {
                    $awal_libur = date_create_from_format('Y-m-d', $holiday->start);
                    $awal_libur = date_format($awal_libur, 'Y-m-d');
                    $awal_libur = strtotime($awal_libur);

                    $akhir_libur = date_create_from_format('Y-m-d', $holiday->end);
                    $akhir_libur = date_format($akhir_libur, 'Y-m-d');
                    $akhir_libur = strtotime($akhir_libur);

                    $work_type_days = Day::select('days.*')->leftJoin(
                        'work_type_days',
                        function ($join) use ($work_type) {
                            $join->on('days.id', '=', 'work_type_days.day_id')
                                ->where('work_type_id', $work_type->id);
                        }
                    )
                        ->where('work_type_days.day_id', '=', null)->get();

                    $jadwallibur = [];
                    foreach ($work_type_days as $work_type_day) {
                        $jadwallibur = array_merge($jadwallibur, [$work_type_day->id != "7" ? '' . $work_type_day->id : '0']);
                    }


                    for ($i = $awal_libur; $i < $akhir_libur; $i += (60 * 60 * 24)) {
                        if (!in_array(date('w', $i), $jadwallibur) &&  !in_array(date("Y-m-d", strtotime(date('Y-m-d', $i))), $tglLibur)) {
                            $harilibur[] = $i;
                            $tglLibur = array_merge($tglLibur, [date("Y-m-d", strtotime(date('Y-m-d', $i)))]);
                        } else {
                        }
                    }
                }

                for ($i = $awal; $i <= $akhir; $i += (60 * 60 * 24)) {

                    if (!in_array(date('w', $i), $jadwallibur) &&  !in_array(date("Y-m-d", strtotime(date('Y-m-d', $i))), $tglLibur)) {
                        $hariefective[] = ['id' => $i, 'date' => date("Y-m-d", strtotime(date('Y-m-d', $i)))];
                    } else {
                        $sabtuminggu[] = $i;
                    }
                }

                $jumlah_efective[] = ['id' => $work_type->id, 'hari_effective' => $hariefective];
                $jadwallibur = [];
                $hariefective = [];
                $tglLibur = [];
            }

            $collection = collect($jumlah_efective);

            $data12 = [];
            $stfs1 = Staff::select('staffs.*',  DB::raw('(CASE WHEN staffs.type = "employee" THEN  SUBSTRING(staffs.NIK, 5) ELSE staffs.NIK END)  AS NIK'), 'work_types.type as work_type')
                ->join('work_types', 'work_types.id', '=', 'staffs.work_type_id')
                ->join('users', 'users.staff_id', '=', 'staffs.id')
                ->groupBy('staffs.id')
                ->orderBy(DB::raw("FIELD(staffs.type , \"employee\", \"contract\" )"))
                ->orderBy('NIK', 'ASC')
                // ->where('staffs.id', '8')
                ->get();

            foreach ($stfs1 as $stf) {
                $report = AbsenceLog::select(
                    DB::raw('(CASE WHEN staffs.type = "employee" THEN  SUBSTRING(staffs.NIK, 5) ELSE staffs.NIK END)  AS NIK'),
                    DB::raw('count(IF(absence_category_id = 1 and register != "" ,1,NULL)) as hadir'),
                    DB::raw('count(IF(absence_category_id = 13 ,1,NULL)) as izin'),
                    DB::raw('count(IF(absence_category_id = 7 ,1,NULL)) as dinas_luar'),
                    DB::raw('count(IF(absence_category_id = 8 ,1,NULL)) as cuti'),
                    DB::raw('count(IF(absence_category_id = 5 ,1,NULL)) as dinas_dalam'),
                    DB::raw('count(IF(absence_category_id = 1 and late != ""  and late != "0" ,1,NULL)) as lambat'),
                    DB::raw('count(IF(absence_category_id = 9 ,1,NULL)) as lembur'),
                    // DB::raw('SUM(CASE WHEN absence_category_id = "1" AND late != "0" THEN 1 ELSE 0 END) as lambat'),
                    DB::raw('count(IF(absence_category_id = 11 ,1,NULL)) as permisi'),
                    // total jam
                    DB::raw('SUM(CASE WHEN absence_category_id = "1" THEN late ELSE "0" END) as jam_lambat'),
                    DB::raw('SUM(CASE WHEN absence_category_id = "2" THEN duration ELSE "0" END) as jam_hadir'),
                    DB::raw('SUM(CASE WHEN absence_category_id = "4" THEN duration ELSE "0" END) as jam_istirahat'),
                    DB::raw('SUM(CASE WHEN absence_category_id = "10" THEN duration ELSE "0" END) as jam_lembur'),
                    DB::raw('SUM(CASE WHEN absence_category_id = "6" THEN duration ELSE "0" END) as jam_dinas_dalam'),
                    DB::raw('SUM(CASE WHEN absence_category_id = "12" THEN duration ELSE "0" END) as jam_permisi'),
                    // DB::raw("SUM(DATEDIFF(hour,duration)  as jam_test"),
                    'staffs.name as staff_name',
                    'staffs.code as staff_code',
                    'jobs.name as job_name',
                    'staffs.work_type_id as work_type_id',
                    'work_types.type as work_type',
                    'staffs.id as staff_id',
                    'subdapertements.name as subdapertement_name'


                )
                    ->join('absences', 'absences.id', '=', 'absence_logs.absence_id')
                    ->join('staffs', 'staffs.id', '=', 'absences.staff_id')
                    ->join('work_types', 'work_types.id', '=', 'staffs.work_type_id')
                    ->leftJoin('subdapertements', 'subdapertements.id', '=', 'staffs.subdapertement_id')
                    ->leftJoin('jobs', 'jobs.id', '=', 'staffs.job_id')
                    // ->groupBy('staffs.id')
                    ->where('staffs.id', $stf->id)
                    ->where('absences.status_active', '')
                    ->FilterDateWeb($request->from, $request->to)
                    ->first();
                if ($report) {
                    $data12 = array_merge($data12, [$report]);
                } else {
                    $data12 = array_merge($data12, []);
                }
            }
            $collectionStf = collect($data12);
            // dd($collectionStf[0]->staff_id);

            // ->get();

            // $day1 = "2023-02-12 12:30:00";
            // $day1 = strtotime($day1);
            // $day2 = "2023-02-12 14:00:00";
            // $day2 = strtotime($day2);

            // $diffHours = ($day2 - $day1) / 3600;
            // dd($report, $jumlah_cuti, $jumlah_sabtuminggu, $abtotal, $diffHours);


            $no = 1;

            // foreach ($report as $value) {
            //     $day = "";
            //     if (date("w", strtotime($value->date)) == 0) {
            //         $day = "Minggu";
            //     } else if (date("w", strtotime($value->date)) == 1) {
            //         $day = "Senin";
            //     } else if (date("w", strtotime($value->date)) == 2) {
            //         $day = "Selasa";
            //     } else if (date("w", strtotime($value->date)) == 3) {
            //         $day = "Rabu";
            //     } else if (date("w", strtotime($value->date)) == 4) {
            //         $day = "Kamis";
            //     } else if (date("w", strtotime($value->date)) == 5) {
            //         $day = "Jumat";
            //     } else if (date("w", strtotime($value->date)) == 6) {
            //         $day = "Sabtu";
            //     }
            //     $data[] = [
            //         'No' => $no++,
            //         'Kode' => $value->staff_code,
            //         'Nama' => $value->staff_name,
            //         'Karyawan' => '',
            //         'Sub_Bagian' => '',
            //         'Job' => $value->job_name,
            //         'Total Hari' => $abtotal,
            //         'Total Libur' => $jumlah_sabtuminggu,
            //         'Total Efektif Kerja' => $abtotal - $jumlah_sabtuminggu,
            //         'Total Hadir' => $value->hadir,
            //         'Total Alfa' => '',
            //         'Total Izin' => $value->izin,
            //         'Total Dinas Luar' => $value->dinas_luar,
            //         'Total Cuti' => $value->cuti,
            //         'Total Jam Kerja' => $value->jam_hadir,
            //         'Total Jam Istirahat' => $value->jam_istirahat,
            //         'Total Jam Lembur' => $value->jam_lembur,
            //         'Total Jam Dinas Dalam' => $value->jam_dinas_dusun,
            //         'Total Hari Dinas Dalam' => $value->dinas_dalam,
            //         'Total Jam Terlambat' => $value->dinas_dalam,
            //         'Total Hari Terlambat' => $value->terlambat,
            //         'Total Jam Permisi' => $value->jam_permisi,
            //         'Total Hari Permisi' => $value->permisi,
            //     ];
            // }

            // dd(count($collection->where('id', 1)->first()['hari_effective']));
            if ($request->ajax()) {
                //set query
                $table = Datatables::of($collectionStf);

                $table->addColumn('placeholder', '&nbsp;');
                $table->addColumn('actions', '&nbsp;');

                $table->editColumn('actions', function ($row) {
                    $viewGate = '';
                    $editGate = '';
                    $deleteGate = '';
                    $crudRoutePart = 'absence';

                    return view('partials.datatablesActions', compact(
                        'viewGate',
                        'editGate',
                        'deleteGate',
                        'crudRoutePart',
                        'row'
                    ));
                });
                $table->editColumn('staff_code', function ($row) {
                    return $row->staff_code ? $row->staff_code : "";
                });

                $table->editColumn('staff_name', function ($row) {
                    return $row->staff_name ? $row->staff_name : "";
                });

                $table->editColumn('dapertement_name', function ($row) {
                    return $row->dapertement_name ? $row->dapertement_name : "";
                });

                $table->editColumn('job_name', function ($row) {
                    return $row->job_name ? $row->job_name : "";
                });

                $table->editColumn('abtotal', function ($row) use ($abtotal) {
                    return $abtotal ? $abtotal : "";
                });

                $table->editColumn('jumlah_libur', function ($row) use ($abtotal, $collection, $from, $to) {
                    // return $jumlah_libur ? $jumlah_libur : "";

                    if ($row->work_type == "reguler") {
                        return ($abtotal - count($collection->where('id', 1)->first()['hari_effective']));
                    } else {
                        return ($abtotal - count(ShiftPlannerStaffs::select('shift_planner_staffs.*', DB::raw('DATE(shift_planner_staffs.start) as start'))->where('staff_id',  $row->staff_id)
                            ->whereBetween(DB::raw('DATE(shift_planner_staffs.start)'), [$from, $to])->get()));
                    }
                });

                $table->editColumn('efective_kerja', function ($row) use ($collection, $from, $to) {
                    if ($row->work_type == "reguler") {
                        return count($collection->where('id', 1)->first()['hari_effective']);
                    } else {
                        return   count(ShiftPlannerStaffs::select('shift_planner_staffs.*', DB::raw('DATE(shift_planner_staffs.start) as start'))->where('staff_id',  $row->staff_id)
                            ->whereBetween(DB::raw('DATE(shift_planner_staffs.start)'), [$from, $to])->get());
                    }
                });

                $table->editColumn('hadir', function ($row) {
                    return $row->hadir ? $row->hadir : "";
                });
                $table->editColumn('alfa', function ($row) use ($collection, $from, $to) {
                    if ($row->work_type == "reguler") {
                        return (count($collection->where('id', 1)->first()['hari_effective']) - ($row->hadir + $row->izin + $row->dinas_luar + $row->cuti));
                    } else {
                        return (count(ShiftPlannerStaffs::select('shift_planner_staffs.*', DB::raw('DATE(shift_planner_staffs.start) as start'))->where('staff_id',  $row->staff_id)
                            ->whereBetween(DB::raw('DATE(shift_planner_staffs.start)'), [$from, $to])->get()) - ($row->hadir + $row->izin + $row->dinas_luar + $row->cuti));
                    }
                });
                $table->editColumn('izin', function ($row) {
                    return $row->izin ? $row->izin : "";
                });
                $table->editColumn('dinas_luar', function ($row) {
                    return $row->dinas_luar ? $row->dinas_luar : "";
                });
                $table->editColumn('cuti', function ($row) {
                    return $row->cuti ? $row->cuti : "";
                });

                $table->editColumn('lembur', function ($row) {
                    return $row->lembur ? $row->lembur : "";
                });
                $table->editColumn('jam_hadir', function ($row) {
                    return $row->jam_hadir ? round($row->jam_hadir, 2) : "0";
                });
                $table->editColumn('jam_istirahat', function ($row) {
                    return $row->jam_istirahat ? round($row->jam_istirahat, 2)  : "0";
                });
                $table->editColumn('dinas_dalam', function ($row) {
                    return $row->dinas_dalam ? $row->dinas_dalam : "";
                });
                $table->editColumn('jam_lembur', function ($row) {
                    return $row->jam_lembur ?  round($row->jam_lembur, 2) : "0";
                });
                $table->editColumn('lambat', function ($row) {
                    return $row->lambat ? $row->lambat : "";
                });
                $table->editColumn('jam_lambat', function ($row) {
                    return $row->jam_lambat ? round($row->jam_lambat, 2) : "0";
                });
                $table->editColumn('cuti', function ($row) {
                    return $row->cuti ? $row->cuti : "";
                });

                $table->editColumn('permisi', function ($row) {
                    return $row->permisi ? $row->permisi : "";
                });
                $table->editColumn('jam_permisi', function ($row) {
                    return $row->jam_permisi ? $row->jam_permisi : "";
                });

                // $table->editColumn('jam_dinas_dalam', function ($row) {
                //     return $row->jam_dinas_dalam ? $row->jam_dinas_dalam : "";
                // });
                // $table->editColumn('hari_dinas_dalam', function ($row) {
                //     return $row->dinas_dalam ? $row->dinas_dalam : "";
                // });

                $table->editColumn('jam_permisi', function ($row) {
                    return $row->jam_permisi ? round($row->jam_permisi, 2)  : "0";
                });
                $table->editColumn('work_type', function ($row) {
                    return $row->work_type ? $row->work_type  : "";
                });


                $table->editColumn('hari_permisi', function ($row) {
                    return $row->permisi ? $row->permisi : "";
                });

                $table->editColumn('NIK', function ($row) {
                    return $row->NIK ? $row->NIK : "";
                });
                $table->rawColumns(['actions', 'placeholder']);

                $table->addIndexColumn();
                return $table->make(true);
            }
            // default view
            // return view('admin.schedule.index');

            return view('admin.absence.report');

            // dd($data);
            // return Excel::download(new AbsenceReport($data), 'report.xlsx');
        } catch (\Throwable $e) {
            // abort(500);
            dd($e);
        }
    }
}
