<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table = 'users';
    protected $fillable = [
        'user_role',
        'unique_id',
        'name',
        'organization_name',
        'primary_email',
        'primary_contact',
        'location',
        'active_status'
    ];
}
