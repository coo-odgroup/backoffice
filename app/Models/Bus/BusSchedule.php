<?php

namespace App\Models\BusSchedule;


use App\Models\Users;
use Illuminate\Database\Eloquent\Model;

class BusSchedule extends Model
{
    protected $table = 'odbusdev.bus';
    protected $fillable = [
        'operator_id',
        'bus_id',
        'running_cycle',
        'active_status',
    ];

   

    public function operator()
    {
        return $this->belongsTo(Users::class, 'bus_operator_id');
    }

}
