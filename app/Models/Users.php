<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table = 'users';
    protected $fillable = [

        'unique_id',
        'name',
        'organization_type_id',
        'organization_id',
        'branch_id',
        'department_id',
        'reporting_to_user_id',
        'role_id',
        'primary_email',
        'primary_contact',
        'location',
        'active_status'
    ];

    public function info()
    {
        return $this->hasOne(UsersInfo::class, 'users_id', 'id');
    }

    public function address()
    {
        return $this->hasOne(UsersAddress::class, 'users_id', 'id');
    }

    public function bankdetails()
    {
        return $this->hasOne(UsersBankDetails::class, 'users_id', 'id');
    }
}
