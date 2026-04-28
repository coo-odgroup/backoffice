<?php

namespace App\Models\Bus;

use App\Models\Master\BoardingDropping;
use App\Models\Master\Cities;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusBoardingDropping extends Model
{
    use HasFactory;

    protected $table = 'odbusdev.bus_boarding_dropping';
    protected $fillable = [
        'bus_id',
        'type',
        'city_id',
        'stop_id',
        'timing',
        'active_status'
    ];

    public function city()
    {
        return $this->belongsTo(Cities::class, 'city_id');
    }

    public function stop()
    {
        return $this->belongsTo(Cities::class, 'stop_id');
    }

    public function station()
    {
        return $this->belongsTo(BoardingDropping::class, 'stop_id');
    }
}
