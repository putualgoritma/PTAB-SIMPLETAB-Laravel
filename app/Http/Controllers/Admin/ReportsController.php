<?php

namespace App\Http\Controllers\Admin;

use App\CtmPelanggan;
use App\Dapertement;
use App\Http\Controllers\Controller;
use App\LockAction;
use App\Ticket;
use App\Traits\TraitModel;
use DateTime;
use Illuminate\Http\Request;
use DB;

class ReportsController extends Controller
{
    use TraitModel;

    public function reportSubHumas()
    {
        $monthList = array(
            0 => '',
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juli',
            7 => 'Juni',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        );
        $departementlist = Dapertement::all();
        $month = $monthList[date('n')];
        return view('admin.reports.subHumas', compact('departementlist', 'month'));
        // return view ('admin.reports.reportSubHumas', compact('tickets'));
    }

    public function reportSubHumasProses(Request $request)
    {
        $monthList = array(
            0 => '',
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juli',
            7 => 'Juni',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        );
        $tickets = Ticket::whereBetween(DB::raw('DATE(created_at)'), [$request->from, $request->to])->FilterDepartment($request->dapertement_id)->FilterStatus($request->status)->with(['action', 'customer', 'category', 'dapertement'])->get();
        $month = $monthList[date('n')];
        return view('admin.reports.reportSubHumas', compact('tickets', 'request', 'month'));
    }

    public function reportSubDistribusi()
    {


        $monthList = array(
            0 => '',
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juli',
            7 => 'Juni',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        );
        $departementlist = Dapertement::all();
        $month = $monthList[date('n')];
        // return view ('admin.reports.reportSubDistribusi');
        return view('admin.reports.subDistribusi', compact('departementlist', 'month'));
    }

    public function reportSubDistribusiProses(Request $request)
    {
        $monthList = array(
            0 => '',
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juli',
            7 => 'Juni',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        );
        $month = $monthList[date('n')];
        $tickets = Ticket::whereBetween(DB::raw('DATE(created_at)'), [$request->from, $request->to])->FilterDepartment($request->dapertement_id)->FilterStatus($request->status)->with(['action', 'customer', 'category', 'dapertement'])->get();
        return view('admin.reports.reportSubDistribusi', compact('tickets', 'request', 'month'));
    }

