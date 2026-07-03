<?php

namespace App\Models\blogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;
    protected $connection = 'mysql_dev';
    protected $table = 'blogs';
    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'short_description',
        'content',
        'thumb_alt_text',
        'thumb_image',
        'feature_alt_text',
        'featured_image',
        'author_name',
        'is_featured',
        'active_status',
        'published_at',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'canonical_url',
        'view_count',
        'author_id',
        'author_name',
        'faq_schema',
        'service_schema',
        'breadcrumb_schema',
        'created_at',
        'updated_at',   
    ];

    public function images()
    {
        return $this->hasMany(BlogImages::class, 'blog_id', 'id');
    }
}
