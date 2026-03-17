<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Annexture extends Model
{
    protected $table = 'mst_annexture';
    protected $fillable = [
        'annexture_type_id',
        'annexture_name',
        'annexture_value',
        'active_status'
    ];
}
