<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftPlannerStaffs extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'shift_group_id',
        'staff_id',
        'date',
        'start',
        'end',
        'created_at',
        'updated_at',
    ];
}
