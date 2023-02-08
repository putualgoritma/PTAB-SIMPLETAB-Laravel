<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Requests;
use App\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PermitController extends Controller
{
    public function index(Request $request)
    {

        abort_unless(\Gate::allows('permit_access'), 403);
        $qry = Requests::selectRaw('requests.*, users.name as user_name')->join('users', 'users.id', '=', 'requests.user_id')->where('requests.category', 'permit')
            ->orderBy('requests.created_at', 'DESC');
        // dd($qry);
        if ($request->ajax()) {
            //set query
            $table = Datatables::of($qry->orderBy('requests.created_at', 'DESC'));

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = '';
                $editGate = '';
                $deleteGate = 'permit_delete';
                $crudRoutePart = 'permit';

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

            $table->editColumn('user_name', function ($row) {
                return $row->user_name ? $row->user_name : "";
            });

            $table->editColumn('desciption', function ($row) {
                return $row->desciption ? $row->desciption : "";
            });

            $table->editColumn('date', function ($row) {
                return $row->date ? $row->date : "";
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
        return view('admin.permit.index');
    }
    public function create(Request $request)
    {
        abort_unless(\Gate::allows('permit_create'), 403);
        $type = $request->type;
        $users = User::where('staff_id', '!=', '0')->orderBy('name')->get();
        return view('admin.permit.create', compact('users', 'type'));
    }
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('permit_create'), 403);
        $data = [
            'category' => 'permit',
            'type' => 'permit',
            'user_id' => $request->user_id,
            'start' => $request->start,
            'date' => $request->date,
            'description' => $request->description,
        ];
        $permit = Requests::create($data);
        return redirect()->route('admin.permit.index');
    }

    public function edit($id)
    {
        abort_unless(\Gate::allows('permit_edit'), 403);
        $requests = Requests::where('id', $id)->first();
        $type = $requests->type;
        $users = User::where('staff_id', '!=', '0')->orderBy('name')->get();
        return view('admin.permit.edit', compact('users', 'type', 'requests'));
    }
    public function update(Request $request, $id)
    {
        abort_unless(\Gate::allows('permit_edit'), 403);
        $permit = Requests::where('id', $id)
            ->update($request->except(['_token', '_method']));
        return redirect()->route('admin.permit.index');
    }


    public function reject($id)
    {
        abort_unless(\Gate::allows('permit_edit'), 403);
        $d = Requests::where('id', $id)
            ->update(['status' => 'reject']);
        return redirect()->back();
    }
    public function approve($id)
    {
        abort_unless(\Gate::allows('permit_edit'), 403);
        $d = Requests::where('id', $id)
            ->update(['status' => 'approve']);
        return redirect()->route('admin.permit.index');
    }
    public function destroy($id)
    {
        abort_unless(\Gate::allows('permit_delete'), 403);
        Requests::where('id', $id)->delete();
        return redirect()->route('admin.permit.index');
    }
}
