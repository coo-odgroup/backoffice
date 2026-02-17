<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class States extends Model
{
    protected $table = 'mst_states';
    protected $fillable = [
        'state_name',
        'active_status'
    ];

    // A state has many districts
    public function districts()
    {
        return $this->hasMany(Districts::class);
    }

    // A state has many cities
    public function cities()
    {
        return $this->hasMany(Cities::class);
    }
}
