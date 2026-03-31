<?php

namespace App\Models\Bus;

use Illuminate\Database\Eloquent\Model;

class BusRoutes extends Model
{
    protected $table = 'odbusdev.bus_routes';
    protected $fillable = [
        'route_name',
        'boarding_city_id',
        'dropping_city_id',
        'route_signature',
        'active_status'
    ];

    public function stops()
    {
        return $this->hasMany(BusRoutesStops::class, 'bus_route_id');
    }
}
