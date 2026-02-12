<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class citiesSynonyms extends Model
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
