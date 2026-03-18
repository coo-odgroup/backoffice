<?php

namespace App\Models\Campaign;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignMaster extends Model
{
    use HasFactory;

    protected $table = 'campaign_master';
    protected $fillable = [
        'campaign_name',
        'short_desc',
        'full_desc',
        'start',
        'stop',
        'active_status',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
