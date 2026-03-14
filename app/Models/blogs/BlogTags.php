<?php

namespace App\Models\blogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogTags extends Model
{
    use HasFactory;
    protected $connection = 'mysql_dev';
    protected $table = 'blog_tags';
    protected $fillable = ['tag_name','slug'];
}
