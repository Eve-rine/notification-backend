<?php

namespace App\Notifications;

use App\Models\Logs\EmailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendEmailNotification extends Notification
{
    use Queueable;
    public $user;
    public $message;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user,$message)
    {
        //
        $this->user=$user;
        $this->message=$message;
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
        EmailLog::query()->create(
            [
                'user_id' => $this->user->id?:null,
                'email_address' => $this->user->email?:null,
                'email_subject' => "Hello",
                'email_description' => $this->message,
                'sent_by' => null,
            ]
        );
        return (new MailMessage)
            ->subject("Hello")
            ->line($this->message);
        // log the email
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