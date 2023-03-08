<?php

namespace App\Http\Controllers\Admin;

use App\AbsenceRequest;
use App\Http\Controllers\Controller;
use App\MessageLog;
use App\Requests;
use App\Staff;
use App\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DutyController extends Controller
{
    public function index(Request $request)
    {

        abort_unless(\Gate::allows('duty_access'), 403);
        $qry = AbsenceRequest::selectRaw('absence_requests.*, staffs.name as staff_name')
            ->join('staffs', 'staffs.id', '=', 'absence_requests.staff_id')
            ->where('absence_requests.category', 'duty')
            ->orWhere('absence_requests.category', 'visit');
        // dd($qry->get());
        if ($request->ajax()) {
            //set query
            $table = Datatables::of($qry);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = '';
                $editGate = 'duty_edit';
                $deleteGate = 'duty_delete';
                $crudRoutePart = 'duty';

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

            $table->editColumn('time', function ($row) {
                return $row->time ? $row->time : "";
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
        return view('admin.duty.index');
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
        MessageLog::create([
            'staff_id' => $d->staff_id,
            'memo' => "Dinas Luar anda tanggal " . $d->start . " sampai dengan " . $d->end . " ditolak",
            'type' => 'message',
            'status' => 'pending',
        ]);


        return redirect()->route('admin.duty.index');
    }
    public function approve($id)
    {
        abort_unless(\Gate::allows('duty_edit'), 403);
        $d = AbsenceRequest::where('id', $id)
            ->update(['status' => 'approve']);

        $d = AbsenceRequest::where('id', $id)->first();
        MessageLog::create([
            'staff_id' => $d->staff_id,
            'memo' => "Dinas Luar anda tanggal " . $d->start . " ditolak",
            'type' => 'message',
            'status' => 'pending',
        ]);


        return redirect()->back();
    }

    public function destroy($id)
    {
        abort_unless(\Gate::allows('duty_delete'), 403);
        AbsenceRequest::where('id', $id)->delete();
        return redirect()->back();
    }
}
