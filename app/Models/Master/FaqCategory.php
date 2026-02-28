<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class FaqCategory extends Model
{
    protected $table = 'faq_category';
    protected $fillable = [
        'category_name',
        'sequence_no',
        'active_status'
    ];
}
