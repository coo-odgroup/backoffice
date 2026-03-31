<?php

namespace App\Models\Bus;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusContacts extends Model
{
    use HasFactory;

    protected $table = 'odbusdev.bus_contacts';
    protected $fillable = [
        'bus_id',
        'type',
        'phone',
        'booking_sms_send',
        'cancel_sms_send',
        'booking_wp_send',
        'cancel_wp_send'
    ];
}
