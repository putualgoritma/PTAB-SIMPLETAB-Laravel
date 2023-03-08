<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftGroups extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        // 'code',
        'title',
        'dapertement_id',
        'job_id',
        'subdapertement_id',
        'shift_parent_id',
        'created_at',
        'updated_at',
        'work_type_id',
        'queue',
    ];
}
