<?php

namespace App\Models\Bus;

use App\Models\Master\Cities;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusRouteFares extends Model
{
    use HasFactory;

    protected $table = 'odbusdev.bus_route_fares';
    protected $fillable = [
        'bus_id',
        'bus_route_id',
        'from_stop_id',
        'from_journey_day',
        'to_stop_id',
        'to_journey_day',
        'seat_fare',
        'upper_sleeper_fare',
        'lower_sleeper_fare',
        'seize_time',
        'close_time',
        'active_status'
    ];

    public function source()
    {
        return $this->belongsTo(Cities::class, 'from_stop_id');
    }

    public function destination()
    {
        return $this->belongsTo(Cities::class, 'to_stop_id');
    }
}
