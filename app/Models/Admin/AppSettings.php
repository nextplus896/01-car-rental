<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSettings extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'version' => 'string',
        'splash_screen_image' => 'string',
        'url_title' => 'string',
        'android_url' => 'string',
        'iso_url' => 'string',
        'vendor_version' => 'string',
        'vendor_splash_screen_image' => 'string',
        'vendor_url_title' => 'string',
        'vendor_android_url' => 'string',
        'vendor_iso_url' => 'string',
    ];
}
