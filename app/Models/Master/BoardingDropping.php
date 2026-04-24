<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class BoardingDropping extends Model
{
    protected $table = 'mst_boarding_droping';


    protected $fillable = [
        'cities_id',
        'type',
        'brd_drp_point',
        'landmark',
        'lattitude',
        'longitude',
        'sequece_no',
        'active_status',
        'created_by',
        'updated_by'
    ];

    public function city()
    {
        return $this->belongsTo(Cities::class, 'cities_id', 'id');
    }
}
