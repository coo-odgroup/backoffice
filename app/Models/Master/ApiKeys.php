<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class ApiKeys extends Model
{
    protected $table = 'api_keys';
    protected $fillable = [
        'api_app_id',
        'api_key',
        'last_used_at',
        'expires_at',
        'active_status'
    ];
}
