<?php

namespace App\Models\Admin\Cars;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaHasType extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'id'                     => 'integer',
        'car_area_id'            => 'integer',
        'car_type_id'            => 'integer',
    ];

    public function area() {
        return $this->belongsTo(CarArea::class,'car_area_id');
    }

    public function type() {
        return $this->belongsTo(CarType::class,'car_type_id');
    }
}
