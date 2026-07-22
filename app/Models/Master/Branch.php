<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $table = 'mst_branches';
    protected $fillable = [
        'organization_id',
        'branch_type_id',
        'parent_branch_id',
        'branch_name',
        'branch_code',
        'email',
        'phone',
        'address1',
        'address2',
        'city_id',
        'state_id',
        'country_id',
        'pincode',
        'latitude',
        'longitude',
        'opening_date',
        'is_head_office',
        'active_status'
    ];
}
