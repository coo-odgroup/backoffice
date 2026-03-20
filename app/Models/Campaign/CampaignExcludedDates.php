<?php

namespace App\Models\Campaign;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignExcludedDates extends Model
{
    use HasFactory;

    protected $table = 'campaign_excluded_dates';
    protected $fillable = [
        'campaign_id',
        'excluded_date'
    ];
}
