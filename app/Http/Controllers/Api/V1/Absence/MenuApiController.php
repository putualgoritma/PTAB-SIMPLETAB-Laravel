<?php

namespace App\Http\Controllers\Api\V1\Absence;

use App\Absence;
use App\Holiday;
use App\Http\Controllers\Controller;
use App\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuApiController extends Controller
{

    public function index(Request $request)
    {
        $holiday = Holiday::selectRaw('count(id) as id')->where('date', date('Y-m-d'))->first()->id;
        $workDay = $this->countDays(date('Y'), date('n'), array(0, 6)) - $holiday;
        $menu = Absence::selectRaw('(SUM(value)/2)/"' . $workDay . '" as persentage, SUM(value) as total')
            ->groupBy(DB::raw('MONTH(register)'))
            ->whereBetween('register', [date("Y-n-d", strtotime('-4 month', strtotime(date('Y-m-01')))), date("Y-n-d", strtotime('+1 month', strtotime(date('Y-m-01'))))])
            ->where('user_id', $request->user_id)
            ->get();
        $duty = Requests::where('category', 'duty')->whereDate('date', '=', date('Y-m-d'))->where('user_id', $request->user_id)->where('status', 'approve')->first();
        $extra = Requests::where('category', 'extra')->whereDate('date', '=', date('Y-m-d'))->where('user_id', $request->user_id)->where('status', 'approve')->first();
        $permit = Requests::where('category', 'permit')->whereDate('date', '=', date('Y-m-d'))->where('user_id', $request->user_id)->where('status', 'approve')->first();
        return response()->json([
            'message' => 'Success',
            'duty' => $duty,
            'permit' => $permit,
            'extra' => $extra,
            // 'month1' => round($menu[0]->persentage * 100),
            'month1' => 50,
            'month2' => 80,
            'month3' => 90,
            'date' => $this->countDays(date('Y'), date('n'), array(0, 6))
        ]);
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
        echo $this->countDays(date('Y'), date('n'), array(0, 6)); // 23
    }
}
