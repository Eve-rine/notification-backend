<?php

namespace App\Notifications;

use App\Models\SmsLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\AfricasTalking\AfricasTalkingChannel;
use NotificationChannels\AfricasTalking\AfricasTalkingMessage;

class SendSmsNotification extends Notification
{
    use Queueable;

    protected $message;
    protected $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($message, $user)
    {
        // message
        $this->message = $message;
        $this->user = $user; // Store the user
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
        SmsLog::create([
            'user_id' => $this->user, // Include the user_id
            'to' => $this->user->phone_number,
            'message' => $this->message,
        ]);

        return (new AfricasTalkingMessage())
            ->content($this->message)
         ->to($this->user->phone_number);

    }


}
