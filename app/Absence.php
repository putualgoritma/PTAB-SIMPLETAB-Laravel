<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    ];
}
