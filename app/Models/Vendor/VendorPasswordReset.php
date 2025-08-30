<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorPasswordReset extends Model
{
    use HasFactory;
    protected $guarded = [
        'id',
    ];
    public function user() {
        return $this->belongsTo(Vendor::class)->select('id','username','email','firstname','lastname');
    }
}
