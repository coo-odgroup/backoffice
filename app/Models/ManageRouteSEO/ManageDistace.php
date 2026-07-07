<?php
namespace App\Models\ManageRouteSEO;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManageDistace extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'mst_routes_details';
    protected $fillable = [
        'id',
        'source_id',
        'destination_id',
        'is_main_route',
        'destination',
        'distance',
        'active_status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];
}
