<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
class TicketFareSlab extends Model
{
    protected $table = 'mst_ticket_fare_slab';

    protected $fillable = [
        'slab_name',
        'small_desc',
        'active_status'
    ];

    public function slabInfo()
    {
        return $this->hasMany(CancellationSlabInfo::class, 'slab_id', 'id');
    }
}
