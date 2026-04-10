<?php

namespace App\Models\Bus;

use App\Models\Master\Amenity;
use Illuminate\Database\Eloquent\Model;

class BusAmenity extends Model
{
    protected $table = 'odbusdev.bus_amenities';
    protected $fillable = [
        'bus_id',
        'category_id',
        'amenities_id',
        'active_status'
    ];

    public function amenity()
    {
        return $this->belongsTo(Amenity::class, 'amenities_id', 'id');
    }
}
