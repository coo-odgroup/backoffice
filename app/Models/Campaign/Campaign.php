<?php

namespace App\Models\Campaign;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $table = 'campaign';
    protected $fillable = [
        'operator_id',
        'campaign_master_id',
        'offer_type',
        'offer_value',
        'min_ticket_value',
        'services',
        'auto_renewal',
        'validity_type',
        'start_date',
        'end_date',
        'duration_value',
        'duration_unit',
        'active_status',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