    public function reportLockAction()
    {

        $last_4_month = date("Y-n-d", strtotime('-4 month', strtotime(date('Y-m-01'))));

        $date_now = date("Y-m-d");
        $date_comp = date("Y-m") . "-20";
        $month_now = date('n');
        $year_now = date('Y');
        $month_next = date('n', strtotime('+1 month')) - 1;


        $monthList = array(
            0 => '',
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juli',
            7 => 'Juni',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        );

        if ($date_now > $date_comp) {
            $customer = CtmPelanggan::selectRaw('tblpelanggan.namapelanggan, tblpelanggan.nomorrekening, tblwilayah.id as wilayah_id, tblpelanggan.alamat,tblpelanggan.idgol , tblpelanggan.nomorrekening ,(((count(tblpembayaran.statuslunas) * 2) - sum(tblpembayaran.statuslunas)) DIV 2) as jumlahtunggakan,  (case when( (((count(tblpembayaran.statuslunas) * 2) - sum(tblpembayaran.statuslunas)) DIV 2) > 1 ) THEN 1 ELSE 0 END) as statusnunggak')
                ->join('tblwilayah', 'tblpelanggan.idareal', '=', 'tblwilayah.id')
                ->join('tblpembayaran', 'tblpelanggan.nomorrekening', '=', 'tblpembayaran.nomorrekening')
                // ->leftJoin('ptabroot_simpletab.lock_action', 'lock_action.customer_id', '=', 'tblpelanggan.nomorrekening')
                // ->leftJoin('ptabroot_simpletab.lock_action', function ($join) {
                //     $join->on('lock_action.customer_id', '=', 'tblpelanggan.nomorrekening')->orderBy('lock_action.customer_');
                // })

                // ->leftJoin('ptabroot_simpletab.lock_action', function ($query) {
                //     $query
                //         ->whereRaw('ptabroot_simpletab.lock_action.id IN (select MAX(ptabroot_simpletab.lock_action.id) from ptabroot_simpletab.lock_action group by ptabroot_simpletab.lock_action.customer_id)');
                // })
                // ->leftJoin('ptabroot_simpletab.lock_action',   function ($query) {
                //     $query->select('lock_action.id')
                //         ->orderBy('lock_action.id', 'desc');
                // })

                ->where('tblpelanggan.status', 1)
                ->whereDate(DB::raw('concat(tblpembayaran.tahunrekening,"-",tblpembayaran.bulanrekening,"-01")'), '<=', date('Y-n-01'))
                ->whereDate(DB::raw('concat(tblpembayaran.tahunrekening,"-",tblpembayaran.bulanrekening,"-01")'), '>=', $last_4_month)
                ->groupBy('tblpembayaran.nomorrekening')
                ->having('jumlahtunggakan', '>', 1)
                ->orderBy('tblpelanggan.idareal', 'ASC')
                ->orderBy('tblpelanggan.nomorrekening', 'ASC')
                // ->limit(20)->get();
                // ->orderBy('lock_action.id', 'desc')
                // ->limit(10)
                ->get();
        } else {
            $customer = CtmPelanggan::selectRaw('tblpelanggan.namapelanggan, tblpelanggan.nomorrekening, tblwilayah.id as wilayah_id, tblpelanggan.alamat,tblpelanggan.idgol , tblpelanggan.nomorrekening ,(((count(tblpembayaran.statuslunas) * 2) - sum(tblpembayaran.statuslunas)) DIV 2) as jumlahtunggakan,  (case when( (((count(tblpembayaran.statuslunas) * 2) - sum(tblpembayaran.statuslunas)) DIV 2) > 1 ) THEN 1 ELSE 0 END) as statusnunggak')
                ->join('tblwilayah', 'tblpelanggan.idareal', '=', 'tblwilayah.id')
                ->join('tblpembayaran', 'tblpelanggan.nomorrekening', '=', 'tblpembayaran.nomorrekening')
                // ->leftJoin('ptabroot_simpletab.lock_action', 'lock_action.customer_id', '=', 'tblpelanggan.nomorrekening')
                // ->leftJoin('ptabroot_simpletab.lock_action', function ($join) {
                //     $join->on('lock_action.customer_id', '=', 'tblpelanggan.nomorrekening')->orderBy('lock_action.customer_');
                // })

                // ->leftJoin('ptabroot_simpletab.lock_action', function ($query) {
                //     $query
                //         ->whereRaw('ptabroot_simpletab.lock_action.id IN (select MAX(ptabroot_simpletab.lock_action.id) from ptabroot_simpletab.lock_action group by ptabroot_simpletab.lock_action.customer_id)');
                // })
                // ->leftJoin('ptabroot_simpletab.lock_action',   function ($query) {
                //     $query->select('lock_action.id')
                //         ->orderBy('lock_action.id', 'desc');
                // })

                ->where('tblpelanggan.status', 1)
                ->whereDate(DB::raw('concat(tblpembayaran.tahunrekening,"-",tblpembayaran.bulanrekening,"-01")'), '<', date('Y-n-01'))
                ->whereDate(DB::raw('concat(tblpembayaran.tahunrekening,"-",tblpembayaran.bulanrekening,"-01")'), '>=', $last_4_month)
                ->groupBy('tblpembayaran.nomorrekening')
                ->having('jumlahtunggakan', '>', 1)
                ->orderBy('tblpelanggan.idareal', 'ASC')
                ->orderBy('tblpelanggan.nomorrekening', 'ASC')
                // ->limit(20)->get();
                // ->orderBy('lock_action.id', 'desc')
                ->get();
        }



        $take =  $customer->count();
        if ($take % 29 === 0) {
            $jum = floor($take / 29);
        } else {
            $jum = floor($take / 29) + 1;
        }

        // $departementlist = Dapertement::all();
        $month = $monthList[date('n')];
        return view('admin.reports.lockAction', compact('month', 'jum', 'take'));
        // return view ('admin.reports.reportSubHumas', compact('tickets'));
    }

