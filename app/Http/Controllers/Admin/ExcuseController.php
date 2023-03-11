<?php

namespace App\Http\Controllers\Admin;

use App\AbsenceRequest;
use App\AbsenceRequestLogs;
use App\Http\Controllers\Controller;
use App\MessageLog;
use App\Requests;
use App\Staff;
use App\User;
use App\WorkTypeDays;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ExcuseController extends Controller
{
    public function index(Request $request)
    {

        abort_unless(\Gate::allows('excuse_access'), 403);
        $qry = AbsenceRequest::selectRaw('absence_requests.*, staffs.name as staff_name')->join('staffs', 'staffs.id', '=', 'absence_requests.staff_id')
            ->where('absence_requests.category', 'excuse')
            ->FilterStatus($request->status)
            ->orderBy('absence_requests.created_at', 'DESC');
        // dd($qry->get());
        if ($request->ajax()) {
            //set query
            $table = Datatables::of($qry);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'duty_access';
                $editGate = '';
                $deleteGate = 'excuse_delete';
                $crudRoutePart = 'excuse';

                return view('partials.datatablesDuties', compact(
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
                return $row->staff_name ? $row->staff_name : "";
            });

            $table->editColumn('desciption', function ($row) {
                return $row->desciption ? $row->desciption : "";
            });

            $table->editColumn('start', function ($row) {
                return $row->start ? date("Y-m-d", strtotime($row->start)) : "";
            });

            $table->editColumn('end', function ($row) {
                return $row->end ? $row->end : "";
            });
            $table->editColumn('type', function ($row) {
                return $row->type ? $row->type : "";
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? $row->status : "";
            });
            $table->editColumn('time', function ($row) {
                return $row->time ? $row->time : "";
            });
            $table->editColumn('category', function ($row) {
                return $row->category ? $row->category : "";
            });


            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at : "";
            });
            $table->editColumn('updated_at', function ($row) {
                return $row->updated_at ? $row->updated_at : "";
            });

            $table->rawColumns(['actions', 'placeholder']);

            $table->addIndexColumn();
            return $table->make(true);
        }
        return view('admin.excuse.index');
    }
    public function create(Request $request)
    {
        abort_unless(\Gate::allows('excuse_create'), 403);
        $type = $request->type;
        $staffs = Staff::orderBy('name')->get();
        return view('admin.excuse.create', compact('staffs', 'type'));
    }
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('excuse_create'), 403);
        // $staff = Staff::selectRaw('work_types.type')
        //     ->join('work_types', 'staffs.work_type_id', '=', 'work_types.id')
        //     ->where('id', $request->staff_id)->first();

        // if ($staff->type == 'shift') {
        //     $day = date('w', $$request->start);
        //     $work_type_day = WorkTypeDays::where('day_id', $day)
        //         ->where('type', 'presence')
        //         ->orderBy('queue', 'Desc')->first();
        //     $end = $work_type_day->end;
        // } else {
        //     $day = date('w', $$request->start);
        //     $work_type_day = WorkTypeDays::where('day_id', $day)
        //         ->where('type', 'presence')
        //         ->orderBy('queue', 'Desc')->first();
        //     $end = $work_type_day->end;
        // }

        $data = [
            'category' => 'excuse',
            'type' => 'other',
            'staff_id' => $request->staff_id,
            'time' => $request->time,
            'start' => date("Y-m-d H:i:s", strtotime($request->start . $request->time)),
            'end' => date("Y-m-d H:i:s", strtotime($request->start . '23:59:59')),
            'description' => $request->description,
        ];
        $excuse = AbsenceRequest::create($data);
        return redirect()->route('admin.excuse.index');
    }

    public function show($id)
    {
        abort_unless(\Gate::allows('excuse_access'), 403);
        $excuse = AbsenceRequest::selectRaw('absence_requests.*, staffs.name as staff_name')
            ->join('staffs', 'staffs.id', '=', 'absence_requests.staff_id')
            ->where('absence_requests.id', $id)->first();

        $file = AbsenceRequestLogs::where('absence_request_id', $id)->get();

        // dd($excuse, $file);
        return view('admin.excuse.show', compact('excuse', 'file'));
    }

    public function edit($id)
    {
        abort_unless(\Gate::allows('excuse_edit'), 403);
        $requests = Requests::where('id', $id)->first();
        $type = $requests->type;
        $users = User::where('staff_id', '!=', '0')->orderBy('name')->get();
        return view('admin.excuse.edit', compact('users', 'type', 'requests'));
    }
    public function update(Request $request, $id)
    {
        abort_unless(\Gate::allows('excuse_edit'), 403);
        $excuse = Requests::where('id', $id)
            ->update($request->except(['_token', '_method']));
        return redirect()->route('admin.excuse.index');
    }


    public function reject($id)
    {
        abort_unless(\Gate::allows('excuse_edit'), 403);
        $d = AbsenceRequest::where('id', $id)
            ->update(['status' => 'reject']);
        $d = AbsenceRequest::where('id', $id)->first();
        MessageLog::create([
            'staff_id' => $d->staff_id,
            'memo' => "permisi anda tanggal " . $d->start . " ditolak",
            'type' => 'message',
            'status' => 'pending',
        ]);
        // dd($d);
        return redirect()->back();
    }
    public function approve($id)
    {
        abort_unless(\Gate::allows('excuse_delete'), 403);
        $d = AbsenceRequest::where('id', $id)
            ->update(['status' => 'approve']);

        $d = AbsenceRequest::where('id', $id)->first();
        // dd($d);
        MessageLog::create([
            'staff_id' => $d->staff_id,
            'memo' => "permisi anda tanggal " . $d->start . " sudah disetujui",
            'type' => 'message',
            'status' => 'pending',
        ]);
        // dd($message);

        return redirect()->back();
    }
    public function destroy($id)
    {
        abort_unless(\Gate::allows('excuse_delete'), 403);
        AbsenceRequest::where('id', $id)
            ->delete();
        return redirect()->back();
    }
}
