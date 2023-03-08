<?php

namespace App\Http\Controllers\Admin;

use App\AbsenceRequest;
use App\Http\Controllers\Controller;
use App\MessageLog;
use App\Requests;
use App\Requests_file;
use App\Staff;
use App\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class WorkPermitController extends Controller
{
    public function index(Request $request)
    {

        abort_unless(\Gate::allows('workpermit_access'), 403);
        $qry = AbsenceRequest::selectRaw('absence_requests.*, staffs.name as staff_name')->join('staffs', 'staffs.id', '=', 'absence_requests.staff_id')
            ->where('absence_requests.category', 'permission')
            ->orderBy('absence_requests.created_at', 'DESC');
        // dd($qry->get());
        if ($request->ajax()) {
            //set query
            $table = Datatables::of($qry);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = '';
                $editGate = '';
                $deleteGate = 'workpermit_delete';
                $crudRoutePart = 'workPermit';

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
        return view('admin.workPermit.index');
    }
    public function create(Request $request)
    {
        abort_unless(\Gate::allows('workpermit_create'), 403);
        $type = $request->type;
        $staffs = Staff::orderBy('name')->get();
        return view('admin.workPermit.create', compact('staffs', 'type'));
    }
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('workpermit_create'), 403);
        $data = [
            'category' => 'permission',
            'type' => 'other',
            'staff_id' => $request->staff_id,
            'end' => $request->end,
            'start' => $request->start,
            'description' => $request->description,
        ];
        $workPermit = AbsenceRequest::create($data);
        return redirect()->route('admin.workPermit.index');
    }

    public function edit($id)
    {
        abort_unless(\Gate::allows('workpermit_edit'), 403);
        $requests = Requests::where('id', $id)->first();
        $type = $requests->type;
        $users = User::where('staff_id', '!=', '0')->orderBy('name')->get();
        return view('admin.workPermit.edit', compact('users', 'type', 'requests'));
    }
    public function update(Request $request, $id)
    {
        abort_unless(\Gate::allows('workpermit_edit'), 403);
        $workPermit = Requests::where('id', $id)
            ->update($request->except(['_token', '_method']));
        return redirect()->route('admin.workPermit.index');
    }


    public function reject($id)
    {
        abort_unless(\Gate::allows('workpermit_edit'), 403);
        $d = AbsenceRequest::where('id', $id)
            ->update(['status' => 'reject']);
        $d = AbsenceRequest::where('id', $id)->first();
        MessageLog::create([
            'staff_id' => $d->staff_id,
            'memo' => "permisi anda tanggal " . $d->start . " disetujui",
            'type' => 'message',
            'status' => 'pending',
        ]);
        return redirect()->back();
    }
    public function approve($id)
    {
        abort_unless(\Gate::allows('workpermit_delete'), 403);
        $d = AbsenceRequest::where('id', $id)
            ->update(['status' => 'approve']);
        $d = AbsenceRequest::where('id', $id)->first();
        MessageLog::create([
            'staff_id' => $d->staff_id,
            'memo' => "permisi anda tanggal " . $d->start . " disetujui",
            'type' => 'message',
            'status' => 'pending',
        ]);

        return redirect()->back();
    }
    public function destroy($id)
    {
        abort_unless(\Gate::allows('workpermit_delete'), 403);
        AbsenceRequest::where('id', $id)
            ->delete();
        return redirect()->back();
    }
}
