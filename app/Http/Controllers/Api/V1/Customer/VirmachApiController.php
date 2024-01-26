<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use App\Traits\TraitModel;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VirmachApiController extends Controller
{
    use TraitModel;

    public function storeImg(Request $request)
    {
        if ($request->hasFile('file')) {

            //make directory
            if (!Storage::disk('sftp')->exists($request->path)) {
                Storage::disk('sftp')->makeDirectory($request->path, 0777, true);
            }

            //get filename without extension
            $filename = $request->path . $request->image_name;

            try {
                //Upload File to external server
                Storage::disk('sftp')->put($filename, fopen($request->file('file'), 'r+'));
                //response
                return response()->json([
                    'status' => true,
                    'data' => [],
                ]);
            } catch (Exception $e) {
                //respomse failed
                return response()->json([
                    'status' => false,
                    'data' => [],
                ]);
            }
        } else {
            //respomse failed
            return response()->json([
                'status' => false,
                'data' => [],
            ]);
        }
    }

    public function getListFiles($dirName, $dirStart, $start, $limit)
    {
        $countStart = $start;
        $countEnd = $countStart + $limit;
        $list = array();
        $blacklist = array('.', '..');
        if (is_dir($dirName . $dirStart)) {
            $files = preg_grep('/^([^.])/', scandir($dirName . $dirStart));
            $files_total = count($files);
            for ($i = $countStart; $i < $countEnd; $i++) {
                if (isset($files[$i]) && !in_array($files[$i], $blacklist)) {
                    //echo $files[$i] . "</br>";
                    $list[] = $files[$i];
                }
            }
        }
        return $list;
    }

    public function transferImg()
    {
        $start = time();
        //scan directory
        $selectedFolder = '/pdam/gambar/';
        $dirName = '/home/ptabroot/public_html' . $selectedFolder;
        //get log transfer
        $limit = 1000;
        $storageLog = DB::table('storage_transfer_logs')->orderBy('register', 'desc')->first();
        if (!$storageLog) {
            //generate dir_start
            $dirStart = (int) date("Y") . "01";
            $start = 0;
        } else {
            $dirStart = $storageLog->dir_start;
            $start = $storageLog->start;
        }

        //return $dirName." : ".$dirStart." : ".$start." : ".$limit;
        $dirList = $this->getListFiles($dirName, $dirStart, $start, $limit);
        $files_total = 0;
        if (is_dir($dirName . $dirStart)) {
            $files = preg_grep('/^([^.])/', scandir($dirName . $dirStart));
            $files_total = count($files);
        }
        //return $dirList;

        //get active ctm
        //$ctmPeriodActive = (int) date("Y") . date("m");
        $ctmPeriodActive = (int) date('Ym', strtotime(date('Y-m')." -1 month"));
        if ($files_total > 0 && $dirStart < $ctmPeriodActive) {
            $nameDirectory = $dirStart;
            $pathDirectory = env('SFTP_ROOT') . $selectedFolder . $nameDirectory;
            if (!Storage::disk('sftp')->exists($selectedFolder . $nameDirectory)) {
                Storage::disk('sftp')->makeDirectory($selectedFolder . $nameDirectory, 0777, true);
            }
            $dirImg = '/home/ptabroot/public_html' . $selectedFolder . $nameDirectory;
            $dirListImg = $dirList;
            foreach ($dirListImg as $item) {
                $sourcePath = $dirImg . "/" . $item;
                $destinationPath = $selectedFolder . $nameDirectory . '/' . $item;
                //Upload File to external server
                //echo $sourcePath . " : " . $destinationPath . "</br>";
                Storage::disk('sftp')->put($destinationPath, fopen($sourcePath, 'r+'));
            }
            //insert log trsf
            $countEnd = $start + $limit;
            if ($countEnd > $files_total) {
                $dirStart++;
                $countEnd = 0;
                //check if over year
                $month = substr($dirStart, 4, 2);
                $year = substr($dirStart, 0, 4);
                if ($month > 12) {
                    $dirStart = ($year + 1) . "01";
                }
            }

            DB::table('storage_transfer_logs')->insert(
                ['register' => date("Y-m-d H:i:s"), 'dir_start' => $dirStart, 'start' => $countEnd]
            );
        }
        //done
        echo "Selesai...." . " <br />";
        $duration = time() - $start;
        var_dump($duration); //seconds
    }
}
