<?php

namespace App\Models\Bus;

use App\Models\Master\Cities;
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

    public function bus()
    {
        return $this->belongsTo(Bus::class, 'bus_id');
    }

    public function route()
    {
        return $this->belongsTo(BusRoutes::class, 'bus_route_id');
    }

    public function city()
    {
        return $this->belongsTo(Cities::class, 'city_id');
    }
}
