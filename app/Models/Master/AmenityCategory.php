<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class AmenityCategory extends Model
{
    protected $table = 'mst_amenity_categories';
    protected $fillable = [
        'category_name',
        'description',
        'display_order',
        'active_status'
    ];

    public function amenities()
    {
        return $this->hasMany(Amenity::class, 'category_id');
    }
}
