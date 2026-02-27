<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersBankDetails extends Model
{
    use HasFactory;

    protected $table = 'users_bank_details';
    protected $fillable = [
        'users_id',
        'bank_account_name',
        'bank_name',
        'bank_ifsc',
        'bank_account_number',
        'bank_address',
        'upi_id',
        'active_status'
    ];
}
