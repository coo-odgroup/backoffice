<?php

namespace App\Models\blogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManageCityContent extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'mst_city_content';
    protected $fillable = [
        'id',
        'city_id',
        'content',
        'active_status',
        'content',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];
}
