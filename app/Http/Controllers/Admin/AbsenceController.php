<?php

namespace App\Http\Controllers\Admin;

use App\Absence;
use App\Absence_categories;
use App\AbsenceLog;
use App\Day;
use App\Holiday;
use App\Http\Controllers\Controller;
use App\Requests;
use App\ShiftStaff;
use App\User;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class AbsenceController extends Controller
{
    public function index(Request $request)
    {
        // abort_unless(\Gate::allows('absence_access'), 403);
        // $qry = AbsenceLog::selectRaw('absence_logs.*, staffs.name as staff, staffs.image as staff_image, absence_categories.title as absence_category')
        //     ->leftJoin('absences', 'absence_logs.absence_id', '=', 'absences.id')
        //     ->leftJoin('staffs', 'absences.staff_id', '=', 'staffs.id')
        //     ->leftJoin('absence_categories', 'absence_logs.absence_category_id', '=', 'absence_categories.id')
        //     ->orderBy('register', 'ASC');
        // dd($qry->get());
        // // $qry = TestModel::Filter($request)->Order('id', 'desc')->skip(0)->take(10)->get();
        // // return $qry;
        // if ($request->ajax()) {
        //     //set query
        //     $table = Datatables::of($qry);

        //     $table->addColumn('placeholder', '&nbsp;');
        //     $table->addColumn('actions', '&nbsp;');

        //     $table->editColumn('actions', function ($row) {
        //         $viewGate = 'absence_show';
        //         $editGate = 'absence_edit';
        //         $deleteGate = 'absence_delete';
        //         $crudRoutePart = 'absence';

        //         return view('partials.datatablesActions', compact(
        //             'viewGate',
        //             'editGate',
        //             'deleteGate',
        //             'crudRoutePart',
        //             'row'
        //         ));
        //     });
        //     $table->editColumn('id', function ($row) {
        //         return $row->id ? $row->id : "";
        //     });

        //     $table->editColumn('user', function ($row) {
        //         return $row->user ? $row->user : "";
        //     });

        //     $table->editColumn('image', function ($row) {
        //         return $row->image ? $row->image : "";
        //     });

        //     $table->editColumn('user_image', function ($row) {
        //         return $row->image ? $row->user_image : "";
        //     });

        //     $table->editColumn('lat', function ($row) {
        //         return $row->lat ? $row->lat : "";
        //     });
        //     $table->editColumn('lng', function ($row) {
        //         return $row->lng ? $row->lng : "";
        //     });
        //     $table->editColumn('register', function ($row) {
        //         return $row->register ? $row->register : "";
        //     });
        //     $table->editColumn('absen_category', function ($row) {
        //         return $row->absen_category ? $row->absen_category : "";
        //     });
        //     $table->editColumn('day', function ($row) {
        //         return $row->day ? $row->day : "";
        //     });
        //     $table->editColumn('late', function ($row) {
        //         return $row->late ? $row->late : "";
        //     });
        //     $table->editColumn('value', function ($row) {
        //         return $row->value ? $row->value : "";
        //     });
        //     $table->editColumn('late', function ($row) {
        //         if ($row->late === 0) {
        //             return "Lambat";
        //         } else {
        //             return "Tepat";
        //         }
        //     });
        //     $table->editColumn('updated_at', function ($row) {
        //         return $row->updated_at ? $row->updated_at : "";
        //     });

        //     $table->rawColumns(['actions', 'placeholder']);

        //     $table->addIndexColumn();
        //     return $table->make(true);
        // }
        //default view
        // return view('admin.schedule.index');

        return view('admin.absence.index');
    }

    public function create()
    {
        abort_unless(\Gate::allows('absence_create'), 403);
        $absence_categories = Absence_categories::where('day_id', null)->get();
        $day = Day::get();
        $users = User::where('staff_id', '!=', '0')->orderBy('name')->get();
        return view('admin.absence.create', compact('absence_categories', 'day', 'users'));
    }

    public function store(Request $request)
    {
        abort_unless(\Gate::allows('absence_create'), 403);
        $checkD = date("w",  strtotime($request->register));
        if ($checkD == "0") {
            $day = 7;
        } else {
            $day = $checkD;
        }
        $value = 0;
        $absence_category_id = "";
        if ($request->absence_category_id == "in" || $request->absence_category_id == "break_in" || $request->absence_category_id == "break_out" || $request->absence_category_id = "out") {
            $absence_category = Absence_categories::where('day_id', $day)->where('title', $request->absence_category_id)->first();
            $value = $absence_category->value;
            $absence_category_id = $absence_category->id;
        } else {
            $absence_category_id = $request->absence_category_id;
        }


        $data = [
            'user_id' => $request->user_id,
            'image' => '',
            'lat' => '',
            'lng' => '',
            'register' => $request->register,
            'shift_id' => '',
            'absence_category_id' => $absence_category_id,
            'value' => $value,
            'day_id' => $day,
        ];
        Absence::create($data);
        return redirect()->route('admin.absence.index');
    }

    public function show($id)
    {
        abort_unless(\Gate::allows('absence_show'), 403);
        $absence = Absence::selectRaw('absences.*, users.name as user, days.name as day, absence_categories.title as absence_category')->leftJoin('users', 'absences.user_id', '=', 'users.id')
            ->leftJoin('days', 'absences.day_id', '=', 'days.id')
            ->leftJoin('absence_categories', 'absences.absence_category_id', '=', 'absence_categories.id')
            ->where('absences.id', $id)->first();

        return view('admin.absence.show', compact('absence'));
    }

    public function edit($id)
    {
        abort_unless(\Gate::allows('absence_edit'), 403);
        $absence = Absence::selectRaw('absences.*, users.name as user, days.name as day, absence_categories.title as absence_category')->leftJoin('users', 'absences.user_id', '=', 'users.id')
            ->leftJoin('days', 'absences.day_id', '=', 'days.id')
            ->leftJoin('absence_categories', 'absences.absence_category_id', '=', 'absence_categories.id')
            ->where('absences.id', $id)->first();
        return view('admin.absence.edit', compact('absence'));
    }

    public function update(Request $request, $id)
    {
        abort_unless(\Gate::allows('absence_edit'), 403);
        $absence = Absence::where('id', $id)->first();
        $img_path = "/images/absence";
        $basepath = str_replace("laravel-simpletab", "public_html/simpletabadmin", \base_path());
        if ($absence->image != "") {
            unlink($basepath . $absence->image);
        }

        if ($request->file('image')) {
            $resource_image = $request->file('image');
            $name_image = $request->file('image')->getClientOriginalName();
            $file_ext_image = $request->file('image')->extension();
            // $id_name_image = str_replace(' ', '-', $id_image);

            $name_image = $img_path . '/' . $name_image . '-' . date('Y-m-d h:i:s') . '-absence.' . $file_ext_image;

            $resource_image->move($basepath . $img_path, $name_image);
            $data_image = $name_image;
        }
        $data = [
            'image' =>  $name_image
        ];
        $data = Absence::where('id', $id)->update($data);
        return redirect()->route('admin.absence.index');
    }

    // untuk nanti di API
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
    public function test()
    {
        echo $this->countDays(2022, 12, array(0, 6)); // 23
    }

    public function absenceMenu()
    {

        $menu = "";
        $shift = ShiftStaff::join('shifts', 'shifts.id', '=', 'shift_staff.shift_id')
            ->where('date', date('Y-m-d'))
            ->where('staff_id', 2)
            ->first();
        $request = Requests::where('date', '<=', date('Y-m-d'))
            ->where('end', '>=', date('Y-m-d'))
            ->where('user_id', '=', '2')
            ->where('status', '=', 'approve')
            ->where('category', 'cuti')
            ->first();

        $holiday = Holiday::where('date', date('Y-m-d'))->first();
        if ($request) {
            dd('Anda Sedang Cuti');
        } else if ($holiday) {
            echo "hari ini libur " . $holiday->title;
        } else if ($shift) {
            $data1 = date('H:i:s', strtotime($shift->start_in));
            $data2 = date('H:i:s', strtotime($shift->end_in));

            $data3 = date('H:i:s', strtotime($shift->start_breakin));
            $data4 = date('H:i:s', strtotime($shift->end_breakin));

            $data5 = date('H:i:s', strtotime($shift->start_breakout));
            $data6 = date('H:i:s', strtotime($shift->end_breakout));

            $data7 = date('H:i:s', strtotime($shift->start_out));
            $data8 = date('H:i:s', strtotime($shift->end_out));
            // $r[] = $data1;
            if ($data1 < date('H:i:s') && $data2 > date('H:i:s')) {
                $menu = "IS";
                echo "absen data1";
            }
            if ($data3 < date('H:i:s') && $data4 > date('H:i:s')) {
                $menu = "BIS";
                echo "absen data2";
            }
            if ($data5 < date('H:i:s') && $data6 > date('H:i:s')) {
                $menu = "BOS";
                echo "absen data3";
            }
            if ($data7 < date('H:i:s') && $data8 > date('H:i:s')) {
                $menu = "IO";
                echo "absen data4";
            }
        } else {
            if (date('w') == '0') {
                $day = '7';
            } else {
                $day = date('w');
            }

            $absen = Absence_categories::selectRaw('absence_categories.*')
                ->where('day_id', 1)
                ->get();

            foreach ($absen as $data) {

                $data1 = date('H:i:s', strtotime($data->start));
                $data2 = date('H:i:s', strtotime($data->end));
                // $r[] = $data1;
                if ($data1 < date('H:i:s') && $data2 > date('H:i:s')) {
                    $menu = $data->title;
                    echo $menu;
                }
            }
        }
    }

    public function destroy($id)
    {
        abort_unless(\Gate::allows('absence_delete'), 403);
        $absence = Absence::where('id', $id)->first();
        $basepath = str_replace("laravel-simpletab", "public_html/simpletabadmin", \base_path());
        unlink($basepath . "images/absence/" . $absence->image);
        Absence::where('id', $id)->delete();
        return redirect()->back();
    }
}
