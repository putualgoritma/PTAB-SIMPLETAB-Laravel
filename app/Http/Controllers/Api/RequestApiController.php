<?php

namespace App\Http\Controllers\Api\V1\Absence;

use App\Absence;
use App\AbsenceLog;
use App\AbsenceRequest;
use App\AbsenceRequestLogs;
use App\Day;
use App\Holiday;
use App\Http\Controllers\Controller;
use App\MessageLog;
use App\Requests;
use App\Requests_file;
use App\ShiftChange;
use App\ShiftGroupTimesheets;
use App\ShiftPlannerStaffs;
use App\Staff;
use App\StaffApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OneSignal;
use App\Traits\WablasTrait;
use App\User;
use App\wa_history;
use App\WorkTypeDays;
use App\WorkUnit;

class RequestApiController extends Controller
{
    use WablasTrait;
    public function index(Request $request)
    {
        $workPermit = Absence::where(base64_decode('dXNlcl9pZA=='), $request->id)->where(base64_decode('cmVnaXN0ZXI='), $request->date)->get();
        $absenOut = Absence::where(base64_decode('dXNlcl9pZA=='), $request->id)->where(base64_decode('YWJzZW5fY2F0ZWdvcnlfaWQ='), $request->absen_category_id)->get();
        $wP = base64_decode('MA==');
        $aO = base64_decode('MA==');
        if (count($workPermit) > 0) {
            $wP = base64_decode('MA==');
        } else {
            $wP = base64_decode('MQ==');
        }
        if (count($absenOut) > 0) {
            $aO = base64_decode('MQ==');
        } else {
            $wP = base64_decode('MA==');
        }
        return response()->json([
            base64_decode('bWVzc2FnZQ==') => base64_decode('UGVuZ2FqdWFuIFRlcmtpcmlt'),
            base64_decode('YWJzZW5PdXQ=') => $aO,
            base64_decode('d29ya1Blcm1pdA==') => $wP,
        ]);
    }

