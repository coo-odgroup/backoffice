<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $table = 'mst_roles';
    protected $fillable = [
        'organization_type_id',
        'organization_id',
        'role_code',
        'role_name',
        'description',
        'is_system_role',
        'active_status'
    ];
}
