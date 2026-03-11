<?php

namespace App\Models\Ad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdCampaign extends Model
{
    use HasFactory;
    protected $connection = 'mysql_dev';
    protected $table = 'ad_campaigns';
    protected $fillable = [
        'vendor_id',
        'placement_id',
        'pricing_plan_id',
        'title',
        'start_date',
        'end_date',
        'total_budget',
        'active_status'
    ];
}
