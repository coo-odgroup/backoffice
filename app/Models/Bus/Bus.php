<?php

namespace App\Models\Bus;

use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    protected $table = 'odbusdev.bus';
    protected $fillable = [
        'bus_operator_id',
        'name',
        'via',
        'bus_number',
        'brand_id',
        'model_id',
        'axle_type_id',
        'service_id',
        'ac_type_id',
        'seat_type_id',
        'seat_layout_type_id',
        'gen_bus_type',
        'cancellationslabs_id',
        'running_cycle',
        'popularity',
        'type',
        'sequence',
        'max_seat_book',
        'lower_sleeper_extra_fare',
        'min_price',
        'min_price_updated_on',
        'is_irctc_model',
        'active_status'
    ];
}
