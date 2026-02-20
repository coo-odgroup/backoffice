<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    protected $table = 'mst_amenities';
    protected $fillable = [
        'category_id',
        'amenity_name',
        'description',
        'icon',
        'is_paid',
        'is_seat_specific',
        'active_status'
    ];
}
