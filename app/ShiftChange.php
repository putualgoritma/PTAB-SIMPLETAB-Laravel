<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftChange extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'shift_id',
        'shift_change_id',
        'description',
        'created_at',
        'updated_at',
    ];
}
