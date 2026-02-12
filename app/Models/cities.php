<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class cities extends Model
{
    protected $table = 'cities';
    protected $fillable = [
        'state_id',
        'district_id',
        'city_name',
        'alias',
        'active_status'
    ];

    // City belongs to state
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    // City belongs to district
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    // City has many synonyms
    public function synonyms()
    {
        return $this->hasMany(CitySynonym::class);
    }
}
