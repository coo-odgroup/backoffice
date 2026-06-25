<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Schema extends Model
{
    protected $table = 'mst_schema';


    protected $fillable = [
        'schema_page_id',
        'schema_type_id',
        'schema_content',
        'updated_at',
        'short_text',
        'created_at',
        'active_status',
        'created_by',
        'updated_by'
    ];

}
