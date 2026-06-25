<?php

namespace App\Models\blogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    use HasFactory;
    protected $connection = 'mysql_dev';
    protected $table = 'blog_categories';
    protected $fillable = [
        'category_name',
        'slug',
        'small_desc',
        'small_desc',
        'description',
        'icon',
        'alt_text',
        'canonical_url',
        'banner_image',
        'active_status',
        'sort_order',
        'og_image',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'breadcrumb_schema',
    ];
}
