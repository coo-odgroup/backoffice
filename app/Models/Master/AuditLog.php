<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
      protected $connection = 'mysql_log';
    protected $table = 'audit_logs_master';

    protected $fillable = [
        'table_name',
        'record_id',
        'action',
        'old_data',
        'new_data',
        'created_by',
        'created_at'
    ];

}
