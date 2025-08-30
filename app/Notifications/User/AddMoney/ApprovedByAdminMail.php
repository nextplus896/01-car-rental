<?php

namespace App\Notifications\User\AddMoney;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class ApprovedByAdminMail extends Notification
{
    use Queueable;

    public $user;
    public $data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user,$data)
    {
        $this->user = $user;
        $this->data = $data;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $user = $this->user;
        $data = $this->data;
        $date = Carbon::now();
        $dateTime = $date->format('Y-m-d h:i:s A');
        return (new MailMessage)
                    ->greeting(__("Hello")." ".$user->fullname." !")
                    ->subject(__("Payment Via")." ".@$data->gateway_currency->code)
                    ->line(__("Admin Approved Your Payment Request via")." ".@$data->gateway_currency->name." ,".__("details of payment").":")
                    ->line(__("request Amount").": " . getAmount($data->request_amount,2).' '. get_default_currency_code())
                    ->line(__("Exchange Rate").": " ." 1 ". get_default_currency_code().' = '. getAmount(@$data->currency->rate,2).' '.@$data->currency->currency_code)
                    ->line(__("Fees & Charges").": " .getAmount( @$data->charge->total_charge,2).' '. @$data->currency->currency_code)
                    ->line(__("Will Get").": " . getAmount(@$data->request_amount,2).' '. get_default_currency_code())
                    ->line(__("Total Payable Amount").": " . getAmount(@$data->payable,2).' '. @$data->currency->currency_code)
                    ->line(__("web_trx_id").": " .@$data->trx_id)
                    ->line(__("Status").": ".__("Success"))
                    ->line(__("Date And Time").": " .$dateTime)
                    ->line(__('Thank you for using our application!'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
