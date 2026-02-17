<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Districts extends Model
{
    protected $table = 'mst_districts';
    protected $fillable = [
        'state_id',
        'district_name',
        'active_status'
    ];

    // District belongs to state
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    // District has many cities
    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
