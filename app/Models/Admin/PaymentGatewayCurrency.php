<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGatewayCurrency extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $callable_data;

    protected $appends = [];

    protected $casts = [
        'id'                    => 'integer',
        'payment_gateway_id'    => 'integer',
        'name'                  => 'string',
        'alias'                 => 'string',
        'currency_code'         => 'string',
        'currency_symbol'       => 'string',
        'min_limit'             => 'double',
        'max_limit'             => 'double',
        'percent_charge'        => 'double',
        'fixed_charge'          => 'double',
        'rate'                  => 'double',
        'image'                 => 'string'
    ];

    /**
     * Get a subset of the model's attributes.
     *
     * @param  array|mixed  $attributes
     * @return array
     */
    public function getOnly($attributes)
    {
        $this->callable_data = $this->only($attributes);
        return $this;
    }

    public function makeJson() {
        return json_encode($this->callable_data);
    }

    public function gateway() {
        return $this->belongsTo(PaymentGateway::class,"payment_gateway_id");
    }

    public function getCryptoAttribute() {
        if($this->gateway->crypto == true) return true;
        return false;
    }

    public function getPaymentGatewayAliasAttribute()
    {
        return $this->gateway->alias;
    }
}
