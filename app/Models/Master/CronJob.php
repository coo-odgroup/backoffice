<?php
namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class CronJob extends Model
{
    protected $table = 'mst_cron_jobs';
    protected $fillable = [
        'id',
        'name',
        'slug',
        'type',
        'schedule_type',
        'interval_minutes',
        'run_times_json',
        'cron_expression',
        'execution_type',
        'job_class',
        'command_name',
        'last_run_at',
        'next_run_at',
        'active_status',
        'notify_email',
        'notify_push',
        'notify_sms',
    ];
}
