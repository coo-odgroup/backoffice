<?php

namespace App\Models\blogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogImages extends Model
{
    use HasFactory;
    protected $connection = 'mysql_dev';
    protected $table = 'blog_images';
    protected $fillable = ['blog_id','image_name','image_path','alt_text','sort_order'];

    public function blog()
    {
        return $this->belongsTo(Blog::class, 'blog_id', 'id');
    }
}
