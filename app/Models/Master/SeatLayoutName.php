<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeatLayoutName extends Model
{
    protected $table="mst_seat_layout_name";
    use HasFactory;

    protected $guarded = [];
}
