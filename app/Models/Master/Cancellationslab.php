<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Cancellationslab extends Model
{
    protected $table = 'mst_cancellationslab';
    protected $fillable = [
        'slab_name',
        'description',
        'active_status'
    ];

    public function slabInfo()
    {
        return $this->hasMany(CancellationSlabInfo::class, 'slab_id', 'id');
    }
}
