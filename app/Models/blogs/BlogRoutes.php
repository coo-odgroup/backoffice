<?php

namespace App\Models\blogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogRoutes extends Model
{
    use HasFactory;
    protected $connection = 'mysql_dev';
    protected $table = 'blog_routes';
    protected $fillable = ['blog_id','from_city_id','to_city_id','route_slug'];
}