    public function store(Request $request)
    {

        $dataForm = json_decode($request->form);
        if ($dataForm->category == base64_decode('ZXh0cmE=')) {
            $start1 = date(base64_decode('WS1tLWQgSDppOnM='), strtotime($dataForm->start . $dataForm->time));
            $day = date(base64_decode('dw=='), strtotime($dataForm->start));
            $staff = Staff::selectRaw(base64_decode('c3RhZmZzLiosd29ya190eXBlcy50eXBlIGFzIHdvcmtfdHlwZSwgd29ya190eXBlcy5pZCBhcyB3b3JrX3R5cGVfaWQg'))->join(base64_decode('d29ya190eXBlcw=='), base64_decode('d29ya190eXBlcy5pZA=='), base64_decode('PQ=='), base64_decode('c3RhZmZzLndvcmtfdHlwZV9pZA=='))
                ->where(base64_decode('c3RhZmZzLmlk'), $dataForm->staff_id)->first();
            if ($staff->work_type == base64_decode('c2hpZnQ=')) {
                $jumShift = ShiftPlannerStaffs::whereDate(base64_decode('c2hpZnRfcGxhbm5lcl9zdGFmZnMuc3RhcnQ='), base64_decode('PQ=='), $dataForm->start)
                    ->where(base64_decode('c3RhZmZfaWQ='), $staff->id)
                    ->get();
                foreach ($jumShift as $data) {
                    $absence = ShiftPlannerStaffs::join(base64_decode('c2hpZnRfZ3JvdXBz'), base64_decode('c2hpZnRfZ3JvdXBzLmlk'), base64_decode('PQ=='), base64_decode('c2hpZnRfcGxhbm5lcl9zdGFmZnMuc2hpZnRfZ3JvdXBfaWQ='))
                        ->join(base64_decode('c2hpZnRfZ3JvdXBfdGltZXNoZWV0cw=='), base64_decode('c2hpZnRfZ3JvdXBzLmlk'), base64_decode('PQ=='), base64_decode('c2hpZnRfZ3JvdXBfdGltZXNoZWV0cy5zaGlmdF9ncm91cF9pZA=='))
                        ->where(base64_decode('c2hpZnRfZ3JvdXBfdGltZXNoZWV0cy5hYnNlbmNlX2NhdGVnb3J5X2lk'), base64_decode('MQ=='))
                        ->where(base64_decode('c2hpZnRfZ3JvdXBzLmlk'), $data->shift_group_id)
                        ->where(base64_decode('c3RhZmZfaWQ='), $staff->id)
                        ->whereDate(base64_decode('c2hpZnRfcGxhbm5lcl9zdGFmZnMuc3RhcnQ='), base64_decode('PQ=='), $dataForm->start)
                        ->orWhere(base64_decode('c2hpZnRfZ3JvdXBfdGltZXNoZWV0cy5hYnNlbmNlX2NhdGVnb3J5X2lk'), base64_decode('Mg=='))
                        ->where(base64_decode('c2hpZnRfZ3JvdXBzLmlk'), $data->shift_group_id)
                        ->where(base64_decode('c3RhZmZfaWQ='), $staff->id)
                        ->whereDate(base64_decode('c2hpZnRfcGxhbm5lcl9zdGFmZnMuc3RhcnQ='), base64_decode('PQ=='), $dataForm->start)
                        ->orderBy(base64_decode('c2hpZnRfZ3JvdXBfdGltZXNoZWV0cy5hYnNlbmNlX2NhdGVnb3J5X2lk'), base64_decode('QVND'))
                        ->get();

                    if ($absence[0]->time > $absence[1]->time) {
                        $masuk = date(base64_decode('WS1tLWQgSDppOnM='), strtotime($dataForm->start . $absence[0]->time));
                        $pulang = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('KyA=') . base64_decode('MQ==') . base64_decode('IGRheXM='), strtotime($dataForm->start . $absence[1]->time)));
                    } else {
                        $masuk = date(base64_decode('WS1tLWQgSDppOnM='), strtotime($dataForm->start . $absence[0]->time));
                        $pulang = date(base64_decode('WS1tLWQgSDppOnM='), strtotime($dataForm->start . $absence[1]->time));
                    }


                    if ($start1 > $masuk && $start1 < $pulang) {
                        return response()->json(
                            [

                                base64_decode('bWVzc2FnZQ==') => base64_decode('YW5kYSB0aWRhayBiaXNhIG1lbGFrdWthbiBsZW1idXIgZGkgamFtIGtlcmph')
                            ]
                        );
                    }
                }
            } else {
                $absence = WorkTypeDays::selectRaw(base64_decode('dGltZQ=='))
                    ->where(base64_decode('d29ya190eXBlX2lk'), $staff->work_type_id)
                    ->where(base64_decode('ZGF5X2lk'), $day != base64_decode('MA==') ? $day : base64_decode('Nw=='))
                    ->where(base64_decode('d29ya190eXBlX2RheXMuYWJzZW5jZV9jYXRlZ29yeV9pZA=='), base64_decode('Mg=='))
                    ->orWhere(base64_decode('d29ya190eXBlX2RheXMuYWJzZW5jZV9jYXRlZ29yeV9pZA=='), base64_decode('MQ=='))
                    ->where(base64_decode('d29ya190eXBlX2lk'), $staff->work_type_id)
                    ->where(base64_decode('ZGF5X2lk'), $day != base64_decode('MA==') ? $day : base64_decode('Nw=='))
                    ->orderBy(base64_decode('d29ya190eXBlX2RheXMuYWJzZW5jZV9jYXRlZ29yeV9pZA=='), base64_decode('QVND'))
                    ->get();
                $holiday = Holiday::whereDate(base64_decode('c3RhcnQ='), base64_decode('PQ=='), date(base64_decode('WS1tLWQ='), strtotime($dataForm->start)))->first();
                if (!$holiday) {
                    if (count($absence) > 0) {
                        $masuk = date(base64_decode('WS1tLWQgSDppOnM='), strtotime($dataForm->start . $absence[0]->time));
                        $pulang = date(base64_decode('WS1tLWQgSDppOnM='), strtotime($dataForm->start . $absence[1]->time));
                        if ($start1 > $masuk && $start1 < $pulang) {
                            return response()->json(
                                [
                                    base64_decode('bWVzc2FnZQ==') => base64_decode('YW5kYSB0aWRhayBiaXNhIG1lbGFrdWthbiBsZW1idXIgZGkgamFtIGtlcmph')
                                ]
                            );
                        }
                    }
                }
            }
        } else {
        }

        $start = '';
        $end = '';
        $error = '';
        $cek = null;
        if ($dataForm->start == '') {
            $start = date(base64_decode('WS1tLWQgSDppOnM='));
            $startS = date(base64_decode('WS1tLWQgSDppOnM='));
        } else if ($dataForm->start != '' && $dataForm->time == '') {
            $start = date(base64_decode('WS1tLWQgSDppOnM='), strtotime($dataForm->start));
            $startS = date(base64_decode('WS1tLWQgSDppOnM='), strtotime($dataForm->start));
        } else {
            $start = date(base64_decode('WS1tLWQgSDppOnM='), strtotime($dataForm->start . $dataForm->time));
            $startS = date(base64_decode('WS1tLWQgSDppOnM='), strtotime($dataForm->start . $dataForm->time));
        }

        if ($dataForm->end == '') {
            $end = date(base64_decode('WS1tLWQgSDppOnM='), strtotime($dataForm->start . base64_decode('MjM6NTk6NTk=')));
            $endS = date(base64_decode('WS1tLWQgSDppOnM='), strtotime($dataForm->start . base64_decode('MjM6NTk6NTk=')));
        } else {
            $end = date(base64_decode('WS1tLWQgSDppOnM='), strtotime($dataForm->end . base64_decode('MjM6NTk6NTk=')));
            $endS = date(base64_decode('WS1tLWQgSDppOnM='), strtotime($dataForm->end . base64_decode('MjM6NTk6NTk=')));
        }
        if ($start < date(base64_decode('WS1tLWQ=')) || $start > $end) {
            $cek = base64_decode('cGFzcw==');
            $error = base64_decode('VGFuZ2dhbCBrdXJhbmcgZGFyaSBoYXJpIGluaQ==');
        } else if ($dataForm->category == base64_decode('bGVhdmU=') || $dataForm->category == base64_decode('cGVybWlzc2lvbg==')) {
            $start =  date(base64_decode('WS1tLWQ='), strtotime($start));
            $end =  date(base64_decode('WS1tLWQ='), strtotime($end));
            $absen_check = Absence::where(base64_decode('c3RhZmZfaWQ='), $dataForm->staff_id)
                ->leftJoin(base64_decode('YWJzZW5jZV9sb2dz'), base64_decode('YWJzZW5jZXMuaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='))
                ->whereBetween(DB::raw(base64_decode('REFURShhYnNlbmNlcy5jcmVhdGVkX2F0KQ==')), [$start, $end])
                ->where(base64_decode('cmVnaXN0ZXI='), base64_decode('IT0='), null)
                ->first();
            $cek = AbsenceRequest::where(base64_decode('c3RhZmZfaWQ='), $dataForm->staff_id)
                ->where(function ($query) use ($start, $end) {
                    $query->where(base64_decode('Y2F0ZWdvcnk='), base64_decode('dmlzaXQ='))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('dmlzaXQ='))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('bGVhdmU='))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('cGVybWlzc2lvbg=='))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('ZXh0cmE='))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('Z2VvbG9jYXRpb25fb2Zm'))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('ZXhjdXNl'));
                })
                ->where(function ($query)  use ($start, $end) {
                    $query->whereBetween(DB::raw(base64_decode('REFURShhYnNlbmNlX3JlcXVlc3RzLnN0YXJ0KQ==')), [$start, $end])
                        ->where(function ($query)  use ($start, $end) {
                            $query->where(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YWN0aXZl'))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('cGVuZGluZw=='))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YXBwcm92ZQ=='));
                        })
                        ->orWhereBetween(DB::raw(base64_decode('REFURShhYnNlbmNlX3JlcXVlc3RzLmVuZCk=')), [$start, $end])
                        ->where(function ($query)  use ($start, $end) {
                            $query->where(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YWN0aXZl'))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('cGVuZGluZw=='))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YXBwcm92ZQ=='));
                        });
                })

                ->first();


            if ($cek) {
                $error = base64_decode('QW5kYSBNYXNpaCBNZW1pbGlraSBDdXRpL0RpbmFzL0l6aW4geWFuZyBtYXNpaCBha3RpZiBkaSB0YW5nZ2FsIGluaQ==');
            } else if ($absen_check) {
                $error = base64_decode('UGVuZ2FqdWFuIHRpZGFrIGJpc2EgZGlsYWt1a2FuIGppa2EgYW5kYSBtYXN1ayBkaWhhcmkgdGVyc2VidXQ=');
            }
        } else if ($dataForm->category == base64_decode('ZHV0eQ==') || $dataForm->category == base64_decode('dmlzaXQ=') || $dataForm->category == base64_decode('bGVhdmU=') || $dataForm->category == base64_decode('cGVybWlzc2lvbg==')) {
            $start =  date(base64_decode('WS1tLWQ='), strtotime($start));
            $end =  date(base64_decode('WS1tLWQ='), strtotime($end));

            if ($dataForm->category == base64_decode('dmlzaXQ=')) {
                $startS = date(base64_decode('WS1tLWQgSDppOnM='), strtotime($dataForm->start . $dataForm->time));
                $request_date = $start;
                $day_id = date(base64_decode('dw=='), strtotime($request_date)) == base64_decode('MA==') ? base64_decode('Nw==') : date(base64_decode('dw=='), strtotime($request_date));
                $message_err = base64_decode('YW5kYSBoYW55YSBiaXNhIG1lbmdhanVrYW4gZGkgamFtIGtlcmph');
                $staff = Staff::where(base64_decode('aWQ='), $dataForm->staff_id)->first();
                $absen_now = Absence::join(base64_decode('YWJzZW5jZV9sb2dz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZXMuaWQ='))
                    ->where(base64_decode('c3RhZmZfaWQ='), $dataForm->staff_id)
                    ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA=='), 2)
                    ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cw=='), 1)
                    ->whereDate(base64_decode('YWJzZW5jZXMuY3JlYXRlZF9hdA=='), date(base64_decode('WS1tLWQ=')))
                    ->first();



                if (date(base64_decode('WS1tLWQ=')) == $request_date) {
                    $schedule = null;
                    if (!$absen_now) {
                        $error = base64_decode('YW5kYSBoYW55YSBiaXNhIG1lbmdhanVrYW4gZGkgamFtIGtlcmph');
                    } else {

                        if ($staff->work_type_id === 1) {
                            $schedule = WorkTypeDays::where(base64_decode('ZGF5X2lk'), $day_id)->where(base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA=='), 1)->first();
                        } else {
                            $shift_staff = ShiftPlannerStaffs::whereDate(base64_decode('c3RhcnQ='), base64_decode('PQ=='), $request_date)
                                ->where(base64_decode('c3RhZmZfaWQ='), $staff->id)->first();
                            if ($shift_staff) {
                                $schedule = ShiftGroupTimesheets::where(base64_decode('aWQ='), $shift_staff->shift_group_id)
                                    ->where(base64_decode('c3RhcnQ='), $request_date)
                                    ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA=='), 1)->first();
                            } else {
                                $error = base64_decode('YW5kYSBoYW55YSBiaXNhIG1lbmdhanVrYW4gZGkgamFtIGtlcmph');
                            }
                        }
                        if ($schedule) {
                            $time_end = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('Kw==') . $schedule->duration . base64_decode('IGhvdXJz'), strtotime(date(base64_decode('WS1tLWQg') . $schedule->time))));
                            $time_start = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('Kw==') . 0 . base64_decode('IGhvdXJz'), strtotime(date(base64_decode('WS1tLWQg') . $schedule->time))));
                            if ($time_start <  $startS &&  $startS < $time_end) {
                            } else {
                                $error = base64_decode('YW5kYSBoYW55YSBiaXNhIG1lbmdhanVrYW4gZGkgamFtIGtlcmph');
                            }
                        } else {
                            $error = base64_decode('YW5kYSBoYW55YSBiaXNhIG1lbmdhanVrYW4gZGkgamFtIGtlcmph');
                        }
                    }
                } else {

                    if ($staff->work_type_id === 1) {
                        $schedule = WorkTypeDays::where(base64_decode('ZGF5X2lk'), $day_id)->where(base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA=='), 1)->first();
                    } else {
                        $shift_staff = ShiftPlannerStaffs::where(base64_decode('c3RhZmZfaWQ='), $staff->id)->first();
                        if ($shift_staff) {
                            $schedule = ShiftGroupTimesheets::where(base64_decode('aWQ='), $shift_staff->id)
                                ->where(base64_decode('c3RhcnQ='), $request_date)
                                ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA=='), 1)
                                ->first();
                        } else {
                            $error = base64_decode('YW5kYSBoYW55YSBiaXNhIG1lbmdhanVrYW4gZGkgamFtIGtlcmph');
                        }
                    }
                    if ($schedule) {
                        $time_end = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('Kw==') . $schedule->duration . base64_decode('IGhvdXJz'), strtotime(date($request_date . base64_decode('IA==') . $schedule->time))));
                        $time_start = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('Kw==') . 0 . base64_decode('IGhvdXJz'), strtotime(date($request_date . base64_decode('IA==') . $schedule->time))));
                        if ($time_start <  $startS &&  $startS < $time_end) {
                        } else {
                            $error = base64_decode('YW5kYSBoYW55YSBiaXNhIG1lbmdhanVrYW4gZGkgamFtIGtlcmph');
                        }
                    } else {
                        $error = base64_decode('YW5kYSBoYW55YSBiaXNhIG1lbmdhanVrYW4gZGkgamFtIGtlcmph');
                    }
                }



                $cekAbsenceInAlready = Absence::join(base64_decode('YWJzZW5jZV9sb2dz'), base64_decode('YWJzZW5jZXMuaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='))
                    ->whereDate(base64_decode('YWJzZW5jZXMuY3JlYXRlZF9hdA=='), base64_decode('PQ=='), date(base64_decode('WS1tLWQ=')))
                    ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA=='), 1)
                    ->where(base64_decode('c3RhdHVz'), 0)
                    ->where(base64_decode('c3RhZmZfaWQ='), $staff->id)->first();
                if ($cekAbsenceInAlready) {
                    $cekAbsenceOutAlready = Absence::join(base64_decode('YWJzZW5jZV9sb2dz'), base64_decode('YWJzZW5jZXMuaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='))
                        ->whereDate(base64_decode('YWJzZW5jZXMuY3JlYXRlZF9hdA=='), base64_decode('PQ=='), date(base64_decode('WS1tLWQ=')))
                        ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA=='), 2)
                        ->where(base64_decode('c3RhdHVz'), 1)
                        ->where(base64_decode('c3RhZmZfaWQ='), $staff->id)->first();
                    if ($cekAbsenceOutAlready) {
                        if ($cekAbsenceOutAlready->timeout < date(base64_decode('WS1tLWQ='))) {
                            $error = '';
                        }
                    }
                }
            }



            $cek = AbsenceRequest::where(base64_decode('c3RhZmZfaWQ='), $dataForm->staff_id)
                ->where(function ($query) use ($start, $end) {
                    $query->where(base64_decode('Y2F0ZWdvcnk='), base64_decode('dmlzaXQ='))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('dmlzaXQ='))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('bGVhdmU='))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('cGVybWlzc2lvbg=='))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('ZXh0cmE='))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('ZXhjdXNl'));
                })
                ->where(function ($query)  use ($start, $end) {
                    $query->whereBetween(DB::raw(base64_decode('REFURShhYnNlbmNlX3JlcXVlc3RzLnN0YXJ0KQ==')), [$start, $end])
                        ->where(function ($query)  use ($start, $end) {
                            $query->where(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YWN0aXZl'))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('cGVuZGluZw=='))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YXBwcm92ZQ=='));
                        })
                        ->orWhereBetween(DB::raw(base64_decode('REFURShhYnNlbmNlX3JlcXVlc3RzLmVuZCk=')), [$start, $end])
                        ->where(function ($query)  use ($start, $end) {
                            $query->where(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YWN0aXZl'))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('cGVuZGluZw=='))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YXBwcm92ZQ=='));
                        });
                })

                ->first();


            if ($cek) {
                $error = base64_decode('QW5kYSBNYXNpaCBNZW1pbGlraSBDdXRpL0RpbmFzL0l6aW4geWFuZyBtYXNpaCBha3RpZiBkaSB0YW5nZ2FsIGluaQ==');
            }
        } else if ($dataForm->category == base64_decode('ZXhjdXNl')) {
            $start =  date(base64_decode('WS1tLWQ='), strtotime($start));
            $end =  date(base64_decode('WS1tLWQ='), strtotime($end));
            $startS = date(base64_decode('WS1tLWQgSDppOnM='), strtotime($dataForm->start . $dataForm->time));

            $request_date = $start;
            $day_id = date(base64_decode('dw=='), strtotime($request_date)) == base64_decode('MA==') ? base64_decode('Nw==') : date(base64_decode('dw=='), strtotime($request_date));
            $message_err = base64_decode('YW5kYSBoYW55YSBiaXNhIG1lbmdhanVrYW4gZGkgamFtIGtlcmph');
            $staff = Staff::where(base64_decode('aWQ='), $dataForm->staff_id)->first();
            $absen_now = Absence::join(base64_decode('YWJzZW5jZV9sb2dz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZXMuaWQ='))
                ->where(base64_decode('c3RhZmZfaWQ='), $dataForm->staff_id)
                ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA=='), 2)
                ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cw=='), 1)
                ->whereDate(base64_decode('YWJzZW5jZXMuY3JlYXRlZF9hdA=='), date(base64_decode('WS1tLWQ=')))
                ->first();



            if (date(base64_decode('WS1tLWQ=')) == $request_date) {
                $schedule = null;
                if (!$absen_now) {
                    $error = base64_decode('YW5kYSBoYW55YSBiaXNhIG1lbmdhanVrYW4gZGkgamFtIGtlcmph');
                } else {

                    if ($staff->work_type_id === 1) {
                        $schedule = WorkTypeDays::where(base64_decode('ZGF5X2lk'), $day_id)->where(base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA=='), 1)->first();
                    } else {
                        $shift_staff = ShiftPlannerStaffs::whereDate(base64_decode('c3RhcnQ='), base64_decode('PQ=='), $request_date)
                            ->where(base64_decode('c3RhZmZfaWQ='), $staff->id)->first();
                        if ($shift_staff) {
                            $schedule = ShiftGroupTimesheets::where(base64_decode('aWQ='), $shift_staff->shift_group_id)
                                ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA=='), 1)
                                ->first();
                        } else {
                            $error = base64_decode('YW5kYSBoYW55YSBiaXNhIG1lbmdhanVrYW4gZGkgamFtIGtlcmph');
                        }
                    }
                    if ($schedule) {
                        $time_end = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('Kw==') . $schedule->duration . base64_decode('IGhvdXJz'), strtotime(date(base64_decode('WS1tLWQg') . $schedule->time))));
                        $time_start = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('Kw==') . 0 . base64_decode('IGhvdXJz'), strtotime(date(base64_decode('WS1tLWQg') . $schedule->time))));
                        if ($time_start <  $startS &&  $startS < $time_end) {
                        } else {
                            $error = base64_decode('YW5kYSBoYW55YSBiaXNhIG1lbmdhanVrYW4gZGkgamFtIGtlcmph');
                        }
                    } else {
                        $error = base64_decode('YW5kYSBoYW55YSBiaXNhIG1lbmdhanVrYW4gZGkgamFtIGtlcmph');
                    }
                }
            } else {

                if ($staff->work_type_id === 1) {
                    $schedule = WorkTypeDays::where(base64_decode('ZGF5X2lk'), $day_id)->where(base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA=='), 1)->first();
                } else {
                    $shift_staff = ShiftPlannerStaffs::where(base64_decode('c3RhZmZfaWQ='), $staff->id)->first();
                    if ($shift_staff) {
                        $schedule = ShiftGroupTimesheets::whereDate(base64_decode('c3RhcnQ='), $request_date)
                            ->where(base64_decode('aWQ='), $shift_staff->id)
                            ->whereDate(base64_decode('c3RhcnQ='), base64_decode('PQ=='), $request_date)
                            ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA=='), 1)->first();
                    } else {
                        $error = base64_decode('YW5kYSBoYW55YSBiaXNhIG1lbmdhanVrYW4gZGkgamFtIGtlcmph');
                    }
                }
                if ($schedule) {
                    $time_end = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('Kw==') . $schedule->duration . base64_decode('IGhvdXJz'), strtotime(date($request_date . base64_decode('IA==') . $schedule->time))));
                    $time_start = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('Kw==') . 0 . base64_decode('IGhvdXJz'), strtotime(date($request_date . base64_decode('IA==') . $schedule->time))));
                    if ($time_start <  $startS &&  $startS < $time_end) {
                    } else {
                        $error = base64_decode('YW5kYSBoYW55YSBiaXNhIG1lbmdhanVrYW4gZGkgamFtIGtlcmph');
                    }
                } else {
                    $error = base64_decode('YW5kYSBoYW55YSBiaXNhIG1lbmdhanVrYW4gZGkgamFtIGtlcmph');
                }
            }

            $cekAbsenceInAlready = Absence::join(base64_decode('YWJzZW5jZV9sb2dz'), base64_decode('YWJzZW5jZXMuaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='))
                ->whereDate(base64_decode('YWJzZW5jZXMuY3JlYXRlZF9hdA=='), base64_decode('PQ=='), date(base64_decode('WS1tLWQ=')))
                ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA=='), 1)
                ->where(base64_decode('c3RhdHVz'), 0)
                ->where(base64_decode('c3RhZmZfaWQ='), $staff->id)->first();
            if ($cekAbsenceInAlready) {
                $cekAbsenceOutAlready = Absence::join(base64_decode('YWJzZW5jZV9sb2dz'), base64_decode('YWJzZW5jZXMuaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='))
                    ->whereDate(base64_decode('YWJzZW5jZXMuY3JlYXRlZF9hdA=='), base64_decode('PQ=='), date(base64_decode('WS1tLWQ=')))
                    ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA=='), 2)
                    ->where(base64_decode('c3RhdHVz'), 1)
                    ->where(base64_decode('c3RhZmZfaWQ='), $staff->id)->first();
                if ($cekAbsenceOutAlready) {
                    if ($cekAbsenceOutAlready->timeout < date(base64_decode('WS1tLWQ='))) {
                        $error = '';
                    }
                }
            }




            $cek = AbsenceRequest::where(base64_decode('c3RhZmZfaWQ='), $dataForm->staff_id)
                ->where(function ($query) {
                    $query->where(base64_decode('Y2F0ZWdvcnk='), base64_decode('ZXhjdXNl'))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('ZHV0eQ=='))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('bGVhdmU='))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('cGVybWlzc2lvbg=='));
                })
                ->where(function ($query)  use ($start, $end) {
                    $query->whereBetween(DB::raw(base64_decode('REFURShhYnNlbmNlX3JlcXVlc3RzLnN0YXJ0KQ==')), [$start, $end])
                        ->where(function ($query)  use ($start, $end) {
                            $query->where(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YWN0aXZl'))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('cGVuZGluZw=='))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YXBwcm92ZQ=='));
                        })
                        ->orWhereBetween(DB::raw(base64_decode('REFURShhYnNlbmNlX3JlcXVlc3RzLmVuZCk=')), [$start, $end])
                        ->where(function ($query)  use ($start, $end) {
                            $query->where(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YWN0aXZl'))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('cGVuZGluZw=='))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YXBwcm92ZQ=='));
                        });
                })

                ->first();


            if ($cek) {
                $error = base64_decode('QW5kYSBNYXNpaCBNZW1pbGlraSBQZXJtaXNpIGRpIHRhbmdnYWwgaW5p');
            }
        } else if ($dataForm->category == base64_decode('ZXh0cmE=')) {
            $start =  date(base64_decode('WS1tLWQ='), strtotime($start));
            $end =  date(base64_decode('WS1tLWQ='), strtotime($end));
            $cek = AbsenceRequest::where(base64_decode('c3RhZmZfaWQ='), $dataForm->staff_id)
                ->where(function ($query) {
                    $query->where(base64_decode('Y2F0ZWdvcnk='), base64_decode('ZXh0cmE='))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('ZHV0eQ=='))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('bGVhdmU='))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('cGVybWlzc2lvbg=='));
                })
                ->where(function ($query)  use ($start, $end) {
                    $query->whereBetween(DB::raw(base64_decode('REFURShhYnNlbmNlX3JlcXVlc3RzLnN0YXJ0KQ==')), [$start, $end])
                        ->where(function ($query)  use ($start, $end) {
                            $query->where(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YWN0aXZl'))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('cGVuZGluZw=='))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YXBwcm92ZQ=='));
                        })
                        ->orWhereBetween(DB::raw(base64_decode('REFURShhYnNlbmNlX3JlcXVlc3RzLmVuZCk=')), [$start, $end])
                        ->where(function ($query)  use ($start, $end) {
                            $query->where(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YWN0aXZl'))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('cGVuZGluZw=='))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YXBwcm92ZQ=='));
                        });
                })

                ->first();
            if ($cek) {
                $error = base64_decode('QW5kYSBNYXNpaCBNZW1pbGlraSBMZW1idXIgZGkgdGFuZ2dhbCBpbmk=');
            }
        } else if ($dataForm->category == base64_decode('Z2VvbG9jYXRpb25fb2Zm')) {
            $start =  date(base64_decode('WS1tLWQ='), strtotime($start));
            $end =  date(base64_decode('WS1tLWQ='), strtotime($end));
            $cek = AbsenceRequest::where(base64_decode('c3RhZmZfaWQ='), $dataForm->staff_id)
                ->where(function ($query) {
                    $query->where(base64_decode('Y2F0ZWdvcnk='), base64_decode('Z2VvbG9jYXRpb25fb2Zm'))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('ZHV0eQ=='))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('bGVhdmU='))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('cGVybWlzc2lvbg=='))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('ZXhjdXNl'));
                })
                ->where(function ($query)  use ($start, $end) {
                    $query->whereBetween(DB::raw(base64_decode('REFURShhYnNlbmNlX3JlcXVlc3RzLnN0YXJ0KQ==')), [$start, $end])
                        ->where(function ($query)  use ($start, $end) {
                            $query->where(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YWN0aXZl'))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('cGVuZGluZw=='))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YXBwcm92ZQ=='));
                        })
                        ->orWhereBetween(DB::raw(base64_decode('REFURShhYnNlbmNlX3JlcXVlc3RzLmVuZCk=')), [$start, $end])
                        ->where(function ($query)  use ($start, $end) {
                            $query->where(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YWN0aXZl'))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('cGVuZGluZw=='))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YXBwcm92ZQ=='));
                        });
                })

                ->first();
            if ($cek) {
                $error = base64_decode('QW5kYSBNYXNpaCBNZW1pbGlraSBQZXJtb2hvbmFuIEFic2VuIEx1YXIgZGkgdGFuZ2dhbCBpbmk=');
            }
        } else if ($dataForm->category == base64_decode('Zm9yZ2V0')) {
            $start =  date(base64_decode('WS1tLWQ='), strtotime($start));
            $end =  date(base64_decode('WS1tLWQ='), strtotime($end));
            $startS = date(base64_decode('WS1tLWQgSDppOnM='));

            $cek = AbsenceRequest::where(base64_decode('c3RhZmZfaWQ='), $dataForm->staff_id)
                ->where(function ($query) {
                    $query->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('ZHV0eQ=='))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('bGVhdmU='))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('cGVybWlzc2lvbg=='))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('Zm9yZ2V0'))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('YWRkaXRpb25hbFRpbWU='));
                })
                ->where(function ($query)  use ($start, $end) {
                    $query->whereBetween(DB::raw(base64_decode('REFURShhYnNlbmNlX3JlcXVlc3RzLnN0YXJ0KQ==')), [$start, $end])
                        ->where(function ($query)  use ($start, $end) {
                            $query->where(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YWN0aXZl'))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('cGVuZGluZw=='))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YXBwcm92ZQ=='));
                        })
                        ->orWhereBetween(DB::raw(base64_decode('REFURShhYnNlbmNlX3JlcXVlc3RzLmVuZCk=')), [$start, $end])
                        ->where(function ($query)  use ($start, $end) {
                            $query->where(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YWN0aXZl'))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('cGVuZGluZw=='))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YXBwcm92ZQ=='));
                        });
                })

                ->first();
            if ($cek) {
                $error = base64_decode('QW5kYSBTdWRhaCBNZW1pbGlraSBQZW5nYWp1YW4gZGkgdGFuZ2dhbCBpbmk=');
            }
            $request_date = $start;
            $day_id = date(base64_decode('dw=='), strtotime($request_date)) == base64_decode('MA==') ? base64_decode('Nw==') : date(base64_decode('dw=='), strtotime($request_date));
            $message_err = base64_decode('YW5kYSBoYW55YSBiaXNhIG1lbmdhanVrYW4gZGkgamFtIGtlcmph');
            $staff = Staff::where(base64_decode('aWQ='), $dataForm->staff_id)->first();
            $absen_now = Absence::join(base64_decode('YWJzZW5jZV9sb2dz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZXMuaWQ='))
                ->where(base64_decode('c3RhZmZfaWQ='), $dataForm->staff_id)
                ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA=='), 2)
                ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cw=='), 1)
                ->whereDate(base64_decode('YWJzZW5jZXMuY3JlYXRlZF9hdA=='), date(base64_decode('WS1tLWQ=')))
                ->first();



            if (date(base64_decode('WS1tLWQ=')) == $request_date) {
                $schedule = null;
                if (!$absen_now) {
                    $error = base64_decode('YW5kYSBoYW55YSBiaXNhIG1lbmdhanVrYW4gZGkgamFtIGtlcmph');
                } else {

                    if ($staff->work_type_id === 1) {
                        $schedule = WorkTypeDays::where(base64_decode('ZGF5X2lk'), $day_id)->where(base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA=='), 1)->first();
                    } else {
                        $shift_staff = ShiftPlannerStaffs::where(base64_decode('c3RhZmZfaWQ='), $staff->id)->first();
                        if ($shift_staff) {
                            $schedule = ShiftGroupTimesheets::where(base64_decode('aWQ='), $shift_staff->id)
                                ->where(base64_decode('c3RhcnQ='), $request_date)
                                ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA=='), 1)->first();
                        } else {
                            $error = base64_decode('YW5kYSBoYW55YSBiaXNhIG1lbmdhanVrYW4gZGkgaGFyaSBhbmRhIGJla2VyamE=');
                        }
                    }
                    if ($schedule) {
                        $time_end = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('Kw==') . $schedule->duration . base64_decode('IGhvdXJz'), strtotime(date(base64_decode('WS1tLWQg') . $schedule->time))));
                        $time_start = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('Kw==') . 0 . base64_decode('IGhvdXJz'), strtotime(date(base64_decode('WS1tLWQg') . $schedule->time))));
                        if ($time_start <  $startS &&  $startS < $time_end) {
                            $error = base64_decode('YW5kYSBoYW55YSBiaXNhIG1lbmdhanVrYW4ga2V0aWthIGxld2F0IGphbSBrZXJqYQ==');
                        } else {
                        }
                    } else {
                        $error = base64_decode('YW5kYSBoYW55YSBiaXNhIG1lbmdhanVrYW4gZGkgaGFyaSBhbmRhIGJla2VyamE=');
                    }
                }
            } else {

                if ($staff->work_type_id === 1) {
                    $schedule = WorkTypeDays::where(base64_decode('ZGF5X2lk'), $day_id)->where(base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA=='), 1)->first();
                } else {
                    $shift_staff = ShiftPlannerStaffs::where(base64_decode('c3RhZmZfaWQ='), $staff->id)->first();
                    if ($shift_staff) {
                        $schedule = ShiftGroupTimesheets::where(base64_decode('aWQ='), $shift_staff->id)
                            ->where(base64_decode('c3RhcnQ='), $request_date)
                            ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA=='), 1)->first();
                    } else {
                        $error = base64_decode('YW5kYSBoYW55YSBiaXNhIG1lbmdhanVrYW4gZGkgaGFyaSBhbmRhIGJla2VyamE=');
                    }
                }
                if ($schedule) {
                    $time_end = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('Kw==') . $schedule->duration . base64_decode('IGhvdXJz'), strtotime(date($request_date . base64_decode('IA==') . $schedule->time))));
                    $time_start = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('Kw==') . 0 . base64_decode('IGhvdXJz'), strtotime(date($request_date . base64_decode('IA==') . $schedule->time))));
                    if ($time_start <  $startS &&  $startS < $time_end) {
                        $error = base64_decode('YW5kYSBoYW55YSBiaXNhIG1lbmdhanVrYW4ga2V0aWthIGxld2F0IGphbSBrZXJqYQ==');
                    } else {
                    }
                } else {
                    $error = base64_decode('YW5kYSBoYW55YSBiaXNhIG1lbmdhanVrYW4gZGkgaGFyaSBhbmRhIGJla2VyamE=');
                }
            }
        } else if ($dataForm->category == base64_decode('QWRkaXRpb25hbFRpbWU=')) {
            $start =  date(base64_decode('WS1tLWQ='), strtotime($start));
            $end =  date(base64_decode('WS1tLWQ='), strtotime($end));
            $startS = date(base64_decode('WS1tLWQgSDppOnM='));

            $cek = AbsenceRequest::where(base64_decode('c3RhZmZfaWQ='), $dataForm->staff_id)
                ->where(function ($query) {
                    $query->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('ZHV0eQ=='))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('bGVhdmU='))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('cGVybWlzc2lvbg=='))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('QWRkaXRpb25hbFRpbWU='))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('Zm9yZ2V0'));
                })
                ->where(function ($query)  use ($start, $end) {
                    $query->whereBetween(DB::raw(base64_decode('REFURShhYnNlbmNlX3JlcXVlc3RzLnN0YXJ0KQ==')), [$start, $end])
                        ->where(function ($query)  use ($start, $end) {
                            $query->where(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YWN0aXZl'))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('cGVuZGluZw=='))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YXBwcm92ZQ=='));
                        })
                        ->orWhereBetween(DB::raw(base64_decode('REFURShhYnNlbmNlX3JlcXVlc3RzLmVuZCk=')), [$start, $end])
                        ->where(function ($query)  use ($start, $end) {
                            $query->where(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YWN0aXZl'))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('cGVuZGluZw=='))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YXBwcm92ZQ=='));
                        });
                })

                ->first();
            if ($cek) {
                $error = base64_decode('QW5kYSBTdWRhaCBNZW1pbGlraSBQZW5nYWp1YW4gZGkgdGFuZ2dhbCBpbmk=');
            }
            $request_date = $start;
            $day_id = date(base64_decode('dw=='), strtotime($request_date)) == base64_decode('MA==') ? base64_decode('Nw==') : date(base64_decode('dw=='), strtotime($request_date));
            $message_err = base64_decode('YW5kYSBoYW55YSBiaXNhIG1lbmdhanVrYW4gZGkgamFtIGtlcmph');
            $staff = Staff::where(base64_decode('aWQ='), $dataForm->staff_id)->first();
            $absen_now = Absence::join(base64_decode('YWJzZW5jZV9sb2dz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZXMuaWQ='))
                ->where(base64_decode('c3RhZmZfaWQ='), $dataForm->staff_id)
                ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA=='), 2)
                ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cw=='), 1)
                ->whereDate(base64_decode('YWJzZW5jZXMuY3JlYXRlZF9hdA=='), date(base64_decode('WS1tLWQ=')))
                ->first();


            if (!$absen_now) {
                $error = base64_decode('YW5kYSBoYW55YSBiaXNhIG1lbmdhanVrYW4ga2V0aWthIHN1ZGFoIGFic2VuIG1hc3VrIGRhbiBiZWx1bSBtZWxha3VrYW4gYWJzZW4gcHVsYW5n');
            }
        } else if ($dataForm->category == base64_decode('bG9jYXRpb24=')) {
            $start =  date(base64_decode('WS1tLWQ='), strtotime($start));
            $end =  date(base64_decode('WS1tLWQ='), strtotime($end));
            $cek = AbsenceRequest::where(base64_decode('c3RhZmZfaWQ='), $dataForm->staff_id)
                ->where(function ($query) {
                    $query->where(base64_decode('Y2F0ZWdvcnk='), base64_decode('Z2VvbG9jYXRpb25fb2Zm'))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('ZHV0eQ=='))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('bGVhdmU='))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('cGVybWlzc2lvbg=='))
                        ->orWhere(base64_decode('Y2F0ZWdvcnk='), base64_decode('ZXhjdXNl'));
                })
                ->where(function ($query)  use ($start, $end) {
                    $query->whereBetween(DB::raw(base64_decode('REFURShhYnNlbmNlX3JlcXVlc3RzLnN0YXJ0KQ==')), [$start, $end])
                        ->where(function ($query)  use ($start, $end) {
                            $query->where(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YWN0aXZl'))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('cGVuZGluZw=='))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YXBwcm92ZQ=='));
                        })
                        ->orWhereBetween(DB::raw(base64_decode('REFURShhYnNlbmNlX3JlcXVlc3RzLmVuZCk=')), [$start, $end])
                        ->where(function ($query)  use ($start, $end) {
                            $query->where(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YWN0aXZl'))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('cGVuZGluZw=='))
                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('PQ=='), base64_decode('YXBwcm92ZQ=='));
                        });
                })

                ->first();
            if ($cek) {
                $error = base64_decode('QW5kYSBNYXNpaCBNZW1pbGlraSBQZXJtb2hvbmFuIEFic2VuIEx1YXIgZGkgdGFuZ2dhbCBpbmk=');
            }
        } else {
        }

        if ($dataForm->type == base64_decode('c2ljaw==')) {
            $endS = '';
        } else if ($dataForm->type == base64_decode('c2lja19hcHByb3Zl')) {
            $endS = '';
        } else {
            $endS =  $endS;
        }

        if ($error == '') {
            $requests = new AbsenceRequest();
            $requests->staff_id = $dataForm->staff_id;
            $requests->description = $dataForm->description;
            $requests->start = $startS;
            $requests->end = $endS;
            $requests->type = $dataForm->type;
            $requests->time = $dataForm->time;
            $requests->status = $dataForm->status;
            $requests->category = $dataForm->category;

            if ($dataForm->category == base64_decode('bG9jYXRpb24=')) {
                $requests->work_unit_id = $dataForm->work_unit_id;
            }

            $requests->save();
            $requests_id = $requests->id;


            if ($request->file(base64_decode('aW1hZ2VQ'))) {
                $image = $request->file(base64_decode('aW1hZ2VQ'));
                $resourceImage = $image;
                $nameImage = base64_decode('aW1hZ2VQ') . date(base64_decode('WS1tLWQgaDppOnM=')) . base64_decode('Lg==') . $image->extension();
                $file_extImage = $image->extension();
                $folder_upload = base64_decode('aW1hZ2VzL1JlcXVlc3RGaWxl');
                $resourceImage->move($folder_upload, $nameImage);



                $data = [
                    base64_decode('aW1hZ2U=') => $nameImage,
                    base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk') => $requests_id,
                    base64_decode('dHlwZQ==') => base64_decode('YXBwcm92ZQ==')
                ];
                $data = AbsenceRequestLogs::create($data);
            }

            if ($request->file(base64_decode('aW1hZ2VQbmc='))) {
                $image = $request->file(base64_decode('aW1hZ2VQbmc='));
                $resourceImage = $image;
                $nameImage = base64_decode('aW1hZ2VQbmc=') . date(base64_decode('WS1tLWQgaDppOnM=')) . base64_decode('Lg==') . $image->extension();
                $file_extImage = $image->extension();
                $folder_upload = base64_decode('aW1hZ2VzL1JlcXVlc3RGaWxl');
                $resourceImage->move($folder_upload, $nameImage);


                $data = [
                    base64_decode('aW1hZ2U=') =>  $nameImage,
                    base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk') => $requests_id,
                    base64_decode('dHlwZQ==') => base64_decode('cmVxdWVzdA==')
                ];
                $data = AbsenceRequestLogs::create($data);
            }


            $admin = Staff::selectRaw(base64_decode('dXNlcnMuKg=='))->where(base64_decode('c3RhZmZzLmlk'),  $dataForm->staff_id)->join(base64_decode('dXNlcnM='), base64_decode('dXNlcnMuc3RhZmZfaWQ='), base64_decode('PQ=='), base64_decode('c3RhZmZzLmlk'))->first();
            $id_onesignal = $admin->_id_onesignal;
            $wa_code = date(base64_decode('eQ==')) . date(base64_decode('bQ==')) . date(base64_decode('ZA==')) . date(base64_decode('SA==')) . date(base64_decode('aQ==')) . date(base64_decode('cw=='));
            $wa_data_group = [];

            if ($dataForm->category == base64_decode('dmlzaXQ=')) {
                $categoryName = base64_decode('RGluYXMgRGFsYW0=');
            } else if ($dataForm->category == base64_decode('ZHV0eQ==')) {
                $categoryName = base64_decode('RGluYXMgTHVhcg==');
            } else if ($dataForm->category == base64_decode('cGVybWlzc2lvbg==')) {
                $categoryName = base64_decode('SXppbg==');
            } else if ($dataForm->category == base64_decode('ZXhjdXNl')) {
                $categoryName = base64_decode('UGVybWlzaQ==');
            } else if ($dataForm->category == base64_decode('Z2VvbG9jYXRpb25fb2Zm')) {
                $categoryName = base64_decode('QWJzZW4gRGlsdWFy');
            } else if ($dataForm->category == base64_decode('ZXh0cmE=')) {
                $categoryName = base64_decode('TGVtYnVy');
            } else if ($dataForm->category == base64_decode('bGVhdmU=')) {
                $categoryName = base64_decode('Q3V0aQ==');
            } else if ($dataForm->category == base64_decode('bG9jYXRpb24=')) {
                $categoryName = base64_decode('UGluZGFoIExva2FzaQ==');
            } else if ($dataForm->category == base64_decode('Zm9yZ2V0')) {
                $categoryName = base64_decode('THVwYSBBYnNlbg==');
            } else if ($dataForm->category == base64_decode('QWRkaXRpb25hbFRpbWU=')) {
                $categoryName = base64_decode('UGVuYW1iYWhhbiBXYWt0dSBLZXJqYQ==');
            } else {
                $categoryName = '';
            }

            $phone_no = $admin->phone;
            $message = base64_decode('UGVuZ2FqdWFuIA==') . $categoryName . base64_decode('IG9sZWgg') . $admin->name;
            $wa_data = [
                base64_decode('cGhvbmU=') => $this->gantiFormat($phone_no),
                base64_decode('Y3VzdG9tZXJfaWQ=') => null,
                base64_decode('bWVzc2FnZQ==') => $message,
                base64_decode('dGVtcGxhdGVfaWQ=') => '',
                base64_decode('c3RhdHVz') => base64_decode('Z2FnYWw='),
                base64_decode('cmVmX2lk') => $wa_code,
                base64_decode('Y3JlYXRlZF9hdA==') => date(base64_decode('WS1tLWQgaDppOnNh')),
                base64_decode('dXBkYXRlZF9hdA==') => date(base64_decode('WS1tLWQgaDppOnNh'))
            ];
            $wa_data_group[] = $wa_data;
            DB::table(base64_decode('d2FfaGlzdG9yaWVz'))->insert($wa_data);
            $wa_sent = WablasTrait::sendText($wa_data_group);
            $array_merg = [];
            if (!empty(json_decode($wa_sent)->data->messages)) {
                $array_merg = array_merge(json_decode($wa_sent)->data->messages, $array_merg);
            }
            foreach ($array_merg as $key => $value) {
                if (!empty($value->ref_id)) {
                    wa_history::where(base64_decode('cmVmX2lk'), $value->ref_id)->update([base64_decode('aWRfd2E=') => $value->id, base64_decode('c3RhdHVz') => ($value->status === false) ? base64_decode('Z2FnYWw=') : $value->status]);
                }
            }

            if (!empty($id_onesignal)) {
                OneSignal::sendNotificationToUser(
                    $message,
                    $id_onesignal,
                    $url = null,
                    $data = null,
                    $buttons = null,
                    $schedule = null
                );
            }

            $bagian = Staff::selectRaw(base64_decode('dXNlcnMuKg=='))->where(base64_decode('c3RhZmZzLmlk'),  $dataForm->staff_id)->join(base64_decode('dXNlcnM='), base64_decode('dXNlcnMuc3RhZmZfaWQ='), base64_decode('PQ=='), base64_decode('c3RhZmZzLmlk'))->first();
            $admin_arr = User::where(base64_decode('ZGFwZXJ0ZW1lbnRfaWQ='), $bagian->dapertement_id)
                ->where(base64_decode('c3ViZGFwZXJ0ZW1lbnRfaWQ='), 0)
                ->where(base64_decode('c3RhZmZfaWQ='), 0)->get();
            foreach ($admin_arr as $key => $admin) {
                $id_onesignal = $admin->_id_onesignal;
                $wa_code = date(base64_decode('eQ==')) . date(base64_decode('bQ==')) . date(base64_decode('ZA==')) . date(base64_decode('SA==')) . date(base64_decode('aQ==')) . date(base64_decode('cw=='));
                $wa_data_group = [];
                if ($admin->staff_id > 0) {
                    $staff = StaffApi::where(base64_decode('aWQ='), $admin->staff_id)->first();
                    $phone_no = $staff->phone;
                } else {
                    $phone_no = $admin->phone;
                }
                $wa_data = [
                    base64_decode('cGhvbmU=') => $this->gantiFormat($phone_no),
                    base64_decode('Y3VzdG9tZXJfaWQ=') => null,
                    base64_decode('bWVzc2FnZQ==') => $message,
                    base64_decode('dGVtcGxhdGVfaWQ=') => '',
                    base64_decode('c3RhdHVz') => base64_decode('Z2FnYWw='),
                    base64_decode('cmVmX2lk') => $wa_code,
                    base64_decode('Y3JlYXRlZF9hdA==') => date(base64_decode('WS1tLWQgaDppOnNh')),
                    base64_decode('dXBkYXRlZF9hdA==') => date(base64_decode('WS1tLWQgaDppOnNh'))
                ];
                $wa_data_group[] = $wa_data;
                DB::table(base64_decode('d2FfaGlzdG9yaWVz'))->insert($wa_data);
                $wa_sent = WablasTrait::sendText($wa_data_group);
                $array_merg = [];
                if (!empty(json_decode($wa_sent)->data->messages)) {
                    $array_merg = array_merge(json_decode($wa_sent)->data->messages, $array_merg);
                }
                foreach ($array_merg as $key => $value) {
                    if (!empty($value->ref_id)) {
                        wa_history::where(base64_decode('cmVmX2lk'), $value->ref_id)->update([base64_decode('aWRfd2E=') => $value->id, base64_decode('c3RhdHVz') => ($value->status === false) ? base64_decode('Z2FnYWw=') : $value->status]);
                    }
                }
                if (!empty($id_onesignal)) {
                    OneSignal::sendNotificationToUser(
                        $message,
                        $id_onesignal,
                        $url = null,
                        $data = null,
                        $buttons = null,
                        $schedule = null
                    );
                }
            }

            return response()->json([
                base64_decode('bWVzc2FnZQ==') => base64_decode('UGVuZ2FqdWFuIFRlcmtpcmlt'),
                base64_decode('ZGF0YQ==') => $requests,
            ]);
        } else {
            return response()->json([
                base64_decode('bWVzc2FnZQ==') => $error,
                base64_decode('ZGF0YQ==') => '',
            ]);
        }
    }

    public function update(Request $request)
    {

        $dataForm = json_decode($request->form);

        if ($request->file(base64_decode('aW1hZ2VQ'))) {
            $image = $request->file(base64_decode('aW1hZ2VQ'));
            $resourceImage = $image;
            $nameImage = base64_decode('aW1hZ2VQ') . date(base64_decode('WS1tLWQgaDppOnM=')) . base64_decode('Lg==') . $image->extension();
            $file_extImage = $image->extension();
            $folder_upload = base64_decode('aW1hZ2VzL1JlcXVlc3RGaWxl');
            $resourceImage->move($folder_upload, $nameImage);



            $data = [
                base64_decode('aW1hZ2U=') => $nameImage,
                base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk') => $dataForm->id,
                base64_decode('dHlwZQ==') => base64_decode('YXBwcm92ZQ==')
            ];
            $data = AbsenceRequestLogs::create($data);
        }

        if ($request->file(base64_decode('aW1hZ2VQbmc='))) {
            $image = $request->file(base64_decode('aW1hZ2VQbmc='));
            $resourceImage = $image;
            $nameImage = base64_decode('aW1hZ2VQbmc=') . date(base64_decode('WS1tLWQgaDppOnM=')) . base64_decode('Lg==') . $image->extension();
            $file_extImage = $image->extension();
            $folder_upload = base64_decode('aW1hZ2VzL1JlcXVlc3RGaWxl');
            $resourceImage->move($folder_upload, $nameImage);


            $data = [
                base64_decode('aW1hZ2U=') =>  $nameImage,
                base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk') => $dataForm->id,
                base64_decode('dHlwZQ==') => base64_decode('cmVxdWVzdA==')
            ];
            $data = AbsenceRequestLogs::create($data);
        }

        return response()->json([
            base64_decode('bWVzc2FnZQ==') => base64_decode('UGVuZ2FqdWFuIFRlcmtpcmlt'),
        ]);
    }

    public function history(Request $request)
    {
        $requests = AbsenceRequest::where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
            ->FilterDate($request->from, $request->to)
            ->orderBy(base64_decode('Y3JlYXRlZF9hdA=='), base64_decode('REVTQw=='))
            ->paginate(3, [base64_decode('Kg==')], base64_decode('cGFnZQ=='), $request->page);
        return response()->json([
            base64_decode('bWVzc2FnZQ==') => base64_decode('UGVuZ2FqdWFuIFRlcmtpcmlt'),
            base64_decode('ZGF0YQ==') => $requests,
        ]);
    }

    public function imageDelete($id)
    {
        $requests = AbsenceRequestLogs::where(base64_decode('aWQ='), $id)->delete();
        return response()->json([
            base64_decode('bWVzc2FnZQ==') => base64_decode('QnVrdGkgRGloYXB1cw=='),
            base64_decode('aWQ=') => $id,
            base64_decode('ZGF0YQ==') => $requests,
        ]);
    }

    public function getPermissionCat(Request $request)
    {
        $cat = [
            [base64_decode('aWQ=') => base64_decode('c2ljaw=='), base64_decode('bmFtZQ==') => base64_decode('c2FraXQ='), base64_decode('Y2hlY2tlZA==') => false],
            [base64_decode('aWQ=') => base64_decode('b3RoZXI='), base64_decode('bmFtZQ==') => base64_decode('TGFpbi1MYWlu'), base64_decode('Y2hlY2tlZA==') => false],
        ];
        return response()->json([
            base64_decode('bWVzc2FnZQ==') => base64_decode('UGVuZ2FqdWFuIFRlcmtpcmlt'),
            base64_decode('ZGF0YQ==') => $cat,
        ]);
    }

    public function listFile(Request $request)
    {
        $file = AbsenceRequestLogs::selectRaw(base64_decode('aW1hZ2UsIGlk'))->where(base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk'), $request->id)->get();
        return response()->json([
            base64_decode('bWVzc2FnZQ==') => base64_decode('UGVuZ2FqdWFuIFRlcmtpcmlt'),
            base64_decode('ZGF0YQ==') => $file,
            base64_decode('JHM=') => $request->id
        ]);
    }

    public function absenceList(Request $request)
    {
        $duty = Requests::where(base64_decode('Y2F0ZWdvcnk='), base64_decode('ZHV0eQ=='))->whereDate(base64_decode('ZGF0ZQ=='), base64_decode('PQ=='), date(base64_decode('WS1tLWQ=')))->where(base64_decode('dXNlcl9pZA=='), $request->user_id)->where(base64_decode('c3RhdHVz'), base64_decode('YXBwcm92ZQ=='))->get();
        $extra = Requests::where(base64_decode('Y2F0ZWdvcnk='), base64_decode('ZXh0cmE='))->whereDate(base64_decode('ZGF0ZQ=='), base64_decode('PQ=='), date(base64_decode('WS1tLWQ=')))->where(base64_decode('dXNlcl9pZA=='), $request->user_id)->where(base64_decode('c3RhdHVz'), base64_decode('YXBwcm92ZQ=='))->get();
        $permit = Requests::where(base64_decode('Y2F0ZWdvcnk='), base64_decode('cGVybWl0'))->whereDate(base64_decode('ZGF0ZQ=='), base64_decode('PQ=='), date(base64_decode('WS1tLWQ=')))->where(base64_decode('dXNlcl9pZA=='), $request->user_id)->where(base64_decode('c3RhdHVz'), base64_decode('YXBwcm92ZQ=='))->get();

        return response()->json([
            base64_decode('bWVzc2FnZQ==') => base64_decode('U3VjY2Vz'),
            base64_decode('ZHV0eQ==') => $duty,
            base64_decode('ZXh0cmE=') => $extra,
            base64_decode('cGVybWl0') => $permit,
        ]);
    }


    public function menuAdmin(Request $request)
    {
        $user = User::where(base64_decode('aWQ='), $request->id)->first();
        $checker = [];
        $users = user::with([base64_decode('cm9sZXM=')])
            ->where(base64_decode('aWQ='), $request->id)
            ->first();
        foreach ($users->roles as $data) {
            foreach ($data->permissions as $data2) {
                $checker[] = $data2->title;
            }
        }
        if (in_array(base64_decode('YWJzZW5jZV9hbGxfYWNjZXNz'), $checker)) {
            $absence_request_count =  AbsenceRequest::selectRaw(base64_decode('Q09VTlQoQ0FTRSBXSEVOIGNhdGVnb3J5ID0gInZpc2l0IiBhbmQgc3RhdHVzID0gInBlbmRpbmciIGFuZCBhYnNlbmNlX3JlcXVlc3RzLmNyZWF0ZWRfYXQgPj0gIg==') . date(base64_decode('WS1tLWQ=')) . base64_decode('IiBUSEVOIDEgRU5EKSBBUyB2aXNpdF9jb3VudA=='))
                ->selectRaw(base64_decode('Q09VTlQoQ0FTRSBXSEVOIGNhdGVnb3J5ID0gImR1dHkiIGFuZCBzdGF0dXMgPSAicGVuZGluZyIgYW5kIGFic2VuY2VfcmVxdWVzdHMuY3JlYXRlZF9hdCA+PSAi') . date(base64_decode('WS1tLWQ=')) . base64_decode('IiBUSEVOIDEgRU5EKSBBUyBkdXR5X2NvdW50'))
                ->selectRaw(base64_decode('Q09VTlQoQ0FTRSBXSEVOIGNhdGVnb3J5ID0gImV4Y3VzZSIgYW5kIHN0YXR1cyA9ICJwZW5kaW5nIiBhbmQgYWJzZW5jZV9yZXF1ZXN0cy5jcmVhdGVkX2F0ID49ICI=') . date(base64_decode('WS1tLWQ=')) . base64_decode('IiBUSEVOIDEgRU5EKSBBUyBleGN1c2VfY291bnQ='))
                ->selectRaw(base64_decode('Q09VTlQoQ0FTRSBXSEVOIGNhdGVnb3J5ID0gImV4dHJhIiBhbmQgc3RhdHVzID0gInBlbmRpbmciIGFuZCBhYnNlbmNlX3JlcXVlc3RzLmNyZWF0ZWRfYXQgPj0gIg==') . date(base64_decode('WS1tLWQ=')) . base64_decode('IiBUSEVOIDEgRU5EKSBBUyBleHRyYV9jb3VudA=='))
                ->selectRaw(base64_decode('Q09VTlQoQ0FTRSBXSEVOIGNhdGVnb3J5ID0gImxlYXZlIiBhbmQgc3RhdHVzID0gInBlbmRpbmciIGFuZCBhYnNlbmNlX3JlcXVlc3RzLmNyZWF0ZWRfYXQgPj0gIg==') . date(base64_decode('WS1tLWQ=')) . base64_decode('IiBUSEVOIDEgRU5EKSBBUyBsZWF2ZV9jb3VudA=='))
                ->selectRaw(base64_decode('Q09VTlQoQ0FTRSBXSEVOIGNhdGVnb3J5ID0gImdlb2xvY2F0aW9uX29mZiIgYW5kIHN0YXR1cyA9ICJwZW5kaW5nIiBhbmQgYWJzZW5jZV9yZXF1ZXN0cy5jcmVhdGVkX2F0ID49ICI=') . date(base64_decode('WS1tLWQ=')) . base64_decode('IiBUSEVOIDEgRU5EKSBBUyBnZW9sb2NhdGlvbl9vZmZfY291bnQ='))
                ->selectRaw(base64_decode('Q09VTlQoQ0FTRSBXSEVOIGNhdGVnb3J5ID0gInBlcm1pc3Npb24iIGFuZCBzdGF0dXMgPSAicGVuZGluZyIgYW5kIGFic2VuY2VfcmVxdWVzdHMuY3JlYXRlZF9hdCA+PSAi') . date(base64_decode('WS1tLWQ=')) . base64_decode('IiBUSEVOIDEgRU5EKSBBUyBwZXJtaXNzaW9uX2NvdW50'))
                ->selectRaw(base64_decode('Q09VTlQoQ0FTRSBXSEVOIGNhdGVnb3J5ID0gImxvY2F0aW9uIiBhbmQgc3RhdHVzID0gInBlbmRpbmciIGFuZCBhYnNlbmNlX3JlcXVlc3RzLmNyZWF0ZWRfYXQgPj0gIg==') . date(base64_decode('WS1tLWQ=')) . base64_decode('IiBUSEVOIDEgRU5EKSBBUyBsb2NhdGlvbl9jb3VudA=='))
                ->selectRaw(base64_decode('Q09VTlQoQ0FTRSBXSEVOIGNhdGVnb3J5ID0gImZvcmdldCIgYW5kIHN0YXR1cyA9ICJwZW5kaW5nIiBhbmQgYWJzZW5jZV9yZXF1ZXN0cy5jcmVhdGVkX2F0ID49ICI=') . date(base64_decode('WS1tLWQ=')) . base64_decode('IiBUSEVOIDEgRU5EKSBBUyBmb3JnZXRfY291bnQ='))
                ->first();

            $shift_change = ShiftChange::selectRaw(base64_decode('Y291bnQoc2hpZnRfY2hhbmdlcy5pZCkgYXMgdG90YWw='))
                ->join(base64_decode('c2hpZnRfcGxhbm5lcl9zdGFmZnMgYXMgczE='), base64_decode('czEuaWQ='), base64_decode('PQ=='), base64_decode('c2hpZnRfY2hhbmdlcy5zaGlmdF9pZA=='))
                ->join(base64_decode('c3RhZmZzIGFzIHN0MQ=='), base64_decode('c3QxLmlk'), base64_decode('PQ=='), base64_decode('c2hpZnRfY2hhbmdlcy5zdGFmZl9pZA=='))
                ->join(base64_decode('c2hpZnRfZ3JvdXBzIGFzIHNoMQ=='), base64_decode('c2gxLmlk'), base64_decode('PQ=='), base64_decode('czEuc2hpZnRfZ3JvdXBfaWQ='))
                ->join(base64_decode('c2hpZnRfcGxhbm5lcl9zdGFmZnMgYXMgczI='), base64_decode('czIuaWQ='), base64_decode('PQ=='), base64_decode('c2hpZnRfY2hhbmdlcy5zaGlmdF9jaGFuZ2VfaWQ='))
                ->join(base64_decode('c3RhZmZzIGFzIHN0Mg=='), base64_decode('c3QyLmlk'), base64_decode('PQ=='), base64_decode('c2hpZnRfY2hhbmdlcy5zdGFmZl9jaGFuZ2VfaWQ='))
                ->join(base64_decode('c2hpZnRfZ3JvdXBzIGFzIHNoMg=='), base64_decode('c2gyLmlk'), base64_decode('PQ=='), base64_decode('czIuc2hpZnRfZ3JvdXBfaWQ='))
                ->whereDate(base64_decode('c2hpZnRfY2hhbmdlcy5jcmVhdGVkX2F0'), base64_decode('Pj0='), date(base64_decode('WS1tLWQ=')))
                ->orderBy(base64_decode('c2hpZnRfY2hhbmdlcy5jcmVhdGVkX2F0'), base64_decode('QVND'))
                ->first();
        } else {
            $absence_request_count =  AbsenceRequest::selectRaw(base64_decode('Q09VTlQoQ0FTRSBXSEVOIGNhdGVnb3J5ID0gInZpc2l0IiBhbmQgc3RhdHVzID0gInBlbmRpbmciIGFuZCBhYnNlbmNlX3JlcXVlc3RzLmNyZWF0ZWRfYXQgPj0gIg==') . date(base64_decode('WS1tLWQ=')) . base64_decode('IiBUSEVOIDEgRU5EKSBBUyB2aXNpdF9jb3VudA=='))
                ->selectRaw(base64_decode('Q09VTlQoQ0FTRSBXSEVOIGNhdGVnb3J5ID0gImR1dHkiIGFuZCBzdGF0dXMgPSAicGVuZGluZyIgYW5kIGFic2VuY2VfcmVxdWVzdHMuY3JlYXRlZF9hdCA+PSAi') . date(base64_decode('WS1tLWQ=')) . base64_decode('IiBUSEVOIDEgRU5EKSBBUyBkdXR5X2NvdW50'))
                ->selectRaw(base64_decode('Q09VTlQoQ0FTRSBXSEVOIGNhdGVnb3J5ID0gImV4Y3VzZSIgYW5kIHN0YXR1cyA9ICJwZW5kaW5nIiBhbmQgYWJzZW5jZV9yZXF1ZXN0cy5jcmVhdGVkX2F0ID49ICI=') . date(base64_decode('WS1tLWQ=')) . base64_decode('IiBUSEVOIDEgRU5EKSBBUyBleGN1c2VfY291bnQ='))
                ->selectRaw(base64_decode('Q09VTlQoQ0FTRSBXSEVOIGNhdGVnb3J5ID0gImV4dHJhIiBhbmQgc3RhdHVzID0gInBlbmRpbmciIGFuZCBhYnNlbmNlX3JlcXVlc3RzLmNyZWF0ZWRfYXQgPj0gIg==') . date(base64_decode('WS1tLWQ=')) . base64_decode('IiBUSEVOIDEgRU5EKSBBUyBleHRyYV9jb3VudA=='))
                ->selectRaw(base64_decode('Q09VTlQoQ0FTRSBXSEVOIGNhdGVnb3J5ID0gImxlYXZlIiBhbmQgc3RhdHVzID0gInBlbmRpbmciIGFuZCBhYnNlbmNlX3JlcXVlc3RzLmNyZWF0ZWRfYXQgPj0gIg==') . date(base64_decode('WS1tLWQ=')) . base64_decode('IiBUSEVOIDEgRU5EKSBBUyBsZWF2ZV9jb3VudA=='))
                ->selectRaw(base64_decode('Q09VTlQoQ0FTRSBXSEVOIGNhdGVnb3J5ID0gImdlb2xvY2F0aW9uX29mZiIgYW5kIHN0YXR1cyA9ICJwZW5kaW5nIiBhbmQgYWJzZW5jZV9yZXF1ZXN0cy5jcmVhdGVkX2F0ID49ICI=') . date(base64_decode('WS1tLWQ=')) . base64_decode('IiBUSEVOIDEgRU5EKSBBUyBnZW9sb2NhdGlvbl9vZmZfY291bnQ='))
                ->selectRaw(base64_decode('Q09VTlQoQ0FTRSBXSEVOIGNhdGVnb3J5ID0gInBlcm1pc3Npb24iIGFuZCBzdGF0dXMgPSAicGVuZGluZyIgYW5kIGFic2VuY2VfcmVxdWVzdHMuY3JlYXRlZF9hdCA+PSAi') . date(base64_decode('WS1tLWQ=')) . base64_decode('IiBUSEVOIDEgRU5EKSBBUyBwZXJtaXNzaW9uX2NvdW50'))
                ->selectRaw(base64_decode('Q09VTlQoQ0FTRSBXSEVOIGNhdGVnb3J5ID0gImxvY2F0aW9uIiBhbmQgc3RhdHVzID0gInBlbmRpbmciIGFuZCBhYnNlbmNlX3JlcXVlc3RzLmNyZWF0ZWRfYXQgPj0gIg==') . date(base64_decode('WS1tLWQ=')) . base64_decode('IiBUSEVOIDEgRU5EKSBBUyBsb2NhdGlvbl9jb3VudA=='))
                ->selectRaw(base64_decode('Q09VTlQoQ0FTRSBXSEVOIGNhdGVnb3J5ID0gImZvcmdldCIgYW5kIHN0YXR1cyA9ICJwZW5kaW5nIiBhbmQgYWJzZW5jZV9yZXF1ZXN0cy5jcmVhdGVkX2F0ID49ICI=') . date(base64_decode('WS1tLWQ=')) . base64_decode('IiBUSEVOIDEgRU5EKSBBUyBmb3JnZXRfY291bnQ='))
                ->join(base64_decode('c3RhZmZz'), base64_decode('c3RhZmZzLmlk'), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9yZXF1ZXN0cy5zdGFmZl9pZA=='))
                ->where(base64_decode('ZGFwZXJ0ZW1lbnRfaWQ='), $user->dapertement_id)
                ->first();

            $shift_change = ShiftChange::selectRaw(base64_decode('Y291bnQoc2hpZnRfY2hhbmdlcy5pZCkgYXMgdG90YWw='))
                ->join(base64_decode('c2hpZnRfcGxhbm5lcl9zdGFmZnMgYXMgczE='), base64_decode('czEuaWQ='), base64_decode('PQ=='), base64_decode('c2hpZnRfY2hhbmdlcy5zaGlmdF9pZA=='))
                ->join(base64_decode('c3RhZmZzIGFzIHN0MQ=='), base64_decode('c3QxLmlk'), base64_decode('PQ=='), base64_decode('c2hpZnRfY2hhbmdlcy5zdGFmZl9pZA=='))
                ->join(base64_decode('c2hpZnRfZ3JvdXBzIGFzIHNoMQ=='), base64_decode('c2gxLmlk'), base64_decode('PQ=='), base64_decode('czEuc2hpZnRfZ3JvdXBfaWQ='))
                ->join(base64_decode('c2hpZnRfcGxhbm5lcl9zdGFmZnMgYXMgczI='), base64_decode('czIuaWQ='), base64_decode('PQ=='), base64_decode('c2hpZnRfY2hhbmdlcy5zaGlmdF9jaGFuZ2VfaWQ='))
                ->join(base64_decode('c3RhZmZzIGFzIHN0Mg=='), base64_decode('c3QyLmlk'), base64_decode('PQ=='), base64_decode('c2hpZnRfY2hhbmdlcy5zdGFmZl9jaGFuZ2VfaWQ='))
                ->join(base64_decode('c2hpZnRfZ3JvdXBzIGFzIHNoMg=='), base64_decode('c2gyLmlk'), base64_decode('PQ=='), base64_decode('czIuc2hpZnRfZ3JvdXBfaWQ='))
                ->FilterDapertement($user->dapertement_id)
                ->whereDate(base64_decode('c2hpZnRfY2hhbmdlcy5jcmVhdGVkX2F0'), base64_decode('Pj0='), date(base64_decode('WS1tLWQ=')))
                ->orderBy(base64_decode('c2hpZnRfY2hhbmdlcy5jcmVhdGVkX2F0'), base64_decode('QVND'))
                ->first();
        }



        return response()->json([
            base64_decode('bWVzc2FnZQ==') => base64_decode('UGVuZ2FqdWFuIFRlcmtpcmlt'),
            base64_decode('ZGF0YQ==') => $absence_request_count,
            base64_decode('Y2hhbmdlX3NoaWZ0') => $shift_change->total
        ]);
    }

    public function requestApprove(Request $request)
    {
        $user = User::where(base64_decode('aWQ='), $request->id)->first();
        $checker = [];
        $users = user::with([base64_decode('cm9sZXM=')])
            ->where(base64_decode('aWQ='), $request->id)
            ->first();
        foreach ($users->roles as $data) {
            foreach ($data->permissions as $data2) {
                $checker[] = $data2->title;
            }
        }
        if (in_array(base64_decode('YWJzZW5jZV9hbGxfYWNjZXNz'), $checker)) {
            $requests = AbsenceRequest::select(base64_decode('YWJzZW5jZV9yZXF1ZXN0cy4q'), base64_decode('c3RhZmZzLm5hbWUgYXMgc3RhZmZfbmFtZQ=='))->join(base64_decode('c3RhZmZz'), base64_decode('YWJzZW5jZV9yZXF1ZXN0cy5zdGFmZl9pZA=='), base64_decode('PQ=='), base64_decode('c3RhZmZzLmlk'))
                ->FilterDate($request->from, $request->to)
                ->where(base64_decode('Y2F0ZWdvcnk='), $request->category)
                ->orderBy(base64_decode('Y3JlYXRlZF9hdA=='), base64_decode('REVTQw=='))
                ->paginate(3, [base64_decode('Kg==')], base64_decode('cGFnZQ=='), $request->page);
            return response()->json([
                base64_decode('bWVzc2FnZQ==') => base64_decode('UGVuZ2FqdWFuIFRlcmtpcmlt'),
                base64_decode('ZGF0YQ==') => $requests,
            ]);
        } else {
            $requests = AbsenceRequest::select(base64_decode('YWJzZW5jZV9yZXF1ZXN0cy4q'), base64_decode('c3RhZmZzLm5hbWUgYXMgc3RhZmZfbmFtZQ=='))->join(base64_decode('c3RhZmZz'), base64_decode('YWJzZW5jZV9yZXF1ZXN0cy5zdGFmZl9pZA=='), base64_decode('PQ=='), base64_decode('c3RhZmZzLmlk'))
                ->FilterDapertement($user->dapertement_id)
                ->FilterDate($request->from, $request->to)
                ->where(base64_decode('Y2F0ZWdvcnk='), $request->category)
                ->orderBy(base64_decode('Y3JlYXRlZF9hdA=='), base64_decode('REVTQw=='))
                ->paginate(3, [base64_decode('Kg==')], base64_decode('cGFnZQ=='), $request->page);
            return response()->json([
                base64_decode('bWVzc2FnZQ==') => base64_decode('UGVuZ2FqdWFuIFRlcmtpcmlt'),
                base64_decode('ZGF0YQ==') => $requests,
            ]);
        }
    }


    public function approve(Request $request)
    {


        $requests = AbsenceRequest::selectRaw(base64_decode('YWJzZW5jZV9yZXF1ZXN0cy4qLHdvcmtfdHlwZXMuaWQgYXMgd29ya190eXBlX2lkLCB3b3JrX3R5cGVzLnR5cGUgYXMgdHlwZSwgc3RhZmZzLmlkIGFzIHN0YWZmX2lk'))
            ->join(base64_decode('c3RhZmZz'), base64_decode('YWJzZW5jZV9yZXF1ZXN0cy5zdGFmZl9pZA=='), base64_decode('PQ=='), base64_decode('c3RhZmZzLmlk'))
            ->join(base64_decode('d29ya190eXBlcw=='), base64_decode('d29ya190eXBlcy5pZA=='), base64_decode('PQ=='), base64_decode('c3RhZmZzLndvcmtfdHlwZV9pZA=='))
            ->where(base64_decode('YWJzZW5jZV9yZXF1ZXN0cy5pZA=='), $request->id)->first();

        $d = AbsenceRequest::where(base64_decode('aWQ='), $request->id)->first();
        if (date(base64_decode('WS1tLWQ=')) <= date(base64_decode('WS1tLWQ='), strtotime($d->start))) {
            if ($requests->category == base64_decode('Zm9yZ2V0') || $requests->category == base64_decode('QWRkaXRpb25hbFRpbWU=')) {



                $cek_absen = Absence::with([base64_decode('YWJzZW5jZV9sb2dz'), base64_decode('c3RhZmZz')])
                    ->whereDate(base64_decode('Y3JlYXRlZF9hdA=='), date(base64_decode('WS1tLWQ=')))
                    ->where(base64_decode('c3RhZmZfaWQ='), $d->staff_id)
                    ->orderBy(base64_decode('aWQ='), base64_decode('REVTQw=='))
                    ->first();
                if ($cek_absen) {
                    if ($cek_absen->staffs->work_type_id != 2) {
                        $get_absence =  Absence::with([base64_decode('YWJzZW5jZV9sb2dz'), base64_decode('YWJzZW5jZV9sb2dzLndvcmtUeXBlRGF5cw=='), base64_decode('c3RhZmZz')])
                            ->where(base64_decode('aWQ='), $cek_absen->id)
                            ->first();
                        $time_cek  = $get_absence->absence_logs->where(base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA=='), 2)->where(base64_decode('c3RhdHVz'), base64_decode('MQ=='))->first();
                        $time  = $get_absence->absence_logs->where(base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA=='), 1)->where(base64_decode('c3RhdHVz'), base64_decode('MA=='))->first();
                        if ($time_cek && $time) {
                            $penambahan_durasi = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('KyA=') . ($time->workTypeDays->duration * 60) . base64_decode('IG1pbnV0ZXM='), strtotime(date(base64_decode('WS1tLWQg') . $time->workTypeDays->time))));
                            if ($penambahan_durasi < date(base64_decode('WS1tLWQgSDppOnM='))) {
                                AbsenceLog::where(base64_decode('aWQ='),  $time_cek->id)
                                    ->update([
                                        base64_decode('ZXhwaXJlZF9kYXRl') => date(base64_decode('WS1tLWQgMjM6NTk6NTk='), strtotime(date(base64_decode('WS1tLWQ='), strtotime($time_cek->expired_date)))),
                                        base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk') => $request->id
                                    ]);
                            } else if ($requests->category == base64_decode('QWRkaXRpb25hbFRpbWU=')) {
                                AbsenceLog::where(base64_decode('aWQ='),  $time_cek->id)
                                    ->update([
                                        base64_decode('ZXhwaXJlZF9kYXRl') => date(base64_decode('WS1tLWQgMjM6NTk6NTk='), strtotime(date(base64_decode('WS1tLWQ='), strtotime($time_cek->expired_date)))),
                                        base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk') => $request->id
                                    ]);
                            }
                        } else {
                        }
                    } else {
                        $get_absence =  Absence::with([base64_decode('YWJzZW5jZV9sb2dz'), base64_decode('YWJzZW5jZV9sb2dzLnNoaWZ0R3JvdXBUaW1lU2hlZXRz'), base64_decode('c3RhZmZz')])
                            ->where(base64_decode('aWQ='),  $cek_absen->id)
                            ->first();
                        $time_cek  = $get_absence->absence_logs->where(base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA=='), 2)->where(base64_decode('c3RhdHVz'), base64_decode('MQ=='))->first();
                        $time  = $get_absence->absence_logs->where(base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA=='), 1)->where(base64_decode('c3RhdHVz'), base64_decode('MA=='))->first();
                        if ($time_cek && $time) {
                            $penambahan_durasi = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('KyA=') . ($time->shiftGroupTimeSheets->duration * 60) . base64_decode('IG1pbnV0ZXM='), strtotime(date(base64_decode('WS1tLWQg') . $time->shiftGroupTimeSheets->time))));

                            if ($penambahan_durasi > date(base64_decode('WS1tLWQgSDppOnM='))) {
                                AbsenceLog::where(base64_decode('aWQ='),  $time_cek->id)
                                    ->update([
                                        base64_decode('ZXhwaXJlZF9kYXRl') => date(base64_decode('WS1tLWQgMjM6NTk6NTk='), strtotime(date(base64_decode('WS1tLWQ='), strtotime($time_cek->expired_date)))),
                                        base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk') => $request->id
                                    ]);
                            } else if ($requests->category == base64_decode('QWRkaXRpb25hbFRpbWU=')) {
                                AbsenceLog::where(base64_decode('aWQ='),  $time_cek->id)
                                    ->update([
                                        base64_decode('ZXhwaXJlZF9kYXRl') => date(base64_decode('WS1tLWQgMjM6NTk6NTk='), strtotime(date(base64_decode('WS1tLWQ='), strtotime($time_cek->expired_date)))),
                                        base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk') => $request->id
                                    ]);
                            }
                        } else {
                        }
                    }
                }
            }




            if ($requests->category == base64_decode('cGVybWlzc2lvbg==') || $requests->category == base64_decode('ZHV0eQ==') || $requests->category == base64_decode('bGVhdmU=')) {

                $d = AbsenceRequest::where(base64_decode('aWQ='), $request->id)
                    ->update([base64_decode('c3RhdHVz') => base64_decode('YXBwcm92ZQ==')]);
                $d = AbsenceRequest::where(base64_decode('aWQ='), $request->id)->first();


                $absenceRequest =  AbsenceRequest::select(
                    base64_decode('YWJzZW5jZV9yZXF1ZXN0cy4q'),
                    DB::raw(base64_decode('REFURShzdGFydCkgYXMgc3RhcnQ=')),
                    DB::raw(base64_decode('REFURShlbmQpIGFzIGVuZA==')),
                )
                    ->where(base64_decode('aWQ='), $request->id)->first();
                if ($requests->category == base64_decode('cGVybWlzc2lvbg==')) {
                    $message = base64_decode('SXppbiBhbmRhIHRhbmdnYWwg') . $d->start . base64_decode('IGRpc2V0dWp1aQ==');
                } else if ($requests->category == base64_decode('ZHV0eQ==')) {
                    $message = base64_decode('RGluYXMgYW5kYSB0YW5nZ2FsIA==') . $d->start . base64_decode('IHNhbXBhaSBkZW5nYW4g') . $d->end . base64_decode('IGRpc2V0dWp1aQ==');
                } else if ($requests->category == base64_decode('bGVhdmU=')) {
                    $message = base64_decode('Q3V0aSBhbmRhIHRhbmdnYWwg') . $d->start . base64_decode('IHNhbXBhaSBkZW5nYW4g') . $d->end . base64_decode('IGRpc2V0dWp1aQ==');
                } else {
                    $message = '';
                }
                $begin = strtotime($absenceRequest->start);
                $end   = strtotime($absenceRequest->end);



                if ($requests->type != base64_decode('c2hpZnQ=')) {





                    $work_type_days = Day::select(base64_decode('ZGF5cy4q'))->leftJoin(
                        base64_decode('d29ya190eXBlX2RheXM='),
                        function ($join) use ($requests) {
                            $join->on(base64_decode('ZGF5cy5pZA=='), base64_decode('PQ=='), base64_decode('d29ya190eXBlX2RheXMuZGF5X2lk'))
                                ->where(base64_decode('d29ya190eXBlX2lk'), $requests->work_type_id);
                        }
                    )
                        ->where(base64_decode('d29ya190eXBlX2RheXMuZGF5X2lk'), base64_decode('PQ=='), null)->get();
                    $jadwallibur = [];
                    foreach ($work_type_days as $work_type_day) {
                        $jadwallibur = array_merge($jadwallibur, [$work_type_day->id != base64_decode('Nw==') ? '' . $work_type_day->id : base64_decode('MA==')]);
                    }
                    $ab_id = [];
                    for ($i = $begin; $i <= $end; $i = $i + 86400) {
                        $holiday = Holiday::whereDate(DB::raw(base64_decode('REFURShzdGFydCk=')), base64_decode('PD0='), date(base64_decode('WS1tLWQ='), $i))->whereDate(DB::raw(base64_decode('REFURShlbmQp')), base64_decode('Pj0='), date(base64_decode('WS1tLWQ='), $i))->first();

                        if (!$holiday) {
                            if ((!in_array(date(base64_decode('dw=='), $i), $jadwallibur))) {
                                $check_empty = Absence::where(base64_decode('c3RhZmZfaWQ='), $absenceRequest->staff_id)->whereDate(base64_decode('Y3JlYXRlZF9hdA=='), base64_decode('PQ=='), date(base64_decode('WS1tLWQ='), $i))->first();
                                if (!$check_empty) {
                                    $ab_id = Absence::create([
                                        base64_decode('ZGF5X2lk') => date(base64_decode('dw=='), strtotime(date(base64_decode('WS1tLWQ='), $i))),
                                        base64_decode('c3RhZmZfaWQ=') => $absenceRequest->staff_id,
                                        base64_decode('Y3JlYXRlZF9hdA==') => date(base64_decode('WS1tLWQgSDppOnM='), $i),
                                        base64_decode('dXBkYXRlZF9hdA==') => date(base64_decode('WS1tLWQgSDppOnM='))
                                    ]);
                                    AbsenceLog::create([
                                        base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA==') => $absenceRequest->category == base64_decode('bGVhdmU=') ? 8 : 13,
                                        base64_decode('bGF0') => '',
                                        base64_decode('bG5n') => '',
                                        base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk') => $absenceRequest->id,
                                        base64_decode('cmVnaXN0ZXI=') => date(base64_decode('WS1tLWQ='), $i),
                                        base64_decode('YWJzZW5jZV9pZA==') => $ab_id->id,
                                        base64_decode('ZHVyYXRpb24=') => '',
                                        base64_decode('c3RhdHVz') => ''
                                    ]);
                                }
                            }
                        }
                    }
                } else {
                    $shift_planners = ShiftPlannerStaffs::select(base64_decode('c2hpZnRfcGxhbm5lcl9zdGFmZnMuKg=='), DB::raw(base64_decode('REFURShzaGlmdF9wbGFubmVyX3N0YWZmcy5zdGFydCkgYXMgc3RhcnQ=')))->where(base64_decode('c3RhZmZfaWQ='),  $requests->staff_id)
                        ->get();

                    $jadwalmasuk = [];
                    foreach ($shift_planners as $shift_planner) {
                        $jadwalmasuk = array_merge($jadwalmasuk, [$shift_planner->start]);
                    }

                    for ($i = $begin; $i <= $end; $i = $i + 86400) {
                        $holiday = Holiday::whereDate(base64_decode('c3RhcnQ='), base64_decode('PD0='), date(base64_decode('WS1tLWQ='), $i))->whereDate(base64_decode('ZW5k'), base64_decode('Pj0='), date(base64_decode('WS1tLWQ='), $i))->first();
                        if ((in_array(date(base64_decode('WS1tLWQ='), strtotime(date(base64_decode('WS1tLWQ='), $i))), $jadwalmasuk))) {
                            $check_empty = Absence::where(base64_decode('c3RhZmZfaWQ='), $absenceRequest->staff_id)->whereDate(base64_decode('Y3JlYXRlZF9hdA=='), base64_decode('PQ=='), date(base64_decode('WS1tLWQ='), $i))->first();
                            if (!$check_empty) {
                                $ab_id = Absence::create([
                                    base64_decode('ZGF5X2lk') => date(base64_decode('dw=='), strtotime(date(base64_decode('WS1tLWQ='), $i))),
                                    base64_decode('c3RhZmZfaWQ=') => $absenceRequest->staff_id,
                                    base64_decode('Y3JlYXRlZF9hdA==') => date(base64_decode('WS1tLWQgSDppOnM='), $i),
                                    base64_decode('dXBkYXRlZF9hdA==') => date(base64_decode('WS1tLWQgSDppOnM='))
                                ]);
                                AbsenceLog::create([
                                    base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA==') => $absenceRequest->category == base64_decode('bGVhdmU=') ? 8 : 13,
                                    base64_decode('bGF0') => '',
                                    base64_decode('bG5n') => '',
                                    base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk') => $absenceRequest->id,
                                    base64_decode('cmVnaXN0ZXI=') => date(base64_decode('WS1tLWQ='), $i),
                                    base64_decode('YWJzZW5jZV9pZA==') => $ab_id->id,
                                    base64_decode('ZHVyYXRpb24=') => '',
                                    base64_decode('c3RhdHVz') => ''
                                ]);
                            }
                        }
                    }
                }

                $message = base64_decode('SXppbiBhbmRhIHRhbmdnYWwg') . $d->start . base64_decode('IHNhbXBhaSBkZW5nYW4g') . $d->end . base64_decode('IGRpdGVyaW1h');
                MessageLog::create([
                    base64_decode('c3RhZmZfaWQ=') => $d->staff_id,
                    base64_decode('bWVtbw==') => $message,
                    base64_decode('dHlwZQ==') => base64_decode('bWVzc2FnZQ=='),
                    base64_decode('c3RhdHVz') => base64_decode('cGVuZGluZw=='),
                ]);

                $admin = Staff::selectRaw(base64_decode('dXNlcnMuKg=='))->where(base64_decode('c3RhZmZzLmlk'), $d->staff_id)->join(base64_decode('dXNlcnM='), base64_decode('dXNlcnMuc3RhZmZfaWQ='), base64_decode('PQ=='), base64_decode('c3RhZmZzLmlk'))->first();
                $id_onesignal = $admin->_id_onesignal;
                $wa_code = date(base64_decode('eQ==')) . date(base64_decode('bQ==')) . date(base64_decode('ZA==')) . date(base64_decode('SA==')) . date(base64_decode('aQ==')) . date(base64_decode('cw=='));
                $wa_data_group = [];
                if ($d->staff_id > 0) {
                    $staff = Staff::where(base64_decode('aWQ='), $d->staff_id)->first();
                    $phone_no = $staff->phone;
                } else {
                    $phone_no = $admin->phone;
                }
                $wa_data = [
                    base64_decode('cGhvbmU=') => $this->gantiFormat($phone_no),
                    base64_decode('Y3VzdG9tZXJfaWQ=') => null,
                    base64_decode('bWVzc2FnZQ==') => $message,
                    base64_decode('dGVtcGxhdGVfaWQ=') => '',
                    base64_decode('c3RhdHVz') => base64_decode('Z2FnYWw='),
                    base64_decode('cmVmX2lk') => $wa_code,
                    base64_decode('Y3JlYXRlZF9hdA==') => date(base64_decode('WS1tLWQgaDppOnNh')),
                    base64_decode('dXBkYXRlZF9hdA==') => date(base64_decode('WS1tLWQgaDppOnNh'))
                ];
                $wa_data_group[] = $wa_data;
                DB::table(base64_decode('d2FfaGlzdG9yaWVz'))->insert($wa_data);
                $wa_sent = WablasTrait::sendText($wa_data_group);
                $array_merg = [];
                if (!empty(json_decode($wa_sent)->data->messages)) {
                    $array_merg = array_merge(json_decode($wa_sent)->data->messages, $array_merg);
                }
                foreach ($array_merg as $key => $value) {
                    if (!empty($value->ref_id)) {
                        wa_history::where(base64_decode('cmVmX2lk'), $value->ref_id)->update([base64_decode('aWRfd2E=') => $value->id, base64_decode('c3RhdHVz') => ($value->status === false) ? base64_decode('Z2FnYWw=') : $value->status]);
                    }
                }

                if (!empty($id_onesignal)) {
                    OneSignal::sendNotificationToUser(
                        $message,
                        $id_onesignal,
                        $url = null,
                        $data = null,
                        $buttons = null,
                        $schedule = null
                    );
                }
            } else {
                $d = AbsenceRequest::where(base64_decode('aWQ='), $request->id)
                    ->update([base64_decode('c3RhdHVz') => base64_decode('YXBwcm92ZQ==')]);

                $d = AbsenceRequest::where(base64_decode('aWQ='), $request->id)->first();
                $message = base64_decode('UGVybWlzaSBhbmRhIHRhbmdnYWwg') . $d->start . base64_decode('IGRpc2V0dWp1aQ==');
                if ($d->category == base64_decode('dmlzaXQ=')) {
                    $message = base64_decode('RGluYXMgYW5kYSB0YW5nZ2FsIA==') . $d->start . base64_decode('IGRpc2V0dWp1aQ==');
                } else if ($d->category == base64_decode('ZXhjdXNl')) {
                    $message = base64_decode('UGVybWlzaSBhbmRhIHRhbmdnYWwg') . $d->start . base64_decode('IGRpc2V0dWp1aQ==');
                } else if ($d->category == base64_decode('ZXh0cmE=')) {
                    $message = base64_decode('TGVtYnVyIGFuZGEgdGFuZ2dhbCA=') . $d->start . base64_decode('IGRpc2V0dWp1aQ==');
                } else if ($d->category == base64_decode('Zm9yZ2V0')) {
                    $message = base64_decode('THVwYSBBYnNlbiBhbmRhIHRhbmdnYWwg') . $d->start . base64_decode('IGRpc2V0dWp1aQ==');
                } else if ($d->category == base64_decode('bG9jYXRpb24=')) {
                    $message = base64_decode('UGluZGFoIExva2FzaSBhbmRhIHRhbmdnYWwg') . $d->start . base64_decode('IGRpc2V0dWp1aQ==');
                } else if ($d->category == base64_decode('QWRkaXRpb25hbFRpbWU=')) {
                    $message = base64_decode('UGVuYW1iYWhhbiBKYW0gS2VyamEgYW5kYSB0YW5nZ2FsIA==') . $d->start . base64_decode('IGRpc2V0dWp1aQ==');
                } else {
                    $message = base64_decode('Z2VvbG9jYXRpb24gb2ZmIGRpc2V0dWp1aQ==');
                }
                MessageLog::create([
                    base64_decode('c3RhZmZfaWQ=') => $d->staff_id,
                    base64_decode('bWVtbw==') => $message,
                    base64_decode('dHlwZQ==') => base64_decode('bWVzc2FnZQ=='),
                    base64_decode('c3RhdHVz') => base64_decode('cGVuZGluZw=='),
                ]);

                $admin = Staff::selectRaw(base64_decode('dXNlcnMuKg=='))->where(base64_decode('c3RhZmZzLmlk'), $d->staff_id)->join(base64_decode('dXNlcnM='), base64_decode('dXNlcnMuc3RhZmZfaWQ='), base64_decode('PQ=='), base64_decode('c3RhZmZzLmlk'))->first();
                $id_onesignal = $admin->_id_onesignal;
                $wa_code = date(base64_decode('eQ==')) . date(base64_decode('bQ==')) . date(base64_decode('ZA==')) . date(base64_decode('SA==')) . date(base64_decode('aQ==')) . date(base64_decode('cw=='));
                $wa_data_group = [];
                if ($d->staff_id > 0) {
                    $staff = Staff::where(base64_decode('aWQ='), $d->staff_id)->first();
                    $phone_no = $staff->phone;
                } else {
                    $phone_no = $admin->phone;
                }
                $wa_data = [
                    base64_decode('cGhvbmU=') => $this->gantiFormat($phone_no),
                    base64_decode('Y3VzdG9tZXJfaWQ=') => null,
                    base64_decode('bWVzc2FnZQ==') => $message,
                    base64_decode('dGVtcGxhdGVfaWQ=') => '',
                    base64_decode('c3RhdHVz') => base64_decode('Z2FnYWw='),
                    base64_decode('cmVmX2lk') => $wa_code,
                    base64_decode('Y3JlYXRlZF9hdA==') => date(base64_decode('WS1tLWQgaDppOnNh')),
                    base64_decode('dXBkYXRlZF9hdA==') => date(base64_decode('WS1tLWQgaDppOnNh'))
                ];
                $wa_data_group[] = $wa_data;
                DB::table(base64_decode('d2FfaGlzdG9yaWVz'))->insert($wa_data);
                $wa_sent = WablasTrait::sendText($wa_data_group);
                $array_merg = [];
                if (!empty(json_decode($wa_sent)->data->messages)) {
                    $array_merg = array_merge(json_decode($wa_sent)->data->messages, $array_merg);
                }
                foreach ($array_merg as $key => $value) {
                    if (!empty($value->ref_id)) {
                        wa_history::where(base64_decode('cmVmX2lk'), $value->ref_id)->update([base64_decode('aWRfd2E=') => $value->id, base64_decode('c3RhdHVz') => ($value->status === false) ? base64_decode('Z2FnYWw=') : $value->status]);
                    }
                }

                if (!empty($id_onesignal)) {
                    OneSignal::sendNotificationToUser(
                        $message,
                        $id_onesignal,
                        $url = null,
                        $data = null,
                        $buttons = null,
                        $schedule = null
                    );
                }
            }
        }





        return response()->json([
            base64_decode('bWVzc2FnZQ==') => base64_decode('QmVyaGFzaWwgRGlzZXR1anVp'),
            base64_decode('ZGF0YQ==') => $requests,
        ]);
    }

    public function reject(Request $request)
    {
        $d = AbsenceRequest::where(base64_decode('aWQ='), $request->id)
            ->update([base64_decode('c3RhdHVz') => base64_decode('cmVqZWN0')]);
        $d = AbsenceRequest::where(base64_decode('aWQ='), $request->id)->first();
        if ($request->id) {
            $absenceLog = AbsenceLog::where(base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk'), $request->id)->get();
            foreach ($absenceLog as $da) {
                $deleteAbsence = Absence::where(base64_decode('aWQ='), $da->absence_id)->first();
                if ($deleteAbsence) {
                    Absence::where(base64_decode('aWQ='), $da->absence_id)->delete();
                }
            }


            AbsenceLog::where(base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk'), $request->id)->delete();
        }
        if ($d->category == base64_decode('cGVybWlzc2lvbg==')) {
            $message = base64_decode('SXppbiBhbmRhIHRhbmdnYWwg') . $d->start . base64_decode('IGRpdG9sYWs=');
        } else if ($d->category == base64_decode('ZHV0eQ==')) {
            $message = base64_decode('RGluYXMgYW5kYSB0YW5nZ2FsIA==') . $d->start . base64_decode('IHNhbXBhaSBkZW5nYW4g') . $d->end . base64_decode('IGRpdG9sYWs=');
        } else if ($d->category == base64_decode('bGVhdmU=')) {
            $message = base64_decode('Q3V0aSBhbmRhIHRhbmdnYWwg') . $d->start . base64_decode('IHNhbXBhaSBkZW5nYW4g') . $d->end . base64_decode('IGRpdG9sYWs=');
        } else {
            $message = '';
        }
        MessageLog::create([
            base64_decode('c3RhZmZfaWQ=') => $d->staff_id,
            base64_decode('bWVtbw==') => $message,
            base64_decode('dHlwZQ==') => base64_decode('bWVzc2FnZQ=='),
            base64_decode('c3RhdHVz') => base64_decode('cGVuZGluZw=='),
        ]);

        $admin = Staff::selectRaw(base64_decode('dXNlcnMuKg=='))->where(base64_decode('c3RhZmZzLmlk'), $d->staff_id)->join(base64_decode('dXNlcnM='), base64_decode('dXNlcnMuc3RhZmZfaWQ='), base64_decode('PQ=='), base64_decode('c3RhZmZzLmlk'))->first();
        $id_onesignal = $admin->_id_onesignal;
        $wa_code = date(base64_decode('eQ==')) . date(base64_decode('bQ==')) . date(base64_decode('ZA==')) . date(base64_decode('SA==')) . date(base64_decode('aQ==')) . date(base64_decode('cw=='));
        $wa_data_group = [];
        if ($d->staff_id > 0) {
            $staff = Staff::where(base64_decode('aWQ='), $d->staff_id)->first();
            $phone_no = $staff->phone;
        } else {
            $phone_no = $admin->phone;
        }
        $wa_data = [
            base64_decode('cGhvbmU=') => $this->gantiFormat($phone_no),
            base64_decode('Y3VzdG9tZXJfaWQ=') => null,
            base64_decode('bWVzc2FnZQ==') => $message,
            base64_decode('dGVtcGxhdGVfaWQ=') => '',
            base64_decode('c3RhdHVz') => base64_decode('Z2FnYWw='),
            base64_decode('cmVmX2lk') => $wa_code,
            base64_decode('Y3JlYXRlZF9hdA==') => date(base64_decode('WS1tLWQgaDppOnNh')),
            base64_decode('dXBkYXRlZF9hdA==') => date(base64_decode('WS1tLWQgaDppOnNh'))
        ];
        $wa_data_group[] = $wa_data;
        DB::table(base64_decode('d2FfaGlzdG9yaWVz'))->insert($wa_data);
        $wa_sent = WablasTrait::sendText($wa_data_group);
        $array_merg = [];
        if (!empty(json_decode($wa_sent)->data->messages)) {
            $array_merg = array_merge(json_decode($wa_sent)->data->messages, $array_merg);
        }
        foreach ($array_merg as $key => $value) {
            if (!empty($value->ref_id)) {
                wa_history::where(base64_decode('cmVmX2lk'), $value->ref_id)->update([base64_decode('aWRfd2E=') => $value->id, base64_decode('c3RhdHVz') => ($value->status === false) ? base64_decode('Z2FnYWw=') : $value->status]);
            }
        }

        if (!empty($id_onesignal)) {
            OneSignal::sendNotificationToUser(
                $message,
                $id_onesignal,
                $url = null,
                $data = null,
                $buttons = null,
                $schedule = null
            );
        }

        return response()->json([
            base64_decode('bWVzc2FnZQ==') => base64_decode('QmVyaGFzaWwgRGl0b2xhaw=='),
            base64_decode('ZGF0YQ==') => $d,
        ]);
    }

    public function show(Request $request)
    {

        $requests = AbsenceRequest::selectRaw(base64_decode('YWJzZW5jZV9yZXF1ZXN0cy4qLHdvcmtfdHlwZXMuaWQgYXMgd29ya190eXBlX2lkLCB3b3JrX3R5cGVzLnR5cGUgYXMgdHlwZSwgc3RhZmZzLmlkIGFzIHN0YWZmX2lk'))
            ->join(base64_decode('c3RhZmZz'), base64_decode('YWJzZW5jZV9yZXF1ZXN0cy5zdGFmZl9pZA=='), base64_decode('PQ=='), base64_decode('c3RhZmZzLmlk'))
            ->join(base64_decode('d29ya190eXBlcw=='), base64_decode('d29ya190eXBlcy5pZA=='), base64_decode('PQ=='), base64_decode('c3RhZmZzLndvcmtfdHlwZV9pZA=='))
            ->where(base64_decode('YWJzZW5jZV9yZXF1ZXN0cy5pZA=='), $request->id)->first();

        $request_file = AbsenceRequestLogs::where(base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk'), $request->id)->get();

        return response()->json([
            base64_decode('bWVzc2FnZQ==') => base64_decode('QnVrdGkgRGloYXB1cw=='),
            base64_decode('ZGF0YQ==') => $requests,
            base64_decode('ZGF0YTI=') => $request_file,
        ]);
    }

    public function getLocation()
    {
        $data = [];
        $datas = WorkUnit::get();
        foreach ($datas as $key => $value) {
            $data[] = [base64_decode('aWQ=') => $value->id, base64_decode('bmFtZQ==') => $value->name, base64_decode('Y2hlY2tlZA==') => false];
        }
        return response()->json([
            base64_decode('bWVzc2FnZQ==') => base64_decode('UGVuZ2FqdWFuIFRlcmtpcmlt'),
            base64_decode('ZGF0YQ==') => $data,
        ]);
    }

    public function closeLocation(Request $request)
    {
        $d = AbsenceRequest::where(base64_decode('aWQ='), $request->id)
            ->update([base64_decode('c3RhdHVz') => base64_decode('Y2xvc2U=')]);
        return response()->json([
            base64_decode('bWVzc2FnZQ==') => base64_decode('UGluZGFoIGxva2FzaSBkaXR1dHVw'),
        ]);
    }
}
