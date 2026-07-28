<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class UserRoles extends Model
{
    protected $table = 'user_roles';
    protected $fillable = [
        'user_id',
        'role_id',
        'organization_type_id',
        'organization_id',
        'branch_id',
        'is_primary',
        'effective_from',
        'effective_to',
        'assigned_by',
        'active_status'
    ];
}
