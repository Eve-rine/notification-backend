<?php

namespace App\Console\Commands;

use App\Models\Logs\NotificationLog;
use App\Models\User;
use App\Notifications\SendSmsNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAdminsNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:send-admin-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command Sends Notifications To All Admins In The System at Midnight Daily On The Number Of Notifications Sent To Users Via SMS And Email';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $notifications = NotificationLog::whereDate('created_at', Carbon::today())->get();
        $sms = $notifications->where('mode', 'sms')->count();
        $email = $notifications->where('mode', 'email')->count();
        $total = $notifications->count();
        $admins = User::where('role', 'admin')->get();
        // if total is 0, do not send the notification
        if ($total == 0) {
            return;
        }
        foreach ($admins as $admin) {
            $title = "Notifications Sent Today";
            $message = "Total Notifications Sent Today: $total, SMS: $sms, Email: $email";
            $admin->notify(new SendSmsNotification($message, $title, $admin));
        }

        return $this->info('Notification Sent Successfully');
    }
}
