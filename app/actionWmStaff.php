<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class actionWmStaff extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'staff_id',
        'action_wm_id',
    ];

    public function scopeFilterStatusWM($query, $ststussm)
    {
        if ($ststussm != '') {
            $query->where('status_wm', $ststussm);
        }
        return $query;
    }

    public function scopeFilterAreas($query, $idareal)
    {
        if ($idareal != '') {
            $query->where('tblpelanggan.idareal', $idareal);
        }
        return $query;
    }

    public function scopeFilterPriority($query, $priority)
    {
        if ($priority != '') {
            $query->where('proposal_wms.priority', $priority);
        }
        return $query;
    }
    public function scopeFilterStatus($query, $status)
    {
        if ($status != '') {
            $query->where('proposal_wms.status', $status);
        }
        return $query;
    }
    public function scopeFilterDate($query, $from, $to)
    {
        if (!empty(request()->input('from')) != "" && !empty(request()->input('to')) != "") {
            $from = request()->input('from');
            $to =  request()->input('to');
            // $from = '2021-09-01';
            // $to = '2021-09-20';
            return $query->whereBetween('proposal_wms.updated_at', [$from, $to]);
            // return $query->where('froms_id', $from);
            // dd(request()->input('from'));

        } else {
            return;
        }
    }
}