    public function reportLockActionProses(Request $request)
    {
        // dd($request->jum);

        // dd($jarak->d);
        $monthList = array(
            0 => '',
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juli',
            7 => 'Juni',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        );

        $monthRomawi = array(
            0 => '',
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        );

        $month = $monthList[date('n') - 1];
        $monthR = $monthRomawi[date('n') - 1];
        $last_4_month = date("Y-n-d", strtotime('-4 month', strtotime(date('Y-m-01'))));

        $date_now = date("Y-m-d");
        $date_comp = date("Y-m") . "-20";
        $month_now = date('n');
        $year_now = date('Y');
        $month_next = date('n', strtotime('+1 month')) - 1;

        $d1 = [];

        $subQuery = DB::table('ptabroot_simpletab.lock_action')

            ->select('lock_action.customer_id', 'lock_action.id', 'lock_action.type', 'created_at as action_date')
            ->whereRaw('lock_action.id in (select max(lock_action.id) from ptabroot_simpletab.lock_action group by (lock_action.customer_id))');

        // dd($subQuery);
        if ($date_now > $date_comp) {
            $customer = CtmPelanggan::selectRaw('tblpelanggan.namapelanggan,lock_action.id,lock_action.action_date, lock_action.type as lockActionType, tblpelanggan.nomorrekening, tblwilayah.id as wilayah_id, tblpelanggan.alamat,tblpelanggan.idgol , tblpelanggan.nomorrekening, staffs.name as staff_name ,(((count(tblpembayaran.statuslunas) * 2) - sum(tblpembayaran.statuslunas)) DIV 2) as jumlahtunggakan,  (case when( (((count(tblpembayaran.statuslunas) * 2) - sum(tblpembayaran.statuslunas)) DIV 2) > 1 ) THEN 1 ELSE 0 END) as statusnunggak')
                ->join('tblwilayah', 'tblpelanggan.idareal', '=', 'tblwilayah.id')
                ->join('tblpembayaran', 'tblpelanggan.nomorrekening', '=', 'tblpembayaran.nomorrekening')
                // ->leftJoin('ptabroot_simpletab.lock_action', 'lock_action.customer_id', '=', 'tblpelanggan.nomorrekening')
                // ->leftJoin('ptabroot_simpletab.lock_action', function ($join) {
                //     $join->on('lock_action.customer_id', '=', 'tblpelanggan.nomorrekening')->orderBy('lock_action.customer_');
                // })

                // ->leftJoin('ptabroot_simpletab.lock_action', function ($query) {
                //     $query
                //         ->whereRaw('ptabroot_simpletab.lock_action.id IN (select MAX(ptabroot_simpletab.lock_action.id) from ptabroot_simpletab.lock_action group by ptabroot_simpletab.lock_action.customer_id)');
                // })
                ->leftJoin(DB::raw('(' . $subQuery->toSql() . ') lock_action'), 'tblpelanggan.nomorrekening', '=', 'lock_action.customer_id')
                // ->leftJoin('ptabroot_simpletab.lock_action',   function ($query) {
                //     $query->select('lock_action.id')
                //         ->orderBy('lock_action.id', 'desc');
                // })
                ->leftJoin('ptabroot_simpletab.staffs', 'staffs.id', '=', 'lock_action.id')
                ->where('tblpelanggan.status', 1)
                ->whereDate(DB::raw('concat(tblpembayaran.tahunrekening,"-",tblpembayaran.bulanrekening,"-01")'), '<=', date('Y-n-01'))
                ->whereDate(DB::raw('concat(tblpembayaran.tahunrekening,"-",tblpembayaran.bulanrekening,"-01")'), '>=', $last_4_month)
                ->groupBy('tblpembayaran.nomorrekening')
                ->having('jumlahtunggakan', '>', 1)
                ->orderBy('tblpelanggan.idareal', 'ASC')
                ->orderBy('tblpelanggan.nomorrekening', 'ASC')
                ->skip($request->jum * 29)
                ->take(29)
                // ->limit(20)->get();
                // ->orderBy('lock_action.id', 'desc')
                // ->limit(10)
                ->get();
        } else {
            $customer = CtmPelanggan::selectRaw('tblpelanggan.namapelanggan,lock_action.id,lock_action.action_date, lock_action.type as lockActionType, tblpelanggan.nomorrekening, tblwilayah.id as wilayah_id, tblpelanggan.alamat,tblpelanggan.idgol , tblpelanggan.nomorrekening, staffs.name as staff_name ,(((count(tblpembayaran.statuslunas) * 2) - sum(tblpembayaran.statuslunas)) DIV 2) as jumlahtunggakan,  (case when( (((count(tblpembayaran.statuslunas) * 2) - sum(tblpembayaran.statuslunas)) DIV 2) > 1 ) THEN 1 ELSE 0 END) as statusnunggak')
                ->join('tblwilayah', 'tblpelanggan.idareal', '=', 'tblwilayah.id')
                ->join('tblpembayaran', 'tblpelanggan.nomorrekening', '=', 'tblpembayaran.nomorrekening')
                // ->leftJoin('ptabroot_simpletab.lock_action', 'lock_action.customer_id', '=', 'tblpelanggan.nomorrekening')
                // ->leftJoin('ptabroot_simpletab.lock_action', function ($join) {
                //     $join->on('lock_action.customer_id', '=', 'tblpelanggan.nomorrekening')->orderBy('lock_action.customer_');
                // })

                // ->leftJoin('ptabroot_simpletab.lock_action', function ($query) {
                //     $query
                //         ->whereRaw('ptabroot_simpletab.lock_action.id IN (select MAX(ptabroot_simpletab.lock_action.id) from ptabroot_simpletab.lock_action group by ptabroot_simpletab.lock_action.customer_id)');
                // })
                ->leftJoin(DB::raw('(' . $subQuery->toSql() . ') lock_action'), 'tblpelanggan.nomorrekening', '=', 'lock_action.customer_id')
                // ->leftJoin('ptabroot_simpletab.lock_action',   function ($query) {
                //     $query->select('lock_action.id')
                //         ->orderBy('lock_action.id', 'desc');
                // })
                ->leftJoin('ptabroot_simpletab.staffs', 'staffs.id', '=', 'lock_action.id')
                ->where('tblpelanggan.status', 1)
                ->whereDate(DB::raw('concat(tblpembayaran.tahunrekening,"-",tblpembayaran.bulanrekening,"-01")'), '<', date('Y-n-01'))
                ->whereDate(DB::raw('concat(tblpembayaran.tahunrekening,"-",tblpembayaran.bulanrekening,"-01")'), '>=', $last_4_month)
                ->groupBy('tblpembayaran.nomorrekening')
                ->having('jumlahtunggakan', '>', 1)
                ->orderBy('tblpelanggan.idareal', 'ASC')
                ->orderBy('tblpelanggan.nomorrekening', 'ASC')
                // ->limit(20)->get();
                // ->orderBy('lock_action.id', 'desc')
                ->skip($request->jum * 29)
                ->take(29)
                ->get();
        }


        // dd($customer);
        if ($month_now > $month_next) {
            $month_next = $month_next + 12;
        }

        foreach ($customer as $d) {
            $data = array(
                'nomorrekening' => $d->nomorrekening,
            );

            $url = 'https://yndvck.perumdatab.com/akademi-pelawak-tpi/tgh.api.php';

            //open connection
            $ch = curl_init();

            //set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, count($data));
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            //execute post
            $ctm = curl_exec($ch);
            $ctm = json_decode($ctm);
            // dd($ctm[count($ctm) - 1]->tglbayarterakhir);
            //close connection
            curl_close($ch);

            if ($date_now > $date_comp) {
                $ctm_lock = 0;
            } else {
                $ctm_lock = 1;
            }

            $ctm_num_row = count($ctm) - 1;
            $status_paid_this_month = 0;
            foreach ($ctm as $key => $item) {
                //get this month paid
                if ($item->bulanrekening == $month_now && $item->tahunrekening == $year_now) {
                    if ($item->statuslunas == 2) {
                        $status_paid_this_month = 1;
                    }
                }
                //get sudah dibayar
                $item->sudahdibayar = 0;
                if ($item->statuslunas == 2) {
                    $item->sudahdibayar = $item->wajibdibayar;
                }
                $sisa = $item->wajibdibayar - $item->sudahdibayar;
                //if not paid
                if ($sisa > 0) {
                    $ctm[$key]->tglbayarterakhir = "";
                }
                //denda & $item->sudahdibayar=$item->wajibdibayar;
                $ctm[$key]->denda = 0;
                $ctm[$key]->sudahdibayar = $item->sudahdibayar;
                //set to prev
                $ctm[$key]->tahunrekening = date('Y', strtotime(date($item->tahunrekening . '-' . $item->bulanrekening . '-01') . " -1 month"));
                $ctm[$key]->bulanrekening = date('m', strtotime(date($item->tahunrekening . '-' . $item->bulanrekening . '-01') . " -1 month"));
                //if status 0
                if ($ctm[$key]->status == 0 && $key == $ctm_num_row) {
                    unset($ctm[$key]);
                }
            }
            $date2 = date('Y-m-d', strtotime('-1 month', strtotime(date('Y-m-d'))));
            if ($d->action_date != null) {
                $tgl2 = new DateTime($d->action_date);
                $tgl1 = new DateTime($date2);
                $jarak = $tgl2->diff($tgl1);
                $jarak = $jarak->days;
                // dd($jarak->days);
            } else {
                $jarak = "";
            }
            // dd($jarak->d);
            $d1[] = ['namapelanggan' => $d->namapelanggan, 'lockActionType' => $d->lockActionType, 'nomorrekening' => $d->nomorrekening, 'wilayah_id' => $d->wilayah_id, 'alamat' => $d->alamat, 'idgol' => $d->idgol, 'staff_name' => $d->staff_name, 'status_paid_this_month' => $status_paid_this_month, 'tglbayarterakhir' =>  $ctm[$key]->tglbayarterakhir, 'jarak' => $jarak, 'action_date' => $d->action_date];
            // return response()->json([
            //     'message' => 'Data CTM',
            //     'data' => $ctm,
            //     'month_next' => $month_next,
            //     'ctm_lock' => $ctm_lock,
            //     'status_paid_this_month' => $status_paid_this_month,
            // ]);
        }
        // dd($d1);


        // $customer = [];
        // dd($d1);
        $jum = $request->jum;
        $take = $request->take - 1;
        return view('admin.reports.reportLockAction', compact('customer', 'request', 'month', 'monthR', 'd1', 'take', 'jum'));
    }
}
