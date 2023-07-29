<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitImage extends Model
{
    use HasFactory;
    protected $fillable = [
        'visit_id',
        'image',
        'created_at',
        'updated_at',
    ];
}
