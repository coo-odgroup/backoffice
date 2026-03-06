<?php

namespace App\Models\Ad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;
    protected $connection = 'mysql_dev';
    protected $table = 'vendors';
    protected $fillable = [
        'company_name',
        'contact_person',
        'email',
        'phone',
        'gst_number',
        'active_status'
    ];
}
