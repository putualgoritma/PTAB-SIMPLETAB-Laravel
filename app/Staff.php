<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staffs';
    protected $fillable = [
        'code',
        'name',
        'phone',
        'dapertement_id',
        'subdapertement_id',
        'work_unit_id',
        'pbk',
        'work_type_id',
        'job_id',
        'NIK',
        'image'
    ];

    public function dapertement()
    {
        return $this->belongsTo(Dapertement::class, 'dapertement_id', 'id');
    }

    public function subdapertement()
    {
        return $this->belongsTo(Subdapertement::class, 'subdapertement_id', 'id');
    }

    public function action()
    {
        return $this->belongsToMany(Action::class, 'action_staff', 'action_id', 'staff_id')
            ->withPivot([
                'status'
            ]);
    }
    public function area()
    {
        return $this->belongsToMany(CtmWilayah::class, 'ptabroot_simpletab.area_staff', 'staff_id', 'area_id');
    }
    public function scopeFilterDapertement($query, $dapertement)
    {
        if ($dapertement != '') {
            $query->where('dapertement_id', $dapertement);
        }
        return $query;
    }
    public function scopeFilterJob($query, $job)
    {
        if ($job != '') {
            $query->where('job_id', $job);
        }
        return $query;
    }
    public function scopeFilterWorkUnit($query, $work_unit)
    {
        if ($work_unit != '') {
            $query->where('work_unit_id', $work_unit);
        }
        return $query;
    }
}
