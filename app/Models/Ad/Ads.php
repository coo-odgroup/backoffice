<?php

namespace App\Models\Ad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ads extends Model
{
    use HasFactory;
    protected $connection = 'mysql_dev';
    protected $table = 'ads';
    protected $fillable = [
        'campaign_id',
        'alt_text',
        'image',
        'redirect_url',
        'impressions',
        'clicks',
        'active_status'
    ];
}
