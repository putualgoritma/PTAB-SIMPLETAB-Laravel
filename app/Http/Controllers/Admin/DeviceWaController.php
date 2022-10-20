<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\WablasTrait;
use Doctrine\DBAL\Schema\View;

class DeviceWaController extends Controller
{
    public function index()
    {
        // abort_unless(\Gate::allows('user_access'), 403);
        $data = WablasTrait::checkOnline();
        // dd(json_decode($data)->data);
        $deviceWa = json_decode($data)->data;
        $scan = WablasTrait::rescan();
        return view('admin.whatsapp.device.index', compact('deviceWa', 'scan'));
    }
    public function disconect()
    {
        $disconnect = WablasTrait::disconect();
        dd($disconnect);
        return redirect()->back();
    }

    public function create()
    {
        return View('admin.whatsapp.device.create');
    }
    public function store(Request $request)
    {
        $phone = WablasTrait::changeNumber($request->phone);
        dd($phone);
        return redirect()->route('admin.devicewa.index');
    }
}
