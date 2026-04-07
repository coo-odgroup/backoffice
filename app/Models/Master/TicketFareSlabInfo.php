<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketFareSlabInfo extends Model
{
    use HasFactory;

    protected $table = 'mst_ticket_fare_slab_info';
    protected $fillable = [
        'slab_id',
        'bus_operator_id',
        'starting_fare',
        'upto_fare',
        'commision',
        'from_date',
        'to_date',
        'active_status'
    ];
}