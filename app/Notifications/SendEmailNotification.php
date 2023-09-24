<?php

namespace App\Notifications;

use App\Models\Logs\NotificationLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class SendEmailNotification extends Notification
{
    use Queueable;
    public $user;
    public $message;
    public $title;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($message,$title,$user)
    {
        //
        $this->user=$user;
        $this->message=$message;
        $this->title=$title;
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
        NotificationLog::query()->create(
            [
                'user_id' => $this->user->id?:null,
                'to' => $this->user->email?:null,
                'title' => $this->title?:null,
                'message' => $this->message,
                'mode' => 'email',
                'sent_by' => Auth::user()->id?:null
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
