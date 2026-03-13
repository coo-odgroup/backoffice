<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $table = 'mst_brand';
    protected $fillable = [
        'brand_name',
        'country',
        'active_status'
    ];
}
