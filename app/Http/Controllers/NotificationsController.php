<?php

namespace App\Http\Controllers;

use App\Models\Logs\EmailLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationsController extends Controller
{
    //send notification to user
    // either email or sms or both depending on the user's preference
    public function sendNotification(Request $request)
    {
        // validate request
        $request->validate([
            'user_id' => 'required',
            'message' => 'required',
        ]);
        // get user
        $user = User::find($request->user_id);
  // if request->email is true, send email
        if ($request->email) {
            // send email
            $user->notify(new SendEmailNotification($request->message));
        }else{
            // send sms
            // get user phone number
            $userPhone = $user->phone;
            // send sms
            $this->sendSms($userPhone, $request->message);
        }
        return response()->json([
            'message' => 'Notification sent successfully',
        ], 200);
    }

    public function logEmail($userid, $emailaddress, $emailsubject, $description)
    {
        if (!$userid) {
            $user = User::where('email', $emailaddress)->first();
            $userid = $user ? $user->id : null;
        }
        EmailLog::query()->create(
            [
                'user_id' => $userid,
                'email_address' => $emailaddress,
                'email_subject' => $emailsubject,
                'email_description' => $description,
                'sent_by' => Auth::user()->id?:null,
            ]
        );
    }

    private function sendSms($userPhone, $message)
    {

    }

}
