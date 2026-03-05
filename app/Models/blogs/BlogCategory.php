<?php

namespace App\Models\blogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    use HasFactory;
    protected $connection = 'mysql_dev';
    protected $table = 'blog_categories';
    protected $fillable = ['category_name','slug','description','icon','alt_text','banner_image','active_status','sort_order','meta_title','meta_keywords','meta_description'];
}
