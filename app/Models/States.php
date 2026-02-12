<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class States extends Model
{
    protected $table = 'states';
    protected $fillable = [
        'state_name',
        'active_status'
    ];

    // A state has many districts
    public function districts()
    {
        return $this->hasMany(District::class);
    }

    // A state has many cities
    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
