<?php

namespace App\Http\Controllers\Admin;

use App\AbsenceRequest;
use App\AbsenceRequestLogs;
use App\Http\Controllers\Controller;
use App\MessageLog;
use App\Requests;
use App\Staff;
use App\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ExtraController extends Controller
{
    public function index(Request $request)
    {

        abort_unless(\Gate::allows('extra_access'), 403);
        $qry = AbsenceRequest::selectRaw('absence_requests.*, staffs.name as staff_name')->join('staffs', 'staffs.id', '=', 'absence_requests.staff_id')
            ->where('absence_requests.category', 'extra')
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
                $deleteGate = 'extra_delete';
                $crudRoutePart = 'extra';

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
                return $row->start ? $row->start : "";
            });

            $table->editColumn('end', function ($row) {
                return $row->end ? $row->end : "";
            });
            $table->editColumn('type', function ($row) {
                return $row->type ? $row->type : "";
            });
            $table->editColumn('start', function ($row) {
                return $row->start ? $row->start : "";
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? $row->status : "";
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
        return view('admin.extra.index');
    }
    public function create(Request $request)
    {
        abort_unless(\Gate::allows('extra_create'), 403);
        $type = $request->type;
        $staffs = Staff::orderBy('name')->get();
        return view('admin.extra.create', compact('staffs', 'type'));
    }
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('extra_create'), 403);
        $data = [
            'category' => 'extra',
            'type' => 'other',
            'staff_id' => $request->staff_id,
            'time' => $request->time,
            'start' => $request->start,
            'description' => $request->description,
        ];
        $extra = AbsenceRequest::create($data);
        return redirect()->route('admin.extra.index');
    }

    public function show($id)
    {
        abort_unless(\Gate::allows('extra_access'), 403);
        $extra = AbsenceRequest::selectRaw('absence_requests.*, staffs.name as staff_name')
            ->join('staffs', 'staffs.id', '=', 'absence_requests.staff_id')
            ->where('absence_requests.id', $id)->first();

        $file = AbsenceRequestLogs::where('absence_request_id', $id)->get();

        // dd($extra, $file);
        return view('admin.extra.show', compact('extra', 'file'));
    }

    public function edit($id)
    {
        abort_unless(\Gate::allows('extra_edit'), 403);
        $requests = Requests::where('id', $id)->first();
        $type = $requests->type;
        $users = User::where('staff_id', '!=', '0')->orderBy('name')->get();
        return view('admin.extra.edit', compact('users', 'type', 'requests'));
    }
    public function update(Request $request, $id)
    {
        abort_unless(\Gate::allows('extra_edit'), 403);
        $extra = Requests::where('id', $id)
            ->update($request->except(['_token', '_method']));
        return redirect()->route('admin.extra.index');
    }


    public function reject($id)
    {
        abort_unless(\Gate::allows('extra_edit'), 403);
        $d = AbsenceRequest::where('id', $id)
            ->update(['status' => 'reject']);

        $d = AbsenceRequest::where('id', $id)->first();
        MessageLog::create([
            'staff_id' => $d->staff_id,
            'memo' => "Lembur anda tanggal " . $d->start . " ditolak",
            'type' => 'message',
            'status' => 'pending',
        ]);


        return redirect()->back();
    }
    public function approve($id)
    {
        abort_unless(\Gate::allows('extra_delete'), 403);
        $d = AbsenceRequest::where('id', $id)
            ->update(['status' => 'approve']);

        $d = AbsenceRequest::where('id', $id)->first();
        MessageLog::create([
            'staff_id' => $d->staff_id,
            'memo' => "Lembur anda tanggal " . $d->start . " diterima",
            'type' => 'message',
            'status' => 'pending',
        ]);


        return redirect()->back();
    }
}
