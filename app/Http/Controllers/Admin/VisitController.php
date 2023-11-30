<?php

namespace App\Http\Controllers\Admin;

use App\Absence;
use App\AbsenceLog;
use App\AbsenceRequest;
use App\AbsenceRequestLogs;
use App\CtmWilayah;
use App\Http\Controllers\Controller;
use App\MessageLog;
use App\Requests;
use App\Staff;
use OneSignal;
use App\Traits\WablasTrait;
use App\wa_history;
use App\User;
use App\Visit;
use App\VisitImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class VisitController extends Controller
{
    use WablasTrait;
    public function index(Request $request)
    {

        // abort_unless(\Gate::allows('visit_access'), 403);
        $checker = [];
        $users = user::with(['roles'])
            ->where('id', Auth::user()->id)
            ->first();
        // foreach ($users->roles as $data) {
        //     foreach ($data->permissions as $data2) {
        //         $checker[] = $data2->title;
        //     }
        // }
        // $subdapertement = Auth::user()->subdapertement_id != '0' ? Auth::user()->subdapertement_id : '';
        // if (!in_array('absence_all_access', $checker)) {
        $qry = Visit::selectRaw('visits.*, tblstatuswm.NamaStatus')
            ->leftJoin('ptabroot_ctm.tblstatuswm', 'tblstatuswm.id', '=', 'visits.status_wm')
            // ->leftJoin('ptabroot_ctm.tblpelanggan', 'tblpelanggan.nomorrekening', '=', 'visits.customer_id')
            ->FilterDate($request->from, $request->to)
            // ->FilterArea($request->area)
            // ->whereHas('customer', function ($q) {
            //     $q->where('idareal', 'K010101');
            // })
            ->FilterType($request->type);
        // dd($qry->get()[0]->customer);

        // } else {
        //     $qry = AbsenceRequest::selectRaw('absence_requests.*, staffs.name as staff_name')
        //         ->join('staffs', 'staffs.id', '=', 'absence_requests.staff_id')
        //         ->where(function ($query) {
        //             $query->where('absence_requests.category', 'duty')
        //                 ->orWhere('absence_requests.category', 'visit');
        //         })
        //         ->FilterCategory($request->category)->FilterStatus($request->status)
        //         ->FilterCategory($request->category)->FilterStatus($request->status)
        //         ->FilterDateStart($request->from, $request->to);
        // }


        // ->orderBy('staffs.NIK');
        // ->orderBy('absence_requests.created_at', 'DESC');
        // dd($qry->get());
        if ($request->ajax()) {
            //set query
            $table = Datatables::of($qry);

            $table->addColumn('placeholder', '');
            $table->addColumn('actions', '');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'duty_access';
                $editGate = '';
                $deleteGate = 'duty_delete';
                $crudRoutePart = 'visit';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });
            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : "";
            });

            $table->editColumn('staff_name', function ($row) {
                return $row->staff->name ? $row->staff->name : "";
            });

            $table->editColumn('desciption', function ($row) {
                return $row->desciption ? $row->desciption : "";
            });

            $table->editColumn('NamaStatus', function ($row) {
                return $row->NamaStatus ? $row->NamaStatus : "";
            });

            $table->editColumn('idareal', function ($row) {
                return $row->customer ? $row->customer->idareal : "";
            });

            $table->editColumn('nomorrekening', function ($row) {
                return $row->customer ? $row->customer->nomorrekening : "";
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at : "";
            });
            $table->editColumn('map', function ($row) {
                return '<a href="https://maps.google.com/?q=' . $row->lat . ',' . $row->lng . '" target="_blank"><i class="fa fa-map-marker" aria-hidden="true" style="font-size:30px;color:red;"></i> Buka Map</a>';
            });

            $table->rawColumns(['actions', 'placeholder']);

            $table->addIndexColumn();
            $table->rawColumns(['map']);
            return $table->make(true);
        }
        return view('admin.visit.index');
    }
    public function create(Request $request)
    {
        abort_unless(\Gate::allows('duty_create'), 403);
        $type = $request->type;
        $staffs = Staff::orderBy('name')->get();
        return view('admin.duty.create', compact('staffs', 'type'));
    }
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('duty_create'), 403);
        $duty = AbsenceRequest::create($request->all());
        return redirect()->route('admin.duty.index');
    }

    public function show($id)
    {
        abort_unless(\Gate::allows('duty_access'), 403);

        $visit = Visit::selectRaw('visits.*, tblstatuswm.NamaStatus')
            ->leftJoin('ptabroot_ctm.tblstatuswm', 'tblstatuswm.id', '=', 'visits.status_wm')
            ->where('visits.id', $id)->first();
        // dd($visit->visitImages);
        // dd($duty, $file);
        return view('admin.visit.show', compact('visit'));
    }

    public function reportForm(Request $request)
    {
        abort_unless(\Gate::allows('duty_create'), 403);
        $areas = CtmWilayah::select('id as code', 'NamaWilayah')->get();
        // dd($areas);

        return view('admin.visit.reportForm', compact('areas'));
    }

    public function report(Request $request)
    {
        abort_unless(\Gate::allows('duty_access'), 403);

        $visits = Visit::selectRaw('visits.*, tblstatuswm.NamaStatus')
            ->leftJoin('ptabroot_ctm.tblstatuswm', 'tblstatuswm.id', '=', 'visits.status_wm')
            ->leftJoin('ptabroot_ctm.tblpelanggan', 'tblpelanggan.nomorrekening', '=', 'visits.customer_id')
            ->FilterDate($request->from, $request->to)
            ->FilterArea($request->area)
            ->FilterType($request->type)
            ->get();
        $from = $request->from;
        $to = $request->to;

        // dd($visit->visitImages);
        // dd($duty, $file);
        return view('admin.visit.report', compact('visits', 'from', 'to'));
    }

    public function edit($id)
    {
        abort_unless(\Gate::allows('duty_edit'), 403);
        $requests = AbsenceRequest::where('id', $id)->first();
        $type = $requests->type;
        $staffs = Staff::orderBy('name')->get();
        return view('admin.duty.edit', compact('staffs', 'type', 'requests'));
    }
    public function update(Request $request, $id)
    {
        abort_unless(\Gate::allows('duty_edit'), 403);
        $duty = AbsenceRequest::where('id', $id)
            ->update($request->except(['_token', '_method']));

        return redirect()->route('admin.duty.index');
    }


    public function reject($id)
    {
        abort_unless(\Gate::allows('duty_edit'), 403);
        $d = AbsenceRequest::where('id', $id)
            ->update(['status' => 'reject']);

        $d = AbsenceRequest::where('id', $id)->first();
        $message = "Dinas anda tanggal " . $d->start . " sampai dengan " . $d->end . " ditolak";
        MessageLog::create([
            'staff_id' => $d->staff_id,
            'memo' => $message,
            'type' => 'message',
            'status' => 'pending',
        ]);
        // untuk Notif start
        $admin = Staff::selectRaw('users.*')->where('staffs.id', $d->staff_id)->join('users', 'users.staff_id', '=', 'staffs.id')->first();
        $id_onesignal = $admin->_id_onesignal;
        // $message = 'Admin: Keluhan Baru Diterima : ' . $dataForm->description;
        //wa notif                
        $wa_code = date('y') . date('m') . date('d') . date('H') . date('i') . date('s');
        $wa_data_group = [];
        //get phone user
        if ($d->staff_id > 0) {
            $staff = Staff::where('id', $d->staff_id)->first();
            $phone_no = $staff->phone;
        } else {
            $phone_no = $admin->phone;
        }
        $wa_data = [
            'phone' => $this->gantiFormat($phone_no),
            'customer_id' => null,
            'message' => $message,
            'template_id' => '',
            'status' => 'gagal',
            'ref_id' => $wa_code,
            'created_at' => date('Y-m-d h:i:sa'),
            'updated_at' => date('Y-m-d h:i:sa')
        ];
        $wa_data_group[] = $wa_data;
        DB::table('wa_histories')->insert($wa_data);
        $wa_sent = WablasTrait::sendText($wa_data_group);
        $array_merg = [];
        if (!empty(json_decode($wa_sent)->data->messages)) {
            $array_merg = array_merge(json_decode($wa_sent)->data->messages, $array_merg);
        }
        foreach ($array_merg as $key => $value) {
            if (!empty($value->ref_id)) {
                wa_history::where('ref_id', $value->ref_id)->update(['id_wa' => $value->id, 'status' => ($value->status === false) ? "gagal" : $value->status]);
            }
        }

        //onesignal notif                                
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
        // untuk notif end


        return redirect()->route('admin.duty.index');
    }
    public function approve($id)
    {
        // $d = AbsenceRequest::where('id', $id)->first();
        // $test = User::where('staff_id', $d->staff_id)->first();
        // $test2 = User::where('dapertement_id', $test->dapertement_id)->first();
        // dd($test2);
        abort_unless(\Gate::allows('duty_edit'), 403);
        $d = AbsenceRequest::where('id', $id)
            ->update(['status' => 'approve']);

        $d = AbsenceRequest::where('id', $id)->first();
        $message = "Dinas anda tanggal " . $d->start . " sampai dengan " . $d->end . " Disetujui";
        MessageLog::create([
            'staff_id' => $d->staff_id,
            'memo' => $message,
            'type' => 'message',
            'status' => 'pending',
        ]);

        // if ($d->category == "duty") {
        //     $begin = strtotime($d->start);
        //     // $end   = strtotime(date('Y-m-d'));
        //     $end   = strtotime($d->end);

        //     $list_abs = Absence::select('absences.*')->join('absence_logs', 'absences.id', '=', 'absence_logs.absence_id')
        //         ->where('absences.staff_id', $d->staff_id)
        //         ->whereDate('absences.created_at', '>=', $d->start)
        //         ->where('absence_logs.absence_request_id', $d->id)
        //         // ->where('absence_category_id', $absenceRequest->category == "duty" ? 7 : 8)
        //         ->get();
        //     foreach ($list_abs as $data) {

        //         AbsenceLog::where('absence_id', $data->id)->delete();
        //         // dd('error');
        //         $data->delete();
        //         # code...
        //     }


        //     for ($i = $begin; $i <= $end; $i = $i + 86400) {
        //         // $holiday = Holiday::whereDate('start', '=', date('Y-m-d', $i))->first();
        //         // if (!$holiday) {
        //         if (date("w", strtotime(date('Y-m-d', $i))) != 0) {
        //             $day =  date("w", strtotime(date('Y-m-d', $i)));
        //         } else {
        //             $day = 7;
        //         }

        //         $ab1 =  Absence::create([
        //             'day_id' => $day,
        //             'staff_id' => $d->staff_id,
        //             'created_at' => date('Y-m-d H:i:s', $i),
        //             'updated_at' => date('Y-m-d H:i:s')
        //         ]);
        //         AbsenceLog::create([
        //             'absence_category_id' => 7,
        //             'absence_request_id' => $d->id,
        //             'lat' => '',
        //             'lng' => '',
        //             'register' => date('Y-m-d', $i),
        //             'absence_id' => $ab1->id,
        //             'duration' => '',
        //             'status' => ''
        //         ]);
        //     }
        // }


        // untuk Notif start
        $admin = Staff::selectRaw('users.*')->where('staffs.id', $d->staff_id)->join('users', 'users.staff_id', '=', 'staffs.id')->first();
        $id_onesignal = $admin->_id_onesignal;
        // $message = 'Admin: Keluhan Baru Diterima : ' . $dataForm->description;
        //wa notif                
        $wa_code = date('y') . date('m') . date('d') . date('H') . date('i') . date('s');
        $wa_data_group = [];
        //get phone user
        if ($d->staff_id > 0) {
            $staff = Staff::where('id', $d->staff_id)->first();
            $phone_no = $staff->phone;
        } else {
            $phone_no = $admin->phone;
        }
        $wa_data = [
            'phone' => $this->gantiFormat($phone_no),
            'customer_id' => null,
            'message' => $message,
            'template_id' => '',
            'status' => 'gagal',
            'ref_id' => $wa_code,
            'created_at' => date('Y-m-d h:i:sa'),
            'updated_at' => date('Y-m-d h:i:sa')
        ];
        $wa_data_group[] = $wa_data;
        DB::table('wa_histories')->insert($wa_data);
        $wa_sent = WablasTrait::sendText($wa_data_group);
        $array_merg = [];
        if (!empty(json_decode($wa_sent)->data->messages)) {
            $array_merg = array_merge(json_decode($wa_sent)->data->messages, $array_merg);
        }
        foreach ($array_merg as $key => $value) {
            if (!empty($value->ref_id)) {
                wa_history::where('ref_id', $value->ref_id)->update(['id_wa' => $value->id, 'status' => ($value->status === false) ? "gagal" : $value->status]);
            }
        }

        //onesignal notif                                
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
        // untuk notif end


        return redirect()->back();
    }

    public function destroy($id)
    {
        abort_unless(\Gate::allows('duty_delete'), 403);
        AbsenceRequest::where('id', $id)->delete();
        return redirect()->back();
    }
}
