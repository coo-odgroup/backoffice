<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'mst_departments';
    protected $fillable = [
        'department_name',
        'department_code',
        'description',
        'active_status'
    ];
}
