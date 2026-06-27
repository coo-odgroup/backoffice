<?php

namespace App\Models\blogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogAuthor extends Model
{
    use HasFactory;
    protected $connection = 'mysql_dev';
    protected $table = 'blog_authors';
    protected $fillable = [
        'author_name',
        'author_slug',
        'about_author',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'person_schema',
        'breadcrumb_schema',
        'active_status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];
}
