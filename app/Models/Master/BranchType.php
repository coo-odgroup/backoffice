<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class BranchType extends Model
{
    protected $table = 'mst_branch_types';
    protected $fillable = [
        'organization_type_id',
        'branch_type_name',
        'branch_type_code',
        'description',
        'display_order',
        'active_status'
    ];
}
