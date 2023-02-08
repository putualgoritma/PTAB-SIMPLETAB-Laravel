<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Requests;
use App\Requests_file;
use App\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class WorkPermitController extends Controller
{
    public function index(Request $request)
    {

        abort_unless(\Gate::allows('workpermit_access'), 403);
        $qry = Requests::selectRaw('requests.*, users.name as user_name')->join('users', 'users.id', '=', 'requests.user_id')->where('requests.category', 'permission')
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
        return view('admin.workPermit.index');
    }
    public function create(Request $request)
    {
        abort_unless(\Gate::allows('workpermit_create'), 403);
        $type = $request->type;
        $users = User::where('staff_id', '!=', '0')->orderBy('name')->get();
        return view('admin.workPermit.create', compact('users', 'type'));
    }
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('workpermit_create'), 403);
        $data = array_merge($request->all(), ['category' => 'permission']);
        $leave = Requests::create($data);
        if ($request->file('image')) {
            $image = $request->file('image');
            $resourceImage = $image;
            $nameImage = 'imagePermit' . date('Y-m-d h:i:s') . '.' . $image->extension();
            $file_extImage = $image->extension();
            $folder_upload = 'images/RequestFile';
            $resourceImage->move($folder_upload, $nameImage);

            // dd($request->file('old_image')->move($folder_upload, $img_name));

            // if ($actionWm->old_image != '') {
            //     foreach (json_decode($actionWm->old_image) as $n) {
            //         if (file_exists($n)) {

            //             unlink($basepath . $n);
            //         }
            //     }
            // }
            $data = [
                'file' => $nameImage,
                'requests_id' => $leave->id,
                'type' => 'persetujuan'
            ];
            $data = Requests_file::create($data);
            return redirect()->route('admin.workPermit.index');
        }

        // if ($request->file('imagePng')) {
        //     $image = $request->file('imagePng');
        //     $resourceImage = $image;
        //     $nameImage = 'imagePng' . date('Y-m-d h:i:s') . '.' . $image->extension();
        //     $file_extImage = $image->extension();
        //     $folder_upload = 'images/RequestFile';
        //     $resourceImage->move($folder_upload, $nameImage);


        //     $data = [
        //         'file' =>  $nameImage,
        //         'requests_id' => $leave->id,
        //         'type' => 'pengajuan'
        //     ];
        //     $data = Requests_file::create($data);
        // }
    }

    public function edit($id)
    {
        abort_unless(\Gate::allows('workpermit_edit'), 403);
        $requests = Requests::where('id', $id)->where('requests.category', 'permission')->first();
        $type = $requests->type;
        $users = User::where('staff_id', '!=', '0')->orderBy('name')->get();
        return view('admin.WorkPermit.edit', compact('users', 'type', 'requests'));
    }
    public function update(Request $request, $id)
    {
        abort_unless(\Gate::allows('workpermit_edit'), 403);
        $workPermit = Requests::where('id', $id)->where('requests.category', 'permission')
            ->update($request->all());
        return redirect()->route('admin.workPermit.index');
    }


    public function reject($id)
    {
        abort_unless(\Gate::allows('workpermit_edit'), 403);
        $d = Requests::where('id', $id)->where('requests.category', 'permission')
            ->update(['status' => 'reject']);
        return redirect()->back();
    }
    public function approve($id)
    {
        abort_unless(\Gate::allows('workpermit_edit'), 403);
        $d = Requests::where('id', $id)->where('requests.category', 'permission')
            ->update(['status' => 'approve']);
        // dd($d);
        return redirect()->back();
    }
    public function destroy($id)
    {
        abort_unless(\Gate::allows('workpermit_delete'), 403);
        Requests::where('id', $id)->delete();
        return redirect()->route('admin.workPermit.index');
    }
}
