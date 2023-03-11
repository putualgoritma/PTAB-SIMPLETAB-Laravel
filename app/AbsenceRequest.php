<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsenceRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'staff_id',
        'title',
        'start',
        'end',
        'type',
        'time',
        'status',
        'category',
        'description',
        'attendance',
        'created_at',
        'updated_at',
        'attendance'

    ];

    public function scopeFilterCategory($query, $category)
    {
        if ($category != '') {
            $query->where('absence_requests.category', $category);
        }
        return $query;
    }

    public function scopeFilterStatus($query, $status)
    {
        if ($status != '') {
            $query->where('absence_requests.status', $status);
        }
        return $query;
    }
}
