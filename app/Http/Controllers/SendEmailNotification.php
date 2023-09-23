<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailNotification
{

    /**
     * @param mixed $message
     */
    public function __construct($message)
    {
        $this->message = $message;

    }

    public function sendEmail($template, $data, $to, $toName, $from, $fromName, $subject, $attachment = null, $resource = null, $resourceId = null, $bcc = null, $bccName = null, $userId = null)
    {
        try {

            //check if data is object and convert to array
            if(is_object($data)){
                $data = (array) $data;
            }

            Mail::send(['html' => $template], $data, function ($message) use ($to, $toName, $from, $fromName, $subject, $attachment, $bcc, $bccName) {

                empty($toName) ? $toName = '' : $toName;
                empty($from) ? $from = 'evrineminayo@gmail.com' : $from;
                empty($fromName) ? $fromName = 'Notifications App' : $fromName;

                $replyToEmail = config('settings.reply_to_email_address') ?: 'evrineminayo@gmail.com';
                $replyToName = config('settings.reply_to_email_name') ?: 'Notifications App';

                $message->to($to, $toName)
                    ->subject($subject)
                    ->from($from, $fromName)
                    ->replyTo($replyToEmail, $replyToName);

                if(!empty($bcc) && !empty($bccName)){
                    $message->bcc($bcc, $bccName);
                }

                if(!empty($attachment) && is_array($attachment)){

                    foreach ($attachment as $attach){
                        $message->attach($this->link.$attach);
                    }
                }
                elseif (!empty($attachment) && is_object($attachment)) {

                    $attachment = (array) $attachment;

                    foreach ($attachment as $attach) {
                        $message->attach($this->link . $attach);
                    }
                }
                else{
                    !empty($attachment) ? $message->attach($this->link.$attachment) : '';
                }
            });

            (new NotificationsController())->logEmail($userId, $to, $subject, $subject);

        }
        catch (Swift_TransportException $transportExp) {
            Log::error($transportExp->getMessage());
        }

    }
}
