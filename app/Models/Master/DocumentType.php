<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $table = 'mst_document_types';
    protected $fillable = [
        'document_code',
        'document_name',
        'is_mandatory',
        'has_expiry',
        'active_status'
    ];
}
