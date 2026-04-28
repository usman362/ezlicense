<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use Illuminate\Http\Request;

class EmailLogsController extends Controller
{
    public function index(Request $request)
    {
        $query = EmailLog::with('user:id,name,role')->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('to_address', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('to_name', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($notif = $request->input('notification')) {
            $query->where('notification_class', 'like', "%{$notif}%");
        }

        if ($days = (int) $request->input('days')) {
            $query->where('created_at', '>=', now()->subDays($days));
        }

        $logs = $query->paginate(40)->withQueryString();

        $stats = [
            'total' => EmailLog::count(),
            'today' => EmailLog::whereDate('created_at', today())->count(),
            'last_7_days' => EmailLog::where('created_at', '>=', now()->subDays(7))->count(),
            'failed' => EmailLog::failed()->count(),
        ];

        // Notification class options for filter
        $notificationTypes = EmailLog::query()
            ->whereNotNull('notification_class')
            ->distinct()
            ->pluck('notification_class')
            ->map(fn ($c) => class_basename($c))
            ->unique()
            ->sort()
            ->values();

        return view('admin.email-logs.index', [
            'logs' => $logs,
            'stats' => $stats,
            'notificationTypes' => $notificationTypes,
        ]);
    }
}
