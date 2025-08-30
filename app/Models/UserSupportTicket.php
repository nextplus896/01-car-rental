<?php

namespace App\Models;

use App\Constants\SupportTicketConst;
use App\Models\Admin\Admin;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSupportTicket extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $with = [
        'user',
        'attachments'
    ];

    protected $appends = ['type','stringStatus'];

    public function scopeAuthTickets($query) {
        $query->where("user_id",auth()->user()->id);
    }

    public function scopeVendorAuthTickets($query) {
        $query->where("vendor_id",auth()->guard('vendor')->user()->id);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function vendor() {
        return $this->belongsTo(Vendor::class);
    }

    public function attachments() {
        return $this->hasMany(UserSupportTicketAttachment::class);
    }

    public function getTypeAttribute() {
        if($this->user_id != null) {
            return "USER";
        }else if($this->vendor_id != null) {
            return "VENDOR";
        }
    }

    public function conversations() {
        return $this->hasMany(UserSupportChat::class,"user_support_ticket_id");
    }

    public function scopePending($query) {
        return $query->where("status",SupportTicketConst::PENDING)->orWhere("status",SupportTicketConst::DEFAULT);
    }

    public function scopeActive($query) {
        return $query->where("status",SupportTicketConst::ACTIVE);
    }

    public function scopeSolved($query) {
        return $query->where("status",SupportTicketConst::SOLVED);
    }

    public function scopeNotSolved($query,$token) {
        $query->where('token',$token)->where('status','!=',SupportTicketConst::SOLVED);
    }

    public function getStringStatusAttribute() {
        $status = $this->status;
        $data = [
            'class' => "",
            'value' => "",
        ];
        if($status == SupportTicketConst::ACTIVE) {
            $data = [
                'class'     => "badge badge--info",
                'value'     => "Active",
            ];
        }else if($status == SupportTicketConst::DEFAULT) {
            $data = [
                'class'     => "badge badge--warning",
                'value'     => "Pending",
            ];
        }else if($status == SupportTicketConst::PENDING) {
            $data = [
                'class'     => "badge badge--warning",
                'value'     => "Pending",
            ];
        }else if($status == SupportTicketConst::SOLVED) {
            $data = [
                'class'     => "badge badge--success",
                'value'     => "Solved",
            ];
        }

        return (object) $data;
    }
}
