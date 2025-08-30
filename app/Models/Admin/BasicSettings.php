<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BasicSettings extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'id'                            => 'integer',
        'site_name'                     => 'string',
        'site_title'                    => 'string',
        'base_color'                    => 'string',
        'secondary_color'               => 'string',
        'site_logo_dark'                => 'string',
        'site_logo'                     => 'string',
        'site_fav_dark'                 => 'string',
        'site_fav'                      => 'string',
        'vendor_logo_dark'              => 'string',
        'vendor_logo'                   => 'string',
        'vendor_fav_dark'               => 'string',
        'vendor_fav'                    => 'string',
        'preloader_image'               => 'string',
        'mail_activity'                 => 'string',
        'push_notification_activity'    => 'string',
        'broadcast_activity'            => 'string',
        'sms_activity'                  => 'string',
        'web_version'                   => 'string',
        'admin_version'                 => 'string',
        'otp_exp_seconds'               => 'integer',
        'user_registration'             => 'integer',
        'vendor_registration'           => 'integer',
        'secure_password'               => 'integer',
        'vendor_secure_password'        => 'integer',
        'agree_policy'                  => 'integer',
        'vendor_agree_policy'           => 'integer',
        'force_ssl'                     => 'integer',
        'vendor_force_ssl'              => 'integer',
        'email_verification'            => 'integer',
        'email_notification'            => 'integer',
        'push_notification'             => 'integer',
        'kyc_verification'              => 'integer',
        'vendor_email_verification'     => 'integer',
        'vendor_email_notification'     => 'integer',
        'vendor_push_notification'      => 'integer',
        'vendor_kyc_verification'       => 'integer',
        'admin_two_factor_verification' => 'integer',
        'user_two_factor_verification'  => 'integer',
        'mail_config'                   => 'object',
        'push_notification_config'      => 'object',
        'broadcast_config'              => 'object',
        'timezone'                      => 'string',
        'status'                        => 'integer',
        'country_code'                  => 'string',
        'google_api_key'                => 'string',
        'location'                      => 'string',
    ];


    public function mailConfig() {}
}
