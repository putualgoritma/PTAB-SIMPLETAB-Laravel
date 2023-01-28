<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index()
    {
        $waktu_awal        = strtotime("2019-10-11 00:01:25");
        $waktu_akhir    = strtotime("2019-10-12 12:07:59:00"); // bisa juga waktu sekarang now()

        //menghitung selisih dengan hasil detik
        $diff    = $waktu_akhir - $waktu_awal;

        //membagi detik menjadi jam
        $jam    = floor($diff / (60 * 60));

        //membagi sisa detik setelah dikurangi $jam menjadi menit
        $menit    = $diff - $jam * (60 * 60);

        //menampilkan / print hasil
        echo 'Hasilnya adalah ' . number_format($diff, 0, ",", ".") . ' detik<br /><br />';
        echo 'Sehingga Anda memiliki sisa waktu promosi selama: ' . $jam .  ' jam dan ' . floor($menit / 60) . ' menit';
        dd('Sehingga Anda memiliki sisa waktu promosi selama: ' . $jam .  ' jam dan ' . floor($menit / 60) . ' menit');
        $leave =  [
            ['id' => '1', 'name' => 'Wayan',  'description' => 'Cuti Tahunan', 'start' => '10-10-2022', 'end' => '13-10-2022', 'status' => 'pending', 'file' => '1.jpg'],
            ['id' => '2', 'name' => 'Kadek', 'description' => 'Cuti Besar', 'start' => '10-10-2022', 'end' => '13-10-2022', 'status' => 'approve', 'file' => ''],
            ['id' => '3', 'name' => 'Nyoman', 'description' => 'Cuti Bersalin', 'start' => '10-10-2022', 'end' => '13-10-2022', 'status' => 'approve', 'file' => '1.jpg'],

        ];
        return view('admin.leave.index', compact('leave'));
    }
}
