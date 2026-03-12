<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewCategory extends Model
{
    protected $table="mst_review_categories";
    use HasFactory;

    protected $fillable = [
        "name",
        "sequence_no",
        "description",
        "active_status"
    ];
}
