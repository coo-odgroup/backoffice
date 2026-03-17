<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CancellationslabInfo extends Model
{
    use HasFactory;

    protected $table = 'mst_cancellationslab_info';
    protected $fillable = [
        'slab_id',
        'duration',
        'deduction',
        'active_status'
    ];

    public function slab()
    {
        return $this->belongsTo(CancellationSlab::class, 'slab_id', 'id');
    }
}
