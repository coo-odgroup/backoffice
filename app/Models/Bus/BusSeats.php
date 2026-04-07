<?php

namespace App\Models\Bus;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusSeats extends Model
{
    use HasFactory;

    protected $table = 'odbusdev.bus_seats';
    protected $fillable = [
        'seat_id',
        'bus_id',
        'type',
        'active_seats',
        'seat_layout_id',
        'seat_code'
    ];
}
