<?php

namespace App\Models\Bus;

use Illuminate\Database\Eloquent\Model;

class BusRoutesMap extends Model
{
    protected $table = 'odbusdev.bus_routes_map';
    protected $fillable = [
        'bus_id',
        'bus_route_id',
        'active_status'
    ];

    // public function route()
    // {
    //     return $this->belongsTo(BusRoutes::class, 'bus_route_id');
    // }

    public function route()
    {
        return $this->belongsTo(BusRoutes::class, 'bus_route_id', 'id');
    }

    public function bus()
    {
        return $this->belongsTo(Bus::class, 'bus_id');
    }
}
