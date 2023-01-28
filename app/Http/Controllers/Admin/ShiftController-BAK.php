<?php

namespace App\Http\Controllers\Admin;

use App\Dapertement;
use App\Http\Controllers\Controller;
use App\Shift;
use App\ShiftStaff;
use App\Staff;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $staffs = Staff::get();
        $dapertements = Dapertement::get();
        $shifts = Shift::selectRaw('shifts.*, dapertements.name as dapertement_name')
            ->join('dapertements', 'dapertements.id', '=', 'shifts.dapertement_id')->get();
        $qry = ShiftStaff::selectRaw('shifts.*, staffs.name as staff_name, shifts.title as shift_title, dapertements.name as dapertement_name, shift_staff.date as shift_date')
            ->join('staffs', 'shift_staff.staff_id', '=', 'staffs.id')
            ->join('shifts', 'shift_staff.shift_id', '=', 'shifts.id')
            ->join('dapertements', 'dapertements.id', '=', 'shifts.dapertement_id');
        // dd($qry->get());
        if ($request->ajax()) {
            //set query
            $table = Datatables::of($qry);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = '';
                $editGate = '';
                $deleteGate = '';
                $crudRoutePart = '';

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
                return $row->staff_name ? $row->staff_name : "";
            });
            $table->editColumn('shift_title', function ($row) {
                return $row->shift_title ? $row->shift_title : "";
            });
            $table->editColumn('dapertement_name', function ($row) {
                return $row->dapertement_name ? $row->dapertement_name : "";
            });
            $table->editColumn('shift_date', function ($row) {
                return $row->shift_date ? $row->shift_date : "";
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
        return view('admin.shift.index', compact('shifts', 'dapertements', 'staffs'));
    }
    function countDays($year, $month, $ignore)
    {
        $count = 0;
        $counter = mktime(0, 0, 0, $month, 1, $year);
        while (date("n", $counter) == $month) {
            if (in_array(date("w", $counter), $ignore) == false) {
                $count++;
            }
            $counter = strtotime("+1 day", $counter);
        }
        return  $count;
    }

    public function create()
    {
        $day = array(
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',

        );
        // dd($day[date('w', strtotime("2022-12-28"))]);
        $countDays = $this->countDays(date('Y', strtotime('+1 month', strtotime(date('Y-m-d')))), date('m', strtotime('+1 month', strtotime(date('Y-m-d')))), array());
        $days = [];
        // dd(date('Y-m-d', strtotime("2022-12-9")));
        for ($i = 1; $i <= $countDays; $i++) {
            $days[] = ['date' => date('Y-m-d', strtotime(date('Y-m', strtotime('+1 month', strtotime(date('Y-m-d')))) . "-" . $i)), 'day' => $day[date('w', strtotime(date('Y-m', strtotime('+1 month', strtotime(date('Y-m-d')))) . "-" . $i))]];
        }
        // dd($days[0]["day"]);
        // dd($days);
        return view('admin.shift.create', compact('days'));
    }
    public function store(Request $request)
    {
        $data = [];
        $countDays = $this->countDays(date('Y', strtotime('+1 month', strtotime(date('Y-m-d')))), date('m', strtotime('+1 month', strtotime(date('Y-m-d')))), array());
        $d = [];
        // dd(date('Y-m-d', strtotime("2022-12-9")));

        for ($i = 1; $i <= $countDays; $i++) {
            // dd(count($request['staff' . $i . date('Y-m-d', strtotime(date('Y-m', strtotime('+1 month', strtotime(date('Y-m-d')))) . "-" . $i))]));

            $data[] = $request->code . date('Y-m-d', strtotime(date('Y-m', strtotime('+1 month', strtotime(date('Y-m-d')))) . "-" . $i));

            for ($k = 1; $k <= 3; $k++) {
                for ($j = 0; $j < count($request['staff' . $k . date('Y-m-d', strtotime(date('Y-m', strtotime('+1 month', strtotime(date('Y-m-d')))) . "-" . $i))]); $j++) {
                    $d[] = ['staff_id' => $request['staff' . $k . date('Y-m-d', strtotime(date('Y-m', strtotime('+1 month', strtotime(date('Y-m-d')))) . "-" . $i))][$j], 'register' => date('Y-m-d', strtotime(date('Y-m', strtotime('+1 month', strtotime(date('Y-m-d')))) . "-" . $i)), 'shift' => $k];
                }
            }
        }

        dd($d);
    }
}
