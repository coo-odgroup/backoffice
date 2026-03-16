<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class MstSeatLayout extends Model
{
    protected $table = 'mst_seat_layout';
    protected $fillable = [
        'seat_layout',
        'active_status'
    ];
}
