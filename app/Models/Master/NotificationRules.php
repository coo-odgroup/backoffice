<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class NotificationRules extends Model
{
    protected $table = 'mst_notification_templates';


    protected $fillable = [
        'cron_job_id',
        'channel',
        'reciptent_type',
        'recipient_value',
        'template_id',
        'role_type',
        'status_condition',
        'active_status',
        'created_by',
        'updated_by'
    ];

}
