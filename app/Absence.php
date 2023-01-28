<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'user_id',
        'image',
        'lat',
        'lng',
        'register',
        'shift_id',
        'requests_id',
        'absence_category_id',
        'day_id',
    ];
}
