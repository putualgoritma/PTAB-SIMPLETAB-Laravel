<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\CtmPelanggan;
use App\Http\Controllers\Controller;
use App\Imports\StaffImport;
use App\Staff;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class GawatDaruratController extends Controller
{
    public function index()
    {
        $date_now = date('Y-m-d');
        $date_comp = date('Y-m') . '-20';
        $last_4_month = date("Y-n-d", strtotime('-4 month', strtotime(date('Y-m-01'))));
        // return view('admin.gawatdarurat.editImport');
        if ($date_now > $date_comp) {
            $qry = CtmPelanggan::selectRaw('tblpelanggan.nomorrekening,tblpelanggan.idareal,tblwilayah.group_unit,tblpembayaran.bulanrekening, (((count(tblpembayaran.statuslunas) * 2) - sum(tblpembayaran.statuslunas)) DIV 2) as jumlahtunggakan,  (case when( (((count(tblpembayaran.statuslunas) * 2) - sum(tblpembayaran.statuslunas)) DIV 2) > 1 ) THEN 1 ELSE 0 END) as statusnunggak, tblpelanggan.status')
                ->join('tblwilayah', 'tblpelanggan.idareal', '=', 'tblwilayah.id')
                ->join('tblpembayaran', 'tblpelanggan.nomorrekening', '=', 'tblpembayaran.nomorrekening')

                ->whereDate(DB::raw('concat(tblpembayaran.tahunrekening,"-",tblpembayaran.bulanrekening,"-01")'), '<=', date('Y-n-01'))
                ->whereDate(DB::raw('concat(tblpembayaran.tahunrekening,"-",tblpembayaran.bulanrekening,"-01")'), '>=', $last_4_month)
                ->having('jumlahtunggakan', '>', 3)
                ->where('tblpelanggan.nomorrekening', '54172')
                // ->orHaving('jumlahtunggakan', '<', 3)
                ->orWhere('tblpelanggan.status', 1)
                ->whereDate(DB::raw('concat(tblpembayaran.tahunrekening,"-",tblpembayaran.bulanrekening,"-01")'), '<=', date('Y-n-01'))
                ->whereDate(DB::raw('concat(tblpembayaran.tahunrekening,"-",tblpembayaran.bulanrekening,"-01")'), '>=', $last_4_month)
                ->having('jumlahtunggakan', '<', 3)
                // ->where('tblpelanggan.nomorrekening', '54172')
                ->groupBy('tblpembayaran.nomorrekening')->paginate(10, ['*'], 'page', 1);
            dd('1', $qry);
        } else {
            $qry = CtmPelanggan::selectRaw('tblpelanggan.nomorrekening,tblpelanggan.idareal,tblwilayah.group_unit,tblpembayaran.bulanrekening, (((count(tblpembayaran.statuslunas) * 2) - sum(tblpembayaran.statuslunas)) DIV 2) as jumlahtunggakan,  (case when( (((count(tblpembayaran.statuslunas) * 2) - sum(tblpembayaran.statuslunas)) DIV 2) > 1 ) THEN 1 ELSE 0 END) as statusnunggak')
                ->join('tblwilayah', 'tblpelanggan.idareal', '=', 'tblwilayah.id')
                ->join('tblpembayaran', 'tblpelanggan.nomorrekening', '=', 'tblpembayaran.nomorrekening')
                ->where('tblpelanggan.status', 1)
                ->whereDate(DB::raw('concat(tblpembayaran.tahunrekening,"-",tblpembayaran.bulanrekening,"-01")'), '<', date('Y-n-01'))
                ->whereDate(DB::raw('concat(tblpembayaran.tahunrekening,"-",tblpembayaran.bulanrekening,"-01")'), '>=', $last_4_month)
                // ->having('jumlahtunggakan', 2)
                ->where('tblpelanggan.nomorrekening', '54172')
                ->groupBy('tblpembayaran.nomorrekening');
            dd('2', $qry->get());
        }
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
