<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Models\Logs\EmailLog;
use App\Models\Logs\NotificationLog;
use App\Models\User;
use App\Notifications\SendSmsNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class NotificationsController extends Controller
{
    //send notification to user
    // either email or sms or both depending on the user's preference
    public function sendNotification(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'title' => 'required',
            'message' => 'required',
            'mode' => 'required',
            'sent_to' => 'required',
        ]);

        if ($validator->fails()) {
            return response()
                ->json([
                    'success'   =>false,
                    'message'   => $validator->errors()->first()
                ],400);
        }
        $users = User::whereIn('id', $request->sent_to)->get();
        foreach ($users as $user) {
            // "mode":["sms","email"]
            if (in_array('email', $request->mode)) {
                // send email
                Notification::route('mail', $user->email)->notify(new \App\Notifications\SendEmailNotification($request->message, $request->title, $user));
            }
            if (in_array('sms', $request->mode)) {
                // send sms
                Notification::send($user, new SendSmsNotification($request->message,$request->title, $user));
            }
        }

        return response()->json([
            'message' => 'Notification sent successfully',
        ], 200);
    }




}
