<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorAuthorization extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'id'                => 'integer',
        'user_id'           => 'integer',
        'code'              => 'integer',
        'token'             => 'string',
    ];
    public function user() {
        return $this->belongsTo(Vendor::class);
    }
}
