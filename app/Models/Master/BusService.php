<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class BusService extends Model
{
    protected $table = 'mst_bus_service';
    protected $fillable = [
        'axle_type',
        'description',
        'active_status'
    ];
}
