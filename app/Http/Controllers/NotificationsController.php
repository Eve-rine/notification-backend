<?php

namespace App\Http\Controllers;

use App\Models\Logs\EmailLog;
use App\Models\User;
use App\Notifications\SendSmsNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class NotificationsController extends Controller
{
    //send notification to user
    // either email or sms or both depending on the user's preference
    public function sendNotification(Request $request)
    {

        $validator=Validator::make($request->all(),[
            'user_id' => 'required',
            'message' => 'required',
        ]);
        if ($validator->fails()) {
            return response()
                ->json([
                    'success'   =>false,
                    'message'   => $validator->errors()->first()
                ],400);
        }

        $user = User::find($request->user_id);
        if ($request->email) {
            // send email
            Notification::route('mail', $user->email)->notify(new \App\Notifications\SendEmailNotification($user, $request->message));
        }else{
            // send sms
             $users = User::all();   // get all users
             foreach ($users as $user) {
               Notification::send($user, new SendSmsNotification($request->message, $user));
             }
        }
        return response()->json([
            'message' => 'Notification sent successfully',
        ], 200);
    }




}
