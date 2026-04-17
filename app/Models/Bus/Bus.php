<?php

namespace App\Models\Bus;

use App\Models\Master\AxleType;
use App\Models\Master\Brand;
use App\Models\Master\BusModel;
use App\Models\Master\BusService;
use App\Models\Master\Cancellationslab;
use App\Models\Master\MstSeatLayout;
use App\Models\Master\SeatType;
use App\Models\User;
use App\Models\Users;
use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    protected $table = 'odbusdev.bus';
    protected $fillable = [
        'bus_operator_id',
        'name',
        'via',
        'bus_number',
        'brand_id',
        'model_id',
        'axle_type_id',
        'service_id',
        'ac_type_id',
        'seat_type_id',
        'seat_layout_type_id',
        'gen_bus_type',
        'cancellationslabs_id',
        'running_cycle',
        'popularity',
        'type',
        'sequence',
        'max_seat_book',
        'lower_sleeper_extra_fare',
        'min_price',
        'min_price_updated_on',
        'is_irctc_model',
        'active_status'
    ];

    public function stops()
    {
        return $this->hasMany(BusRoutesStops::class, 'bus_id');
    }

    public function operator()
    {
        return $this->belongsTo(Users::class, 'bus_operator_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function model()
    {
        return $this->belongsTo(BusModel::class, 'model_id');
    }

    public function axleType()
    {
        return $this->belongsTo(AxleType::class, 'axle_type_id');
    }

    public function service()
    {
        return $this->belongsTo(BusService::class, 'service_id');
    }

    public function seatType()
    {
        return $this->belongsTo(SeatType::class, 'seat_type_id');
    }

    public function seatLayout()
    {
        return $this->belongsTo(MstSeatLayout::class, 'seat_layout_type_id');
    }

    public function cancellationslab()
    {
        return $this->belongsTo(Cancellationslab::class, 'cancellationslabs_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function routemap()
    {
        return $this->belongsTo(BusRoutesMap::class, 'bus_id');
    }
}
