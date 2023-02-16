<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsenceLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'staff_id',
        'absence_id',
        'image',
        'lat',
        'lng',
        'register',
        'shift_planner_id',
        'requests_id',
        'memo',
        'early',
        'duration',
        'created_by_staff_id',
        'updated_by_staff_id',
        'absence_category_id',
        'expired_date',
        'start_date',
        'status',
        'late',
        'day_id',
    ];
}
