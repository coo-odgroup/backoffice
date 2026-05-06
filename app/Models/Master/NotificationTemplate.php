<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    protected $table = 'mst_notification_templates';


    protected $fillable = [
        'name',
        'slug',
        'type',
        'category',
        'event_trigger',
        'subject',
        'title',
        'body',
        'short_text',
        'variables_json',
        'active_status',
        'created_by',
        'updated_by'
    ];

}
