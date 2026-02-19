<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class SeatType extends Model
{
    protected $table = 'mst_seat_type';
    protected $fillable = [
        'seat_type',
        'active_status'
    ];
}
