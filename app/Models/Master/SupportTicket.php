<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    protected $table = 'support_tickets';
    protected $fillable = [
        'ticket_code',
        'title',
        'description',
        'module',
        'project_id',
        'severity',
        'category',
        'priority',
        'status',
        'assigned_to',
        'reported_by',
        'assigned_by',
        'due_date',
        'estimated_hours',
        'actual_hours',
        'environment',
        'browser',
        'device',
        'app_version',
        'file_title',
        'file_path',
        'file_type',
        'active_status',
        'created_by',
        'updated_by'
    ];
}
