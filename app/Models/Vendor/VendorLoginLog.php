<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorLoginLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'ip',
        'mac',
        'city',
        'country',
        'longitude',
        'latitude',
        'browser',
        'os',
        'timezone',
        'first_name','created_at'
    ];
}
