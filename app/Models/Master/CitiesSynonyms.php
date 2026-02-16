<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class CitiesSynonyms extends Model
{
    protected $table = 'cities_synonyms';
    protected $fillable = [
        'city_id',
        'synonym'
    ];

    // Synonym belongs to city
    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
