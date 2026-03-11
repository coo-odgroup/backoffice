<?php

namespace App\Models\Ad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingPlan extends Model
{
    use HasFactory;
    protected $connection = 'mysql_dev';
    protected $table = 'ad_pricing_plans';
    protected $fillable = [
        'plan_name',
        'placement_id',
        'model',
        'price',
        'duration_days',
        'active_status'
    ];
}
