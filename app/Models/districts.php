<?php

namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

class districts extends Model
{
    protected $table = 'districts';
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
