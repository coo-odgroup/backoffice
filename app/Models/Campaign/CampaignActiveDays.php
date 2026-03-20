<?php

namespace App\Models\Campaign;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignActiveDays extends Model
{
    use HasFactory;

    protected $table = 'campaign_active_days';
    protected $fillable = [
        'campaign_id',
        'day_of_week'
    ];
}
