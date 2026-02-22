<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $table = 'mst_roles';
    protected $fillable = [
        'code',
        'name',
        'description',
        'is_system_role',
        'active_status'
    ];
}
