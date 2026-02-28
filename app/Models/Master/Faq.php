<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $table = 'faq';
    protected $fillable = [
        'faq_category_id',
        'title',
        'content',
        'icon',
        'active_status',
        'updated_by',
        'updated_by '
    ];
}
