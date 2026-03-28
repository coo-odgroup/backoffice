<?php

namespace App\Models\Master;

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
        return $this->belongsTo(mst_State::class);
    }

    // City belongs to district
    public function district()
    {
        return $this->belongsTo(mst_District::class);
    }

    // City has many synonyms
    public function synonyms()
    {
        return $this->hasMany(CitySynonym::class);
    }
}
