<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorKycData extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'data'      => 'object',
    ];
}
