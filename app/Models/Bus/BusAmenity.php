<?php

namespace App\Models\Bus;

use App\Models\Master\Amenity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusAmenity extends Model
{
    use SoftDeletes;
    protected $table = 'odbusdev.bus_amenities';
    protected $fillable = [
        'bus_id',
        'category_id',
        'amenities_id',
        'active_status',
        'deleted_at',
        'deleted_by'
    ];

    public function amenity()
    {
        return $this->belongsTo(Amenity::class, 'amenities_id', 'id');
    }
}
