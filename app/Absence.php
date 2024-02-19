<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Absence extends Model
{
    use HasFactory;
    protected $fillable = [
        'day_id',
        'shift_group_id',
        'staff_id',
        'status_active',
        'created_at',
        'updated_at',
        'description'

    ];

    public function absence_logs()
    {
        return $this->hasMany(AbsenceLog::class, 'absence_id', 'id');
    }
    public function staffs()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'id');
    }

    // public function workTypeDay()
    // {
    //     return $this->belongsTo(WorkTypeDays::class, 'work_type_day_id', 'id');
    // }

    // public function shiftGroupTimeSheet()
    // {
    //     return $this->belongsTo(ShiftGroupTimesheets::class, 'shift_group_timesheet_id', 'id');
    // }

    public function getCreatedAtAttribute()
    {
        $timeStamp = date("Y-m-d", strtotime($this->attributes['created_at']));
        return $timeStamp;
    }

    public function scopeFilterDate($query, $from, $to)
    {
        if (!empty(request()->input('from')) && !empty(request()->input('to'))) {
            $from = request()->input('from');
            $to =  request()->input('to');
            // $from = '2021-09-01';
            // $to = '2021-09-20';
            //return $query->whereBetween('lock_action.created_at', [$from, $to]);
            return $query->whereBetween(DB::raw('DATE(absences.created_at)'), [$from, $to]);
            // return $query->where('froms_id', $from);
            // dd(request()->input('from'));

        } else {
            if (date('d') > 20) {
                $from = date("Y-m-d", strtotime(date('Y-m') . "-21"));
                $to = date("Y-m-d", strtotime('+1 month', strtotime(date('Y-m') . "-20")));
            } else {
                $from = date("Y-m-d", strtotime('-1 month', strtotime(date('Y-m') . "-21")));
                $to = date("Y-m-d", strtotime('0 month', strtotime(date('Y-m') . "-20")));
            }

            return $query->whereBetween(DB::raw('DATE(absences.created_at)'), [$from, $to]);
        }
    }

    public function scopeFilterAbsence($query, $id)
    {
        if ($id != '') {
            return $query->where('absences.id', '=', $id);
        } else {
            return $query;
        }
    }

    public function scopeFilterSubdapertement($query, $subdapertement, $job)
    {
        if ($job == '' || $job == '0' || $job == null) {
            if ($subdapertement != '' && $subdapertement != '') {
                $query->where('staffs.subdapertement_id', $subdapertement);
            }
            return $query;
        }
    }

    public function scopeFilterDapertement($query, $dapertement)
    {
        if ($dapertement != '') {
            $query->where('staffs.dapertement_id', $dapertement);
        }
        return $query;
    }

    public function scopeFilterJob($query, $job)
    {
        if ($job != '' && $job != '0' && $job != null) {
            $query->where('staffs.job_id', '=', $job);
        }
        return $query;
    }

    public function scopeFilterWorkUnit($query, $WorkUnit)
    {
        if ($WorkUnit != '' && $WorkUnit != '0') {
            $query->where('shift_groups.work_unit_id', '=', $WorkUnit);
        }
        return $query;
    }
}
