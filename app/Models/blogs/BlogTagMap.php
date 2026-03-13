<?php

namespace App\Models\blogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogTagMap extends Model
{
    use HasFactory;
    protected $connection = 'mysql_dev';
    protected $table = 'blog_tag_map';
    protected $fillable = ['blog_id','tag_id'];
}
