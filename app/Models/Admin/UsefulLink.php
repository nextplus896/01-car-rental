<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsefulLink extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'id'       => 'integer',
        'type'     => 'string',
        'slug'     => 'string',
        'url'      => 'string',
        'title'    => 'object',
        'content'  => 'object',
        'status'    => 'integer',
        'editable'  => 'integer',
    ];
}
