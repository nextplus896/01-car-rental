<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionSetting extends Model
{
    use HasFactory;


    protected $guarded = ['id','slug'];


    protected $with = ['admin'];

    protected $casts = [
        'id'        => 'integer',
        'admin_id'  => 'integer',
        'slug'      => 'string',
        'title'     => 'string',
        'status'    => 'integer',
        'fixed_charge' => 'double',
        'percent_charge' => 'double',
        'min_limit' => 'double',
        'max_limit' => 'double',
    ];


    public function admin() {
        return $this->belongsTo(Admin::class);
    }
}
