<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class BusModel extends Model
{
    protected $table = 'mst_bus_models';
    protected $fillable = [
        'brand_id',
        'model-name',
        'description',
        'active_status'
    ];
}
