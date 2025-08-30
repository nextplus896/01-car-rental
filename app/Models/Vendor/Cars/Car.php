<?php

namespace App\Models\Vendor\Cars;

use App\Models\Admin\Cars\CarArea;
use App\Models\Admin\Cars\CarType;
use App\Models\CarBooking;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'id'          => 'integer',
        'vendor_id'   => 'integer',
        'car_area_id' => 'integer',
        'car_type_id' => 'integer',
        'car_title'   => 'object',
        'slug'        => 'string',
        'image'       => 'string',
        'car_model'   => 'string',
        'car_number'  => 'string',
        'seat'        => 'integer',
        'experience'  => 'integer',
        'fees'        => 'decimal:8',
        'status'      => 'integer',
        'approval'    => 'integer',
    ];

    public function type(){
        return $this->belongsTo(CarType::class,'car_type_id');
    }
    public function area(){
        return $this->belongsTo(CarArea::class,'car_area_id');
    }
    public function branch(){
        return $this->belongsTo(CarArea::class,'car_area_id');
    }
    public function bookings(){
        return $this->hasMany(CarBooking::class,'car_id','id');
    }
    public function vendor(){
        return $this->belongsTo(Vendor::class,'vendor_id','id');
    }
}
