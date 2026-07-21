<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class RolesHierarchy extends Model
{
    protected $table = 'mst_role_hierarchy';
    protected $fillable = [
        'organization_type_id',
        'role_id',
        'hierarchy_level',
        'parent_role_id',
        'can_create_users',
        'can_manage_lower_roles',
        'active_status'
    ];
}
