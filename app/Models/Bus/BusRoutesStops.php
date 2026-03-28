<?php

namespace App\Models\Bus;

use Illuminate\Database\Eloquent\Model;

class BusRoutesStops extends Model
{
    protected $table = 'odbusdev.bus_route_stops';
    protected $fillable = [
        'bus_route_id',
        'bus_id',
        'city_id',
        'stop_order',
        'is_boarding',
        'is_dropping',
        'active_status'
    ];
}
