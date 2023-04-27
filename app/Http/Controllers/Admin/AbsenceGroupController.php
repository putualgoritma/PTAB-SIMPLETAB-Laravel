<?php

namespace App\Http\Controllers\Admin;

use App\Absence;
use App\Absence_categories;
use App\Dapertement;
use App\Http\Controllers\Controller;
use App\Staff;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class AbsenceGroupController extends Controller
{
    public function index(Request $request)
    {
        $qry = Absence::selectRaw('staffs.*,absences.*')
            ->where('status_active', '!=', '')
            ->join('staffs', 'staffs.id', '=', 'absences.staff_id');
        // ->get();
        // dd($qry->get());

        if ($request->ajax()) {
            //set query
            $table = Datatables::of($qry);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $approveGate = 'absence_edit';
                // $editGate = '';
                // $deleteGate = '';
                $crudRoutePart = 'absencegroup';

                return view('partials.datatablesApprove', compact(
                    'approveGate',
                    // 'editGate',
                    // 'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });
            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : "";
            });

            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : "";
            });

            $table->editColumn('NIK', function ($row) {
                return $row->NIK ? $row->NIK : "";
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at : "";
            });

            $table->editColumn('status_active', function ($row) {
                if ($row->status_active == "1") {
                    return "Fingerprint Bermasalah";
                } else if ($row->status_active == "2") {
                    return "Lembur Mendesak";
                } else if ($row->status_active == "3") {
                    return "Permisi Tidak Kembail";
                }
                return $row->status_active ? $row->status_active : "";
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

        return view('admin.absenceGroup.index', compact('staffs', 'dapertements', 'absence_categories'));
    }
    public function approve(Request $request)
    {
        // dd("hjk");
        $absence = Absence::where('id', $request->id)->first();
        // dd($absence);
        $absence->update([
            'status_active' => ''
        ]);
        return back();
    }
}
