<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Reason extends Model
{
    protected $table = 'mst_reason';
    protected $fillable = [
        'reason',
        'active_status'
    ];
}
