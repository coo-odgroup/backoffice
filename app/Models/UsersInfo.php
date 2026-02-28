<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersInfo extends Model
{
    protected $table = 'users_info';
    protected $fillable = [
        'users_id',
        'secondary_email',
        'secondary_contact',
        'aadhaar_no',
        'pancard_no',
        'president_name',
        'president_phone',
        'general_secretary_name',
        'general_secretary_phone',
        'has_gst',
        'gst_no',
        'active_status'
    ];

    public function user()
    {
        return $this->belongsTo(Users::class, 'users_id', 'id');
    }
}
