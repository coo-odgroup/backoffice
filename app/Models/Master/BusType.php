<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class BusType extends Model
{
    protected $table = 'mst_bus_type';
    protected $fillable = [
        'class_id',
        'bus_type',
        'active_status'
    ];
}
