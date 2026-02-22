<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Modules extends Model
{
    protected $table = 'mst_modules';
    protected $fillable = [
        'code',
        'name',
        'parent_id',
        'sequence_no',
        'active_status'
    ];
}
