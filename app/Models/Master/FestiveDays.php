<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class FestiveDays extends Model
{
    protected $table = 'mst_festive_days';
    protected $fillable = [
        'festive_name',
        'festive_date',
        'short_desc',
        'year',
        'active_status',
        'created_by',
        'updated_by'
    ];
}
