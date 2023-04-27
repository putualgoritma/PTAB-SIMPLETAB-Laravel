<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\StaffImport;
use App\Staff;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class GawatDaruratController extends Controller
{
    public function index()
    {
        return view('admin.gawatdarurat.editImport');
    }
    public function store(Request $request)
    {
        // Staff::where('id', $id)->first();

    }

    public function import(Request $request)
    {
        abort_unless(\Gate::allows('staff_edit'), 403);
        $import = new StaffImport;
        $test =  Excel::import($import, $request->file('file'));
        // dd($test);
        $array = $import->getArray();
        // dd($array);
        abort_unless(\Gate::allows('wablast_access'), 403);

        $staffs = $import->getArray();

        // dd($staffs);
        ini_set("memory_limit", -1);
        set_time_limit(0);
        //ini test

        // dd($staffs[2]['id']);
        for ($i = 0; $i < (count($staffs) - 1); $i++) {
            if ($staffs[$i]['work_unit_id'] != null && $staffs[$i]['id'] != null) {
                $staff = Staff::where('id', $staffs[$i]['id'])->update([
                    'work_unit_id' => $staffs[$i]['work_unit_id']
                ]);
                // dd($staff);
                // $staff->work_unit_id = $staffs[$i]['work_unit_id'];
                // $staff->nomorhp = $staffs[$i]['nomorhp'];
                // $staff->_synced = 0;
                // $staff->save();
                // dd($staff);
            }
        }
        dd($staffs);

        // return redirect()->route('admin.staffs.index');

        dd($request->file('file'));
        Staff::where('id', $id)->first();
    }
}
