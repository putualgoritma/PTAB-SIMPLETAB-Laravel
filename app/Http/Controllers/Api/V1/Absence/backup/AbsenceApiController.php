<?php

namespace App\Http\Controllers\Api\V1\Absence;

use App\Absence;
use App\Absence_categories;
use App\AbsenceLog;
use App\AbsenceProblem;
use App\AbsenceRequest;
use App\AbsenceRequestLogs;
use App\Day;
use App\Holiday;
use App\Http\Controllers\Controller;
use App\MessageLog;
use App\Requests;
use App\Shift;
use App\ShiftGroups;
use App\ShiftPlannerStaff;
use App\ShiftPlannerStaffs;
use App\ShiftStaff;
use App\StaffSpecial;
use App\User;
use App\Visit;
use App\WorkTypeDays;
use App\WorkUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Image;

class AbsenceApiController extends Controller
{

    public function index(Request $request)
    {
        $reguler = '';
        $holiday = '';
        $break = '';
        $duty = '';
        $finish = '';
        $excuse_id = '';
        $absenceOut = [];

        $excuse = [];
        $visit = [];
        $duty = [];
        $extra = [];

        $menuReguler = base64_decode('T0ZG');
        $menuHoliday = base64_decode('T0ZG');
        $menuBreak = base64_decode('T0ZG');
        $menuExcuse = base64_decode('T0ZG');
        $menuVisit = base64_decode('T0ZG');
        $menuDuty = base64_decode('T0ZG');
        $menuFinish = base64_decode('T0ZG');
        $menuExtra = base64_decode('T0ZG');
        $menuLeave = base64_decode('T0ZG');
        $menuWaiting = base64_decode('T0ZG');
        $menuPermission = base64_decode('T0ZG');
        $geofence_off = base64_decode('T0ZG');

        $excuseC = [];
        $visitC = [];
        $extraC = [];


        $absence_excuse = [];


        $geolocation = AbsenceRequest::where(base64_decode('c3RhcnQ='), base64_decode('PD0='), date(base64_decode('WS1tLWQgSDppOnM=')))
            ->where(base64_decode('ZW5k'), base64_decode('Pj0='), date(base64_decode('WS1tLWQgSDppOnM=')))
            ->where(base64_decode('Y2F0ZWdvcnk='), base64_decode('Z2VvbG9jYXRpb25fb2Zm'))
            ->where(base64_decode('c3RhdHVz'), base64_decode('YXBwcm92ZQ=='))
            ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
            ->first();
        if ($geolocation) {
            $geofence_off = base64_decode('T04=');
        }

        $forget = AbsenceRequest::where(base64_decode('c3RhcnQ='), base64_decode('PD0='), date(base64_decode('WS1tLWQgSDppOnM=')))
            ->where(base64_decode('ZW5k'), base64_decode('Pj0='), date(base64_decode('WS1tLWQgSDppOnM=')))
            ->where(base64_decode('Y2F0ZWdvcnk='), base64_decode('Zm9yZ2V0'))
            ->where(base64_decode('c3RhdHVz'), base64_decode('YXBwcm92ZQ=='))
            ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
            ->first();
        if ($forget) {
            $geofence_off = base64_decode('T04=');
        }

        $additionalTime = AbsenceRequest::where(base64_decode('c3RhcnQ='), base64_decode('PD0='), date(base64_decode('WS1tLWQgSDppOnM=')))
            ->where(base64_decode('ZW5k'), base64_decode('Pj0='), date(base64_decode('WS1tLWQgSDppOnM=')))
            ->where(base64_decode('Y2F0ZWdvcnk='), base64_decode('QWRkaXRpb25hbFRpbWU='))
            ->where(base64_decode('c3RhdHVz'), base64_decode('YXBwcm92ZQ=='))
            ->where(base64_decode('dHlwZQ=='), base64_decode('b3V0'))
            ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
            ->first();
        if ($additionalTime) {
            $geofence_off = base64_decode('T04=');
        }

        $fingerprint = base64_decode('T04=');
        $camera = base64_decode('T04=');
        $gps = base64_decode('T04=');

        $coordinat = WorkUnit::join(base64_decode('c3RhZmZz'), base64_decode('c3RhZmZzLndvcmtfdW5pdF9pZA=='), base64_decode('PQ=='), base64_decode('d29ya191bml0cy5pZA=='))
            ->join(base64_decode('d29ya190eXBlcw=='), base64_decode('c3RhZmZzLndvcmtfdHlwZV9pZA=='), base64_decode('PQ=='), base64_decode('d29ya190eXBlcy5pZA=='))
            ->where(base64_decode('c3RhZmZzLmlk'), $request->staff_id)->first();

        $lat = $coordinat->lat;
        $lng = $coordinat->lng;
        $radius = $coordinat->radius;

        $Rlocation = AbsenceRequest::join(base64_decode('d29ya191bml0cw=='), base64_decode('d29ya191bml0cy5pZA=='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9yZXF1ZXN0cy53b3JrX3VuaXRfaWQ='))
            ->where(base64_decode('c3RhcnQ='), base64_decode('PD0='), date(base64_decode('WS1tLWQgSDppOnM=')))
            ->where(base64_decode('ZW5k'), base64_decode('Pj0='), date(base64_decode('WS1tLWQgSDppOnM=')))
            ->where(base64_decode('Y2F0ZWdvcnk='), base64_decode('bG9jYXRpb24='))
            ->where(base64_decode('c3RhdHVz'), base64_decode('YXBwcm92ZQ=='))
            ->where(base64_decode('YWJzZW5jZV9yZXF1ZXN0cy53b3JrX3VuaXRfaWQ='), base64_decode('IT0='), base64_decode('YXBwcm92ZQ=='))
            ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
            ->orderBy(base64_decode('YWJzZW5jZV9yZXF1ZXN0cy5pZA=='), base64_decode('REVTQw=='))
            ->first();

        if ($Rlocation) {

            $lat = $Rlocation->lat;
            $lng = $Rlocation->lng;
            $radius = $Rlocation->radius;
        }


        $staff_special = StaffSpecial::select(base64_decode('c3RhZmZfc3BlY2lhbHMuKg=='))
            ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)->whereDate(base64_decode('ZXhwaXJlZF9kYXRl'), base64_decode('Pj0='), date(base64_decode('WS1tLWQ=')))->first();
        if ($staff_special) {
            $fingerprint = $staff_special->fingerprint;
            $camera = $staff_special->camera;
            $gps = $staff_special->gps;
        }

        if ($gps == base64_decode('T0ZG')) {
            $geofence_off = base64_decode('T04=');
        }
        $problem = AbsenceProblem::where(base64_decode('aWQ='), $coordinat->absence_problem_id)->first();
        $menu = '';
        $leave = AbsenceRequest::where(base64_decode('c3RhcnQ='), base64_decode('PD0='), date(base64_decode('WS1tLWQgSDppOnM=')))
            ->where(base64_decode('ZW5k'), base64_decode('Pj0='), date(base64_decode('WS1tLWQgSDppOnM=')))
            ->where(base64_decode('Y2F0ZWdvcnk='), base64_decode('bGVhdmU='))
            ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
            ->where(function ($query) {
                $query->where(base64_decode('c3RhdHVz'), base64_decode('YXBwcm92ZQ=='))
                    ->orWhere(base64_decode('c3RhdHVz'), base64_decode('YWN0aXZl'));
            })
            ->first();



        $permission = AbsenceRequest::where(base64_decode('c3RhcnQ='), base64_decode('PD0='), date(base64_decode('WS1tLWQgSDppOnM=')))
            ->where(base64_decode('ZW5k'), base64_decode('Pj0='), date(base64_decode('WS1tLWQgSDppOnM=')))
            ->where(base64_decode('Y2F0ZWdvcnk='), base64_decode('cGVybWlzc2lvbg=='))
            ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
            ->where(function ($query) {
                $query->where(base64_decode('c3RhdHVz'), base64_decode('YXBwcm92ZQ=='))
                    ->orWhere(base64_decode('c3RhdHVz'), base64_decode('YWN0aXZl'));
            })
            ->orWhere(base64_decode('c3RhcnQ='), base64_decode('PD0='), date(base64_decode('WS1tLWQgSDppOnM=')))
            ->where(base64_decode('Y2F0ZWdvcnk='), base64_decode('cGVybWlzc2lvbg=='))
            ->where(base64_decode('dHlwZQ=='), base64_decode('c2ljaw=='))
            ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
            ->where(function ($query) {
                $query->where(base64_decode('c3RhdHVz'), base64_decode('YXBwcm92ZQ=='))
                    ->orWhere(base64_decode('c3RhdHVz'), base64_decode('YWN0aXZl'));
            })
            ->first();

        $absence_visit = AbsenceRequest::where(base64_decode('c3RhcnQ='), base64_decode('PD0='), date(base64_decode('WS1tLWQgSDppOnM=')))
            ->where(base64_decode('ZW5k'), base64_decode('Pj0='), date(base64_decode('WS1tLWQgSDppOnM=')))
            ->where(base64_decode('Y2F0ZWdvcnk='), base64_decode('dmlzaXQ='))
            ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
            ->where(function ($query) {
                $query->where(base64_decode('c3RhdHVz'), base64_decode('YXBwcm92ZQ=='))
                    ->orWhere(base64_decode('c3RhdHVz'), base64_decode('YWN0aXZl'));
            })
            ->orderBy(DB::raw(base64_decode('RklFTEQoc3RhdHVzICwgImFjdGl2ZSIsICJhcHByb3ZlIiAp')))
            ->first();
        if ($absence_visit) {
            $visit = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cywgYWJzZW5jZV9yZXF1ZXN0X2lkICwgYWJzZW5jZV9pZCwgYWJzZW5jZV9jYXRlZ29yaWVzLnR5cGUgYXMgYWJzZW5jZV9jYXRlZ29yeV90eXBlLCBhYnNlbmNlX2xvZ3MuZXhwaXJlZF9kYXRlLHNoaWZ0X3BsYW5uZXJfaWQsIHF1ZXVlLCBzdGF0dXNfYWN0aXZlLCBhYnNlbmNlX2NhdGVnb3JpZXMuaWQgYXMgYWJzZW5jZV9jYXRlZ29yeV9pZCwgYWJzZW5jZV9sb2dzLmlkIGFzIGlk'))
                ->leftJoin(base64_decode('YWJzZW5jZXM='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZXMuaWQ='))
                ->leftJoin(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
                ->where(base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk'), $absence_visit->id)
                ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXJ0X2RhdGU='), base64_decode('PD0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                ->where(base64_decode('YWJzZW5jZV9sb2dzLmV4cGlyZWRfZGF0ZQ=='), base64_decode('Pj0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cw=='), base64_decode('PQ=='), 1)
                ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLnR5cGU='), base64_decode('PQ=='), base64_decode('dmlzaXQ='))
                ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLnF1ZXVl'), base64_decode('PQ=='), base64_decode('Mg=='))
                ->orderBy(base64_decode('YWJzZW5jZV9sb2dzLmlk'), base64_decode('REVTQw=='))
                ->first();
            if ($visit) {
                $visit_id = $visit->id;
                $visitEtc =  Visit::where(base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk'), $visit->absence_request_id)->first();
                if ($visitEtc) {
                    $menuVisit = base64_decode('T04=');
                } else {

                    $menuVisit = base64_decode('QUNUSVZF');
                }
            } else {
                $visitC = Absence_categories::where(base64_decode('dHlwZQ=='), base64_decode('dmlzaXQ='))->get();
            }
        }



        $duty = AbsenceRequest::where(base64_decode('c3RhcnQ='), base64_decode('PD0='), date(base64_decode('WS1tLWQgSDppOnM=')))
            ->where(base64_decode('ZW5k'), base64_decode('Pj0='), date(base64_decode('WS1tLWQgSDppOnM=')))
            ->where(base64_decode('Y2F0ZWdvcnk='), base64_decode('ZHV0eQ=='))
            ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
            ->where(function ($query) {
                $query->where(base64_decode('c3RhdHVz'), base64_decode('YXBwcm92ZQ=='))
                    ->orWhere(base64_decode('c3RhdHVz'), base64_decode('YWN0aXZl'));
            })
            ->first();



        $absence_extra_active = Absence::selectRaw(base64_decode('YWJzZW5jZV9sb2dzLmlkIGFzIGlkLCBhYnNlbmNlX2xvZ3MuYWJzZW5jZV9yZXF1ZXN0X2lk'))
            ->join(base64_decode('YWJzZW5jZV9sb2dz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZXMuaWQ='))
            ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cw=='), 1)->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
            ->where(base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), 10)->first();

        if ($absence_extra_active) {
            $menu = base64_decode('T0ZG');

            $extra = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cywgYWJzZW5jZV9yZXF1ZXN0X2lkICwgYWJzZW5jZV9pZCwgYWJzZW5jZV9jYXRlZ29yaWVzLnR5cGUgYXMgYWJzZW5jZV9jYXRlZ29yeV90eXBlLCBhYnNlbmNlX2xvZ3MuZXhwaXJlZF9kYXRlLHNoaWZ0X3BsYW5uZXJfaWQsIHF1ZXVlLCBzdGF0dXNfYWN0aXZlLCBhYnNlbmNlX2NhdGVnb3JpZXMuaWQgYXMgYWJzZW5jZV9jYXRlZ29yeV9pZCwgYWJzZW5jZV9sb2dzLmlkIGFzIGlk'))
                ->leftJoin(base64_decode('YWJzZW5jZXM='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZXMuaWQ='))
                ->leftJoin(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                ->where(base64_decode('YWJzZW5jZV9sb2dzLmlk'), $absence_extra_active->id)
                ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cw=='), base64_decode('PQ=='), 1)
                ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLnR5cGU='), base64_decode('PQ=='), base64_decode('ZXh0cmE='))
                ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLnF1ZXVl'), base64_decode('PQ=='), base64_decode('Mg=='))
                ->orderBy(base64_decode('YWJzZW5jZV9sb2dzLmlk'), base64_decode('REVTQw=='))
                ->first();

            $absence_extra = AbsenceRequest::where(base64_decode('aWQ='), $absence_extra_active->absence_request_id)
                ->first();



            if ($extra) {
                $extra_id = $extra->id;
            } else {
                $extraC = Absence_categories::where(base64_decode('dHlwZQ=='), base64_decode('ZXh0cmE='))->get();
            }
            $menuExtra = base64_decode('T04=');
            if ($absence_extra->type == base64_decode('b3V0c2lkZQ==')) {
                $geofence_off = base64_decode('T04=');
            }

            return response()->json([
                base64_decode('bWVzc2FnZQ==') => base64_decode('U3VjY2Vzcw=='),
                base64_decode('bWVudQ==') => [
                    base64_decode('bWVudVJlZ3VsZXI=') => $menuReguler,
                    base64_decode('bWVudUhvbGlkYXk=') => $menuHoliday,
                    base64_decode('bWVudUJyZWFr') => $menuBreak,
                    base64_decode('bWVudUV4Y3VzZQ==') => $menuExcuse,
                    base64_decode('bWVudUV4dHJh') => $menuExtra,
                    base64_decode('bWVudUR1dHk=') => $menuDuty,
                    base64_decode('bWVudUZpbmlzaA==') =>  $menuFinish,
                    base64_decode('Z2VvbG9jYXRpb25PZmY=') => $geofence_off
                ],
                base64_decode('c2ViZWx1bQ==') => base64_decode('eWFh'),
                base64_decode('ZXh0cmFD') => $extraC,
                base64_decode('ZXh0cmE=') => $extra,
                base64_decode('cmVxdWVzdF9leHRyYQ==') =>  $absence_extra,
                base64_decode('ZGF0ZQ==') => date(base64_decode('WS1tLWQgaDppOnM=')),
                base64_decode('bGF0') => $lat,
                base64_decode('ZmluZ2VyZnJpbnQ=') => $fingerprint,
                base64_decode('c2VsZmll') => $camera,
                base64_decode('Z3Bz') => $gps,
                base64_decode('bG5n') => $lng,
                base64_decode('cmFkaXVz') => $radius,
            ]);
        }




        $showExtra = base64_decode('Tm8=');
        $absence_extra = null;
        $absenIn = Absence::whereDate(base64_decode('Y3JlYXRlZF9hdA=='), base64_decode('PQ=='), date(base64_decode('WS1tLWQ=')))->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)->get();
        foreach ($absenIn as $data) {
            $c_in = $data->absence_logs->where(base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA=='), 1)->where(base64_decode('c3RhdHVz'), 0)->first();
            $c_out = $data->absence_logs->where(base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA=='), 2)->where(base64_decode('c3RhdHVz'), 0)->first();
            if ($c_in && $c_out) {
                $showExtra = base64_decode('WWVz');
            }
        }
        if (count($absenIn) <= 0) {
            $showExtra = base64_decode('WWVz');
        }

        if ($showExtra == base64_decode('WWVz')) {
            $absence_extra = AbsenceRequest::where(base64_decode('c3RhcnQ='), base64_decode('PD0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                ->where(base64_decode('ZW5k'), base64_decode('Pj0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                ->where(base64_decode('Y2F0ZWdvcnk='), base64_decode('ZXh0cmE='))
                ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
                ->where(function ($query) {
                    $query->where(base64_decode('c3RhdHVz'), base64_decode('YXBwcm92ZQ=='))
                        ->orWhere(base64_decode('c3RhdHVz'), base64_decode('YWN0aXZl'));
                })

                ->orderBy(DB::raw(base64_decode('RklFTEQoc3RhdHVzICwgImFjdGl2ZSIsICJhcHByb3ZlIiAp')))
                ->first();
        }


        if ($leave) {


            if ($leave->status == base64_decode('cGVuZGluZw==')) {
                $menuWaiting = base64_decode('T04=');
                return response()->json([
                    base64_decode('bWVzc2FnZQ==') => base64_decode('U3VjY2Vzcw=='),
                    base64_decode('bWVudQ==') => [
                        base64_decode('bWVudVJlZ3VsZXI=') => $menuReguler,
                        base64_decode('bWVudUhvbGlkYXk=') => $menuHoliday,
                        base64_decode('bWVudUJyZWFr') => $menuBreak,
                        base64_decode('bWVudUV4Y3VzZQ==') => $menuExcuse,
                        base64_decode('bWVudUR1dHk=') => $menuDuty,
                        base64_decode('bWVudUZpbmlzaA==') =>  $menuFinish,
                        base64_decode('bWVudUxlYXZl') => $menuLeave,
                        base64_decode('bWVudVdhaXRpbmc=') => $menuWaiting,
                    ],
                    base64_decode('d2FpdGluZ01lc3NhZ2U=') => base64_decode('TWVudW5nZ3UgUGVyc2V0dWp1YW4gQ3V0aQ=='),
                    base64_decode('bGVhdmU=') => $leave,
                    base64_decode('ZGF0ZQ==') => date(base64_decode('WS1tLWQgaDppOnM='))
                ]);
            }
            if ($leave->status == base64_decode('Y2xvc2U=')) {
                $menuWaiting = base64_decode('T04=');
                return response()->json([
                    base64_decode('bWVzc2FnZQ==') => base64_decode('U3VjY2Vzcw=='),
                    base64_decode('bWVudQ==') => [
                        base64_decode('bWVudVJlZ3VsZXI=') => $menuReguler,
                        base64_decode('bWVudUhvbGlkYXk=') => $menuHoliday,
                        base64_decode('bWVudUJyZWFr') => $menuBreak,
                        base64_decode('bWVudUV4Y3VzZQ==') => $menuExcuse,
                        base64_decode('bWVudUR1dHk=') => $menuDuty,
                        base64_decode('bWVudUZpbmlzaA==') =>  $menuFinish,
                        base64_decode('bWVudUxlYXZl') => $menuLeave,
                        base64_decode('bWVudVdhaXRpbmc=') => $menuWaiting
                    ],
                    base64_decode('d2FpdGluZ01lc3NhZ2U=') => base64_decode('TWVudW5nZ3UgUGVyc2V0dWp1YW4gQ3V0aQ=='),
                    base64_decode('bGVhdmU=') => $leave,
                    base64_decode('ZGF0ZQ==') => date(base64_decode('WS1tLWQgaDppOnM='))
                ]);
            } else {
                $menuLeave = base64_decode('T04=');
                return response()->json([
                    base64_decode('bWVzc2FnZQ==') => base64_decode('U3VjY2Vzcw=='),
                    base64_decode('bWVudQ==') => [
                        base64_decode('bWVudVJlZ3VsZXI=') => $menuReguler,
                        base64_decode('bWVudUhvbGlkYXk=') => $menuHoliday,
                        base64_decode('bWVudUJyZWFr') => $menuBreak,
                        base64_decode('bWVudUV4Y3VzZQ==') => $menuExcuse,
                        base64_decode('bWVudUR1dHk=') => $menuDuty,
                        base64_decode('bWVudUZpbmlzaA==') =>  $menuFinish,
                        base64_decode('bWVudUxlYXZl') => $menuLeave
                    ],
                    base64_decode('bGVhdmU=') => $leave,
                    base64_decode('ZGF0ZQ==') => date(base64_decode('WS1tLWQgaDppOnM='))
                ]);
            }
        } else if ($permission) {
            if ($permission->status == base64_decode('cGVuZGluZw==')) {
                $menuWaiting = base64_decode('T04=');
                return response()->json([
                    base64_decode('bWVzc2FnZQ==') => base64_decode('U3VjY2Vzcw=='),
                    base64_decode('bWVudQ==') => [
                        base64_decode('bWVudVJlZ3VsZXI=') => $menuReguler,
                        base64_decode('bWVudUhvbGlkYXk=') => $menuHoliday,
                        base64_decode('bWVudUJyZWFr') => $menuBreak,
                        base64_decode('bWVudUV4Y3VzZQ==') => $menuExcuse,
                        base64_decode('bWVudUR1dHk=') => $menuDuty,
                        base64_decode('bWVudUZpbmlzaA==') =>  $menuFinish,
                        base64_decode('bWVudVdhaXRpbmc=') => $menuWaiting,
                        base64_decode('bWVudVBlcm1pc3Npb24=') =>  $menuPermission
                    ],
                    base64_decode('d2FpdGluZ01lc3NhZ2U=') => base64_decode('TWVudW5nZ3UgUGVyc2V0dWp1YW4gSXppbg=='),
                    base64_decode('ZGF0ZQ==') => date(base64_decode('WS1tLWQgaDppOnM='))
                ]);
            } else if ($permission->status == base64_decode('Y2xvc2U=')) {
                $menuWaiting = base64_decode('T04=');
                return response()->json([
                    base64_decode('bWVzc2FnZQ==') => base64_decode('U3VjY2Vzcw=='),
                    base64_decode('bWVudQ==') => [
                        base64_decode('bWVudVJlZ3VsZXI=') => $menuReguler,
                        base64_decode('bWVudUhvbGlkYXk=') => $menuHoliday,
                        base64_decode('bWVudUJyZWFr') => $menuBreak,
                        base64_decode('bWVudUV4Y3VzZQ==') => $menuExcuse,
                        base64_decode('bWVudUR1dHk=') => $menuDuty,
                        base64_decode('bWVudUZpbmlzaA==') =>  $menuFinish,
                        base64_decode('bWVudVdhaXRpbmc=') => $menuWaiting,
                        base64_decode('bWVudVBlcm1pc3Npb24=') =>  $menuPermission
                    ],
                    base64_decode('d2FpdGluZ01lc3NhZ2U=') => base64_decode('QmVzb2sgQW5kYSBTdWRhaCBCaXNhIE11bGFpIEJla2VyamE='),
                    base64_decode('ZGF0ZQ==') => date(base64_decode('WS1tLWQgaDppOnM='))
                ]);
            } else {

                $menuPermission = base64_decode('T04=');
                if ($permission->type == base64_decode('b3RoZXI=')) {
                    $menuWaiting = base64_decode('T04=');
                    $menuPermission = base64_decode('T0ZG');
                }
                return response()->json([
                    base64_decode('bWVzc2FnZQ==') => base64_decode('U3VjY2Vzcw=='),
                    base64_decode('bWVudQ==') => [
                        base64_decode('bWVudVJlZ3VsZXI=') => $menuReguler,
                        base64_decode('bWVudUhvbGlkYXk=') => $menuHoliday,
                        base64_decode('bWVudUJyZWFr') => $menuBreak,
                        base64_decode('bWVudUV4Y3VzZQ==') => $menuExcuse,
                        base64_decode('bWVudUR1dHk=') => $menuDuty,
                        base64_decode('bWVudUZpbmlzaA==') =>  $menuFinish,
                        base64_decode('bWVudVdhaXRpbmc=') => $menuWaiting,
                        base64_decode('bWVudVBlcm1pc3Npb24=') =>  $menuPermission
                    ],
                    base64_decode('cGVybWlzc2lvbg==') => $permission,
                    base64_decode('d2FpdGluZ01lc3NhZ2U=') => base64_decode('QW5kYSBNYXNpaCBJemlu'),
                    base64_decode('ZGF0ZQ==') => date(base64_decode('WS1tLWQgaDppOnM='))
                ]);
            }
        } else if ($duty) {

            if ($duty->status == base64_decode('cGVuZGluZw==')) {
                $menuWaiting = base64_decode('T04=');
                return response()->json([
                    base64_decode('bWVzc2FnZQ==') => base64_decode('U3VjY2Vzcw=='),
                    base64_decode('ZmluZ2VyZnJpbnQ=') => $fingerprint,
                    base64_decode('c2VsZmll') => $camera,
                    base64_decode('bWVudQ==') => [
                        base64_decode('bWVudVJlZ3VsZXI=') => $menuReguler,
                        base64_decode('bWVudUhvbGlkYXk=') => $menuHoliday,
                        base64_decode('bWVudUJyZWFr') => $menuBreak,
                        base64_decode('bWVudUV4Y3VzZQ==') => $menuExcuse,
                        base64_decode('bWVudUR1dHk=') => $menuDuty,
                        base64_decode('bWVudUZpbmlzaA==') =>  $menuFinish,
                        base64_decode('bWVudVdhaXRpbmc=') => $menuWaiting
                    ],
                    base64_decode('d2FpdGluZ01lc3NhZ2U=') => base64_decode('TWVudW5nZ3UgUGVyc2V0dWp1YW4gRGluYXMgTHVhcg=='),
                    base64_decode('ZHV0eQ==') => $duty,
                    base64_decode('ZGF0ZQ==') => date(base64_decode('WS1tLWQgaDppOnM='))
                ]);
            } else if ($duty->status == base64_decode('Y2xvc2U=')) {
                $menuWaiting = base64_decode('T04=');
                return response()->json([
                    base64_decode('bWVzc2FnZQ==') => base64_decode('U3VjY2Vzcw=='),
                    base64_decode('ZmluZ2VyZnJpbnQ=') => $fingerprint,
                    base64_decode('c2VsZmll') => $camera,
                    base64_decode('bWVudQ==') => [
                        base64_decode('bWVudVJlZ3VsZXI=') => $menuReguler,
                        base64_decode('bWVudUhvbGlkYXk=') => $menuHoliday,
                        base64_decode('bWVudUJyZWFr') => $menuBreak,
                        base64_decode('bWVudUV4Y3VzZQ==') => $menuExcuse,
                        base64_decode('bWVudUR1dHk=') => $menuDuty,
                        base64_decode('bWVudUZpbmlzaA==') =>  $menuFinish,
                        base64_decode('bWVudVdhaXRpbmc=') => $menuWaiting
                    ],
                    base64_decode('d2FpdGluZ01lc3NhZ2U=') => base64_decode('QmVzb2sgQW5kYSBTdWRhaCBCaXNhIE11bGFpIEJla2VyamE='),
                    base64_decode('ZHV0eQ==') => $duty,
                    base64_decode('ZGF0ZQ==') => date(base64_decode('WS1tLWQgaDppOnM='))
                ]);
            } else {
                $AbsenceRequestLogs = AbsenceRequestLogs::where(base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk'), $duty->id)
                    ->first();

                $menuDuty = base64_decode('T04=');
                return response()->json([
                    base64_decode('bWVzc2FnZQ==') => base64_decode('U3VjY2Vzcw=='),
                    base64_decode('ZmluZ2VyZnJpbnQ=') => $fingerprint,
                    base64_decode('c2VsZmll') => $camera,
                    base64_decode('bWVudQ==') => [
                        base64_decode('bWVudVJlZ3VsZXI=') => $menuReguler,
                        base64_decode('bWVudUhvbGlkYXk=') => $menuHoliday,
                        base64_decode('bWVudUJyZWFr') => $menuBreak,
                        base64_decode('bWVudUV4Y3VzZQ==') => $menuExcuse,
                        base64_decode('bWVudUR1dHk=') => $menuDuty,
                        base64_decode('bWVudUZpbmlzaA==') =>  $menuFinish
                    ],
                    base64_decode('QWJzZW5jZVJlcXVlc3RMb2dz') => $AbsenceRequestLogs,
                    base64_decode('ZHV0eQ==') => $duty,
                    base64_decode('Y29vcmRpbmF0') => $coordinat,
                    base64_decode('ZGF0ZQ==') => date(base64_decode('WS1tLWQgaDppOnM='))
                ]);
            }
        } else if ($absence_extra) {
            $menu = base64_decode('T0ZG');

            if ($absence_extra) {
                $extra = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cywgYWJzZW5jZV9yZXF1ZXN0X2lkICwgYWJzZW5jZV9pZCwgYWJzZW5jZV9jYXRlZ29yaWVzLnR5cGUgYXMgYWJzZW5jZV9jYXRlZ29yeV90eXBlLCBhYnNlbmNlX2xvZ3MuZXhwaXJlZF9kYXRlLHNoaWZ0X3BsYW5uZXJfaWQsIHF1ZXVlLCBzdGF0dXNfYWN0aXZlLCBhYnNlbmNlX2NhdGVnb3JpZXMuaWQgYXMgYWJzZW5jZV9jYXRlZ29yeV9pZCwgYWJzZW5jZV9sb2dzLmlkIGFzIGlk'))
                    ->leftJoin(base64_decode('YWJzZW5jZXM='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZXMuaWQ='))
                    ->leftJoin(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                    ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
                    ->where(base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk'), $absence_extra->id)
                    ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cw=='), base64_decode('PQ=='), 1)
                    ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLnR5cGU='), base64_decode('PQ=='), base64_decode('ZXh0cmE='))
                    ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLnF1ZXVl'), base64_decode('PQ=='), base64_decode('Mg=='))
                    ->orderBy(base64_decode('YWJzZW5jZV9sb2dzLmlk'), base64_decode('REVTQw=='))
                    ->first();
            }


            if ($extra) {
                $extra_id = $extra->id;
            } else {
                $extraC = Absence_categories::where(base64_decode('dHlwZQ=='), base64_decode('ZXh0cmE='))->get();
            }
            $menuExtra = base64_decode('T04=');
            if ($absence_extra->type == base64_decode('b3V0c2lkZQ==')) {
                $geofence_off = base64_decode('T04=');
            }

            return response()->json([
                base64_decode('bWVzc2FnZQ==') => base64_decode('U3VjY2Vzcw=='),
                base64_decode('bWVudQ==') => [
                    base64_decode('bWVudVJlZ3VsZXI=') => $menuReguler,
                    base64_decode('bWVudUhvbGlkYXk=') => $menuHoliday,
                    base64_decode('bWVudUJyZWFr') => $menuBreak,
                    base64_decode('bWVudUV4Y3VzZQ==') => $menuExcuse,
                    base64_decode('bWVudUV4dHJh') => $menuExtra,
                    base64_decode('bWVudUR1dHk=') => $menuDuty,
                    base64_decode('bWVudUZpbmlzaA==') =>  $menuFinish,
                    base64_decode('Z2VvbG9jYXRpb25PZmY=') => $geofence_off
                ],
                base64_decode('c2ViZWx1bQ==') => base64_decode('eWFh'),
                base64_decode('ZXh0cmFD') => $extraC,
                base64_decode('ZXh0cmE=') => $extra,
                base64_decode('cmVxdWVzdF9leHRyYQ==') =>  $absence_extra,
                base64_decode('ZGF0ZQ==') => date(base64_decode('WS1tLWQgaDppOnM=')),
                base64_decode('bGF0') => $lat,
                base64_decode('ZmluZ2VyZnJpbnQ=') => $fingerprint,
                base64_decode('c2VsZmll') => $camera,
                base64_decode('Z3Bz') => $gps,
                base64_decode('bG5n') => $lng,
                base64_decode('cmFkaXVz') => $radius,
            ]);
        } else {

            $absenceBreak = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9pZCwgYWJzZW5jZV9sb2dzLnN0YXR1cywgYWJzZW5jZV9sb2dzLmV4cGlyZWRfZGF0ZSxzaGlmdF9wbGFubmVyX2lkLCBxdWV1ZSwgc3RhdHVzX2FjdGl2ZSwgYWJzZW5jZV9jYXRlZ29yaWVzLmlkIGFzIGFic2VuY2VfY2F0ZWdvcnlfaWQ='))
                ->leftJoin(base64_decode('YWJzZW5jZXM='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZXMuaWQ='))
                ->leftJoin(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
                ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXJ0X2RhdGU='), base64_decode('PD0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                ->where(base64_decode('YWJzZW5jZV9sb2dzLmV4cGlyZWRfZGF0ZQ=='), base64_decode('Pj0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cw=='), base64_decode('PQ=='), 0)
                ->where(base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), 1)
                ->orderBy(base64_decode('YWJzZW5jZV9sb2dzLmlk'), base64_decode('REVTQw=='))
                ->first();


            $braeakCheck = null;
            if ($absenceBreak) {
                $absence_excuse = AbsenceRequest::where(base64_decode('c3RhcnQ='), base64_decode('PD0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                    ->where(base64_decode('ZW5k'), base64_decode('Pj0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                    ->where(base64_decode('Y2F0ZWdvcnk='), base64_decode('ZXhjdXNl'))
                    ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
                    ->where(function ($query) {
                        $query->where(base64_decode('c3RhdHVz'), base64_decode('YXBwcm92ZQ=='))
                            ->orWhere(base64_decode('c3RhdHVz'), base64_decode('YWN0aXZl'));
                    })
                    ->orderBy(DB::raw(base64_decode('RklFTEQoc3RhdHVzICwgImFjdGl2ZSIsICJhcHByb3ZlIiAp')))
                    ->first();
                if ($absence_excuse) {
                    $excuse = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cywgYWJzZW5jZV9yZXF1ZXN0X2lkICwgYWJzZW5jZV9pZCwgYWJzZW5jZV9jYXRlZ29yaWVzLnR5cGUgYXMgYWJzZW5jZV9jYXRlZ29yeV90eXBlLCBhYnNlbmNlX2xvZ3MuZXhwaXJlZF9kYXRlLHNoaWZ0X3BsYW5uZXJfaWQsIHF1ZXVlLCBzdGF0dXNfYWN0aXZlLCBhYnNlbmNlX2NhdGVnb3JpZXMuaWQgYXMgYWJzZW5jZV9jYXRlZ29yeV9pZCwgYWJzZW5jZV9sb2dzLmlkIGFzIGlk'))
                        ->leftJoin(base64_decode('YWJzZW5jZXM='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZXMuaWQ='))
                        ->leftJoin(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                        ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
                        ->where(base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk'), $absence_excuse->id)
                        ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXJ0X2RhdGU='), base64_decode('PD0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                        ->where(base64_decode('YWJzZW5jZV9sb2dzLmV4cGlyZWRfZGF0ZQ=='), base64_decode('Pj0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                        ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cw=='), base64_decode('PQ=='), 1)
                        ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLnR5cGU='), base64_decode('PQ=='), base64_decode('ZXhjdXNl'))
                        ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLnF1ZXVl'), base64_decode('PQ=='), base64_decode('Mg=='))
                        ->orderBy(base64_decode('YWJzZW5jZV9sb2dzLmlk'), base64_decode('REVTQw=='))
                        ->first();
                    if ($excuse) {
                        $excuse_id = $excuse->id;
                    } else {
                        $excuseC = Absence_categories::where(base64_decode('dHlwZQ=='), base64_decode('ZXhjdXNl'))->get();
                    }
                    $menuExcuse = base64_decode('T04=');
                }

                $absence_visit = AbsenceRequest::where(base64_decode('c3RhcnQ='), base64_decode('PD0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                    ->where(base64_decode('ZW5k'), base64_decode('Pj0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                    ->where(base64_decode('Y2F0ZWdvcnk='), base64_decode('dmlzaXQ='))
                    ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
                    ->where(function ($query) {
                        $query->where(base64_decode('c3RhdHVz'), base64_decode('YXBwcm92ZQ=='))
                            ->orWhere(base64_decode('c3RhdHVz'), base64_decode('YWN0aXZl'));
                    })
                    ->orderBy(DB::raw(base64_decode('RklFTEQoc3RhdHVzICwgImFjdGl2ZSIsICJhcHByb3ZlIiAp')))
                    ->first();
                if ($absence_visit) {
                    $visit = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cywgYWJzZW5jZV9yZXF1ZXN0X2lkICwgYWJzZW5jZV9pZCwgYWJzZW5jZV9jYXRlZ29yaWVzLnR5cGUgYXMgYWJzZW5jZV9jYXRlZ29yeV90eXBlLCBhYnNlbmNlX2xvZ3MuZXhwaXJlZF9kYXRlLHNoaWZ0X3BsYW5uZXJfaWQsIHF1ZXVlLCBzdGF0dXNfYWN0aXZlLCBhYnNlbmNlX2NhdGVnb3JpZXMuaWQgYXMgYWJzZW5jZV9jYXRlZ29yeV9pZCwgYWJzZW5jZV9sb2dzLmlkIGFzIGlk'))
                        ->leftJoin(base64_decode('YWJzZW5jZXM='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZXMuaWQ='))
                        ->leftJoin(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                        ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
                        ->where(base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk'), $absence_visit->id)
                        ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXJ0X2RhdGU='), base64_decode('PD0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                        ->where(base64_decode('YWJzZW5jZV9sb2dzLmV4cGlyZWRfZGF0ZQ=='), base64_decode('Pj0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                        ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cw=='), base64_decode('PQ=='), 1)
                        ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLnR5cGU='), base64_decode('PQ=='), base64_decode('dmlzaXQ='))
                        ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLnF1ZXVl'), base64_decode('PQ=='), base64_decode('Mg=='))
                        ->orderBy(base64_decode('YWJzZW5jZV9sb2dzLmlk'), base64_decode('REVTQw=='))
                        ->first();
                    if ($visit) {
                        $visit_id = $visit->id;
                        $visitEtc =  Visit::where(base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk'), $visit->absence_request_id)->first();
                        if ($visitEtc) {
                            $menuVisit = base64_decode('T04=');
                        } else {
                            $menuVisit = base64_decode('QUNUSVZF');
                        }
                    } else {
                        $visitC = Absence_categories::where(base64_decode('dHlwZQ=='), base64_decode('dmlzaXQ='))->get();
                        $menuVisit = base64_decode('T04=');
                    }
                }

                $break = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cywgYWJzZW5jZV9jYXRlZ29yaWVzLnR5cGUsIGFic2VuY2VfbG9ncy5leHBpcmVkX2RhdGUsc2hpZnRfcGxhbm5lcl9pZCwgcXVldWUsIHN0YXR1c19hY3RpdmUsIGFic2VuY2VfY2F0ZWdvcmllcy5pZCBhcyBhYnNlbmNlX2NhdGVnb3J5X2lkLCBhYnNlbmNlX2xvZ3MuYWJzZW5jZV9pZCBhcyBhYnNlbmNlX2lkLCBhYnNlbmNlX2xvZ3MuaWQgYXMgaWQ='))
                    ->leftJoin(base64_decode('YWJzZW5jZXM='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZXMuaWQ='))
                    ->leftJoin(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                    ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
                    ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXJ0X2RhdGU='), base64_decode('PD0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                    ->where(base64_decode('YWJzZW5jZV9sb2dzLmV4cGlyZWRfZGF0ZQ=='), base64_decode('Pj0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                    ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLnR5cGU='), base64_decode('PQ=='), base64_decode('YnJlYWs='))
                    ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cw=='), base64_decode('PQ=='), base64_decode('MQ=='))
                    ->orderBy(base64_decode('YWJzZW5jZV9sb2dzLmlk'), base64_decode('QVND'))
                    ->first();
                if ($break) {
                    $menuBreak = base64_decode('T04=');
                }
                $absenceOut = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9sb2dzLmV4cGlyZWRfZGF0ZSwgYWJzZW5jZV9sb2dzLnN0YXJ0X2RhdGUsc2hpZnRfcGxhbm5lcl9pZCwgcXVldWUsIHN0YXR1c19hY3RpdmUsIGFic2VuY2VfY2F0ZWdvcmllcy5pZCBhcyBhYnNlbmNlX2NhdGVnb3J5X2lkLCBhYnNlbmNlcy5pZCBhcyBhYnNlbmNlX2lk'))
                    ->leftJoin(base64_decode('YWJzZW5jZXM='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZXMuaWQ='))
                    ->leftJoin(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                    ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
                    ->where(base64_decode('YWJzZW5jZV9sb2dzLmV4cGlyZWRfZGF0ZQ=='), base64_decode('Pj0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                    ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cw=='), base64_decode('PQ=='), 1)
                    ->where(base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='), $absenceBreak->absence_id)
                    ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'), base64_decode('PQ=='), base64_decode('Mg=='))
                    ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLnR5cGU='), base64_decode('PQ=='), base64_decode('cHJlc2VuY2U='))
                    ->orderBy(base64_decode('YWJzZW5jZV9sb2dzLnN0YXJ0X2RhdGU='), base64_decode('QVND'))
                    ->first();
            }


            if (date(base64_decode('dw==')) == base64_decode('MA==')) {
                $day = base64_decode('Nw==');
            } else {
                $day = date(base64_decode('dw=='));
            }

            $absence = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9sb2dzLmV4cGlyZWRfZGF0ZSxzaGlmdF9wbGFubmVyX2lkLCBxdWV1ZSwgc3RhdHVzX2FjdGl2ZSwgYWJzZW5jZV9jYXRlZ29yaWVzLmlkIGFzIGFic2VuY2VfY2F0ZWdvcnlfaWQsIGFic2VuY2VzLmlkIGFzIGFic2VuY2VfaWQsIGFic2VuY2VfbG9ncy5pZCBhcyBpZA=='))
                ->leftJoin(base64_decode('YWJzZW5jZXM='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZXMuaWQ='))
                ->leftJoin(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
                ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXJ0X2RhdGU='), base64_decode('PD0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                ->where(base64_decode('YWJzZW5jZV9sb2dzLmV4cGlyZWRfZGF0ZQ=='), base64_decode('Pj0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cw=='), base64_decode('PQ=='), 1)
                ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLnR5cGU='), base64_decode('PQ=='), base64_decode('cHJlc2VuY2U='))
                ->orderBy(base64_decode('YWJzZW5jZV9sb2dzLnN0YXJ0X2RhdGU='), base64_decode('QVND'))
                ->first();

            $pengecekanApaAdaAbsenMasuk = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9sb2dzLmV4cGlyZWRfZGF0ZSxzaGlmdF9wbGFubmVyX2lkLCBxdWV1ZSwgc3RhdHVzX2FjdGl2ZSwgYWJzZW5jZV9jYXRlZ29yaWVzLmlkIGFzIGFic2VuY2VfY2F0ZWdvcnlfaWQsIGFic2VuY2VzLmlkIGFzIGFic2VuY2VfaWQsIGFic2VuY2VfbG9ncy5pZCBhcyBpZA=='))
                ->leftJoin(base64_decode('YWJzZW5jZXM='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZXMuaWQ='))
                ->leftJoin(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
                ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXJ0X2RhdGU='), base64_decode('PD0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                ->where(base64_decode('YWJzZW5jZV9sb2dzLmV4cGlyZWRfZGF0ZQ=='), base64_decode('Pj0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cw=='), base64_decode('PQ=='), 0)
                ->where(base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('MQ=='))
                ->orderBy(base64_decode('YWJzZW5jZV9sb2dzLnN0YXJ0X2RhdGU='), base64_decode('REVTQw=='))
                ->first();

            if ($pengecekanApaAdaAbsenMasuk) {
                $pengecekanApaAdaAbsenLanjutan = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9sb2dzLmV4cGlyZWRfZGF0ZSxhYnNlbmNlX2xvZ3Muc3RhcnRfZGF0ZSwgYWJzZW5jZV9sb2dzLnN0YXR1cyBhcyBhYnNlbmNlX2xvZ19zdGF0dXMsIGFic2VuY2VfbG9ncy5leHBpcmVkX2RhdGUsc2hpZnRfcGxhbm5lcl9pZCwgcXVldWUsIHN0YXR1c19hY3RpdmUsIGFic2VuY2VfY2F0ZWdvcmllcy5pZCBhcyBhYnNlbmNlX2NhdGVnb3J5X2lkLCBhYnNlbmNlcy5pZCBhcyBhYnNlbmNlX2lkLCBhYnNlbmNlX2xvZ3MuaWQgYXMgaWQ='))
                    ->leftJoin(base64_decode('YWJzZW5jZXM='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZXMuaWQ='))
                    ->leftJoin(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                    ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
                    ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cw=='), base64_decode('PQ=='), 1)
                    ->where(base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='), $pengecekanApaAdaAbsenMasuk->absence_id)
                    ->orderBy(base64_decode('YWJzZW5jZV9sb2dzLnN0YXJ0X2RhdGU='), base64_decode('QVND'))

                    ->first();

                if (!$pengecekanApaAdaAbsenLanjutan) {
                    $absenceOut = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9sb2dzLmV4cGlyZWRfZGF0ZSwgYWJzZW5jZV9sb2dzLnN0YXJ0X2RhdGUsc2hpZnRfcGxhbm5lcl9pZCwgcXVldWUsIHN0YXR1c19hY3RpdmUsIGFic2VuY2VfY2F0ZWdvcmllcy5pZCBhcyBhYnNlbmNlX2NhdGVnb3J5X2lkLCBhYnNlbmNlcy5pZCBhcyBhYnNlbmNlX2lk'))
                        ->leftJoin(base64_decode('YWJzZW5jZXM='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZXMuaWQ='))
                        ->leftJoin(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                        ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
                        ->where(base64_decode('YWJzZW5jZV9sb2dzLmV4cGlyZWRfZGF0ZQ=='), base64_decode('Pj0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                        ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cw=='), base64_decode('PQ=='), 1)
                        ->where(base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='),  $pengecekanApaAdaAbsenMasuk->absence_id)
                        ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'), base64_decode('PQ=='), base64_decode('Mg=='))
                        ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLnR5cGU='), base64_decode('PQ=='), base64_decode('cHJlc2VuY2U='))
                        ->orderBy(base64_decode('YWJzZW5jZV9sb2dzLnN0YXJ0X2RhdGU='), base64_decode('QVND'))
                        ->first();
                }

                if ($pengecekanApaAdaAbsenLanjutan) {
                    if ($pengecekanApaAdaAbsenLanjutan->start_date <= date(base64_decode('WS1tLWQgSDppOnM=')) && $pengecekanApaAdaAbsenLanjutan->expired_date >= date(base64_decode('WS1tLWQgSDppOnM='))) {
                        $absence = $pengecekanApaAdaAbsenLanjutan;
                    } else {
                        $absence = null;
                    }
                }
            }





            $a1 = base64_decode('MQ==');

            if ($absence) {
                if ($absence->shift_planner_id === 0) {
                    $absen = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLiosIGFic2VuY2VfbG9ncy5pZCBhcyBpZCwgYWJzZW5jZV9pZCwgd29ya190eXBlX2RheXMuc3RhcnQsIHdvcmtfdHlwZV9kYXlzLmVuZA=='))
                        ->join(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='))
                        ->join(base64_decode('d29ya190eXBlX2RheXM='), base64_decode('d29ya190eXBlX2RheXMuYWJzZW5jZV9jYXRlZ29yeV9pZA=='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                        ->join(base64_decode('d29ya190eXBlcw=='), base64_decode('d29ya190eXBlX2RheXMud29ya190eXBlX2lk'), base64_decode('PQ=='), base64_decode('d29ya190eXBlcy5pZA=='))
                        ->where(base64_decode('d29ya190eXBlcy5pZA=='), $coordinat->work_type_id)
                        ->where(base64_decode('YWJzZW5jZV9sb2dzLmlk'), $absence->id)
                        ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLnR5cGU='), base64_decode('PQ=='), base64_decode('cHJlc2VuY2U='))
                        ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cw=='), base64_decode('PQ=='), 1)
                        ->first();
                    $a1 = base64_decode('Mg==');
                    $menuReguler = base64_decode('T04=');
                    $reguler =  $absen;
                } else {
                    $absen = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9sb2dzLiosIGFic2VuY2VfY2F0ZWdvcmllcy50eXBlLCBhYnNlbmNlX2NhdGVnb3JpZXMucXVldWUsc2hpZnRfZ3JvdXBfdGltZXNoZWV0cy5zdGFydCwgc2hpZnRfZ3JvdXBfdGltZXNoZWV0cy5lbmQ='))->leftJoin(base64_decode('YWJzZW5jZXM='), base64_decode('YWJzZW5jZXMuaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='))
                        ->join(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='))
                        ->join(base64_decode('c2hpZnRfZ3JvdXBfdGltZXNoZWV0cw=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'), base64_decode('PQ=='), base64_decode('c2hpZnRfZ3JvdXBfdGltZXNoZWV0cy5hYnNlbmNlX2NhdGVnb3J5X2lk'))
                        ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cw=='), base64_decode('PQ=='), 1)
                        ->where(base64_decode('YWJzZW5jZV9sb2dzLmlk'), $absence->id)
                        ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLnR5cGU='), base64_decode('PQ=='), base64_decode('cHJlc2VuY2U='))
                        ->orderBy(base64_decode('YWJzZW5jZV9sb2dzLmlk'), base64_decode('REVTQw=='))
                        ->first();

                    $a1 = base64_decode('Mg==');
                    $menuReguler = base64_decode('T04=');
                    $reguler =  $absen;
                }
            } else {
                if ($coordinat->type == base64_decode('c2hpZnQ=')) {
                    $a1 = base64_decode('Mw==');
                    $c = ShiftPlannerStaffs::selectRaw(base64_decode('c2hpZnRfcGxhbm5lcl9zdGFmZnMuaWQgYXMgc2hpZnRfcGxhbm5lcl9pZCwgc2hpZnRfcGxhbm5lcl9zdGFmZnMuc2hpZnRfZ3JvdXBfaWQ='))
                        ->join(base64_decode('c2hpZnRfZ3JvdXBz'), base64_decode('c2hpZnRfcGxhbm5lcl9zdGFmZnMuc2hpZnRfZ3JvdXBfaWQ='), base64_decode('PQ=='), base64_decode('c2hpZnRfZ3JvdXBzLmlk'))
                        ->leftJoin(base64_decode('YWJzZW5jZV9sb2dz'), base64_decode('c2hpZnRfcGxhbm5lcl9zdGFmZnMuaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9sb2dzLnNoaWZ0X3BsYW5uZXJfaWQ='))
                        ->where(base64_decode('c2hpZnRfcGxhbm5lcl9zdGFmZnMuc3RhZmZfaWQ='), base64_decode('PQ=='), $request->staff_id)
                        ->whereDate(base64_decode('c2hpZnRfcGxhbm5lcl9zdGFmZnMuc3RhcnQ='), base64_decode('PQ=='), date(base64_decode('WS1tLWQ=')))
                        ->where(base64_decode('YWJzZW5jZV9sb2dzLmlk'), base64_decode('PQ=='), null)
                        ->orderBy(base64_decode('c2hpZnRfZ3JvdXBzLnF1ZXVl'), base64_decode('QVND'))
                        ->get();


                    if (count($c) > 0) {
                        foreach ($c as $item) {

                            $data = [
                                base64_decode('ZGF5X2lk') => $day,
                                base64_decode('c2hpZnRfZ3JvdXBfaWQ=') => $item->shift_group_id,
                                base64_decode('c3RhZmZfaWQ=') => $request->staff_id,
                                base64_decode('Y3JlYXRlZF9hdA==') => date(base64_decode('WS1tLWQ='))
                            ];
                            $absence = Absence::create($data);
                            $list_absence = ShiftGroups::selectRaw(base64_decode('ZHVyYXRpb24sIGR1cmF0aW9uX2V4cCwgYWJzZW5jZV9jYXRlZ29yaWVzLnF1ZXVlLCB0eXBlLCB0aW1lLCBzdGFydCwgYWJzZW5jZV9jYXRlZ29yeV9pZCxzaGlmdF9ncm91cF90aW1lc2hlZXRzLmlkIGFzIHNoaWZ0X2dyb3VwX3RpbWVzaGVldF9pZCA='))
                                ->join(base64_decode('c2hpZnRfZ3JvdXBfdGltZXNoZWV0cw=='), base64_decode('c2hpZnRfZ3JvdXBfdGltZXNoZWV0cy5zaGlmdF9ncm91cF9pZA=='), base64_decode('PQ=='), base64_decode('c2hpZnRfZ3JvdXBzLmlk'))
                                ->join(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('c2hpZnRfZ3JvdXBfdGltZXNoZWV0cy5hYnNlbmNlX2NhdGVnb3J5X2lk'), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                                ->where(base64_decode('c2hpZnRfZ3JvdXBzLmlk'), $item->shift_group_id)
                                ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLnR5cGU='), base64_decode('IT0='), base64_decode('YnJlYWs='))
                                ->orderBy(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLnF1ZXVl'), base64_decode('QVND'))
                                ->get();

                            if ($problem) {
                                $str_date = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(date(base64_decode('WS1tLWQg') . $list_absence[0]->time)));
                                $exp_date = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('KyA=') . (($list_absence[0]->duration - $problem->duration) * 60) . base64_decode('IG1pbnV0ZXM='), strtotime(date(base64_decode('WS1tLWQg') . $list_absence[0]->time))));

                                $i = 0;
                                while ($str_date < $exp_date) {
                                    $i = +$problem->duration;
                                    $str_date = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('KyA=') . $i * 60 . base64_decode('IG1pbnV0ZXM='), strtotime($str_date)));
                                    $message = base64_decode('QW5kYSBEYWxhbSBQZW5nYXdhc2FuLCBCdWthIFVudHVrIEFic2VuIExva2FzaQ==');
                                    MessageLog::create([
                                        base64_decode('c3RhZmZfaWQ=') => $request->staff_id,
                                        base64_decode('bWVtbw==') => $message,
                                        base64_decode('dHlwZQ==') => base64_decode('Y2hlY2s='),
                                        base64_decode('c3RhdHVz') => base64_decode('cGVuZGluZw=='),
                                        base64_decode('Y3JlYXRlZF9hdA==') => $str_date,
                                    ]);
                                }
                            }




                            try {
                                for ($n = 0; $n < count($list_absence); $n++) {
                                    $expired_date = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('KyA=') . (($list_absence[0]->duration + $list_absence[0]->duration_exp) * 60) . base64_decode('IG1pbnV0ZXM='), strtotime(date(base64_decode('WS1tLWQg') . $list_absence[0]->time))));
                                    $timeout = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('KyA=') . ($list_absence[0]->duration * 60) . base64_decode('IG1pbnV0ZXM='), strtotime(date(base64_decode('WS1tLWQg') . $list_absence[0]->time))));
                                    $timein = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(date(base64_decode('WS1tLWQg') . $list_absence[0]->time)));

                                    $status = 0;
                                    if ($n === (count($list_absence) - 1)) {
                                        $status =  1;
                                    } else if ($n === 2 && $list_absence[$n]->type == base64_decode('YnJlYWs=')) {
                                        $status =  1;
                                    } else {
                                        $status =  0;
                                    }

                                    if ($list_absence[$n]->queue == base64_decode('MQ==')) {
                                        $start_date = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('LSA=') . ($list_absence[0]->duration_exp * 60) . base64_decode('IG1pbnV0ZXM='), strtotime(date(base64_decode('WS1tLWQg') . $list_absence[0]->time))));
                                    } else {
                                        $start_date = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('KyA=') . ($list_absence[0]->duration * 60) . base64_decode('IG1pbnV0ZXM='), strtotime(date(base64_decode('WS1tLWQg') . $list_absence[0]->time))));
                                    }
                                    $upload_image = new AbsenceLog;
                                    $upload_image->absence_id = $absence->id;
                                    $upload_image->shift_planner_id = $item->shift_planner_id;
                                    $upload_image->shift_group_timesheet_id = $list_absence[$n]->shift_group_timesheet_id;
                                    $upload_image->timein = $timein;
                                    $upload_image->timeout = $timeout;

                                    $upload_image->start_date = $start_date;
                                    $upload_image->expired_date = $expired_date;
                                    $upload_image->created_at = date(base64_decode('WS1tLWQgSDppOnM='));
                                    $upload_image->updated_at = date(base64_decode('WS1tLWQgSDppOnM='));
                                    $upload_image->status = 1;
                                    $upload_image->absence_category_id =  $list_absence[$n]->absence_category_id;

                                    $upload_image->save();
                                }
                            } catch (QueryException $ex) {
                                return response()->json([
                                    base64_decode('bWVzc2FnZQ==') => base64_decode('Z2FnYWw='),
                                ]);
                            }
                        }
                        if (date(base64_decode('dw==')) == base64_decode('MA==')) {
                            $day = base64_decode('Nw==');
                        } else {
                            $day = date(base64_decode('dw=='));
                        }

                        $absence = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9sb2dzLmV4cGlyZWRfZGF0ZSxzaGlmdF9wbGFubmVyX2lkLCBxdWV1ZSwgc3RhdHVzX2FjdGl2ZSwgYWJzZW5jZV9jYXRlZ29yaWVzLmlkIGFzIGFic2VuY2VfY2F0ZWdvcnlfaWQsIGFic2VuY2VzLmlkIGFzIGFic2VuY2VfaWQsIGFic2VuY2VfbG9ncy5pZCBhcyBpZA=='))
                            ->leftJoin(base64_decode('YWJzZW5jZXM='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZXMuaWQ='))
                            ->leftJoin(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                            ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
                            ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXJ0X2RhdGU='), base64_decode('PD0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                            ->where(base64_decode('YWJzZW5jZV9sb2dzLmV4cGlyZWRfZGF0ZQ=='), base64_decode('Pj0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                            ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cw=='), base64_decode('PQ=='), 1)
                            ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLnR5cGU='), base64_decode('PQ=='), base64_decode('cHJlc2VuY2U='))
                            ->orderBy(base64_decode('YWJzZW5jZV9sb2dzLnN0YXJ0X2RhdGU='), base64_decode('QVND'))
                            ->first();
                        $a1 = base64_decode('MQ==');
                        $absen = '';
                        if ($absence) {
                            if ($absence->shift_planner_id === 0) {
                                $absen = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLiosYWJzZW5jZV9sb2dzLmlkIGFzIGlkLCBhYnNlbmNlX2lkLCB3b3JrX3R5cGVfZGF5cy5zdGFydCwgd29ya190eXBlX2RheXMuZW5k'))
                                    ->join(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='))
                                    ->join(base64_decode('d29ya190eXBlX2RheXM='), base64_decode('d29ya190eXBlX2RheXMuYWJzZW5jZV9jYXRlZ29yeV9pZA=='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                                    ->join(base64_decode('d29ya190eXBlcw=='), base64_decode('d29ya190eXBlX2RheXMud29ya190eXBlX2lk'), base64_decode('PQ=='), base64_decode('d29ya190eXBlcy5pZA=='))
                                    ->where(base64_decode('d29ya190eXBlcy5pZA=='), $coordinat->work_type_id)
                                    ->where(base64_decode('YWJzZW5jZV9sb2dzLmlk'), $absence->id)
                                    ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLnR5cGU='), base64_decode('PQ=='), base64_decode('cHJlc2VuY2U='))
                                    ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cw=='), base64_decode('PQ=='), 1)
                                    ->first();
                                $a1 = base64_decode('Mg==');
                                $menuReguler = base64_decode('T04=');
                                $reguler =  $absen;
                            } else {
                                $absen = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9sb2dzLiosIGFic2VuY2VfY2F0ZWdvcmllcy50eXBlLCBhYnNlbmNlX2NhdGVnb3JpZXMucXVldWUsIHNoaWZ0X2dyb3VwX3RpbWVzaGVldHMuc3RhcnQsIHNoaWZ0X2dyb3VwX3RpbWVzaGVldHMuZW5k'))->leftJoin(base64_decode('YWJzZW5jZXM='), base64_decode('YWJzZW5jZXMuaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='))
                                    ->join(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='))
                                    ->join(base64_decode('c2hpZnRfZ3JvdXBfdGltZXNoZWV0cw=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'), base64_decode('PQ=='), base64_decode('c2hpZnRfZ3JvdXBfdGltZXNoZWV0cy5hYnNlbmNlX2NhdGVnb3J5X2lk'))
                                    ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cw=='), base64_decode('PQ=='), 1)
                                    ->where(base64_decode('YWJzZW5jZV9sb2dzLmlk'), $absence->id)
                                    ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLnR5cGU='), base64_decode('PQ=='), base64_decode('cHJlc2VuY2U='))
                                    ->orderBy(base64_decode('YWJzZW5jZV9sb2dzLmlk'), base64_decode('REVTQw=='))
                                    ->first();
                                $reguler = $absen;
                                $a1 = base64_decode('Mg==');
                                $menuReguler = base64_decode('T04=');
                            }

                            return response()->json([
                                base64_decode('bGF0') => $lat,
                                base64_decode('ZmluZ2VyZnJpbnQ=') => $fingerprint,
                                base64_decode('c2VsZmll') => $camera,
                                base64_decode('Z3Bz') => $gps,
                                base64_decode('bG5n') => $lng,
                                base64_decode('cmFkaXVz') => $radius,
                                base64_decode('cmVndWxlcg==') => $reguler,
                                base64_decode('d29ya190eXBl') => $coordinat->work_type_id,
                                base64_decode('bWVudQ==') => [
                                    base64_decode('bWVudVJlZ3VsZXI=') => $menuReguler,
                                    base64_decode('bWVudUhvbGlkYXk=') => $menuHoliday,
                                    base64_decode('bWVudUJyZWFr') => $menuBreak,
                                    base64_decode('bWVudUV4Y3VzZQ==') => $menuExcuse,
                                    base64_decode('bWVudUR1dHk=') => $menuDuty,
                                    base64_decode('bWVudUZpbmlzaA==') =>  $menuFinish,
                                    base64_decode('Z2VvbG9jYXRpb25PZmY=') => $geofence_off
                                ],
                                base64_decode('YnJlYWs=') => $break,
                                base64_decode('ZGF0ZQ==') => $coordinat->type,
                                base64_decode('YWJzZW5jZQ==') => $absence,
                                base64_decode('dGVzc3M=') => $absen,
                                base64_decode('YTE=') => $a1,
                            ]);
                        }
                    } else {


                        if (!$absenceOut) {
                            if (date(base64_decode('WS1tLWQgSDppOnM=')) > date(base64_decode('WS1tLWQgMjE6MDA6MDA=')) && date(base64_decode('WS1tLWQgSDppOnM=')) < date(base64_decode('WS1tLWQgMjM6NTk6NTk=')) || date(base64_decode('WS1tLWQgSDppOnM=')) > date(base64_decode('WS1tLWQgMDE6MDA6MDA=')) && date(base64_decode('WS1tLWQgSDppOnM=')) < date(base64_decode('WS1tLWQgMDY6MDA6MDA='))) {
                                $absence_extra = AbsenceRequest::where(base64_decode('c3RhcnQ='), base64_decode('PD0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                                    ->where(base64_decode('ZW5k'), base64_decode('Pj0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                                    ->where(base64_decode('Y2F0ZWdvcnk='), base64_decode('ZXh0cmE='))
                                    ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
                                    ->where(function ($query) {
                                        $query->where(base64_decode('c3RhdHVz'), base64_decode('YXBwcm92ZQ=='))
                                            ->orWhere(base64_decode('c3RhdHVz'), base64_decode('YWN0aXZl'))
                                            ->orWhere(base64_decode('c3RhdHVz'), base64_decode('cGVuZGluZw=='));
                                    })

                                    ->orderBy(DB::raw(base64_decode('RklFTEQoc3RhdHVzICwgImFjdGl2ZSIsICJhcHByb3ZlIiAp')))
                                    ->first();
                            } else {
                                $absence_extra = AbsenceRequest::where(base64_decode('c3RhcnQ='), base64_decode('PD0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                                    ->where(base64_decode('ZW5k'), base64_decode('Pj0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                                    ->where(base64_decode('Y2F0ZWdvcnk='), base64_decode('ZXh0cmE='))
                                    ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
                                    ->where(function ($query) {
                                        $query->where(base64_decode('c3RhdHVz'), base64_decode('YXBwcm92ZQ=='))
                                            ->orWhere(base64_decode('c3RhdHVz'), base64_decode('YWN0aXZl'));
                                    })

                                    ->orderBy(DB::raw(base64_decode('RklFTEQoc3RhdHVzICwgImFjdGl2ZSIsICJhcHByb3ZlIiAp')))
                                    ->first();
                            }

                            if ($absence_extra) {
                                $menu = base64_decode('T0ZG');
                                if ($absence_extra) {
                                    $extra = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cywgYWJzZW5jZV9yZXF1ZXN0X2lkICwgYWJzZW5jZV9pZCwgYWJzZW5jZV9jYXRlZ29yaWVzLnR5cGUgYXMgYWJzZW5jZV9jYXRlZ29yeV90eXBlLCBhYnNlbmNlX2xvZ3MuZXhwaXJlZF9kYXRlLHNoaWZ0X3BsYW5uZXJfaWQsIHF1ZXVlLCBzdGF0dXNfYWN0aXZlLCBhYnNlbmNlX2NhdGVnb3JpZXMuaWQgYXMgYWJzZW5jZV9jYXRlZ29yeV9pZCwgYWJzZW5jZV9sb2dzLmlkIGFzIGlk'))
                                        ->leftJoin(base64_decode('YWJzZW5jZXM='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZXMuaWQ='))
                                        ->leftJoin(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                                        ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
                                        ->where(base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk'), $absence_extra->id)
                                        ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXJ0X2RhdGU='), base64_decode('PD0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                                        ->where(base64_decode('YWJzZW5jZV9sb2dzLmV4cGlyZWRfZGF0ZQ=='), base64_decode('Pj0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                                        ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cw=='), base64_decode('PQ=='), 1)
                                        ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLnR5cGU='), base64_decode('PQ=='), base64_decode('ZXh0cmE='))
                                        ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLnF1ZXVl'), base64_decode('PQ=='), base64_decode('Mg=='))
                                        ->orderBy(base64_decode('YWJzZW5jZV9sb2dzLmlk'), base64_decode('REVTQw=='))
                                        ->first();
                                    if ($extra) {
                                        $extra_id = $extra->id;
                                    } else {
                                        $extraC = Absence_categories::where(base64_decode('dHlwZQ=='), base64_decode('ZXh0cmE='))->get();
                                    }
                                    $menuExtra = base64_decode('T04=');
                                }
                                if ($absence_extra->type == base64_decode('b3V0c2lkZQ==')) {
                                    $geofence_off = base64_decode('T04=');
                                }

                                return response()->json([
                                    base64_decode('bWVzc2FnZQ==') => base64_decode('U3VjY2Vzcw=='),
                                    base64_decode('bWVudQ==') => [
                                        base64_decode('bWVudVJlZ3VsZXI=') => $menuReguler,
                                        base64_decode('bWVudUhvbGlkYXk=') => $menuHoliday,
                                        base64_decode('bWVudUJyZWFr') => $menuBreak,
                                        base64_decode('bWVudUV4Y3VzZQ==') => $menuExcuse,
                                        base64_decode('bWVudUV4dHJh') => $menuExtra,
                                        base64_decode('bWVudUR1dHk=') => $menuDuty,
                                        base64_decode('bWVudUZpbmlzaA==') =>  $menuFinish,
                                        base64_decode('Z2VvbG9jYXRpb25PZmY=') => $geofence_off
                                    ],
                                    base64_decode('ZXh0cmFD') => $extraC,
                                    base64_decode('ZXh0cmE=') => $extra,
                                    base64_decode('cmVxdWVzdF9leHRyYQ==') =>  $absence_extra,
                                    base64_decode('ZGF0ZQ==') => date(base64_decode('WS1tLWQgaDppOnM=')),
                                    base64_decode('bGF0') => $lat,
                                    base64_decode('ZmluZ2VyZnJpbnQ=') => $fingerprint,
                                    base64_decode('c2VsZmll') => $camera,
                                    base64_decode('Z3Bz') => $gps,
                                    base64_decode('c2VsZmll') => $camera,
                                    base64_decode('bG5n') => $lng,
                                    base64_decode('cmFkaXVz') => $radius,
                                ]);
                            } else {
                                $menuWaiting = base64_decode('T04=');
                                return response()->json([
                                    base64_decode('bWVzc2FnZQ==') => base64_decode('QWJzZW4gVGVya2lyaW0='),
                                    base64_decode('bWVzc2FnZQ==') => base64_decode('c3VkYWggcHVsYW5n'),
                                    base64_decode('ZGF0YQ==') =>   $c,
                                    base64_decode('cmFkaXVz') => $radius,
                                    base64_decode('cmVndWxlcg==') => $reguler,
                                    base64_decode('YnJlYWs=') => $break,
                                    base64_decode('bWVudQ==') => [
                                        base64_decode('bWVudUJyZWFr') => $menuBreak,
                                        base64_decode('bWVudUV4Y3VzZQ==') => $menuExcuse,
                                        base64_decode('bWVudVJlZ3VsZXI=') => $menuReguler,
                                        base64_decode('bWVudUhvbGlkYXk=') => $menuHoliday,
                                        base64_decode('bWVudUR1dHk=') => $menuDuty,
                                        base64_decode('bWVudUZpbmlzaA==') => $menuFinish,
                                        base64_decode('Z2VvbG9jYXRpb25PZmY=') => $geofence_off,
                                        base64_decode('bWVudVdhaXRpbmc=') => $menuWaiting

                                    ],
                                    base64_decode('d2FpdGluZ01lc3NhZ2U=') => base64_decode('QWJzZW4gU3VkYWggU2VsZXNhaQ=='),
                                ]);
                            }
                        } else {
                            return response()->json([
                                base64_decode('bWVzc2FnZQ==') => base64_decode('QWJzZW4gVGVya2lyaW0='),
                                base64_decode('bWVzc2FnZQ==') => base64_decode('c3Nzc3NzYQ=='),
                                base64_decode('ZXhjdXNlQw==') => $excuseC,
                                base64_decode('ZXhjdXNl') => $excuse,
                                base64_decode('cmVxdWVzdF9leGN1c2U=') =>  $absence_excuse,
                                base64_decode('dmlzaXRD') => $visitC,
                                base64_decode('dmlzaXQ=') => $visit,
                                base64_decode('cmVxdWVzdF92aXNpdA==') =>  $absence_visit,
                                base64_decode('ZGF0YQ==') =>   $c,

                                base64_decode('bGF0') => $lat,
                                base64_decode('ZmluZ2VyZnJpbnQ=') => $fingerprint,
                                base64_decode('c2VsZmll') => $camera,
                                base64_decode('Z3Bz') => $gps,
                                base64_decode('bG5n') => $lng,
                                base64_decode('cmFkaXVz') => $radius,
                                base64_decode('cmVndWxlcg==') => $reguler,
                                base64_decode('YnJlYWs=') => $break,
                                base64_decode('YWJzZW5jZU91dA==') => $absenceOut,
                                base64_decode('bWVudQ==') => [
                                    base64_decode('bWVudUJyZWFr') => $menuBreak,
                                    base64_decode('bWVudUV4Y3VzZQ==') => $menuExcuse,
                                    base64_decode('bWVudVJlZ3VsZXI=') => $menuReguler,
                                    base64_decode('bWVudUhvbGlkYXk=') => $menuHoliday,
                                    base64_decode('bWVudVZpc2l0') => $menuVisit,
                                    base64_decode('bWVudUR1dHk=') => $menuDuty,
                                    base64_decode('bWVudUZpbmlzaA==') => $menuFinish,
                                    base64_decode('Z2VvbG9jYXRpb25PZmY=') => $geofence_off
                                ],
                            ]);
                        }
                    }
                } else {
                    $holiday = Holiday::whereDate(base64_decode('c3RhcnQ='), base64_decode('PD0='), date(base64_decode('WS1tLWQ=')))->whereDate(base64_decode('ZW5k'), base64_decode('Pj0='), date(base64_decode('WS1tLWQ=')))->first();
                    if ($holiday) {
                        $menu = base64_decode('T0ZG');
                        return response()->json([
                            base64_decode('bWVzc2FnZQ==') => base64_decode('U3VjY2Vzcw=='),
                            base64_decode('bWVudQ==') => [
                                base64_decode('bWVudVJlZ3VsZXI=') => $menuReguler,
                                base64_decode('bWVudUhvbGlkYXk=') => $menuHoliday,
                                base64_decode('bWVudUJyZWFr') => $menuBreak,
                                base64_decode('bWVudUV4Y3VzZQ==') => $menuExcuse,
                                base64_decode('bWVudUR1dHk=') => $menuDuty,
                                base64_decode('bWVudUZpbmlzaA==') =>  $menuFinish,
                                base64_decode('Z2VvbG9jYXRpb25PZmY=') => $geofence_off
                            ],
                            base64_decode('ZGF0ZQ==') => date(base64_decode('WS1tLWQgaDppOnM='))
                        ]);
                    } else {
                        $absen = Absence_categories::selectRaw(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLiosIHdvcmtfdHlwZV9kYXlzLnN0YXJ0LCB3b3JrX3R5cGVfZGF5cy5lbmQ='))
                            ->join(base64_decode('d29ya190eXBlX2RheXM='), base64_decode('d29ya190eXBlX2RheXMuYWJzZW5jZV9jYXRlZ29yeV9pZA=='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                            ->join(base64_decode('d29ya190eXBlcw=='), base64_decode('d29ya190eXBlX2RheXMud29ya190eXBlX2lk'), base64_decode('PQ=='), base64_decode('d29ya190eXBlcy5pZA=='))
                            ->where(base64_decode('d29ya190eXBlcy5pZA=='), $coordinat->work_type_id)
                            ->where(base64_decode('ZGF5X2lk'), $day)
                            ->first();


                        if ($absen) {
                            $c = Absence::join(base64_decode('YWJzZW5jZV9sb2dz'), base64_decode('YWJzZW5jZXMuaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='))
                                ->whereDate(base64_decode('YWJzZW5jZXMuY3JlYXRlZF9hdA=='), base64_decode('PQ=='), date(base64_decode('WS1tLWQ=')))
                                ->whereNotIn(base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA=='), [9, 10])
                                ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)->first();
                            if (!$c) {
                                $data = [
                                    base64_decode('ZGF5X2lk') => $day,
                                    base64_decode('c2hpZnRfZ3JvdXBfaWQ=') => $request->shift_group_id,
                                    base64_decode('c3RhZmZfaWQ=') => $request->staff_id,
                                    base64_decode('Y3JlYXRlZF9hdA==') => date(base64_decode('WS1tLWQ='))
                                ];
                                $absence = Absence::create($data);
                                $list_absence = WorkTypeDays::selectRaw(base64_decode('ZHVyYXRpb24sIGR1cmF0aW9uX2V4cCwgcXVldWUsIHR5cGUsIHRpbWUsIHN0YXJ0LCBhYnNlbmNlX2NhdGVnb3J5X2lkLHdvcmtfdHlwZV9kYXlzLmlkIGFzIHdvcmtfdHlwZV9kYXlfaWQg'))
                                    ->join(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('d29ya190eXBlX2RheXMuYWJzZW5jZV9jYXRlZ29yeV9pZA=='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                                    ->where(base64_decode('d29ya190eXBlX2lk'), $coordinat->work_type_id)
                                    ->where(base64_decode('ZGF5X2lk'), $day)
                                    ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLnR5cGU='), base64_decode('PQ=='), base64_decode('cHJlc2VuY2U='))
                                    ->orderBy(base64_decode('cXVldWU='), base64_decode('QVND'))
                                    ->get();

                                if ($problem) {
                                    $str_date = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(date(base64_decode('WS1tLWQg') . $list_absence[0]->time)));
                                    $exp_date = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('KyA=') . (($list_absence[0]->duration - $problem->duration) * 60) . base64_decode('IG1pbnV0ZXM='), strtotime(date(base64_decode('WS1tLWQg') . $list_absence[0]->time))));

                                    $i = 0;
                                    while ($str_date < $exp_date) {
                                        $i = +$problem->duration;
                                        $str_date = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('KyA=') . $i * 60 . base64_decode('IG1pbnV0ZXM='), strtotime($str_date)));
                                        $message = base64_decode('QW5kYSBEYWxhbSBQZW5nYXdhc2FuLCBCdWthIFVudHVrIEFic2VuIExva2FzaQ==');
                                        MessageLog::create([
                                            base64_decode('c3RhZmZfaWQ=') => $request->staff_id,
                                            base64_decode('bWVtbw==') => $message,
                                            base64_decode('dHlwZQ==') => base64_decode('Y2hlY2s='),
                                            base64_decode('c3RhdHVz') => base64_decode('cGVuZGluZw=='),
                                            base64_decode('Y3JlYXRlZF9hdA==') => $str_date,
                                        ]);
                                    }
                                }

                                $expired_date = date(base64_decode('WS1tLWQgSDppOnM='));
                                try {
                                    for ($n = 0; $n < count($list_absence); $n++) {
                                        $expired_date = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('Kw==') . (($list_absence[0]->duration + $list_absence[0]->duration_exp) * 60) . base64_decode('IG1pbnV0ZXM='), strtotime(date(base64_decode('WS1tLWQg') . $list_absence[0]->time))));
                                        $timeout = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('Kw==') . ($list_absence[0]->duration * 60) . base64_decode('IG1pbnV0ZXM='), strtotime(date(base64_decode('WS1tLWQg') . $list_absence[0]->time))));
                                        $timein = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(date(base64_decode('WS1tLWQg') . $list_absence[0]->time)));


                                        if ($list_absence[$n]->queue == base64_decode('MQ==')) {
                                            $start_date = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('LSA=') . ($list_absence[0]->duration_exp * 60) . base64_decode('IG1pbnV0ZXM='), strtotime(date(base64_decode('WS1tLWQg') . $list_absence[0]->time))));
                                        } else {
                                            $start_date = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('KyA=') . ($list_absence[0]->duration * 60) . base64_decode('IG1pbnV0ZXM='), strtotime(date(base64_decode('WS1tLWQg') . $list_absence[0]->time))));
                                        }
                                        $upload_image = new AbsenceLog;
                                        $upload_image->absence_id = $absence->id;
                                        $upload_image->start_date = $start_date;
                                        $upload_image->expired_date = $expired_date;
                                        $upload_image->work_type_day_id = $list_absence[$n]->work_type_day_id;
                                        $upload_image->timein = $timein;
                                        $upload_image->timeout = $timeout;
                                        $upload_image->created_at = date(base64_decode('WS1tLWQgSDppOnM='));
                                        $upload_image->updated_at = date(base64_decode('WS1tLWQgSDppOnM='));
                                        $upload_image->status = 1;
                                        $upload_image->absence_category_id =  $list_absence[$n]->absence_category_id;
                                        $upload_image->save();
                                    }



                                    $absence = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9sb2dzLmV4cGlyZWRfZGF0ZSxzaGlmdF9wbGFubmVyX2lkLCBxdWV1ZSwgc3RhdHVzX2FjdGl2ZSwgYWJzZW5jZV9jYXRlZ29yaWVzLmlkIGFzIGFic2VuY2VfY2F0ZWdvcnlfaWQsIGFic2VuY2VzLmlkIGFzIGFic2VuY2VfaWQsIGFic2VuY2VfbG9ncy5pZCBhcyBpZA=='))
                                        ->leftJoin(base64_decode('YWJzZW5jZXM='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZXMuaWQ='))
                                        ->leftJoin(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                                        ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
                                        ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXJ0X2RhdGU='), base64_decode('PD0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                                        ->where(base64_decode('YWJzZW5jZV9sb2dzLmV4cGlyZWRfZGF0ZQ=='), base64_decode('Pj0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                                        ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cw=='), base64_decode('PQ=='), 1)
                                        ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLnR5cGU='), base64_decode('PQ=='), base64_decode('cHJlc2VuY2U='))
                                        ->orderBy(base64_decode('YWJzZW5jZV9sb2dzLnN0YXJ0X2RhdGU='), base64_decode('QVND'))
                                        ->first();
                                    $a1 = base64_decode('MQ==');
                                    $absen = '';

                                    if ($absence) {
                                        if ($absence->shift_planner_id === 0) {
                                            $absen = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLiosYWJzZW5jZV9sb2dzLmlkIGFzIGlkLCBhYnNlbmNlX2lkLCB3b3JrX3R5cGVfZGF5cy5zdGFydCwgd29ya190eXBlX2RheXMuZW5k'))
                                                ->join(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='))
                                                ->join(base64_decode('d29ya190eXBlX2RheXM='), base64_decode('d29ya190eXBlX2RheXMuYWJzZW5jZV9jYXRlZ29yeV9pZA=='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                                                ->join(base64_decode('d29ya190eXBlcw=='), base64_decode('d29ya190eXBlX2RheXMud29ya190eXBlX2lk'), base64_decode('PQ=='), base64_decode('d29ya190eXBlcy5pZA=='))
                                                ->where(base64_decode('d29ya190eXBlcy5pZA=='), $coordinat->work_type_id)
                                                ->where(base64_decode('YWJzZW5jZV9sb2dzLmlk'), $absence->id)
                                                ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLnR5cGU='), base64_decode('PQ=='), base64_decode('cHJlc2VuY2U='))
                                                ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cw=='), base64_decode('PQ=='), 1)
                                                ->first();
                                            $a1 = base64_decode('Mg==');
                                            $menuReguler = base64_decode('T04=');
                                            $reguler =  $absen;
                                        } else {
                                            $absen = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9sb2dzLiosIHNoaWZ0X2dyb3VwX3RpbWVzaGVldHMuc3RhcnQsIHNoaWZ0X2dyb3VwX3RpbWVzaGVldHMuZW5k'))
                                                ->leftJoin(base64_decode('YWJzZW5jZXM='), base64_decode('YWJzZW5jZXMuaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='))
                                                ->join(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='))
                                                ->join(base64_decode('c2hpZnRfZ3JvdXBfdGltZXNoZWV0cw=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'), base64_decode('PQ=='), base64_decode('c2hpZnRfZ3JvdXBfdGltZXNoZWV0cy5hYnNlbmNlX2NhdGVnb3J5X2lk'))
                                                ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cw=='), base64_decode('PQ=='), 1)
                                                ->where(base64_decode('YWJzZW5jZV9sb2dzLmlk'), $absence->id)
                                                ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLnR5cGU='), base64_decode('PQ=='), base64_decode('cHJlc2VuY2U='))
                                                ->orderBy(base64_decode('YWJzZW5jZV9sb2dzLmlk'), base64_decode('REVTQw=='))
                                                ->first();

                                            $a1 = base64_decode('Mg==');
                                        }
                                        return response()->json([
                                            base64_decode('bGF0') => $lat,
                                            base64_decode('ZmluZ2VyZnJpbnQ=') => $fingerprint,
                                            base64_decode('c2VsZmll') => $camera,
                                            base64_decode('Z3Bz') => $gps,
                                            base64_decode('bG5n') => $lng,
                                            base64_decode('cmFkaXVz') => $radius,
                                            base64_decode('cmVndWxlcg==') => $reguler,
                                            base64_decode('d29ya190eXBl') => $coordinat->work_type_id,
                                            base64_decode('bWVudQ==') => [
                                                base64_decode('bWVudVJlZ3VsZXI=') => $menuReguler,
                                                base64_decode('bWVudUhvbGlkYXk=') => $menuHoliday,
                                                base64_decode('bWVudUJyZWFr') => $menuBreak,
                                                base64_decode('bWVudUV4Y3VzZQ==') => $menuExcuse,
                                                base64_decode('bWVudUR1dHk=') => $menuDuty,
                                                base64_decode('bWVudUZpbmlzaA==') =>  $menuFinish,
                                                base64_decode('Z2VvbG9jYXRpb25PZmY=') => $geofence_off,
                                            ],
                                            base64_decode('YnJlYWs=') => $break,
                                            base64_decode('ZGF0ZQ==') => $coordinat->type,
                                            base64_decode('YWJzZW5jZQ==') => $absence,
                                            base64_decode('dGVzc3M=') => $absen,
                                            base64_decode('YTE=') => $a1,
                                        ]);
                                    }
                                } catch (QueryException $ex) {
                                    return response()->json([
                                        base64_decode('bWVzc2FnZQ==') => base64_decode('Z2FnYWw='),
                                    ]);
                                }
                            }


                            if (!$absenceOut) {
                                if (date(base64_decode('WS1tLWQgSDppOnM=')) > date(base64_decode('WS1tLWQgMjE6MDA6MDA=')) && date(base64_decode('WS1tLWQgSDppOnM=')) < date(base64_decode('WS1tLWQgMjM6NTk6NTk=')) || date(base64_decode('WS1tLWQgSDppOnM=')) > date(base64_decode('WS1tLWQgMDE6MDA6MDA=')) && date(base64_decode('WS1tLWQgSDppOnM=')) < date(base64_decode('WS1tLWQgMDY6MDA6MDA='))) {
                                    $absence_extra = AbsenceRequest::where(base64_decode('c3RhcnQ='), base64_decode('PD0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                                        ->where(base64_decode('ZW5k'), base64_decode('Pj0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                                        ->where(base64_decode('Y2F0ZWdvcnk='), base64_decode('ZXh0cmE='))
                                        ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
                                        ->where(function ($query) {
                                            $query->where(base64_decode('c3RhdHVz'), base64_decode('YXBwcm92ZQ=='))
                                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('YWN0aXZl'))
                                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('cGVuZGluZw=='));
                                        })

                                        ->orderBy(DB::raw(base64_decode('RklFTEQoc3RhdHVzICwgImFjdGl2ZSIsICJhcHByb3ZlIiAp')))
                                        ->first();
                                } else {
                                    $absence_extra = AbsenceRequest::where(base64_decode('c3RhcnQ='), base64_decode('PD0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                                        ->where(base64_decode('ZW5k'), base64_decode('Pj0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                                        ->where(base64_decode('Y2F0ZWdvcnk='), base64_decode('ZXh0cmE='))
                                        ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
                                        ->where(function ($query) {
                                            $query->where(base64_decode('c3RhdHVz'), base64_decode('YXBwcm92ZQ=='))
                                                ->orWhere(base64_decode('c3RhdHVz'), base64_decode('YWN0aXZl'));
                                        })

                                        ->orderBy(DB::raw(base64_decode('RklFTEQoc3RhdHVzICwgImFjdGl2ZSIsICJhcHByb3ZlIiAp')))
                                        ->first();
                                }

                                if ($absence_extra) {
                                    $menu = base64_decode('T0ZG');
                                    if ($absence_extra) {
                                        $extra = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cywgYWJzZW5jZV9yZXF1ZXN0X2lkICwgYWJzZW5jZV9pZCwgYWJzZW5jZV9jYXRlZ29yaWVzLnR5cGUgYXMgYWJzZW5jZV9jYXRlZ29yeV90eXBlLCBhYnNlbmNlX2xvZ3MuZXhwaXJlZF9kYXRlLHNoaWZ0X3BsYW5uZXJfaWQsIHF1ZXVlLCBzdGF0dXNfYWN0aXZlLCBhYnNlbmNlX2NhdGVnb3JpZXMuaWQgYXMgYWJzZW5jZV9jYXRlZ29yeV9pZCwgYWJzZW5jZV9sb2dzLmlkIGFzIGlk'))
                                            ->leftJoin(base64_decode('YWJzZW5jZXM='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZXMuaWQ='))
                                            ->leftJoin(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                                            ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
                                            ->where(base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk'), $absence_extra->id)
                                            ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXJ0X2RhdGU='), base64_decode('PD0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                                            ->where(base64_decode('YWJzZW5jZV9sb2dzLmV4cGlyZWRfZGF0ZQ=='), base64_decode('Pj0='), date(base64_decode('WS1tLWQgSDppOnM=')))
                                            ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cw=='), base64_decode('PQ=='), 1)
                                            ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLnR5cGU='), base64_decode('PQ=='), base64_decode('ZXh0cmE='))
                                            ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLnF1ZXVl'), base64_decode('PQ=='), base64_decode('Mg=='))
                                            ->orderBy(base64_decode('YWJzZW5jZV9sb2dzLmlk'), base64_decode('REVTQw=='))
                                            ->first();
                                        if ($extra) {
                                            $extra_id = $extra->id;
                                        } else {
                                            $extraC = Absence_categories::where(base64_decode('dHlwZQ=='), base64_decode('ZXh0cmE='))->get();
                                        }
                                        $menuExtra = base64_decode('T04=');
                                    }
                                    if ($absence_extra->type == base64_decode('b3V0c2lkZQ==')) {
                                        $geofence_off = base64_decode('T04=');
                                    }

                                    return response()->json([
                                        base64_decode('bWVzc2FnZQ==') => base64_decode('U3VjY2Vzcw=='),
                                        base64_decode('bWVudQ==') => [
                                            base64_decode('bWVudVJlZ3VsZXI=') => $menuReguler,
                                            base64_decode('bWVudUhvbGlkYXk=') => $menuHoliday,
                                            base64_decode('bWVudUJyZWFr') => $menuBreak,
                                            base64_decode('bWVudUV4Y3VzZQ==') => $menuExcuse,
                                            base64_decode('bWVudUV4dHJh') => $menuExtra,
                                            base64_decode('bWVudUR1dHk=') => $menuDuty,
                                            base64_decode('bWVudUZpbmlzaA==') =>  $menuFinish,
                                            base64_decode('Z2VvbG9jYXRpb25PZmY=') => $geofence_off
                                        ],
                                        base64_decode('ZXh0cmFD') => $extraC,
                                        base64_decode('ZXh0cmE=') => $extra,
                                        base64_decode('cmVxdWVzdF9leHRyYQ==') =>  $absence_extra,
                                        base64_decode('ZGF0ZQ==') => date(base64_decode('WS1tLWQgaDppOnM=')),
                                        base64_decode('bGF0') => $lat,
                                        base64_decode('ZmluZ2VyZnJpbnQ=') => $fingerprint,
                                        base64_decode('c2VsZmll') => $camera,
                                        base64_decode('Z3Bz') => $gps,
                                        base64_decode('bG5n') => $lng,
                                        base64_decode('cmFkaXVz') => $radius,
                                    ]);
                                } else {
                                    $menuWaiting = base64_decode('T04=');
                                    return response()->json([
                                        base64_decode('bWVzc2FnZQ==') => base64_decode('QWJzZW4gVGVya2lyaW0='),
                                        base64_decode('bWVzc2FnZQ==') => base64_decode('c3VkYWggcHVsYW5n'),
                                        base64_decode('ZGF0YQ==') =>   $c,
                                        base64_decode('cmFkaXVz') => $radius,
                                        base64_decode('cmVndWxlcg==') => $reguler,
                                        base64_decode('YnJlYWs=') => $break,
                                        base64_decode('bWVudQ==') => [
                                            base64_decode('bWVudUJyZWFr') => $menuBreak,
                                            base64_decode('bWVudUV4Y3VzZQ==') => $menuExcuse,
                                            base64_decode('bWVudVJlZ3VsZXI=') => $menuReguler,
                                            base64_decode('bWVudUhvbGlkYXk=') => $menuHoliday,
                                            base64_decode('bWVudUR1dHk=') => $menuDuty,
                                            base64_decode('bWVudUZpbmlzaA==') => $menuFinish,
                                            base64_decode('Z2VvbG9jYXRpb25PZmY=') => $geofence_off,
                                            base64_decode('bWVudVdhaXRpbmc=') => $menuWaiting
                                        ],
                                        base64_decode('d2FpdGluZ01lc3NhZ2U=') => base64_decode('QWJzZW4gU3VkYWggU2VsZXNhaQ=='),
                                    ]);
                                }
                            } else {
                                return response()->json([
                                    base64_decode('bWVzc2FnZQ==') => base64_decode('QWJzZW4gVGVya2lyaW0='),
                                    base64_decode('bWVzc2FnZQ==') => base64_decode('c3Nzc3NzYQ=='),
                                    base64_decode('ZXhjdXNlQw==') => $excuseC,
                                    base64_decode('ZXhjdXNl') => $excuse,
                                    base64_decode('cmVxdWVzdF9leGN1c2U=') =>  $absence_excuse,
                                    base64_decode('dmlzaXRD') => $visitC,
                                    base64_decode('dmlzaXQ=') => $visit,
                                    base64_decode('cmVxdWVzdF92aXNpdA==') =>  $absence_visit,
                                    base64_decode('ZGF0YQ==') =>   $c,

                                    base64_decode('bGF0') => $lat,
                                    base64_decode('ZmluZ2VyZnJpbnQ=') => $fingerprint,
                                    base64_decode('c2VsZmll') => $camera,
                                    base64_decode('Z3Bz') => $gps,
                                    base64_decode('bG5n') => $lng,
                                    base64_decode('cmFkaXVz') => $radius,
                                    base64_decode('cmVndWxlcg==') => $reguler,
                                    base64_decode('YnJlYWs=') => $break,
                                    base64_decode('YWJzZW5jZU91dA==') => $absenceOut,
                                    base64_decode('bWVudQ==') => [
                                        base64_decode('bWVudUJyZWFr') => $menuBreak,
                                        base64_decode('bWVudUV4Y3VzZQ==') => $menuExcuse,
                                        base64_decode('bWVudVJlZ3VsZXI=') => $menuReguler,
                                        base64_decode('bWVudUhvbGlkYXk=') => $menuHoliday,
                                        base64_decode('bWVudVZpc2l0') => $menuVisit,
                                        base64_decode('bWVudUR1dHk=') => $menuDuty,
                                        base64_decode('bWVudUZpbmlzaA==') => $menuFinish,
                                        base64_decode('Z2VvbG9jYXRpb25PZmY=') => $geofence_off
                                    ],
                                ]);
                            }
                        }
                        $a1 = base64_decode('NA==');
                    }
                }
            }

            return response()->json([
                base64_decode('bWVzc2FnZQ==') => base64_decode('QWJzZW4gVGVya2lyaW0='),
                base64_decode('bWVzc2FnZQ==') => base64_decode('c3Nzc3NzYQ=='),

                base64_decode('ZXhjdXNlQw==') => $excuseC,
                base64_decode('ZXhjdXNl') => $excuse,
                base64_decode('cmVxdWVzdF9leGN1c2U=') =>  $absence_excuse,

                base64_decode('dmlzaXRD') => $visitC,
                base64_decode('dmlzaXQ=') => $visit,
                base64_decode('cmVxdWVzdF92aXNpdA==') =>  $absence_visit,

                base64_decode('bGF0') => $lat,
                base64_decode('ZmluZ2VyZnJpbnQ=') => $fingerprint,
                base64_decode('c2VsZmll') => $camera,
                base64_decode('Z3Bz') => $gps,
                base64_decode('bG5n') => $lng,
                base64_decode('cmFkaXVz') => $radius,
                base64_decode('cmVndWxlcg==') => $reguler,
                base64_decode('YnJlYWs=') => $break,
                base64_decode('YWJzZW5jZU91dA==') => $absenceOut,
                base64_decode('bWVudQ==') => [
                    base64_decode('bWVudUJyZWFr') => $menuBreak,
                    base64_decode('bWVudUV4Y3VzZQ==') => $menuExcuse,
                    base64_decode('bWVudVJlZ3VsZXI=') => $menuReguler,
                    base64_decode('bWVudUhvbGlkYXk=') => $menuHoliday,
                    base64_decode('bWVudVZpc2l0') => $menuVisit,
                    base64_decode('bWVudUR1dHk=') => $menuDuty,
                    base64_decode('bWVudUZpbmlzaA==') => $menuFinish,
                    base64_decode('Z2VvbG9jYXRpb25PZmY=') => $geofence_off
                ],
            ]);
        }
    }

    public function store(Request $request)
    {


        $img_path = base64_decode('L2ltYWdlcy9hYnNlbmNl');
        $basepath = str_replace(base64_decode('bGFyYXZlbC1zaW1wbGV0YWI='), base64_decode('cHVibGljX2h0bWwvc2ltcGxldGFiYWRtaW4v'), \base_path());
        $responseImage = '';
        $data_image = '';

        if (date(base64_decode('dw==')) == base64_decode('MA==')) {
            $day = base64_decode('Nw==');
        } else {
            $day = date(base64_decode('dw=='));
        }


        if ($request->fingerprintError == base64_decode('eWVz')) {
            Absence::where(base64_decode('aWQ='), $request->absence_id)->update([
                base64_decode('c3RhdHVzX2FjdGl2ZQ==') =>  base64_decode('MQ==')
            ]);
        }


        if ($request->file(base64_decode('aW1hZ2U='))) {
            $resource_image = $request->file(base64_decode('aW1hZ2U='));
            $name_image = $request->staff_id;
            $file_ext_image = $request->file(base64_decode('aW1hZ2U='))->extension();

            $name_image = $img_path . base64_decode('Lw==') . $name_image . base64_decode('LQ==') . date(base64_decode('WS1tLWQgaDppOnM=')) . base64_decode('LWFic2VuY2Uu') . $file_ext_image;

            $image = $request->file(base64_decode('aW1hZ2U='));

            $imgFile = Image::make($image->getRealPath());

            $imgFile->insert($basepath . base64_decode('L2ltYWdlcy9Mb2dvLnBuZw=='), base64_decode('Ym90dG9tLXJpZ2h0'), 10, 10);
            $imgFile->orientate();
            $imgFile->text('' . Date(base64_decode('WS1tLWQgSDppOnM=')) . base64_decode('IGxhdCA6IA==') . $request->lat . base64_decode('IGxuZyA6IA==') . $request->lng, 10, 10, function ($font) {
                $font->file(str_replace(base64_decode('bGFyYXZlbC1zaW1wbGV0YWI='), base64_decode('cHVibGljX2h0bWwvc2ltcGxldGFiYWRtaW4v'), \base_path()) . base64_decode('L2ZvbnQvVGl0YW5pYS1SZWd1bGFyLnR0Zg=='));
                $font->size(14);
                $font->color(base64_decode('IzAwMDAwMA=='));
                $font->valign(base64_decode('dG9w'));
            })->save($basepath . base64_decode('Lw==') . $name_image);



            $data_image = $name_image;
        }


        if ($responseImage != '') {
            return response()->json([
                base64_decode('bWVzc2FnZQ==') => $responseImage,
            ]);
        }

        $absenceBefore = AbsenceLog::select(base64_decode('cmVnaXN0ZXI='))
            ->leftJoin(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
            ->where(base64_decode('YWJzZW5jZV9pZA=='), $request->absence_id)
            ->where(base64_decode('cXVldWU='), base64_decode('MQ=='))
            ->where(base64_decode('dHlwZQ=='), base64_decode('cHJlc2VuY2U='))
            ->first();

        $duration = 0;
        if ($request->queue == base64_decode('Mg==')) {
            $absenceBefore2 = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9sb2dzLmlkLCByZWdpc3RlciwgYWJzZW5jZV9sb2dzLmV4cGlyZWRfZGF0ZSwgYWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='))
                ->join(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                ->where(base64_decode('YWJzZW5jZV9pZA=='), $request->absence_id)
                ->where(base64_decode('dHlwZQ=='), $request->type)
                ->where(base64_decode('cXVldWU='), base64_decode('MQ=='))
                ->first();
            $day3 = $absenceBefore2->register;
            $day3 = strtotime($day3);
            $day4 = date(base64_decode('WS1tLWQgSDppOnM='));
            $day4 = strtotime($day4);

            $duration = ($day4 - $day3) / 3600;
        }

        if ($absenceBefore != null) {
            $day1 = $absenceBefore->register;
        } else {
            $day1 = $absenceBefore->register;
        }

        if ($request->type == base64_decode('cHJlc2VuY2U=') && $request->queue == base64_decode('MQ==')) {
            $outDuration = 0;
        } else {
            $day1 = strtotime($day1);
            $day2 = date(base64_decode('WS1tLWQgSDppOnM='));
            $day2 = strtotime($day2);

            $outDuration = ($day2 - $day1) / 3600;
        }

        $late = 0;
        $early = 0;
        try {
            $upload_image = AbsenceLog::where(base64_decode('aWQ='), $request->id)->first();
            if ($request->type == base64_decode('cHJlc2VuY2U=')) {
                if (date(base64_decode('WS1tLWQgSDppOnM=')) > $upload_image->timein) {
                    $dayL1 = $upload_image->timein;
                    $dayL1 = strtotime($dayL1);
                    $dayL2 = date(base64_decode('WS1tLWQgSDppOnM='));
                    $dayL2 = strtotime($dayL2);

                    $late = ($dayL2 - $dayL1) / 3600;
                } else {
                    $dayE1 = $upload_image->timein;
                    $dayE1 = strtotime($dayE1);
                    $dayE2 = date(base64_decode('WS1tLWQgSDppOnM='));
                    $dayE2 = strtotime($dayE2);

                    $early = ($dayE1 - $dayE2) / 3600;
                }
            }


            $upload_image->late = $late;
            $upload_image->early = $early;


            $upload_image->image = $data_image;
            $upload_image->created_by_staff_id = $request->staff_id;
            $upload_image->updated_by_staff_id = $request->staff_id;
            $upload_image->register = date(base64_decode('WS1tLWQgSDppOnM='));
            $upload_image->duration = $duration;

            $upload_image->register = date(base64_decode('WS1tLWQgSDppOnM='));
            $upload_image->updated_at = date(base64_decode('WS1tLWQgSDppOnM='));
            $upload_image->lat = $request->lat;
            $upload_image->lng = $request->lng;
            $upload_image->status =  0;
            $upload_image->accuracy = $request->accuracy;
            $upload_image->distance = $request->distance;

            $upload_image->save();

            if ($request->queue == base64_decode('MQ==')) {
                $end = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9sb2dzLmlkLCBhYnNlbmNlX2xvZ3MuZXhwaXJlZF9kYXRlLCBhYnNlbmNlX2xvZ3MuYWJzZW5jZV9pZA=='))
                    ->join(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                    ->where(base64_decode('YWJzZW5jZV9pZA=='), $request->absence_id)
                    ->where(base64_decode('dHlwZQ=='), $request->type)
                    ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cw=='), base64_decode('MQ=='))
                    ->where(base64_decode('cXVldWU='), base64_decode('Mg=='))
                    ->first();
                AbsenceLog::where(base64_decode('aWQ='), $end->id)->update([base64_decode('cmVnaXN0ZXI=') => date(base64_decode('WS1tLWQgSDppOnM='))]);
            }

            if ($upload_image->absence_request_id != '' && $upload_image->absence_request_id != null) {
                AbsenceRequest::where(base64_decode('aWQ='), $upload_image->absence_request_id)->update([base64_decode('c3RhdHVz') => base64_decode('Y2xvc2U=')]);
            }

            if ($request->type != base64_decode('ZXh0cmE=')) {
                $out = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9sb2dzLmlkLCBhYnNlbmNlX2xvZ3MuZXhwaXJlZF9kYXRlLCBhYnNlbmNlX2xvZ3MuYWJzZW5jZV9pZA=='))->join(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                    ->where(base64_decode('YWJzZW5jZV9pZA=='), $request->absence_id)
                    ->where(base64_decode('dHlwZQ=='), base64_decode('cHJlc2VuY2U='))
                    ->orderBy(base64_decode('cXVldWU='), base64_decode('REVTQw=='))
                    ->first();
                AbsenceLog::where(base64_decode('aWQ='), $out->id)->update([
                    base64_decode('cmVnaXN0ZXI=') => date(base64_decode('WS1tLWQgSDppOnM=')),
                    base64_decode('ZHVyYXRpb24=') => $outDuration
                ]);
            }

            if ($request->queue == base64_decode('MQ==') && $request->type == base64_decode('cHJlc2VuY2U=')) {
                $check = AbsenceLog::where(base64_decode('YWJzZW5jZV9pZA=='), $out->absence_id)
                    ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA=='), 3)
                    ->first();
                if (!$check) {
                    AbsenceLog::create([

                        base64_decode('YWJzZW5jZV9pZA==') => $out->absence_id,
                        base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA==') => 3,
                        base64_decode('c3RhdHVz') => base64_decode('MQ=='),
                        base64_decode('ZXhwaXJlZF9kYXRl') => $out->expired_date,
                        base64_decode('c3RhcnRfZGF0ZQ==') => date(base64_decode('WS1tLWQgSDppOjEw')),

                    ]);
                    AbsenceLog::create([

                        base64_decode('YWJzZW5jZV9pZA==') => $out->absence_id,
                        base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA==') => 4,
                        base64_decode('c3RhdHVz') => base64_decode('MQ=='),
                        base64_decode('ZXhwaXJlZF9kYXRl') => $out->expired_date,
                        base64_decode('c3RhcnRfZGF0ZQ==') => date(base64_decode('WS1tLWQgSDppOjEx')),

                    ]);
                }
            }


            return response()->json([
                base64_decode('bWVzc2FnZQ==') => base64_decode('QWJzZW4gVGVya2lyaW0='),
                base64_decode('ZGF0YQ==') => $upload_image,
            ]);
        } catch (QueryException $ex) {
            return response()->json([
                base64_decode('bWVzc2FnZQ==') => base64_decode('Z2FnYWw='),
            ]);
        }
    }


    public function storeNew(Request $request)
    {


        $img_path = base64_decode('L2ltYWdlcy9hYnNlbmNl');
        $basepath = str_replace(base64_decode('bGFyYXZlbC1zaW1wbGV0YWI='), base64_decode('cHVibGljX2h0bWwvc2ltcGxldGFiYWRtaW4v'), \base_path());
        $responseImage = '';
        $data_image = '';

        if (date(base64_decode('dw==')) == base64_decode('MA==')) {
            $day = base64_decode('Nw==');
        } else {
            $day = date(base64_decode('dw=='));
        }


        if ($request->fingerprintError == base64_decode('eWVz')) {
            Absence::where(base64_decode('aWQ='), $request->absence_id)->update([
                base64_decode('c3RhdHVzX2FjdGl2ZQ==') =>  base64_decode('MQ==')
            ]);
        }


        if ($request->file(base64_decode('aW1hZ2U='))) {
            $resource_image = $request->file(base64_decode('aW1hZ2U='));
            $name_image = $request->staff_id;
            $file_ext_image = $request->file(base64_decode('aW1hZ2U='))->extension();

            $name_image = $img_path . base64_decode('Lw==') . $name_image . base64_decode('LQ==') . date(base64_decode('WS1tLWQgaDppOnM=')) . base64_decode('LWFic2VuY2Uu') . $file_ext_image;

            $image = $request->file(base64_decode('aW1hZ2U='));

            $imgFile = Image::make($image->getRealPath())->orientate();

            $imgFile->insert($basepath . base64_decode('L2ltYWdlcy9Mb2dvLnBuZw=='), base64_decode('Ym90dG9tLXJpZ2h0'), 10, 10);

            $imgFile->text('' . Date(base64_decode('WS1tLWQgSDppOnM=')) . base64_decode('IGxhdCA6IA==') . $request->lat . base64_decode('IGxuZyA6IA==') . $request->lng, 10, 10, function ($font) {
                $font->file(str_replace(base64_decode('bGFyYXZlbC1zaW1wbGV0YWI='), base64_decode('cHVibGljX2h0bWwvc2ltcGxldGFiYWRtaW4v'), \base_path()) . base64_decode('L2ZvbnQvVGl0YW5pYS1SZWd1bGFyLnR0Zg=='));
                $font->size(14);
                $font->color(base64_decode('IzAwMDAwMA=='));
                $font->valign(base64_decode('dG9w'));
            })->save($basepath . base64_decode('Lw==') . $name_image);



            $data_image = $name_image;
        }


        if ($responseImage != '') {
            return response()->json([
                base64_decode('bWVzc2FnZQ==') => $responseImage,
            ]);
        }

        $absenceBefore = AbsenceLog::select(base64_decode('cmVnaXN0ZXI='))
            ->leftJoin(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
            ->where(base64_decode('YWJzZW5jZV9pZA=='), $request->absence_id)
            ->where(base64_decode('cXVldWU='), base64_decode('MQ=='))
            ->where(base64_decode('dHlwZQ=='), base64_decode('cHJlc2VuY2U='))
            ->first();

        $duration = 0;
        if ($request->queue == base64_decode('Mg==')) {
            $absenceBefore2 = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9sb2dzLmlkLCByZWdpc3RlciwgYWJzZW5jZV9sb2dzLmV4cGlyZWRfZGF0ZSwgYWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='))
                ->join(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                ->where(base64_decode('YWJzZW5jZV9pZA=='), $request->absence_id)
                ->where(base64_decode('dHlwZQ=='), $request->type)
                ->where(base64_decode('cXVldWU='), base64_decode('MQ=='))
                ->first();
            $day3 = $absenceBefore2->register;
            $day3 = strtotime($day3);
            $day4 = date(base64_decode('WS1tLWQgSDppOnM='));
            $day4 = strtotime($day4);

            $duration = ($day4 - $day3) / 3600;
        }

        if ($absenceBefore != null) {
            $day1 = $absenceBefore->register;
        } else {
            $day1 = $absenceBefore->register;
        }

        if ($request->type == base64_decode('cHJlc2VuY2U=') && $request->queue == base64_decode('MQ==')) {
            $outDuration = 0;
        } else {
            $day1 = strtotime($day1);
            $day2 = date(base64_decode('WS1tLWQgSDppOnM='));
            $day2 = strtotime($day2);

            $outDuration = ($day2 - $day1) / 3600;
        }

        $late = 0;
        $early = 0;
        try {
            $upload_image = AbsenceLog::where(base64_decode('aWQ='), $request->id)->first();
            if ($request->type == base64_decode('cHJlc2VuY2U=')) {
                if (date(base64_decode('WS1tLWQgSDppOnM=')) > $upload_image->timein) {
                    $dayL1 = $upload_image->timein;
                    $dayL1 = strtotime($dayL1);
                    $dayL2 = date(base64_decode('WS1tLWQgSDppOnM='));
                    $dayL2 = strtotime($dayL2);

                    $late = ($dayL2 - $dayL1) / 3600;
                } else {
                    $dayE1 = $upload_image->timein;
                    $dayE1 = strtotime($dayE1);
                    $dayE2 = date(base64_decode('WS1tLWQgSDppOnM='));
                    $dayE2 = strtotime($dayE2);

                    $early = ($dayE1 - $dayE2) / 3600;
                }
            }

            $absence = Absence::where(base64_decode('aWQ='), $request->absence_id)
                ->first();

            $change_register = base64_decode('ZmFsc2U=');
            if ($request->type == base64_decode('cHJlc2VuY2U=')) {
                $cek_toleransi_untuk2shift = Absence::join(base64_decode('YWJzZW5jZV9sb2dz'), base64_decode('YWJzZW5jZXMuaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='))
                    ->where(base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('Mg=='))
                    ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cw=='), base64_decode('MA=='))
                    ->where(base64_decode('YWJzZW5jZV9sb2dzLnRpbWVvdXQ='), base64_decode('Pj0='), (date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('LTUgbWludXRlcw=='), strtotime(date(base64_decode('WS1tLWQgSDppOnM=')))))))
                    ->where(base64_decode('c3RhZmZfaWQ='), $absence->staff_id)
                    ->first();
                if ($cek_toleransi_untuk2shift) {
                    $late = 0;
                    $change_register = base64_decode('dHJ1ZQ==');
                }
            }


            $upload_image->late = $late;
            $upload_image->early = $early;


            $upload_image->image = $data_image;
            $upload_image->created_by_staff_id = $request->staff_id;
            $upload_image->updated_by_staff_id = $request->staff_id;
            $upload_image->register = date(base64_decode('WS1tLWQgSDppOnM='));
            $upload_image->duration = $duration;

            $upload_image->register = $change_register == base64_decode('dHJ1ZQ==') ? $upload_image->timein : date(base64_decode('WS1tLWQgSDppOnM='));
            $upload_image->updated_at = date(base64_decode('WS1tLWQgSDppOnM='));
            $upload_image->lat = $request->lat;
            $upload_image->lng = $request->lng;
            $upload_image->status =  0;
            $upload_image->accuracy = base64_decode('MA==');
            $upload_image->distance = $request->distance;

            $upload_image->save();

            if ($request->queue == base64_decode('MQ==')) {
                $end = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9sb2dzLmlkLCBhYnNlbmNlX2xvZ3MuZXhwaXJlZF9kYXRlLCBhYnNlbmNlX2xvZ3MuYWJzZW5jZV9pZA=='))
                    ->join(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                    ->where(base64_decode('YWJzZW5jZV9pZA=='), $request->absence_id)
                    ->where(base64_decode('dHlwZQ=='), $request->type)
                    ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cw=='), base64_decode('MQ=='))
                    ->where(base64_decode('cXVldWU='), base64_decode('Mg=='))
                    ->first();
                AbsenceLog::where(base64_decode('aWQ='), $end->id)->update([base64_decode('cmVnaXN0ZXI=') => date(base64_decode('WS1tLWQgSDppOnM='))]);
            }

            if ($upload_image->absence_request_id != '' && $upload_image->absence_request_id != null) {
                AbsenceRequest::where(base64_decode('aWQ='), $upload_image->absence_request_id)->update([base64_decode('c3RhdHVz') => base64_decode('Y2xvc2U=')]);
            }

            if ($request->type != base64_decode('ZXh0cmE=')) {
                $out = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9sb2dzLmlkLCBhYnNlbmNlX2xvZ3MuZXhwaXJlZF9kYXRlLCBhYnNlbmNlX2xvZ3MuYWJzZW5jZV9pZA=='))->join(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                    ->where(base64_decode('YWJzZW5jZV9pZA=='), $request->absence_id)
                    ->where(base64_decode('dHlwZQ=='), base64_decode('cHJlc2VuY2U='))
                    ->orderBy(base64_decode('cXVldWU='), base64_decode('REVTQw=='))
                    ->first();
                AbsenceLog::where(base64_decode('aWQ='), $out->id)->update([
                    base64_decode('cmVnaXN0ZXI=') => date(base64_decode('WS1tLWQgSDppOnM=')),
                    base64_decode('ZHVyYXRpb24=') => $outDuration
                ]);
            }

            if ($request->queue == base64_decode('MQ==') && $request->type == base64_decode('cHJlc2VuY2U=')) {
                $check = AbsenceLog::where(base64_decode('YWJzZW5jZV9pZA=='), $out->absence_id)
                    ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA=='), 3)
                    ->first();
                if (!$check) {
                    AbsenceLog::create([

                        base64_decode('YWJzZW5jZV9pZA==') => $out->absence_id,
                        base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA==') => 3,
                        base64_decode('c3RhdHVz') => base64_decode('MQ=='),
                        base64_decode('ZXhwaXJlZF9kYXRl') => $out->expired_date,
                        base64_decode('c3RhcnRfZGF0ZQ==') => date(base64_decode('WS1tLWQgSDppOjEw')),

                    ]);
                    AbsenceLog::create([

                        base64_decode('YWJzZW5jZV9pZA==') => $out->absence_id,
                        base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA==') => 4,
                        base64_decode('c3RhdHVz') => base64_decode('MQ=='),
                        base64_decode('ZXhwaXJlZF9kYXRl') => $out->expired_date,
                        base64_decode('c3RhcnRfZGF0ZQ==') => date(base64_decode('WS1tLWQgSDppOjEx')),

                    ]);
                }
            }


            return response()->json([
                base64_decode('bWVzc2FnZQ==') => base64_decode('QWJzZW4gVGVya2lyaW0='),
                base64_decode('ZGF0YQ==') => $upload_image,
            ]);
        } catch (QueryException $ex) {
            return response()->json([
                base64_decode('bWVzc2FnZQ==') => base64_decode('Z2FnYWw='),
            ]);
        }
    }


    public function storeLocation(Request $request)
    {


        $img_path = base64_decode('L2ltYWdlcy9hYnNlbmNl');
        $basepath = str_replace(base64_decode('bGFyYXZlbC1zaW1wbGV0YWI='), base64_decode('cHVibGljX2h0bWwvc2ltcGxldGFiYWRtaW4v'), \base_path());
        $responseImage = '';
        $data_image = '';
        if (date(base64_decode('dw==')) == base64_decode('MA==')) {
            $day = base64_decode('Nw==');
        } else {
            $day = date(base64_decode('dw=='));
        }

        $workDuration = Absence::join(base64_decode('YWJzZW5jZV9sb2dz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZXMuaWQ='))
            ->join(base64_decode('d29ya190eXBlX2RheXM='), base64_decode('d29ya190eXBlX2RheXMuaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9sb2dzLndvcmtfdHlwZV9kYXlfaWQ='))
            ->select(base64_decode('d29ya190eXBlX2RheXMuZHVyYXRpb24='))->where(base64_decode('YWJzZW5jZV9pZA=='), $request->absence_id)->where(base64_decode('d29ya190eXBlX2RheXMuYWJzZW5jZV9jYXRlZ29yeV9pZA=='), base64_decode('MQ=='))
            ->first();
        if (!$workDuration) {
            $workDuration = Absence::join(base64_decode('YWJzZW5jZV9sb2dz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZXMuaWQ='))
                ->join(base64_decode('c2hpZnRfZ3JvdXBfdGltZXNoZWV0cw=='), base64_decode('c2hpZnRfZ3JvdXBfdGltZXNoZWV0cy5pZA=='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9sb2dzLnNoaWZ0X2dyb3VwX3RpbWVzaGVldF9pZA=='))
                ->select(base64_decode('c2hpZnRfZ3JvdXBfdGltZXNoZWV0cy5kdXJhdGlvbg=='))->where(base64_decode('YWJzZW5jZV9pZA=='), $request->absence_id)->where(base64_decode('c2hpZnRfZ3JvdXBfdGltZXNoZWV0cy5hYnNlbmNlX2NhdGVnb3J5X2lk'), base64_decode('MQ=='))
                ->first()->duration;
        } else {
            $workDuration = $workDuration->duration;
        }
        $absenceBefore = AbsenceLog::select(base64_decode('cmVnaXN0ZXI='))
            ->leftJoin(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
            ->where(base64_decode('YWJzZW5jZV9pZA=='), $request->absence_id)
            ->where(base64_decode('cXVldWU='), base64_decode('MQ=='))
            ->where(base64_decode('dHlwZQ=='), base64_decode('cHJlc2VuY2U='))
            ->first();




        $day1 = $absenceBefore->register;
        $day1 = strtotime($day1);
        $day2 = date(base64_decode('WS1tLWQgSDppOnM='));
        $day2 = strtotime($day2);

        $outDuration = ($day2 - $day1) / 3600;

        if ($request->absence_category_id == base64_decode('MTE=')) {
            if ($outDuration < ($workDuration / 2)) {
                Absence::where(base64_decode('aWQ='), $request->absence_id)->update([
                    base64_decode('c3RhdHVzX2FjdGl2ZQ==') => base64_decode('Mw==')
                ]);
            }
        }


        if ($request->file(base64_decode('aW1hZ2U='))) {
            $resource_image = $request->file(base64_decode('aW1hZ2U='));
            $name_image = $request->satff_id;
            $file_ext_image = $request->file(base64_decode('aW1hZ2U='))->extension();

            $name_image = $img_path . base64_decode('Lw==') . $name_image . base64_decode('LQ==') . date(base64_decode('WS1tLWQgaDppOnM=')) . base64_decode('LWFic2VuY2Uu') . $file_ext_image;

            $image = $request->file(base64_decode('aW1hZ2U='));

            $imgFile = Image::make($image->getRealPath());

            $imgFile->insert($basepath . base64_decode('L2ltYWdlcy9Mb2dvLnBuZw=='), base64_decode('Ym90dG9tLXJpZ2h0'), 10, 10);

            $imgFile->text('' . Date(base64_decode('WS1tLWQgSDppOnM=')) . base64_decode('IGxhdCA6IA==') . $request->lat . base64_decode('IGxuZyA6IA==') . $request->lng, 10, 10, function ($font) {
                $font->file(str_replace(base64_decode('bGFyYXZlbC1zaW1wbGV0YWI='), base64_decode('cHVibGljX2h0bWwvc2ltcGxldGFiYWRtaW4v'), \base_path()) . base64_decode('L2ZvbnQvVGl0YW5pYS1SZWd1bGFyLnR0Zg=='));
                $font->size(14);
                $font->color(base64_decode('IzAwMDAwMA=='));
                $font->valign(base64_decode('dG9w'));
            })->save($basepath . base64_decode('Lw==') . $name_image);

            $data_image = $name_image;
        }


        if ($responseImage != '') {
            return response()->json([
                base64_decode('bWVzc2FnZQ==') => $responseImage,
            ]);
        }

        $check = AbsenceLog::where(base64_decode('YWJzZW5jZV9pZA=='), $request->absence_id)
            ->where(base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk'), $request->absence_request_id)
            ->first();
        if (!$check) {
            $upload_image = new AbsenceLog;
            $upload_image->image = $data_image;
            $upload_image->created_by_staff_id = $request->satff_id;
            $upload_image->updated_by_staff_id = $request->satff_id;
            $upload_image->register = date(base64_decode('WS1tLWQgSDppOnM='));
            $upload_image->absence_id = $request->absence_id;
            $upload_image->absence_request_id = $request->absence_request_id;
            $upload_image->register = date(base64_decode('WS1tLWQgSDppOnM='));
            $upload_image->created_at = date(base64_decode('WS1tLWQgSDppOnM='));
            $upload_image->updated_at = date(base64_decode('WS1tLWQgSDppOnM='));
            $upload_image->absence_category_id = $request->absence_category_id;
            $upload_image->lat = $request->lat;
            $upload_image->lng = $request->lng;
            $upload_image->status =  0;
            $upload_image->expired_date = $request->expired_date;
            $upload_image->start_date = date(base64_decode('WS1tLWQgSDppOjEw'));
            $upload_image->accuracy = $request->accuracy;
            $upload_image->distance = $request->distance;

            $upload_image->save();

            $out = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9sb2dzLmlkLCBhYnNlbmNlX2xvZ3MuZXhwaXJlZF9kYXRlLCBhYnNlbmNlX2xvZ3MuYWJzZW5jZV9pZA=='))->join(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                ->where(base64_decode('YWJzZW5jZV9pZA=='), $request->absence_id)
                ->where(base64_decode('dHlwZQ=='), base64_decode('cHJlc2VuY2U='))
                ->orderBy(base64_decode('cXVldWU='), base64_decode('REVTQw=='))
                ->first();

            AbsenceLog::where(base64_decode('aWQ='), $out->id)->update([base64_decode('cmVnaXN0ZXI=') => date(base64_decode('WS1tLWQgSDppOnM=')), base64_decode('ZHVyYXRpb24=') => $outDuration]);

            $breakin = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9sb2dzLmlkLCBhYnNlbmNlX2xvZ3MuZXhwaXJlZF9kYXRlLCBhYnNlbmNlX2xvZ3MuYWJzZW5jZV9pZA=='))->join(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                ->where(base64_decode('YWJzZW5jZV9pZA=='), $request->absence_id)
                ->where(base64_decode('dHlwZQ=='), base64_decode('YnJlYWs='))
                ->where(base64_decode('c3RhdHVz'), 1)
                ->where(base64_decode('cXVldWU='), base64_decode('MQ=='))
                ->first();
            if ($breakin) {
                AbsenceLog::where(base64_decode('aWQ='), $breakin->id)->update([base64_decode('cmVnaXN0ZXI=') => date(base64_decode('WS1tLWQgSDppOnM=')), base64_decode('c3RhdHVz') => base64_decode('MA==')]);
            }
            $breakout = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9sb2dzLmlkLCBhYnNlbmNlX2xvZ3MuZXhwaXJlZF9kYXRlLCBhYnNlbmNlX2xvZ3MuYWJzZW5jZV9pZA=='))->join(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                ->where(base64_decode('YWJzZW5jZV9pZA=='), $request->absence_id)
                ->where(base64_decode('dHlwZQ=='), base64_decode('YnJlYWs='))
                ->where(base64_decode('c3RhdHVz'), 1)
                ->where(base64_decode('cXVldWU='), base64_decode('Mg=='))
                ->first();

            if ($breakout) {
                AbsenceLog::where(base64_decode('aWQ='), $breakout->id)->update([base64_decode('cmVnaXN0ZXI=') => date(base64_decode('WS1tLWQgSDppOnM=')), base64_decode('c3RhdHVz') => base64_decode('MA==')]);
            }
            $absenceR = AbsenceRequest::where(base64_decode('aWQ='), $request->absence_request_id)->first();


            if ($request->absence_category_id == base64_decode('MTE=') && $absenceR->type == base64_decode('b3V0')) {
                AbsenceLog::create([
                    base64_decode('YWJzZW5jZV9pZA==') => $request->absence_id,
                    base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA==') => $request->absence_category_id_end,
                    base64_decode('c3RhdHVz') => base64_decode('MA=='),
                    base64_decode('cmVnaXN0ZXI=') => date(base64_decode('WS1tLWQgSDppOnM=')),
                    base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk') => $request->absence_request_id,
                    base64_decode('ZXhwaXJlZF9kYXRl') => $request->expired_date,
                    base64_decode('c3RhcnRfZGF0ZQ==') => date(base64_decode('WS1tLWQgSDppOjEw')),

                ]);
                AbsenceLog::where(base64_decode('aWQ='), $out->id)->update([base64_decode('cmVnaXN0ZXI=') => date(base64_decode('WS1tLWQgSDppOnM=')), base64_decode('ZHVyYXRpb24=') => $outDuration, base64_decode('c3RhdHVz') => base64_decode('MA==')]);
            } else {
                AbsenceLog::create([
                    base64_decode('YWJzZW5jZV9pZA==') => $request->absence_id,
                    base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA==') => $request->absence_category_id_end,
                    base64_decode('c3RhdHVz') => base64_decode('MQ=='),
                    base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk') => $request->absence_request_id,
                    base64_decode('ZXhwaXJlZF9kYXRl') => $request->expired_date,
                    base64_decode('c3RhcnRfZGF0ZQ==') => date(base64_decode('WS1tLWQgSDppOjEw')),

                ]);
            }



            AbsenceRequest::where(base64_decode('aWQ='), $request->absence_request_id)->update([base64_decode('c3RhdHVz') => base64_decode('YWN0aXZl')]);
            if ($request->absence_category_id == base64_decode('MTE=') && $absenceR->type == base64_decode('b3V0')) {
                AbsenceRequest::where(base64_decode('aWQ='), $request->absence_request_id)->update([base64_decode('c3RhdHVz') => base64_decode('Y2xvc2U=')]);
            }
            return response()->json([
                base64_decode('bWVzc2FnZQ==') => base64_decode('QWJzZW4gVGVya2lyaW0='),
                base64_decode('ZGF0YQ==') => $upload_image,
            ]);
        } else {
            return response()->json([
                base64_decode('bWVzc2FnZQ==') => base64_decode('VGFkaSBTdWRhaCBBYnNlbg=='),
            ]);
        }
    }

    public function storeLocationEnd(Request $request)
    {


        $img_path = base64_decode('L2ltYWdlcy9hYnNlbmNl');
        $basepath = str_replace(base64_decode('bGFyYXZlbC1zaW1wbGV0YWI='), base64_decode('cHVibGljX2h0bWwvc2ltcGxldGFiYWRtaW4v'), \base_path());
        $responseImage = '';
        $data_image = '';
        if (date(base64_decode('dw==')) == base64_decode('MA==')) {
            $day = base64_decode('Nw==');
        } else {
            $day = date(base64_decode('dw=='));
        }

        $absence_check = AbsenceLog::where(base64_decode('aWQ='), $request->id)->where(base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk'), $request->absence_request_id)->first();
        if ($absence_check->absence_category_id == base64_decode('MTI=')) {

            Absence::where(base64_decode('aWQ='), $absence_check->absence_id)->update([
                base64_decode('c3RhdHVzX2FjdGl2ZQ==') => ''
            ]);
        }

        if ($request->file(base64_decode('aW1hZ2U='))) {
            $resource_image = $request->file(base64_decode('aW1hZ2U='));
            $name_image = $request->staff_id;
            $file_ext_image = $request->file(base64_decode('aW1hZ2U='))->extension();

            $name_image = $img_path . base64_decode('Lw==') . $name_image . base64_decode('LQ==') . date(base64_decode('WS1tLWQgaDppOnM=')) . base64_decode('LWFic2VuY2Uu') . $file_ext_image;
            $image = $request->file(base64_decode('aW1hZ2U='));

            $imgFile = Image::make($image->getRealPath());

            $imgFile->insert($basepath . base64_decode('L2ltYWdlcy9Mb2dvLnBuZw=='), base64_decode('Ym90dG9tLXJpZ2h0'), 10, 10);

            $imgFile->text('' . Date(base64_decode('WS1tLWQgSDppOnM=')) . base64_decode('IGxhdCA6IA==') . $request->lat . base64_decode('IGxuZyA6IA==') . $request->lng, 10, 10, function ($font) {
                $font->file(str_replace(base64_decode('bGFyYXZlbC1zaW1wbGV0YWI='), base64_decode('cHVibGljX2h0bWwvc2ltcGxldGFiYWRtaW4v'), \base_path()) . base64_decode('L2ZvbnQvVGl0YW5pYS1SZWd1bGFyLnR0Zg=='));
                $font->size(14);
                $font->color(base64_decode('IzAwMDAwMA=='));
                $font->valign(base64_decode('dG9w'));
            })->save($basepath . base64_decode('Lw==') . $name_image);

            $data_image = $name_image;
        }


        if ($responseImage != '') {
            return response()->json([
                base64_decode('bWVzc2FnZQ==') => $responseImage,
            ]);
        }

        $absenceBefore = AbsenceLog::select(base64_decode('cmVnaXN0ZXI='))
            ->leftJoin(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
            ->where(base64_decode('YWJzZW5jZV9pZA=='), $request->absence_id)
            ->where(base64_decode('cXVldWU='), base64_decode('MQ=='))
            ->where(base64_decode('dHlwZQ=='), base64_decode('cHJlc2VuY2U='))
            ->first();

        $duration = 0;
        if ($request->queue == base64_decode('Mg==')) {
            $absenceBefore2 = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9sb2dzLmlkLCByZWdpc3RlciwgYWJzZW5jZV9sb2dzLmV4cGlyZWRfZGF0ZSwgYWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='))
                ->join(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                ->where(base64_decode('YWJzZW5jZV9pZA=='), $request->absence_id)
                ->where(base64_decode('dHlwZQ=='), $request->type)
                ->where(base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk'), $request->absence_request_id)
                ->where(base64_decode('cXVldWU='), base64_decode('MQ=='))
                ->first();
            $day3 = $absenceBefore2->register;
            $day3 = strtotime($day3);
            $day4 = date(base64_decode('WS1tLWQgSDppOnM='));
            $day4 = strtotime($day4);

            $duration = ($day4 - $day3) / 3600;
        }


        $day1 = $absenceBefore->register;
        $day1 = strtotime($day1);
        $day2 = date(base64_decode('WS1tLWQgSDppOnM='));
        $day2 = strtotime($day2);

        $outDuration = ($day2 - $day1) / 3600;

        try {
            $upload_image = AbsenceLog::where(base64_decode('aWQ='), $request->id)->where(base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk'), $request->absence_request_id)->first();
            $upload_image->image = $data_image;
            $upload_image->created_by_staff_id = $request->staff_id;
            $upload_image->updated_by_staff_id = $request->staff_id;
            $upload_image->register = date(base64_decode('WS1tLWQgSDppOnM='));
            $upload_image->duration = $duration;
            $upload_image->register = date(base64_decode('WS1tLWQgSDppOnM='));
            $upload_image->updated_at = date(base64_decode('WS1tLWQgSDppOnM='));
            $upload_image->lat = $request->lat;
            $upload_image->lng = $request->lng;
            $upload_image->status =  0;
            $upload_image->accuracy = $request->accuracy;
            $upload_image->distance = $request->distance;

            $upload_image->save();

            AbsenceLog::where(base64_decode('YWJzZW5jZV9pZA=='), $request->absence_id)->where(base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk'), $request->absence_request_id)->update([base64_decode('c3RhdHVz') => 0]);


            if ($request->queue == base64_decode('MQ==')) {
                $end = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9sb2dzLmlkLCBhYnNlbmNlX2xvZ3MuZXhwaXJlZF9kYXRlLCBhYnNlbmNlX2xvZ3MuYWJzZW5jZV9pZA=='))
                    ->join(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                    ->where(base64_decode('YWJzZW5jZV9pZA=='), $request->absence_id)
                    ->where(base64_decode('dHlwZQ=='), $request->type)
                    ->where(base64_decode('YWJzZW5jZV9sb2dzLnN0YXR1cw=='), base64_decode('MQ=='))
                    ->where(base64_decode('cXVldWU='), base64_decode('Mg=='))
                    ->first();
                AbsenceLog::where(base64_decode('aWQ='), $end->id)->update([base64_decode('cmVnaXN0ZXI=') => date(base64_decode('WS1tLWQgSDppOnM='))]);
            }

            if ($upload_image->absence_request_id != '' && $upload_image->absence_request_id != null) {
                AbsenceRequest::where(base64_decode('aWQ='), $upload_image->absence_request_id)->update([base64_decode('c3RhdHVz') => base64_decode('Y2xvc2U=')]);
            }

            if ($request->type != base64_decode('ZXh0cmE=')) {
                $out = AbsenceLog::selectRaw(base64_decode('YWJzZW5jZV9sb2dzLmlkLCBhYnNlbmNlX2xvZ3MuZXhwaXJlZF9kYXRlLCBhYnNlbmNlX2xvZ3MuYWJzZW5jZV9pZA=='))->join(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                    ->where(base64_decode('YWJzZW5jZV9pZA=='), $request->absence_id)
                    ->where(base64_decode('dHlwZQ=='), base64_decode('cHJlc2VuY2U='))
                    ->orderBy(base64_decode('cXVldWU='), base64_decode('REVTQw=='))
                    ->first();
                AbsenceLog::where(base64_decode('aWQ='), $out->id)->update([
                    base64_decode('cmVnaXN0ZXI=') => date(base64_decode('WS1tLWQgSDppOnM=')),
                    base64_decode('ZHVyYXRpb24=') => $outDuration
                ]);
            }








            return response()->json([
                base64_decode('bWVzc2FnZQ==') => base64_decode('QWJzZW4gVGVya2lyaW0='),
                base64_decode('ZGF0YQ==') => $upload_image,
            ]);
        } catch (QueryException $ex) {
            return response()->json([
                base64_decode('bWVzc2FnZQ==') => base64_decode('Z2FnYWw='),
            ]);
        }
    }

    public function storeLocationDuty(Request $request)
    {


        $img_path = base64_decode('L2ltYWdlcy9SZXF1ZXN0RmlsZQ==');
        $basepath = str_replace(base64_decode('bGFyYXZlbC1zaW1wbGV0YWI='), base64_decode('cHVibGljX2h0bWwvc2ltcGxldGFiYWRtaW4v'), \base_path());
        $responseImage = '';
        $data_image = '';
        if (date(base64_decode('dw==')) == base64_decode('MA==')) {
            $day = base64_decode('Nw==');
        } else {
            $day = date(base64_decode('dw=='));
        }

        $AbsenceRequestLogs = AbsenceRequestLogs::where(base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk'), $request->absence_request_id)
            ->first();

        if ($request->file(base64_decode('aW1hZ2U='))) {
            $resource_image = $request->file(base64_decode('aW1hZ2U='));
            $name_image = $request->satff_id;
            $file_ext_image = $request->file(base64_decode('aW1hZ2U='))->extension();

            $name_image = $img_path . base64_decode('Lw==') . $name_image . base64_decode('LQ==') . date(base64_decode('WS1tLWQgaDppOnM=')) . base64_decode('LWFic2VuY2Uu') . $file_ext_image;
            $image = $request->file(base64_decode('aW1hZ2U='));

            $imgFile = Image::make($image->getRealPath());

            $imgFile->insert($basepath . base64_decode('L2ltYWdlcy9Mb2dvLnBuZw=='), base64_decode('Ym90dG9tLXJpZ2h0'), 10, 10);

            $imgFile->text('' . Date(base64_decode('WS1tLWQgSDppOnM=')) . base64_decode('IGxhdCA6IA==') . $request->lat . base64_decode('IGxuZyA6IA==') . $request->lng, 10, 10, function ($font) {
                $font->file(str_replace(base64_decode('bGFyYXZlbC1zaW1wbGV0YWI='), base64_decode('cHVibGljX2h0bWwvc2ltcGxldGFiYWRtaW4v'), \base_path()) . base64_decode('L2ZvbnQvVGl0YW5pYS1SZWd1bGFyLnR0Zg=='));
                $font->size(14);
                $font->color(base64_decode('IzAwMDAwMA=='));
                $font->valign(base64_decode('dG9w'));
            })->save($basepath . base64_decode('Lw==') . $name_image);

            $data_image = $name_image;
        }


        if ($responseImage != '') {
            return response()->json([
                base64_decode('bWVzc2FnZQ==') => $responseImage,
            ]);
        }
        if ($AbsenceRequestLogs) {
            $type = base64_decode('cmVxdWVzdF9sb2dfb3V0');
        } else {
            $type = base64_decode('cmVxdWVzdF9sb2dfaW4=');
        }

        try {

            $upload_image = new AbsenceRequestLogs;
            $upload_image->image = $data_image;
            $upload_image->register = date(base64_decode('WS1tLWQgSDppOnM='));
            $upload_image->absence_request_id = $request->absence_request_id;
            $upload_image->type = $type;
            $upload_image->memo = $request->memo;
            $upload_image->created_at = date(base64_decode('WS1tLWQgSDppOnM='));
            $upload_image->updated_at = date(base64_decode('WS1tLWQgSDppOnM='));
            $upload_image->lat = $request->lat;
            $upload_image->lng = $request->lng;

            $upload_image->save();

            if ($AbsenceRequestLogs) {
                $absenceRequest = AbsenceRequest::select(base64_decode('Y2F0ZWdvcnk='), DB::raw(base64_decode('REFURShzdGFydCkgYXMgc3RhcnQ=')), DB::raw(base64_decode('REFURShlbmQpIGFzIGVuZA==')))
                    ->where(base64_decode('aWQ='), $request->absence_request_id)->first();
                AbsenceRequest::where(base64_decode('aWQ='), $request->absence_request_id)->update([base64_decode('c3RhdHVz') => base64_decode('Y2xvc2U='), base64_decode('YXR0ZW5kYW5jZQ==') => date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('LSA=') . 1 . base64_decode('IGRheXM='), strtotime(date(base64_decode('WS1tLWQg') . base64_decode('MjM6NTk6NTk=')))))]);
                $check = AbsenceLog::join(base64_decode('YWJzZW5jZXM='), base64_decode('YWJzZW5jZXMuaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='))
                    ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA=='), $absenceRequest->category)
                    ->where(base64_decode('YWJzZW5jZXMuc3RhZmZfaWQ='), $request->staff_id)
                    ->where(base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfcmVxdWVzdF9pZA=='), $request->absence_request_id)
                    ->whereDate(base64_decode('YWJzZW5jZXMuY3JlYXRlZF9hdA=='), $absenceRequest->start)
                    ->first();
                if (!$check) {
                    Absence::whereDate(base64_decode('Y3JlYXRlZF9hdA=='), base64_decode('Pg=='), date(base64_decode('WS1tLWQ=')))->delete();
                    AbsenceLog::whereDate(base64_decode('cmVnaXN0ZXI='), base64_decode('Pg=='), date(base64_decode('WS1tLWQ=')))->delete();
                    $begin = strtotime($absenceRequest->start);
                    $end   = strtotime(date(base64_decode('WS1tLWQ=')));

                    for ($i = $begin; $i <= $end; $i = $i + 86400) {
                        $holiday = Holiday::whereDate(base64_decode('c3RhcnQ='), base64_decode('PD0='), date(base64_decode('WS1tLWQ='), $i))->whereDate(base64_decode('ZW5k'), base64_decode('Pj0='), date(base64_decode('WS1tLWQ='), $i))->first();
                        if (!$holiday) {
                            if (date(base64_decode('dw=='), strtotime(date(base64_decode('WS1tLWQ='), $i))) != 0 && date(base64_decode('dw=='), strtotime(date(base64_decode('WS1tLWQ='), $i))) != 6) {

                                Absence::create([
                                    base64_decode('ZGF5X2lk') => date(base64_decode('dw=='), strtotime(date(base64_decode('WS1tLWQ='), $i))),
                                    base64_decode('c3RhZmZfaWQ=') => $request->staff_id,
                                    base64_decode('Y3JlYXRlZF9hdA==') => date(base64_decode('WS1tLWQgSDppOnM='), $i),
                                    base64_decode('dXBkYXRlZF9hdA==') => date(base64_decode('WS1tLWQgSDppOnM='))
                                ]);
                                AbsenceLog::create([
                                    base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA==') => $absenceRequest->category == base64_decode('ZHV0eQ==') ? 7 : 8,
                                    base64_decode('bGF0') => '',
                                    base64_decode('bG5n') => '',
                                    base64_decode('cmVnaXN0ZXI=') => date(base64_decode('WS1tLWQ='), $i),
                                    base64_decode('YWJzZW5jZV9pZA==') => '',
                                    base64_decode('ZHVyYXRpb24=') => '',
                                    base64_decode('c3RhdHVz') => ''
                                ]);
                            }
                        }
                    }
                } else {
                    return response()->json([
                        base64_decode('bWVzc2FnZQ==') => base64_decode('VGFkaSBTdWRhaCBBYnNlbg=='),
                    ]);
                }
            } else {
                AbsenceRequest::where(base64_decode('aWQ='), $request->absence_request_id)->update([base64_decode('c3RhdHVz') => base64_decode('YWN0aXZl')]);
            }


            return response()->json([
                base64_decode('bWVzc2FnZQ==') => base64_decode('QWJzZW4gVGVya2lyaW0='),
                base64_decode('ZGF0YQ==') => $upload_image,
            ]);
        } catch (QueryException $ex) {
            return response()->json([
                base64_decode('bWVzc2FnZQ==') => base64_decode('Z2FnYWw='),
            ]);
        }
    }

    public function storeLocationExtra(Request $request)
    {

        $img_path = base64_decode('L2ltYWdlcy9hYnNlbmNl');
        $basepath = str_replace(base64_decode('bGFyYXZlbC1zaW1wbGV0YWI='), base64_decode('cHVibGljX2h0bWwvc2ltcGxldGFiYWRtaW4v'), \base_path());
        $responseImage = '';
        $data_image = '';
        if (date(base64_decode('dw==')) == base64_decode('MA==')) {
            $day = base64_decode('Nw==');
        } else {
            $day = date(base64_decode('dw=='));
        }

        $cekStatus = AbsenceRequest::where(base64_decode('aWQ='), $request->absence_request_id)->first();

        if ($cekStatus->status && $cekStatus->status == base64_decode('cGVuZGluZw==')) {
            $data = [
                base64_decode('ZGF5X2lk') => $day,
                base64_decode('c3RhZmZfaWQ=') => $request->staff_id,
                base64_decode('Y3JlYXRlZF9hdA==') => date(base64_decode('WS1tLWQ=')),
                base64_decode('c3RhdHVzX2FjdGl2ZQ==') => base64_decode('Mg==')
            ];
        } else {
            $data = [
                base64_decode('ZGF5X2lk') => $day,
                base64_decode('c3RhZmZfaWQ=') => $request->staff_id,
                base64_decode('Y3JlYXRlZF9hdA==') => date(base64_decode('WS1tLWQ='))
            ];
        }
        $check = AbsenceLog::join(base64_decode('YWJzZW5jZXM='), base64_decode('YWJzZW5jZXMuaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='))
            ->where(base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA=='), $request->absence_category_id)
            ->where(base64_decode('YWJzZW5jZXMuc3RhZmZfaWQ='), $request->staff_id)
            ->where(base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfcmVxdWVzdF9pZA=='), $request->absence_request_id)
            ->whereDate(base64_decode('YWJzZW5jZXMuY3JlYXRlZF9hdA=='), date(base64_decode('WS1tLWQ=')))
            ->first();

        if (!$check) {
            $absence = Absence::create($data);

            if ($request->file(base64_decode('aW1hZ2U='))) {
                $resource_image = $request->file(base64_decode('aW1hZ2U='));
                $name_image = $request->staff_id;
                $file_ext_image = $request->file(base64_decode('aW1hZ2U='))->extension();
                $name_image = $img_path . base64_decode('Lw==') . $name_image . base64_decode('LQ==') . date(base64_decode('WS1tLWQgaDppOnM=')) . base64_decode('LWFic2VuY2Uu') . $file_ext_image;

                $image = $request->file(base64_decode('aW1hZ2U='));

                $imgFile = Image::make($image->getRealPath());

                $imgFile->insert($basepath . base64_decode('L2ltYWdlcy9Mb2dvLnBuZw=='), base64_decode('Ym90dG9tLXJpZ2h0'), 10, 10);

                $imgFile->text('' . Date(base64_decode('WS1tLWQgSDppOnM=')) . base64_decode('IGxhdCA6IA==') . $request->lat . base64_decode('IGxuZyA6IA==') . $request->lng, 10, 10, function ($font) {
                    $font->file(str_replace(base64_decode('bGFyYXZlbC1zaW1wbGV0YWI='), base64_decode('cHVibGljX2h0bWwvc2ltcGxldGFiYWRtaW4v'), \base_path()) . base64_decode('L2ZvbnQvVGl0YW5pYS1SZWd1bGFyLnR0Zg=='));
                    $font->size(14);
                    $font->color(base64_decode('IzAwMDAwMA=='));
                    $font->valign(base64_decode('dG9w'));
                })->save($basepath . base64_decode('Lw==') . $name_image);


                $data_image = $name_image;
            }


            if ($responseImage != '') {
                return response()->json([
                    base64_decode('bWVzc2FnZQ==') => $responseImage,
                ]);
            }
        }

        try {
            if (!$check) {
                $upload_image = new AbsenceLog;
                $upload_image->image = $data_image;
                $upload_image->created_by_staff_id = $request->staff_id;
                $upload_image->updated_by_staff_id = $request->staff_id;
                $upload_image->register = date(base64_decode('WS1tLWQgSDppOnM='));
                $upload_image->absence_id = $absence->id;
                $upload_image->absence_request_id = $request->absence_request_id;
                $upload_image->register = date(base64_decode('WS1tLWQgSDppOnM='));
                $upload_image->created_at = date(base64_decode('WS1tLWQgSDppOnM='));
                $upload_image->updated_at = date(base64_decode('WS1tLWQgSDppOnM='));
                $upload_image->absence_category_id = $request->absence_category_id;
                $upload_image->lat = $request->lat ?  $request->lat : '';
                $upload_image->lng = $request->lng ?  $request->lng : '';
                $upload_image->status =  0;
                $upload_image->expired_date = date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('KzEyIGhvdXJz'), strtotime(date(base64_decode('WS1tLWQgSDppOnM=')))));
                $upload_image->start_date = date(base64_decode('WS1tLWQgSDppOjEw'));
                $upload_image->accuracy = $request->accuracy;
                $upload_image->distance = $request->distance;

                $upload_image->save();




                AbsenceLog::create([
                    base64_decode('YWJzZW5jZV9pZA==') => $absence->id,
                    base64_decode('YWJzZW5jZV9jYXRlZ29yeV9pZA==') => $request->absence_category_id_end,
                    base64_decode('c3RhdHVz') => base64_decode('MQ=='),
                    base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk') => $request->absence_request_id,
                    base64_decode('ZXhwaXJlZF9kYXRl') =>  date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('KzEyIGhvdXJz'), strtotime(date(base64_decode('WS1tLWQgSDppOnM='))))),
                    base64_decode('c3RhcnRfZGF0ZQ==') => date(base64_decode('WS1tLWQgSDppOjEw')),

                ]);
                AbsenceRequest::where(base64_decode('aWQ='), $request->absence_request_id)->update([base64_decode('c3RhdHVz') => base64_decode('YWN0aXZl')]);


                return response()->json([
                    base64_decode('bWVzc2FnZQ==') => base64_decode('QWJzZW4gVGVya2lyaW0='),
                    base64_decode('ZGF0YQ==') => $upload_image,
                ]);
            } else {
                return response()->json([
                    base64_decode('bWVzc2FnZQ==') => base64_decode('VGFkaSBTdWRhaCBBYnNlbg=='),
                ]);
            }
        } catch (QueryException $ex) {
            return response()->json([
                base64_decode('bWVzc2FnZQ==') => base64_decode('Z2FnYWw='),
            ]);
        }
    }

    public function leaveEnd(Request $request)
    {
        try {
            $absenceRequest =  AbsenceRequest::where(base64_decode('aWQ='), $request->id)->first();

            if (date(base64_decode('WS1tLWQ=')) > $absenceRequest->start) {
                AbsenceRequest::where(base64_decode('aWQ='), $request->id)->update([base64_decode('c3RhdHVz') => base64_decode('Y2xvc2U='), base64_decode('YXR0ZW5kYW5jZQ==') => date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('LSA=') . 1 . base64_decode('IGRheXM='), strtotime(date(base64_decode('WS1tLWQg') . base64_decode('MjM6NTk6NTk=')))))]);
                $absenceLog = AbsenceLog::where(base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk'), $absenceRequest->id)->get();
                foreach ($absenceLog as $d) {
                    $deleteAbsence = Absence::where(base64_decode('aWQ='), $absence_id)->first();
                    if ($deleteAbsence) {
                        Absence::where(base64_decode('aWQ='), $d->id)->delete();
                    }
                }

                AbsenceLog::where(base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk'), $absenceRequest->id)->delete();

                $begin = strtotime($absenceRequest->start);
                $end   = strtotime(date(base64_decode('WS1tLWQ=')));

                for ($i = $begin; $i < $end; $i = $i + 86400) {
                    $holiday = Holiday::whereDate(base64_decode('c3RhcnQ='), base64_decode('PD0='), date(base64_decode('WS1tLWQ='), $i))->whereDate(base64_decode('ZW5k'), base64_decode('Pj0='), date(base64_decode('WS1tLWQ='), $i))->first();
                    if (!$holiday) {
                        if (date(base64_decode('dw=='), strtotime(date(base64_decode('WS1tLWQ='), $i))) != 0 && date(base64_decode('dw=='), strtotime(date(base64_decode('WS1tLWQ='), $i))) != 6) {

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
                return response()->json([
                    base64_decode('bWVzc2FnZQ==') => base64_decode('QWJzZW4gVGVya2lyaW0='),
                ]);
            } else {
                return response()->json([
                    base64_decode('bWVzc2FnZQ==') => base64_decode('dGlkYWsgYmlzYSBkaWFraGlyaSBoYXJpIGluaSBrYXJlbmEgdGFuZ2dhbCBtdWxhaSBzYW1hIGRlbmdhbiB0YW5nZ2FsIHNla2FyYW5n'),
                ]);
            }
        } catch (QueryException $ex) {
            return response()->json([
                base64_decode('bWVzc2FnZQ==') => base64_decode('Z2FnYWw='),
            ]);
        }
    }

    public function checkAbsenceLocation(Request $request)
    {
        $absence = Absence::where(base64_decode('dXNlcl9pZA=='), $request->user_id)->where(base64_decode('cmVxdWVzdHNfaWQ='), $request->requests_id)->whereDate(base64_decode('cmVnaXN0ZXI='), base64_decode('PQ=='), date(base64_decode('WS1tLWQ=')))->first();

        if ($absence != null) {
            $cek = base64_decode('MQ==');
        } else {
            $cek = base64_decode('MA==');
        }
        return response()->json([
            base64_decode('bWVzc2FnZQ==') => base64_decode('c3VjY2Vzcw=='),
            base64_decode('ZGF0YQ==') => $cek,
        ]);
    }

    public function history(Request $request)
    {
        $data = [];

        $absence = Absence::join(base64_decode('ZGF5cw=='), base64_decode('ZGF5cy5pZA=='), base64_decode('PQ=='), base64_decode('YWJzZW5jZXMuZGF5X2lk'))
            ->join(base64_decode('YWJzZW5jZV9sb2dz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZXMuaWQ='))
            ->selectRaw(base64_decode('YWJzZW5jZXMuaWQsREFURShhYnNlbmNlcy5jcmVhdGVkX2F0KSBhcyBjcmVhdGVkX2F0LCBkYXlzLm5hbWUgYXMgZGF5X25hbWU='))
            ->where(base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('Mg=='))
            ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
            ->FilterDate($request->from, $request->to)
            ->orderBy(base64_decode('YWJzZW5jZXMuY3JlYXRlZF9hdA=='), base64_decode('REVTQw=='))
            ->get();

        foreach ($absence as $d) {
            $absence_log = AbsenceLog::join(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                ->select(
                    base64_decode('YWJzZW5jZV9sb2dzLio='),
                    base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLnRpdGxlIGFzIGNhdGVnb3J5X3RpdGxl'),
                    DB::raw(base64_decode('KENBU0UgV0hFTiBzdGF0dXMgPSAwIFRIRU4gcmVnaXN0ZXIgRUxTRSAnMjAyMDowMTowMSAwMDowMDowMCcgRU5EKSBhcyByZWdpc3Rlcg=='))
                )
                ->where(base64_decode('YWJzZW5jZV9sb2dzLnJlZ2lzdGVy'), base64_decode('IT0='), '')
                ->where(base64_decode('YWJzZW5jZV9pZA=='), base64_decode('PQ=='), $d->id)->get();
            if (count($absence_log) > 0) {
                if ($absence_log[0]->absence_category_id != 9 && $absence_log[0]->absence_category_id != 10) {
                    $data[] = [base64_decode('ZGF0ZQ==') => $d->created_at, base64_decode('ZGF5X25hbWU=') => $d->day_name, base64_decode('bGlzdA==') => $absence_log];
                }
            }
        }
        return response()->json([
            base64_decode('bWVzc2FnZQ==') => base64_decode('c3VjY2Vzcw=='),
            base64_decode('ZGF0YQ==') => $data,
            base64_decode('dGVzc3M=') => $absence,
        ]);
    }

    public function schedule(Request $request)
    {
        $type = '';
        $schedule = [];
        $coordinat = WorkUnit::join(base64_decode('c3RhZmZz'), base64_decode('c3RhZmZzLndvcmtfdW5pdF9pZA=='), base64_decode('PQ=='), base64_decode('d29ya191bml0cy5pZA=='))
            ->join(base64_decode('d29ya190eXBlcw=='), base64_decode('c3RhZmZzLndvcmtfdHlwZV9pZA=='), base64_decode('PQ=='), base64_decode('d29ya190eXBlcy5pZA=='))
            ->where(base64_decode('c3RhZmZzLmlk'), $request->staff_id)->first();
        $type = $coordinat->type;
        if ($coordinat->type == base64_decode('c2hpZnQ=')) {

            $list_absence = ShiftPlannerStaffs::select(DB::raw(base64_decode('REFURShzaGlmdF9wbGFubmVyX3N0YWZmcy5zdGFydCkgQVMgZGF0ZQ==')), base64_decode('c2hpZnRfcGxhbm5lcl9zdGFmZnMuaWQgYXMgc2hpZnRfcGxhbm5lcl9pZA=='), base64_decode('c2hpZnRfcGxhbm5lcl9zdGFmZnMuc2hpZnRfZ3JvdXBfaWQ='))
                ->join(base64_decode('c2hpZnRfZ3JvdXBz'), base64_decode('c2hpZnRfcGxhbm5lcl9zdGFmZnMuc2hpZnRfZ3JvdXBfaWQ='), base64_decode('PQ=='), base64_decode('c2hpZnRfZ3JvdXBzLmlk'))
                ->leftJoin(base64_decode('YWJzZW5jZV9sb2dz'), base64_decode('c2hpZnRfcGxhbm5lcl9zdGFmZnMuaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9sb2dzLnNoaWZ0X3BsYW5uZXJfaWQ='))
                ->where(base64_decode('c2hpZnRfcGxhbm5lcl9zdGFmZnMuc3RhZmZfaWQ='), base64_decode('PQ=='), $request->staff_id)
                ->groupBy(base64_decode('c2hpZnRfcGxhbm5lcl9zdGFmZnMuaWQ='))
                ->whereDate(base64_decode('c2hpZnRfcGxhbm5lcl9zdGFmZnMuc3RhcnQ='), base64_decode('Pj0='), date(base64_decode('WS1tLWQ=')))
                ->orderBy(base64_decode('c2hpZnRfcGxhbm5lcl9zdGFmZnMuc3RhcnQ='), base64_decode('QVND'))
                ->get();

            foreach ($list_absence as $data) {
                $schedule[] = [
                    base64_decode('aWQ=') => $data->shift_planner_id,
                    base64_decode('ZGF0ZQ==') => $data->date,
                    base64_decode('bGlzdA==') => ShiftGroups::selectRaw(base64_decode('ZHVyYXRpb24sIGR1cmF0aW9uX2V4cCwgdHlwZSwgdGltZSwgc3RhcnQsIGFic2VuY2VfY2F0ZWdvcnlfaWQsc2hpZnRfZ3JvdXBfdGltZXNoZWV0cy5pZCBhcyBzaGlmdF9ncm91cF90aW1lc2hlZXRfaWQg'))
                        ->join(base64_decode('c2hpZnRfZ3JvdXBfdGltZXNoZWV0cw=='), base64_decode('c2hpZnRfZ3JvdXBfdGltZXNoZWV0cy5zaGlmdF9ncm91cF9pZA=='), base64_decode('PQ=='), base64_decode('c2hpZnRfZ3JvdXBzLmlk'))
                        ->join(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('c2hpZnRfZ3JvdXBfdGltZXNoZWV0cy5hYnNlbmNlX2NhdGVnb3J5X2lk'), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                        ->where(base64_decode('c2hpZnRfZ3JvdXBzLmlk'), $data->shift_group_id)
                        ->orderBy(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'), base64_decode('QVND'))
                        ->get()

                ];
            }
            $schedule = $schedule;
        } else {
            $day = Day::get();
            foreach ($day as $data) {
                $schedule[] = [
                    base64_decode('ZGF5') => $data->name,
                    base64_decode('bGlzdA==') => WorkTypeDays::selectRaw(base64_decode('ZHVyYXRpb24sIGR1cmF0aW9uX2V4cCwgdHlwZSwgdGltZSwgc3RhcnQsIGFic2VuY2VfY2F0ZWdvcnlfaWQsd29ya190eXBlX2RheXMuaWQgYXMgd29ya190eXBlX2RheV9pZCA='))
                        ->join(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('d29ya190eXBlX2RheXMuYWJzZW5jZV9jYXRlZ29yeV9pZA=='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
                        ->where(base64_decode('d29ya190eXBlX2lk'), $coordinat->work_type_id)
                        ->where(base64_decode('ZGF5X2lk'), $data->id)
                        ->orderBy(base64_decode('ZGF5X2lk'), base64_decode('QVND'))
                        ->get()

                ];
            }
        }
        return response()->json([
            base64_decode('bWVzc2FnZQ==') => base64_decode('c3VjY2Vzcw=='),
            base64_decode('dHlwZQ==') => $type,
            base64_decode('ZGF0YQ==') => $schedule,
        ]);
    }

    public function holiday(Request $request)
    {
        $holiday = Holiday::whereDate(base64_decode('c3RhcnQ='), base64_decode('Pj0='), date(base64_decode('WS1tLWQ=')))->paginate(3, [base64_decode('Kg==')], base64_decode('cGFnZQ=='), $request->page);
        return response()->json([
            base64_decode('bWVzc2FnZQ==') => base64_decode('c3VjY2Vzcw=='),
            base64_decode('ZGF0YQ==') => $holiday,
        ]);
    }

    public function storeExtra(Request $request)
    {


        $img_path = base64_decode('L2ltYWdlcy9hYnNlbmNl');
        $basepath = str_replace(base64_decode('bGFyYXZlbC1zaW1wbGV0YWI='), base64_decode('cHVibGljX2h0bWwvc2ltcGxldGFiYWRtaW4v'), \base_path());
        $responseImage = '';
        $data_image = '';
        if (date(base64_decode('dw==')) == base64_decode('MA==')) {
            $day = base64_decode('Nw==');
        } else {
            $day = date(base64_decode('dw=='));
        }

        if ($request->file(base64_decode('aW1hZ2U='))) {
            $resource_image = $request->file(base64_decode('aW1hZ2U='));
            $name_image = $request->staff_id;
            $file_ext_image = $request->file(base64_decode('aW1hZ2U='))->extension();

            $name_image = $img_path . base64_decode('Lw==') . $name_image . base64_decode('LQ==') . date(base64_decode('WS1tLWQgaDppOnM=')) . base64_decode('LWFic2VuY2Uu') . $file_ext_image;

            $image = $request->file(base64_decode('aW1hZ2U='));

            $imgFile = Image::make($image->getRealPath());

            $imgFile->insert($basepath . base64_decode('L2ltYWdlcy9Mb2dvLnBuZw=='), base64_decode('Ym90dG9tLXJpZ2h0'), 10, 10);

            $imgFile->text('' . Date(base64_decode('WS1tLWQgSDppOnM=')) . base64_decode('IGxhdCA6IA==') . $request->lat . base64_decode('IGxuZyA6IA==') . $request->lng, 10, 10, function ($font) {
                $font->file(str_replace(base64_decode('bGFyYXZlbC1zaW1wbGV0YWI='), base64_decode('cHVibGljX2h0bWwvc2ltcGxldGFiYWRtaW4v'), \base_path()) . base64_decode('L2ZvbnQvVGl0YW5pYS1SZWd1bGFyLnR0Zg=='));
                $font->size(14);
                $font->color(base64_decode('IzAwMDAwMA=='));
                $font->valign(base64_decode('dG9w'));
            })->save($basepath . base64_decode('Lw==') . $name_image);


            $data_image = $name_image;
        }


        if ($responseImage != '') {
            return response()->json([
                base64_decode('bWVzc2FnZQ==') => $responseImage,
            ]);
        }

        $absenceBefore = AbsenceLog::select(base64_decode('cmVnaXN0ZXI='))
            ->leftJoin(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))
            ->where(base64_decode('YWJzZW5jZV9pZA=='), $request->absence_id)
            ->where(base64_decode('cXVldWU='), base64_decode('MQ=='))
            ->where(base64_decode('dHlwZQ=='), base64_decode('ZXh0cmE='))
            ->first();

        $duration = 0;


        if ($absenceBefore != null) {
            $day1 = $absenceBefore->register;
        } else {
            $day1 = $absenceBefore->register;
        }


        $day1 = strtotime($day1);
        $day2 = date(base64_decode('WS1tLWQgSDppOnM='));
        $day2 = strtotime($day2);

        $duration = ($day2 - $day1) / 3600;

        if ($duration > 8) {
            $duration = 8;
        }

        $late = 0;
        $early = 0;
        try {
            $upload_image = AbsenceLog::where(base64_decode('aWQ='), $request->id)->first();



            $upload_image->late = $late;
            $upload_image->early = $early;


            $upload_image->image = $data_image;
            $upload_image->created_by_staff_id = $request->staff_id;
            $upload_image->updated_by_staff_id = $request->staff_id;
            $upload_image->register = date(base64_decode('WS1tLWQgSDppOnM='));
            $upload_image->duration = $duration;
            $upload_image->register = date(base64_decode('WS1tLWQgSDppOnM='));
            $upload_image->updated_at = date(base64_decode('WS1tLWQgSDppOnM='));
            $upload_image->lat = $request->lat;
            $upload_image->lng = $request->lng;
            $upload_image->status =  0;
            $upload_image->accuracy = $request->accuracy;
            $upload_image->distance = $request->distance;

            $upload_image->save();

            if ($upload_image->absence_request_id != '' && $upload_image->absence_request_id != null) {
                AbsenceRequest::where(base64_decode('aWQ='), $upload_image->absence_request_id)->update([base64_decode('c3RhdHVz') => base64_decode('Y2xvc2U=')]);
            }

            return response()->json([
                base64_decode('bWVzc2FnZQ==') => base64_decode('QWJzZW4gVGVya2lyaW0='),
                base64_decode('ZGF0YQ==') => $upload_image,
            ]);
        } catch (QueryException $ex) {
            return response()->json([
                base64_decode('bWVzc2FnZQ==') => base64_decode('Z2FnYWw='),
            ]);
        }
    }

    public function historyExtra(Request $request)
    {
        $data = [];
        $absence = Absence::join(base64_decode('ZGF5cw=='), base64_decode('ZGF5cy5pZA=='), base64_decode('PQ=='), base64_decode('YWJzZW5jZXMuZGF5X2lk'))
            ->join(base64_decode('YWJzZW5jZV9sb2dz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZXMuaWQ='))
            ->selectRaw(base64_decode('YWJzZW5jZXMuaWQsREFURShhYnNlbmNlcy5jcmVhdGVkX2F0KSBhcyBjcmVhdGVkX2F0LCBkYXlzLm5hbWUgYXMgZGF5X25hbWU='))
            ->where(base64_decode('c3RhZmZfaWQ='), $request->staff_id)
            ->where(base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('OQ=='))
            ->FilterDate($request->from, $request->to)
            ->groupBy(base64_decode('YWJzZW5jZXMuaWQ='))->get();

        foreach ($absence as $d) {
            $absence_log = AbsenceLog::join(base64_decode('YWJzZW5jZV9jYXRlZ29yaWVz'), base64_decode('YWJzZW5jZV9sb2dzLmFic2VuY2VfY2F0ZWdvcnlfaWQ='), base64_decode('PQ=='), base64_decode('YWJzZW5jZV9jYXRlZ29yaWVzLmlk'))->selectRaw(base64_decode('YWJzZW5jZV9sb2dzLiosIGFic2VuY2VfY2F0ZWdvcmllcy50aXRsZSBhcyBjYXRlZ29yeV90aXRsZQ=='))->where(base64_decode('YWJzZW5jZV9pZA=='), base64_decode('PQ=='), $d->id)->get();
            if (count($absence_log) > 0) {
                $data[] = [base64_decode('ZGF0ZQ==') => $d->created_at, base64_decode('ZGF5X25hbWU=') => $d->day_name, base64_decode('bGlzdA==') => $absence_log];
            }
        }
        return response()->json([
            base64_decode('bWVzc2FnZQ==') => base64_decode('c3VjY2Vzcw=='),
            base64_decode('ZGF0YQ==') => $data,
            base64_decode('dGVzc3M=') => $absence,
        ]);
    }

    public function sickAdd(Request $request)
    {
        $dataForm = json_decode($request->form);
        try {
            $absenceRequest =  AbsenceRequest::where(base64_decode('aWQ='), $dataForm->id)->first();
            if ($dataForm->end > $absenceRequest->end) {
                AbsenceRequest::where(base64_decode('aWQ='), $dataForm->id)->update([base64_decode('c3RhdHVz') => base64_decode('Y2xvc2U='), base64_decode('ZW5k') => $dataForm->end, base64_decode('YXR0ZW5kYW5jZQ==') => date(base64_decode('WS1tLWQgSDppOnM='), strtotime(base64_decode('LSA=') . 1 . base64_decode('IGRheXM='), strtotime(date(base64_decode('WS1tLWQg') . base64_decode('MjM6NTk6NTk=')))))]);
                $absenceLog = AbsenceLog::where(base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk'), $absenceRequest->id)->get();
                foreach ($absenceLog as $d) {
                    $deleteAbsence = Absence::where(base64_decode('aWQ='), $d->absence_id)->first();
                    if ($deleteAbsence) {
                        Absence::where(base64_decode('aWQ='), $d->id)->delete();
                    }
                }

                AbsenceLog::where(base64_decode('YWJzZW5jZV9yZXF1ZXN0X2lk'), $absenceRequest->id)->delete();

                $begin = strtotime($absenceRequest->start);
                $end   = strtotime($dataForm->end);

                for ($i = $begin; $i < $end; $i = $i + 86400) {
                    $holiday = Holiday::whereDate(base64_decode('c3RhcnQ='), base64_decode('PD0='), date(base64_decode('WS1tLWQ='), $i))->whereDate(base64_decode('ZW5k'), base64_decode('Pj0='), date(base64_decode('WS1tLWQ='), $i))->first();
                    if (!$holiday) {
                        if (date(base64_decode('dw=='), strtotime(date(base64_decode('WS1tLWQ='), $i))) != 0 && date(base64_decode('dw=='), strtotime(date(base64_decode('WS1tLWQ='), $i))) != 6) {

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
                return response()->json([
                    base64_decode('bWVzc2FnZQ==') => base64_decode('QWJzZW4gVGVya2lyaW0='),
                ]);
            } else {
                return response()->json([
                    base64_decode('bWVzc2FnZQ==') => base64_decode('dGlkYWsgYmlzYSBrdXJhbmcgZGFyaSB0YW5nZ2FsIHBlbmdhanVhbiBzZWJlbHVtbnlh'),
                ]);
            }
        } catch (QueryException $ex) {
            return response()->json([
                base64_decode('bWVzc2FnZQ==') => base64_decode('Z2FnYWw='),
            ]);
        }
    }
}
