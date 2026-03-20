<?php

namespace App\Models\Campaign;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignServices extends Model
{
    use HasFactory;

    protected $table = 'campaign_services';
    protected $fillable = [
        'campaign_id',
        'campaign_routes_id',
        'bus_id',
        'active_status'
    ];
}
