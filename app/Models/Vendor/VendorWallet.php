<?php

namespace App\Models\Vendor;

use App\Constants\GlobalConst;
use App\Models\Admin\Currency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorWallet extends Model
{
    use HasFactory;
    protected $fillable = ['balance','due_payment','status','vendor_id','currency_id','created_at','updated_at'];

    protected $casts = [
        'id'                    => 'integer',
        'vendor_id'             => 'integer',
        'currency_id'           => 'integer',
        'balance'               => 'double',
        'due_payment'           => 'double',
        'profit_balance'        => 'decimal:8',
        'status'                => 'boolean',
    ];

    public function scopeAuth($query) {
        return $query->where('vendor_id',auth()->guard('vendor')->user()->id);
    }

    public function scopeAuthApi($query) {
        return $query->where('vendor_id',auth()->guard('vendor_api')->user()->id);
    }

    public function scopeActive($query) {
        return $query->where("status",true);
    }

    public function vendor() {
        return $this->belongsTo(Vendor::class);
    }

    public function currency() {
        return $this->belongsTo(Currency::class);
    }

    public function scopeSender($query) {
        return $query->whereHas('currency',function($q) {
            $q->where("sender",GlobalConst::ACTIVE);
        });
    }

}
