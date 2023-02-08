<?php

namespace App\Http\Controllers\Api\V1\Absence;

use App\Absence;
use App\Absence_categories;
use App\Holiday;
use App\Http\Controllers\Controller;
use App\Requests;
use App\Shift;
use App\ShiftPlannerStaff;
use App\ShiftStaff;
use Illuminate\Http\Request;

class AbsenceApiController extends Controller
{

    public function index(Request $request)
    {

        $menu = "";
        $data = [];
        $next = [];
        // $duty = Requests::where('category', 'duty')->whereDate('date', '=', date('Y-m-d'))->where('user_id', $request->user_id)->where('status', 'approve')->first();
        // $absence_out = Requests::where('category', 'absence_out')->whereDate('date', '=', date('Y-m-d'))->where('user_id', $request->user_id)->where('status', 'approve')->first();
        // $shift = ShiftStaff::join('shifts', 'shifts.id', '=', 'shift_staff.shift_id')
        //     ->where('date', date('Y-m-d'))
        //     ->where('staff_id', $request->user_id)
        //     ->first();
        // $requests = Requests::where('date', '<=', date('Y-m-d'))
        //     ->where('end', '>=', date('Y-m-d'))
        //     ->where('user_id', '=', $request->user_id)
        //     ->where('status', '=', 'approve')
        //     ->where('category', 'cuti')
        //     ->first();

        $holiday = Holiday::whereDate('start', '<=', date('Y-m-d'))->whereDate('end', '>=', date('Y-m-d'))->first();
        // cek cuti
        // if ($requests) {
        //     $menu = 'OFF';
        //     return response()->json([
        //         'message' => 'Success',
        //         'menu' => $menu,
        //         'date' => date('Y-m-d h:i:s')
        //     ]);
        // }

        // cek shift
        // else if ($shift) {
        //     $data1 = date('H:i:s', strtotime($shift->start_in));
        //     $data2 = date('H:i:s', strtotime($shift->end_in));

        //     $data3 = date('H:i:s', strtotime($shift->start_breakin));
        //     $data4 = date('H:i:s', strtotime($shift->end_breakin));

        //     $data5 = date('H:i:s', strtotime($shift->start_breakout));
        //     $data6 = date('H:i:s', strtotime($shift->end_breakout));

        //     $data7 = date('H:i:s', strtotime($shift->start_out));
        //     $data8 = date('H:i:s', strtotime($shift->end_out));
        //     // $r[] = $data1;



        //     // Next
        //     if ($data2 < date('H:i:s') && $data3 > date('H:i:s')) {
        //         $menu = "";
        //         $nextN = "Istirahat Mulai";
        //         $nextS = $data3;
        //         $nextE = $data4;
        //     } else if ($data4 < date('H:i:s') && $data5 > date('H:i:s')) {
        //         $menu = "";
        //         $nextN = "Istirahat Selesai";
        //         $nextS = $data5;
        //         $nextE = $data6;
        //     } else if ($data6 < date('H:i:s') && $data7 > date('H:i:s')) {
        //         $menu = "";
        //         $nextN = "Pulang";
        //         $nextS = $data7;
        //         $nextE = $data8;
        //     } else {
        //         $menu = "close";
        //         $nextN = "";
        //         $nextS = "";
        //         $nextE = "";
        //     }


        //     if ($data1 < date('H:i:s') && $data2 > date('H:i:s')) {
        //         $menu = "IS";
        //         $nextN = "Istirahat Mulai";
        //         $nextS = $data3;
        //         $nextE = $data4;
        //         $d = Absence::where('user_id', $request->user_id)->whereDate('register', '=', date('Y-m-d'))->first();
        //         if ($d) {
        //             $menu = "";
        //         }
        //     }
        //     if ($data3 < date('H:i:s') && $data4 > date('H:i:s')) {
        //         $menu = "BIS";
        //         $nextN = "Istirahat Selesai";
        //         $nextS = $data5;
        //         $nextE = $data6;
        //         $d = Absence::where('user_id', $request->user_id)->whereDate('register', '=', date('Y-m-d'))->first();
        //         if ($d) {
        //             $menu = "";
        //         }
        //     }
        //     if ($data5 < date('H:i:s') && $data6 > date('H:i:s')) {
        //         $menu = "BOS";
        //         $nextN = "Pulang";
        //         $nextS = $data7;
        //         $nextE = $data8;
        //         $d = Absence::where('user_id', $request->user_id)->whereDate('register', '=', date('Y-m-d'))->first();
        //         if ($d) {
        //             $menu = "";
        //         }
        //     }
        //     if ($data7 < date('H:i:s') && $data8 > date('H:i:s')) {
        //         $menu = "IO";
        //         $nextN = "";
        //         $nextS = "";
        //         $nextE = "";
        //         $d = Absence::where('user_id', $request->user_id)->whereDate('register', '=', date('Y-m-d'))->first();
        //         if ($d) {
        //             $menu = "";
        //         }
        //     }

        //     if ($menu == "") {
        //         $data = Absence::where('user_id', $request->user_id)->whereDate('register', '=', $request->register)->get();
        //         $menu == "wait";
        //     }

        //     return response()->json([
        //         'message' => 'Success',
        //         'menu' => $menu,
        //         'data' => $data,
        //         'shift_id' => $shift->id,
        //         // 'nextN' => $nextN,
        //         // 'nextS' => $nextS,
        //         // 'nextE' => $nextE,
        //         // 'data1' => $data1,
        //         // 'data2' => $data2,
        //         // 'data3' => $data3,
        //         // 'data4' => $data4,
        //         // 'data5' => $data5,
        //         // 'data6' => $data6,
        //         // 'data7' => $data7,
        //         // 'data8' => $data8,
        //         'date' => date('Y-m-d h:i:s')
        //     ]);
        // }
        // cek hari libur
        if ($holiday) {
            $menu = 'OFF';
            return response()->json([
                'message' => 'Success',
                'menu' => $menu,
                'date' => date('Y-m-d h:i:s')
            ]);
        }
        // cek jadwal biasa
        else {
            if (date('w') == '0') {
                $day = '7';
            } else {
                $day = date('w');
            }
            $absence = Absence::selectRaw('queue, status_active')
                ->rightJoin('absence_logs', 'absence_logs.absence_id', '=', 'absences.id')
                ->leftJoin('absence_categories', 'absence_logs.absence_category_id', '=', 'absence_categories.id')
                ->where('staff_id', '323')
                ->where('day_id', '3')
                ->where('created_by_staff_id', null)
                ->orderBy('absence_logs.id', 'ASC')
                ->first();
            if ($absence) {
                $absen = Absence_categories::selectRaw('absence_categories.*, work_type_day.start, work_type_day.end')
                    ->join('work_type_day', 'work_type_day.absence_category_id', '=', 'absence_categories.id')
                    ->join('work_types', 'work_type_day.work_type_id', '=', 'work_types.id')
                    ->where('work_types.id', '1')
                    ->where('absence_categories.type', $absence->status_active)
                    ->where('queue', $absence->queue)
                    ->first();
                if ($absen == null) {
                    $absen = "Absen Hari Ini Sudah Selesai";
                }
            } else {
                $shift = ShiftPlannerStaff::get();
                if ($shift) {
                } else {
                    $absen = Absence_categories::selectRaw('absence_categories.*, work_type_day.start, work_type_day.end')
                        ->join('work_type_day', 'work_type_day.absence_category_id', '=', 'absence_categories.id')
                        ->join('work_types', 'work_type_day.work_type_id', '=', 'work_types.id')
                        ->where('work_types.id', '1')
                        // ->where('absence_categories.type', $absence->type)
                        ->where('queue', $absence->queue)
                        ->first();
                }
            }


            $nextN = "";
            $nextS = "";
            $nextE = "";

            // for ($i = 0; $i < count($absen); $i++) {

            //     $data1 = date('H:i:s', strtotime($absen[$i]['start']));
            //     $data2 = date('H:i:s', strtotime($absen[$i]['end']));

            //     // $r[] = $data1;



            //     if (($i + 1) < count($absen)) {
            //         if ($absen[$i + 1]['start'] > date('H:i:s') && $absen[$i]['end'] < date('H:i:s')) {
            //             $nextN = $absen[$i + 1]['title'];
            //             $nextS = $absen[$i + 1]['start'];
            //             $nextE = $absen[$i + 1]['end'];
            //         }
            //     }

            //     // if ($data1 < date('H:i:s') && $data2 > date('H:i:s')) {
            //     //     $d = "";
            //     //     // Absence::selectRaw('absences.*, absence_categories.title as title')
            //     //     //     ->join('absence_categories', 'absences.absence_category_id', '=', 'absence_categories.id')
            //     //     //     ->where('absences.user_id', $request->user_id)->whereDate('absences.register', '=', date('Y-m-d'))
            //     //     //     ->where('absence_categories.day_id', '!=', '')->where('absence_categories.day_id', '!=', null)
            //     //     //     ->where('absence_category_id', $absen[$i]['id'])
            //     //     //     ->orderBy('queue', 'DESC')
            //     //     //     ->first();
            //     //     $absence_category_id = $absen[$i]['id'];
            //     //     $menu = $absen[$i]['title'];

            //     //     if ($d) {
            //     //         $menu = "";
            //     //         $nextN = $absen[$i + 1]['title'];
            //     //         $nextS = $absen[$i + 1]['start'];
            //     //         $nextE = $absen[$i + 1]['end'];
            //     //         $data = Absence::join('absence_categories', 'absence_categories.id', '=', 'absences.absence_category_id')->where('user_id', $request->user_id)->whereDate('register', '=', date('Y-m-d'))->get();
            //     //     }
            //     // } else if (date('H:i:s', strtotime($absen[0]['start'])) < date('H:i:s') && date('H:i:s', strtotime($absen[count($absen) - 1]['start'])) > date('H:i:s')) {
            //     //     $d = "";
            //     //     // Absence::selectRaw('absences.*, absence_categories.title as title')
            //     //     //     ->join('absence_categories', 'absences.absence_category_id', '=', 'absence_categories.id')
            //     //     //     ->where('absences.user_id', $request->user_id)->whereDate('absences.register', '=', date('Y-m-d'))
            //     //     //     ->where('absence_categories.day_id', '!=', '')->where('absence_categories.day_id', '!=', null)
            //     //     //     ->where('absence_category_id', $absen[$i]['id'])
            //     //     //     ->orderBy('queue', 'DESC')
            //     //     //     ->first();
            //     //     $absence_category_id = $absen[$i]['id'];
            //     //     $menu = $absen[$i]['title'];

            //     //     if ($d) {
            //     //         $menu = "";
            //     //         $nextN = $absen[$i + 1]['title'];
            //     //         $nextS = $absen[$i + 1]['start'];
            //     //         $nextE = $absen[$i + 1]['end'];
            //     //         $data = Absence::join('absence_categories', 'absence_categories.id', '=', 'absences.absence_category_id')->where('user_id', $request->user_id)->whereDate('register', '=', date('Y-m-d'))->get();
            //     //     }
            //     // }
            //     // // kondisi pulang start
            //     // else if (date('H:i:s', strtotime($absen[count($absen) - 1]['end'])) < date('H:i:s') && date('H:i:s', strtotime('23:59:59')) > date('H:i:s')) {
            //     //     $d = "";
            //     //     // Absence::selectRaw('absences.*, absence_categories.title as title')
            //     //     //     ->join('absence_categories', 'absences.absence_category_id', '=', 'absence_categories.id')
            //     //     //     ->where('absences.user_id', $request->user_id)->whereDate('absences.register', '=', date('Y-m-d'))
            //     //     //     ->where('absence_categories.day_id', '!=', '')->where('absence_categories.day_id', '!=', null)
            //     //     //     ->where('absence_category_id', $absen[$i]['id'])
            //     //     //     ->orderBy('queue', 'DESC')
            //     //     //     ->first();
            //     //     $absence_category_id = $absen[$i]['id'];
            //     //     $menu = "C1";

            //     //     if ($d) {
            //     //         $menu = "C1";
            //     //         $nextN = $absen[$i + 1]['title'];
            //     //         $nextS = $absen[$i + 1]['start'];
            //     //         $nextE = $absen[$i + 1]['end'];
            //     //         $data = Absence::join('absence_categories', 'absence_categories.id', '=', 'absences.absence_category_id')->where('user_id', $request->user_id)->whereDate('register', '=', date('Y-m-d'))->get();
            //     //     }
            //     // } else if (date('H:i:s', strtotime('00:00:00')) < date('H:i:s') && date('H:i:s',  strtotime($absen[0]['start'])) > date('H:i:s')) {
            //     //     $d = "";
            //     //     // Absence::selectRaw('absences.*, absence_categories.title as title')
            //     //     //     ->join('absence_categories', 'absences.absence_category_id', '=', 'absence_categories.id')
            //     //     //     ->where('absences.user_id', $request->user_id)->whereDate('absences.register', '=', date('Y-m-d'))
            //     //     //     ->where('absence_categories.day_id', '!=', '')->where('absence_categories.day_id', '!=', null)
            //     //     //     ->where('absence_category_id', $absen[$i]['id'])
            //     //     //     ->orderBy('queue', 'DESC')
            //     //     //     ->first();
            //     //     $absence_category_id = $absen[$i]['id'];
            //     //     $menu = "C2";

            //     //     if ($d) {
            //     //         $menu = "C2";
            //     //         $nextN = $absen[$i + 1]['title'];
            //     //         $nextS = $absen[$i + 1]['start'];
            //     //         $nextE = $absen[$i + 1]['end'];
            //     //         $data = Absence::join('absence_categories', 'absence_categories.id', '=', 'absences.absence_category_id')->where('user_id', $request->user_id)->whereDate('register', '=', date('Y-m-d'))->get();
            //     //     }
            //     // }
            //     // kondisi pulang end




            //     // if (date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime('0 day', strtotime(date('Y-m-d')))) . " " . $absen[count($absen) - 1]['end'])) <  date('Y-m-d H:i:s') && date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d')))) . " " . $absen[0]['start'])) > date('Y-m-d H:i:s')) {
            //     //     $menu = "close";
            //     //     $nextN = "2";
            //     //     $nextS = "2";
            //     //     $nextE = "2";
            //     // }
            // }


            // if (count($absen) <= 0) {
            //     // $next = Absence_categories::where('queue', count($data))->where('day_id', $day)->first();


            //     $menu == "OFF";
            //     return response()->json([
            //         'message' => 'Success',
            //         'menu' => "OFF",
            //         'date' => date('Y-m-d h:i:s')
            //     ]);
            // } else if ($menu == "") {
            //     $data = Absence::join('absence_categories', 'absence_categories.id', '=', 'absences.absence_category_id')->where('user_id', $request->user_id)->whereDate('register', '=', date('Y-m-d'))->where('absence_categories.day_id', '!=', '')->where('absence_categories.day_id', '!=', null)->get();
            //     // $next = Absence_categories::where('queue', count($data))->where('day_id', $day)->first();


            //     $menu == "wait";
            //     return response()->json([
            //         'message' => 'Success',
            //         'absence_out' => $absence_out,
            //         'duty' => $duty,
            //         'menu' => "wait",
            //         'data' => $data,
            //         'nextN' => $nextN,
            //         'nextS' => $nextS,
            //         'nextE' => $nextE,
            //         'date' => date('Y-m-d h:i:s')
            //     ]);
            // } else if ($menu == "close") {
            //     $data = Absence::join('absence_categories', 'absence_categories.id', '=', 'absences.absence_category_id')->where('user_id', $request->user_id)->whereDate('register', '=', date('Y-m-d'))->get();
            //     // $next = Absence_categories::where('queue', count($data))->where('day_id', $day)->first();


            //     $menu == "close";
            //     return response()->json([
            //         'message' => 'Success',
            //         'absence_out' => $absence_out,
            //         'duty' => $duty,
            //         'menu' => "close",
            //         'data' => $data,
            //         'nextN' => $nextN,
            //         'nextS' => $nextS,
            //         'nextE' => $nextE,
            //         'date' => date('Y-m-d h:i:s')
            //     ]);
            // } else {
            // $mulai  = date_create(date('H:i:s', strtotime($absen[count($absen) - 1]['end'])));
            // $selesai = date_create(date('H:i:s'));
            // $hasil  = date_diff($mulai, $selesai);

            // if ($hasil) {
            // } else {
            // }


            return response()->json([
                // 'message' => $d,
                // 'absence_out' => $absence_out,
                // 'duty' => $duty,
                // 'absence_category_id' => $absence_category_id,
                'menu' => $menu,
                'date' => date('Y-m-d h:i:s'),
                // 'jam1' =>  date('H:i:s', strtotime($absen[count($absen) - 1]['end'])),
                // 'jam2' => date('H:i:s'),
                'absence' => $absence,
                'tesss' => $absen
            ]);
            // }
        }
    }

