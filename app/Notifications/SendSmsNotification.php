<?php

namespace App\Notifications;

use App\Models\Logs\NotificationLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\AfricasTalking\AfricasTalkingChannel;
use NotificationChannels\AfricasTalking\AfricasTalkingMessage;

class SendSmsNotification extends Notification
{
    use Queueable;

    protected $message;
    protected $user;
    protected $title;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($message,$title, $user)
    {
        // message
        $this->message = $message;
        $this->user = $user;
        $this->title = $title;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [AfricasTalkingChannel::class];
    }

    public function toAfricasTalking($notifiable)
    {
        // Log the SMS in the database with user_id
        NotificationLog::create([
            'user_id' => $this->user->id, // Include the user_id
            'to' => $this->user->phone_number,
            'title' => $this->title,
            'message' => $this->message,
            'mode' => 'sms',
            'sent_by' => null,
        ]);

        return (new AfricasTalkingMessage())
            ->content($this->message)
         ->to($this->user->phone_number);

    }


}
