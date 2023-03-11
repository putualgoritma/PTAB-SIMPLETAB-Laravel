<?php

namespace App\Http\Controllers\Admin;

use App\Absence;
use App\Exports\AbsenceExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Absence_categories;
use App\AbsenceLog;
use App\Day;
use App\Exports\AbsenceReport;
use App\Holiday;
use App\Http\Controllers\Controller;
use App\Requests;
use App\ShiftStaff;
use App\User;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbsenceController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(\Gate::allows('absence_access'), 403);
        $qry = AbsenceLog::selectRaw('absence_logs.*, staffs.name as staff, staffs.image as staff_image, absence_categories.title as absence_category')
            ->leftJoin('absences', 'absence_logs.absence_id', '=', 'absences.id')
            ->leftJoin('staffs', 'absences.staff_id', '=', 'staffs.id')
            ->leftJoin('absence_categories', 'absence_logs.absence_category_id', '=', 'absence_categories.id')
            ->orderBy('register', 'ASC');
        // dd($qry->get());
        // $qry = TestModel::Filter($request)->Order('id', 'desc')->skip(0)->take(10)->get();
        // return $qry;
        if ($request->ajax()) {
            //set query
            $table = Datatables::of($qry);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'absence_show';
                $editGate = 'absence_edit';
                $deleteGate = 'absence_delete';
                $crudRoutePart = 'absence';

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

            $table->editColumn('user', function ($row) {
                return $row->user ? $row->user : "";
            });

            $table->editColumn('image', function ($row) {
                return $row->image ? $row->image : "";
            });

            $table->editColumn('user_image', function ($row) {
                return $row->image ? $row->user_image : "";
            });

            $table->editColumn('lat', function ($row) {
                return $row->lat ? $row->lat : "";
            });
            $table->editColumn('lng', function ($row) {
                return $row->lng ? $row->lng : "";
            });
            $table->editColumn('register', function ($row) {
                return $row->register ? $row->register : "";
            });
            $table->editColumn('absen_category', function ($row) {
                return $row->absen_category ? $row->absen_category : "";
            });
            $table->editColumn('day', function ($row) {
                return $row->day ? $row->day : "";
            });
            $table->editColumn('late', function ($row) {
                return $row->late ? $row->late : "";
            });
            $table->editColumn('value', function ($row) {
                return $row->value ? $row->value : "";
            });
            $table->editColumn('late', function ($row) {
                if ($row->late === 0) {
                    return "Lambat";
                } else {
                    return "Tepat";
                }
            });
            $table->editColumn('updated_at', function ($row) {
                return $row->updated_at ? $row->updated_at : "";
            });

            $table->rawColumns(['actions', 'placeholder']);

            $table->addIndexColumn();
            return $table->make(true);
        }
        // default view
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

    public function reportAbsenceExcelView()
    {
        return view('admin.absence.reportExcel');
    }

    public function reportAbsenceView()
    {
        return view('admin.absence.report');
    }

    public function reportAbsenceExcel(Request $request)
    {
        $report =  Absence::select(DB::raw('RIGHT(staffs.NIK , 3 ) AS No'), 'staffs.name as Name', DB::raw('TIME(in.timein) AS on_duty'), DB::raw('TIME(out.timein) AS off_duty'), DB::raw('TIME(in.register) AS clock_in'), DB::raw('TIME(out.register) AS clock_out'), 'days.name as timetable', DB::raw('DATE(absences.created_at) AS date'))
            ->join('staffs', 'staffs.id', '=', 'absences.staff_id')
            ->join('days', 'days.id', '=', 'absences.day_id')
            ->rightJoin('absence_logs as in', function ($join) {
                $join->on('in.absence_id', '=', 'absences.id')
                    ->where('in.absence_category_id', '=', 1);
            })
            ->join('absence_logs as out', function ($join) {
                $join->on('out.absence_id', '=', 'absences.id')
                    ->where('out.absence_category_id', '=', 2);
            })
            ->get();

        // perulangan tanggal
        $begin = strtotime('21-01-2023');
        $end   = strtotime('21-02-2023');
        $data = [];

        foreach ($report as $value) {
            $day = "";
            if (date("w", strtotime($value->date)) == 0) {
                $day = "Minggu";
            } else if (date("w", strtotime($value->date)) == 1) {
                $day = "Senin";
            } else if (date("w", strtotime($value->date)) == 2) {
                $day = "Selasa";
            } else if (date("w", strtotime($value->date)) == 3) {
                $day = "Rabu";
            } else if (date("w", strtotime($value->date)) == 4) {
                $day = "Kamis";
            } else if (date("w", strtotime($value->date)) == 5) {
                $day = "Jumat";
            } else if (date("w", strtotime($value->date)) == 6) {
                $day = "Sabtu";
            }
            $data[] = [
                'Emp No' => '',
                'AC-No' => '',
                'No' => $value->No,
                'Name' => $value->Name,
                'Auto-Asign' => '',
                'Date' => $value->date,
                'TimeTable' => $day,
                'On_Duty' => $value->on_duty,
                'Off_Duty' => $value->off_duty,
                'Clock_in' => $value->clock_in,
                'Clock_out' => $value->clock_out,
            ];
        }


        // Excel::create('Filename', function ($excel) use ($data) {

        //     $excel->sheet('Sheetname', function ($sheet) use ($data) {

        //         $sheet->fromArray($data);
        //     });
        // })->export('xls');
        // $data = [
        //     [
        //         'name' => 'Povilasaaa',
        //         'surname' => 'Korop',
        //         'email' => 'povilas@laraveldaily.com',
        //         'twitter' => '@povilaskorop'
        //     ],
        //     [
        //         'name' => 'Taylor',
        //         'surname' => 'Otwell',
        //         'email' => 'taylor@laravel.com',
        //         'twitter' => '@taylorotwell'
        //     ]
        // ];
        return Excel::download(new AbsenceExport($data), 'report_excel.xlsx');
        // dd($report);
    }

    public function reportAbsence(Request $request)
    {
        $awal_cuti = '2023-02-20';
        $akhir_cuti = '2023-03-21';

        // tanggalnya diubah formatnya ke Y-m-d 
        $awal_cuti = date_create_from_format('Y-m-d', $awal_cuti);
        $awal_cuti = date_format($awal_cuti, 'Y-m-d');
        $awal_cuti = strtotime($awal_cuti);

        $akhir_cuti = date_create_from_format('Y-m-d', $akhir_cuti);
        $akhir_cuti = date_format($akhir_cuti, 'Y-m-d');
        $akhir_cuti = strtotime($akhir_cuti);

        $haricuti = array();
        $sabtuminggu = array();

        for ($i = $awal_cuti; $i <= $akhir_cuti; $i += (60 * 60 * 24)) {
            if (date('w', $i) !== '0' && date('w', $i) !== '6') {
                $haricuti[] = $i;
            } else {
                $sabtuminggu[] = $i;
            }
        }
        $jumlah_cuti = count($haricuti);
        $jumlah_sabtuminggu = count($sabtuminggu);
        $abtotal = $jumlah_cuti + $jumlah_sabtuminggu;


        $report = AbsenceLog::select(
            DB::raw('count(IF(absence_category_id = 1 ,1,NULL)) as hadir'),
            DB::raw('count(IF(absence_category_id = 13 ,1,NULL)) as izin'),
            DB::raw('count(IF(absence_category_id = 7 ,1,NULL)) as dinas_luar'),
            DB::raw('count(IF(absence_category_id = 8 ,1,NULL)) as cuti'),
            DB::raw('count(IF(absence_category_id = 5 ,1,NULL)) as dinas_dalam'),
            DB::raw('SUM(CASE WHEN absence_category_id = "1" AND late != "00:00:00" THEN 1 ELSE 0 END) as terlambat'),
            DB::raw('count(IF(absence_category_id = 11 ,1,NULL)) as permisi'),
            // total jam
            DB::raw('SUM(CASE WHEN absence_category_id = "1" AND late != "00:00:00" THEN duration ELSE "00:00:00" END) as jam_terlambat'),
            DB::raw('SUM(CASE WHEN absence_category_id = "2" THEN duration ELSE "00:00:00" END) as jam_hadir'),
            DB::raw('SUM(CASE WHEN absence_category_id = "4" THEN duration ELSE "00:00:00" END) as jam_istirahat'),
            DB::raw('SUM(CASE WHEN absence_category_id = "10" THEN duration ELSE "00:00:00" END) as jam_lembur'),
            DB::raw('SUM(CASE WHEN absence_category_id = "6" THEN duration ELSE "00:00:00" END) as jam_dinas_umum'),
            DB::raw('SUM(CASE WHEN absence_category_id = "13" THEN duration ELSE "00:00:00" END) as jam_permisi'),
            // DB::raw("SUM(DATEDIFF(hour,duration)  as jam_test"),
            'staffs.name as staff_name',
            'staffs.code as staff_code',
            'jobs.name as job_name',


        )
            ->join('absences', 'absences.id', '=', 'absence_logs.absence_id')
            ->join('staffs', 'staffs.id', '=', 'absences.staff_id')
            ->join('jobs', 'jobs.id', '=', 'staffs.job_id')
            ->groupBy('staffs.id');
        // ->get();


        // foreach ($report as $value) {
        //     $day = "";
        //     if (date("w", strtotime($value->date)) == 0) {
        //         $day = "Minggu";
        //     } else if (date("w", strtotime($value->date)) == 1) {
        //         $day = "Senin";
        //     } else if (date("w", strtotime($value->date)) == 2) {
        //         $day = "Selasa";
        //     } else if (date("w", strtotime($value->date)) == 3) {
        //         $day = "Rabu";
        //     } else if (date("w", strtotime($value->date)) == 4) {
        //         $day = "Kamis";
        //     } else if (date("w", strtotime($value->date)) == 5) {
        //         $day = "Jumat";
        //     } else if (date("w", strtotime($value->date)) == 6) {
        //         $day = "Sabtu";
        //     }
        //     $data[] = [
        //         'Emp No' => '',
        //         'AC-No' => '',
        //         'No' => $value->No,
        //         'Name' => $value->Name,
        //         'Auto-Asign' => '',
        //         'Date' => $value->date,
        //         'TimeTable' => $day,
        //         'On_Duty' => $value->on_duty,
        //         'Off_Duty' => $value->off_duty,
        //         'Clock_in' => $value->clock_in,
        //         'Clock_out' => $value->clock_out,
        //     ];
        // }

        $day1 = "2023-02-12 12:30:00";
        $day1 = strtotime($day1);
        $day2 = "2023-02-12 14:00:00";
        $day2 = strtotime($day2);

        $diffHours = ($day2 - $day1) / 3600;
        // dd($report, $jumlah_cuti, $jumlah_sabtuminggu, $abtotal, $diffHours);


        $no = 1;

        foreach ($report as $value) {
            $day = "";
            if (date("w", strtotime($value->date)) == 0) {
                $day = "Minggu";
            } else if (date("w", strtotime($value->date)) == 1) {
                $day = "Senin";
            } else if (date("w", strtotime($value->date)) == 2) {
                $day = "Selasa";
            } else if (date("w", strtotime($value->date)) == 3) {
                $day = "Rabu";
            } else if (date("w", strtotime($value->date)) == 4) {
                $day = "Kamis";
            } else if (date("w", strtotime($value->date)) == 5) {
                $day = "Jumat";
            } else if (date("w", strtotime($value->date)) == 6) {
                $day = "Sabtu";
            }
            $data[] = [
                'No' => $no++,
                'Kode' => $value->staff_code,
                'Nama' => $value->staff_name,
                'Karyawan' => '',
                'Sub_Bagian' => '',
                'Job' => $value->job_name,
                'Total Hari' => $abtotal,
                'Total Libur' => $jumlah_sabtuminggu,
                'Total Efektif Kerja' => $abtotal - $jumlah_sabtuminggu,
                'Total Hadir' => $value->hadir,
                'Total Alfa' => '',
                'Total Izin' => $value->izin,
                'Total Dinas Luar' => $value->dinas_luar,
                'Total Cuti' => $value->cuti,
                'Total Jam Kerja' => $value->jam_hadir,
                'Total Jam Istirahat' => $value->jam_istirahat,
                'Total Jam Lembur' => $value->jam_lembur,
                'Total Jam Dinas Dalam' => $value->jam_dinas_dusun,
                'Total Hari Dinas Dalam' => $value->dinas_dalam,
                'Total Jam Terlambat' => $value->dinas_dalam,
                'Total Hari Terlambat' => $value->terlambat,
                'Total Jam Permisi' => $value->jam_permisi,
                'Total Hari Permisi' => $value->permisi,
            ];
        }

        if ($request->ajax()) {
            //set query
            $table = Datatables::of($report);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = '';
                $editGate = '';
                $deleteGate = '';
                $crudRoutePart = 'absence';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });
            $table->editColumn('staff_code', function ($row) {
                return $row->staff_code ? $row->staff_code : "";
            });

            $table->editColumn('staff_name', function ($row) {
                return $row->staff_name ? $row->staff_name : "";
            });

            $table->editColumn('job_name', function ($row) {
                return $row->job_name ? $row->job_name : "";
            });

            $table->editColumn('abtotal', function ($row) use ($abtotal) {
                return $abtotal ? $abtotal : "";
            });

            $table->editColumn('jumlah_sabtuminggu', function ($row) use ($jumlah_sabtuminggu) {
                return $jumlah_sabtuminggu ? $jumlah_sabtuminggu : "";
            });

            $table->editColumn('efective_kerja', function ($row) use ($jumlah_sabtuminggu, $abtotal) {
                return $abtotal - $jumlah_sabtuminggu;
            });

            $table->editColumn('hadir', function ($row) {
                return $row->hadir ? $row->hadir : "";
            });
            $table->editColumn('izin', function ($row) {
                return $row->izin ? $row->izin : "";
            });
            $table->editColumn('dinas_luar', function ($row) {
                return $row->dinas_luar ? $row->dinas_luar : "";
            });
            $table->editColumn('cuti', function ($row) {
                return $row->cuti ? $row->cuti : "";
            });
            $table->editColumn('jam_hadir', function ($row) {
                return $row->jam_hadir ? $row->jam_hadir : "";
            });
            $table->editColumn('jam_istirahat', function ($row) {
                return $row->jam_istirahat ? $row->jam_istirahat : "";
            });
            $table->editColumn('jam_lembur', function ($row) {
                return $row->jam_lembur ? $row->jam_lembur : "";
            });


            $table->editColumn('jam_dinas_dalam', function ($row) {
                return $row->jam_dinas_dalam ? $row->jam_dinas_dalam : "";
            });
            $table->editColumn('hari_dinas_dalam', function ($row) {
                return $row->dinas_dalam ? $row->dinas_dalam : "";
            });

            // $table->editColumn('jam_dinas_dalam', function ($row) {
            //     return $row->jam_dinas_dalam ? $row->jam_dinas_dalam : "";
            // });
            // $table->editColumn('hari_dinas_dalam', function ($row) {
            //     return $row->dinas_dalam ? $row->dinas_dalam : "";
            // });

            $table->editColumn('jam_permisi', function ($row) {
                return $row->jam_permisi ? $row->jam_permisi : "";
            });
            $table->editColumn('hari_permisi', function ($row) {
                return $row->permisi ? $row->permisi : "";
            });
            $table->rawColumns(['actions', 'placeholder']);

            $table->addIndexColumn();
            return $table->make(true);
        }
        // default view
        // return view('admin.schedule.index');

        return view('admin.absence.report');

        // dd($data);
        // return Excel::download(new AbsenceReport($data), 'report.xlsx');
    }
}
