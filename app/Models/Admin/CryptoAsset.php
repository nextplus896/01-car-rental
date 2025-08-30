<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CryptoAsset extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'id'                    => 'integer',
        'payment_gateway_id'    => 'integer',
        'type'                  => 'string',
        'chain'                 => 'string',
        'coin'                  => 'string',
        'assets'                => 'string',
        'credentials'           => 'object',
    ];


    public function gateway() {
        return $this->belongsTo(PaymentGateway::class,'payment_gateway_id');
    }
}
