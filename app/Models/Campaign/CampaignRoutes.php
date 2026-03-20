<?php

namespace App\Models\Campaign;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignRoutes extends Model
{
    use HasFactory;

    protected $table = 'campaign_routes';
    protected $fillable = [
        'campaign_id',
        'src_id',
        'dest_id',
        'active_status'
    ];
}
