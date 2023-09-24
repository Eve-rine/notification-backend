<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Models\Logs\NotificationLog;
use Illuminate\Http\Request;

class NotificationsLogController extends Controller
{


    public function getNotificationsStats(Request $request)
    {
        $counts = NotificationLog::selectRaw('
                SUM(CASE WHEN mode = "sms" THEN 1 ELSE 0 END) AS sms,
                SUM(CASE WHEN mode = "email" THEN 1 ELSE 0 END) AS email,
                COUNT(*) AS total
            ')->first();

        return response()->json($counts, 200);
    }

    public function getNotifications(Request $request)
    {
        $notifications = NotificationLog::query()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json([
            'data' => $notifications,
            'success' => true,
        ], 200);
    }
}
