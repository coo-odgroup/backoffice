<?php

namespace App\Models\blogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    use HasFactory;
    protected $connection = 'mysql_dev';
    protected $table = 'blog_categories';
    protected $fillable = ['category_name','slug','active_status'];
   
}
