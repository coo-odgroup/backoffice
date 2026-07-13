<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $table = 'mst_organization_types';
    protected $fillable = [
        'type_code',
        'type_name',
        'small_desc',
        'is_branches',
        'is_employees',
        'is_sell_tickets',
        'active_status',
    ];
}