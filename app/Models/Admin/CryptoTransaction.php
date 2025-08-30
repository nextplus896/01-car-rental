<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CryptoTransaction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'id'            => 'integer',
        'internal_trx_ref_id' => 'integer',
        'internal_trx_type'   => 'string',
        'transaction_type'         => 'string',
        'sender_address'          => 'string',
        'receiver_address'        => 'string',
        'amount'        => 'string',
        'asset'        => 'string',
        'block_number' => 'string',
        'txn_hash'        => 'string',
        'chain'        => 'string',
        'callback_response'  => 'string',
        'status'   => 'string',
    ];
}
