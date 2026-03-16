<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class AnnextureType extends Model
{
    protected $table = 'mst_annexture_type';
    protected $fillable = [
        'anexture_type',
        'active_status'
    ];
}
