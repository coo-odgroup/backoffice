<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class CityApis extends Model
{
    protected $table = 'city_api_ids';
    protected $fillable = [
        'city_id',
        'api_app_id',
        'api_city_ids',
        'active_status'
    ];
}
