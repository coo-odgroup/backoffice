<?php

namespace App\Models\Bus;

use Illuminate\Database\Eloquent\Model;

class BusAmenity extends Model
{
    protected $table = 'odbusdev.bus_amenities';
    protected $fillable = [
        'bus_id',
        'category_id',
        'amenities_id',
        'active_status'
    ];
}
