<?php

namespace App\Http\Controllers\Admin;

use App\Absence;
use App\Absence_categories;
use App\Dapertement;
use App\Day;
use App\Http\Controllers\Controller;
use App\Job;
use Illuminate\Http\Request;
use App\Traits\TraitModel;
use App\ShiftParent;
use App\WorkUnit;

class ShiftParentController extends Controller
{
    use TraitModel;

    public function index(Request $request)
    {
        $shift_parents = ShiftParent::selectRaw('shift_parents.*, dapertements.name as dapertement_name, jobs.name as job_name, work_units.name as work_unit_name')
            ->leftJoin('jobs', 'jobs.id', '=', 'shift_parents.job_id')
            ->leftJoin('dapertements', 'dapertements.id', '=', 'shift_parents.dapertement_id')
            ->leftJoin('work_units', 'work_units.id', '=', 'shift_parents.work_unit_id')
            ->orderBy('updated_at')
            ->get();
        return view('admin.shift_parent.index', compact('shift_parents'));
    }
    public function create()
    {
        // $last_code = $this->get_last_code('shift_parent');

        $dapertements = Dapertement::all();
        $work_units = WorkUnit::all();
        $jobs = Job::all();

        return view('admin.shift_parent.create', compact('dapertements', 'work_units', 'jobs'));
    }
    public function store(Request $request)
    {
        $data = array_merge($request->all());

        ShiftParent::create($data);

        return redirect()->route('admin.shift_parent.index');
    }
    public function show($id)
    {
        abort_unless(\Gate::allows('permission_show'), 403);
        $shift_parent = ShiftParent::where('shift_parent.id', $id)->first();
        return view('admin.shift_parent.show', compact('shift_parent'));
    }
    public function edit($id)
    {
        // dd($id);
        $shift_parent = ShiftParent::where('id', $id)->first();
        // $dapertements = Dapertement::all();
        // $subdapertements = Subdapertement::where('dapertement_id', $shift_parent->dapertement_id)->get();

        return view('admin.shift_parent.edit', compact('shift_parent'));
    }
    public function update($id, Request $request)
    {
        $shift_parent = ShiftParent::where('id', $id)->first();
        $shift_parent->update($request->all());
        return redirect()->route('admin.shift_parent.index', ['id' => $shift_parent->work_type_id]);
    }
    public function destroy($id)
    {
        $shift_parent = ShiftParent::where('id', $id)->first();
        $shift_parent = ShiftParent::where('work_type_id', $shift_parent->work_type_id)
            ->where('day_id', $shift_parent->day_id);
        $shift_parent->delete();
        return back();
    }
    public function massDestroy(Request $request)
    {
        ShiftParent::whereIn('id', request('ids'))->delete();
        return response(null, 204);
    }
}
