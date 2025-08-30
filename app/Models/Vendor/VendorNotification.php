<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class VendorNotification extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'id'            => 'integer',
        'vendor_id'     => 'integer',
        'type'          => 'string',
        'message'       => 'object',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    public function scopeAuth(){
        $this->where('vendor_id', Auth::id());
    }

}
