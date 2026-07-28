<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'mst_department';
    protected $fillable = [
        'organization_id',
        'department_id',
        'parent_department_id',
        'branch_id',
        'department_head_user_id',
        'active_status'
    ];
}
