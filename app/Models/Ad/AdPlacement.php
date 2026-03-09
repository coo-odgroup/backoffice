<?php

namespace App\Models\Ad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdPlacement extends Model
{
    use HasFactory;
    protected $connection = 'mysql_dev';
    protected $table = 'ad_placements';
    protected $fillable = [
        'name',
        'slug',
        'description',
        'default_model',
        'active_status'
    ];
}
