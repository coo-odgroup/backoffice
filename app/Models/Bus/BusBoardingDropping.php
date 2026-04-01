<?php

namespace App\Models\Bus;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusBoardingDropping extends Model
{
    use HasFactory;

    protected $table = 'odbusdev.bus_boarding_dropping';
    protected $fillable = [
        'bus_id',
        'type',
        'city_id',
        'stop_id',
        'timing',
        'active_status'
    ];
}
