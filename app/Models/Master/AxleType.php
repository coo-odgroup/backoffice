<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class AxleType extends Model
{
    protected $table = 'mst_axle_type';
    protected $fillable = [
        'axle_type',
        'active_status'
    ];
}
