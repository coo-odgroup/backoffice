<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class DepartmentType extends Model
{
    protected $table = 'mst_department_type';
    protected $fillable = [
        'department_name',
        'department_code',
        'description',
        'active_status'
    ];
}
