<?php

namespace App\Models\Master;

use App\Models\Bus\BusRoutesStops;
use Illuminate\Database\Eloquent\Model;

class Cities extends Model
{
    protected $table = 'mst_cities';
    protected $fillable = [
        'state_id',
        'district_id',
        'city_name',
        'alias',
        'latitude',
        'longitude',
        'active_status'
    ];

    // City belongs to state
    public function state()
    {
        return $this->belongsTo(States::class);
    }

    // City belongs to district
    public function district()
    {
        return $this->belongsTo(Districts::class);
    }

    // City has many synonyms
    public function synonyms()
    {
        return $this->hasMany(CitiesSynonyms::class);
    }

    public function stops()
    {
        return $this->hasMany(BusRoutesStops::class, 'city_id');
    }

    public function boardingdroppings()
    {
        return $this->hasMany(BoardingDropping::class, 'cities_id');
    }
}
