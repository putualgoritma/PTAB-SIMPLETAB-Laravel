<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkTypes extends Model
{
    use HasFactory;
    protected $fillable = [
        // 'code',
        'title',
        'type',
    ];
}
