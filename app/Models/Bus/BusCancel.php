<?php

namespace App\Models\Bus;


use App\Models\Users;
use Illuminate\Database\Eloquent\Model;

class BusCAncel extends Model
{
    protected $table = 'odbusdev.bus_schedule';
    
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
