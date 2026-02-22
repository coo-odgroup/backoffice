<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class ApiApps extends Model
{
    protected $table = 'api_apps';
    protected $fillable = [
        'app_name',
        'app_code',
        'active_status'
    ];
}