    function distance($lat1, $lon1, $lat2, $lon2, $unit)
    {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        } else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($unit);

            if ($unit == "K") {
                return ($miles * 1.609344);
            } else if ($unit == "N") {
                return ($miles * 0.8684);
            } else {
                return $miles;
            }
        }
    }



    public function cekradius(Request $request)
    {
        // dd($this->getRadius([[-8.5372862, 115.132938]], [-8.459556, 115.046600], 10000));
        // dd($this->getRadius([[-8.6556162, 115.2316827]], [-8.5392225, 115.1339101], 1000));
        // dd($this->getRadius([[-6.1421489, 106.8109178, 15]], [-8.5392225, 115.1339101], 1000));

        // if ($this->distance(-8.5357391, 115.131616, -8.5357967, 115.1323389, "K") < 10) {
        //     echo "True";
        // } else {
        //     echo "False";
        // }

        $lng = $request->lng;
        $lat = $request->lat;
        $posisi = $this->distance(-8.5852182, 115.1296247, -8.5852182, 115.1296247, "K");
        if ($posisi < 1) {
            return response()->json([
                'message' => 'Success',
                'posisi' => $posisi,
                'lng' =>  $lng,
                'lat' => $lat,
            ]);
        } else {
            return response()->json([
                'message' => 'Anda Diluar Wilayah',
                'posisi' => $posisi,
                'lng' =>  $lng,
                'lat' => $lat,
            ]);
        }
    }


    public function store(Request $request)
    {

        // $last_code = $this->get_last_code('lock_action');

        // $code = acc_code_generate($last_code, 8, 3);
        $img_path = "/images/absence";
        $basepath = str_replace("laravel-simpletab", "public_html/simpletabadmin/", \base_path());
        // $dataForm = json_decode($request->form);
        $responseImage = '';

        if (date('w') == '0') {
            $day = '7';
        } else {
            $day = date('w');
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


        if ($responseImage != '') {
            return response()->json([
                'message' => $responseImage,
            ]);
        }

        $absen = Absence_categories::selectRaw('absence_categories.*, work_type_day.start, work_type_day.time, work_type_day.end')
            ->join('work_type_day', 'work_type_day.absence_category_id', '=', 'absence_categories.id')
            ->join('work_types', 'work_type_day.work_type_id', '=', 'work_types.id')
            ->where('day_id', $day)
            ->get();

        if (date('H:i:s') <= date('H:i:s', strtotime($absen->time))) {
            $late = 0;
        } else {
            $late = 1;
        }
        $absence_category = $request->absence_category_id;




        try {
            $upload_image = new Absence;
            $upload_image->image = $data_image;
            // $upload_image->user_id = $dataForm->user_id;
            // $upload_image->register = $dataForm->register;
            // $upload_image->late = $dataForm->late;
            // $upload_image->onesignal_id = $dataForm->onesignal_id;
            // $upload_image->value = $dataForm->value;

            // sementara start
            $upload_image->user_id = $request->user_id;
            $upload_image->register = date('Y-m-d H:i:s');
            $upload_image->late = $late;
            $upload_image->onesignal_id = "dddddwdwdww";
            $upload_image->value = isset($absen) ? $absen->value : $val;
            // sementara end
            $upload_image->created_at = date('Y-m-d H:i:s');
            $upload_image->updated_at = date('Y-m-d H:i:s');
            $upload_image->lat = $request->lat;
            $upload_image->lng = $request->lng;
            $upload_image->absence_category_id =  $absence_category;
            $upload_image->shift_id = $request->shift_id;
            $upload_image->day_id = $day;

            $upload_image->save();


            return response()->json([
                'message' => 'Absen Terkirim',
                'data' => $upload_image,
            ]);
        } catch (QueryException $ex) {
            return response()->json([
                'message' => 'gagal',
            ]);
        }
    }

    public function storeLocation(Request $request)
    {

        // $last_code = $this->get_last_code('lock_action');

        // $code = acc_code_generate($last_code, 8, 3);
        $img_path = "/images/absence";
        $basepath = str_replace("laravel-simpletab", "public_html/simpletabadmin/", \base_path());
        // $dataForm = json_decode($request->form);
        $responseImage = '';

        if (date('w') == '0') {
            $day = '7';
        } else {
            $day = date('w');
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


        if ($responseImage != '') {
            return response()->json([
                'message' => $responseImage,
            ]);
        }



        try {
            $upload_image = new Absence;
            $upload_image->image = $data_image;
            // $upload_image->user_id = $dataForm->user_id;
            // $upload_image->register = $dataForm->register;
            // $upload_image->late = $dataForm->late;
            // $upload_image->onesignal_id = $dataForm->onesignal_id;
            // $upload_image->value = $dataForm->value;

            // sementara start
            $upload_image->user_id = $request->user_id;
            $upload_image->register = date('Y-m-d H:i:s');
            $upload_image->late = 0;
            $upload_image->onesignal_id = "dddddwdwdww";
            $upload_image->value = 0;
            // sementara end
            $upload_image->created_at = date('Y-m-d H:i:s');
            $upload_image->updated_at = date('Y-m-d H:i:s');
            $upload_image->lat = $request->lat;
            $upload_image->lng = $request->lng;
            $upload_image->requests_id = $request->id;
            $upload_image->absence_category_id = $request->absence_category_id;
            $upload_image->shift_id = $request->shift_id;
            $upload_image->day_id = $day;

            $upload_image->save();

            $requests = Requests::where('id', $request->id)->update(['status' => $request->status]);

            return response()->json([
                'message' => 'Absen Terkirim',
                'data' => $upload_image,
            ]);
        } catch (QueryException $ex) {
            return response()->json([
                'message' => 'gagal',
            ]);
        }
    }

    public function checkAbsenceLocation(Request $request)
    {
        $absence = Absence::where('user_id', $request->user_id)->where('requests_id', $request->requests_id)->whereDate('register', '=', date('Y-m-d'))->first();

        if ($absence != null) {
            $cek = "1";
        } else {
            $cek = "0";
        }
        return response()->json([
            'message' => 'success',
            'data' => $cek,
        ]);
    }

    public function history(Request $request)
    {
        $data = [];
        $absence = Absence::join('days', 'days.id', '=', 'absences.day_id')->selectRaw('DATE(register) as register, days.name as day_name')->where('user_id', $request->user_id)->groupByRaw('DATE(register)')->get();

        foreach ($absence as $d) {
            $data[] = ['date' => $d->register, 'day_name' => $d->day_name, 'list' => Absence::join('absence_categories', 'absences.absence_category_id', '=', 'absence_categories.id')->selectRaw('absences.*, absence_categories.title as category_title')->where('user_id', $request->user_id)->whereDate('register', '=', $d->register)->get()];
        }
        return response()->json([
            'message' => 'success',
            'data' => $data,
        ]);
    }
}
