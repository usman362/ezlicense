<?php

namespace App\Listeners;

use App\Models\EmailLog;
use App\Models\User;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Log;

/**
 * Listens to Laravel Mail events and persists every email send to the
 * `email_logs` table for audit, debugging, and admin visibility.
 */
class LogSentEmail
{
    public function handleSent(MessageSent $event): void
    {
        try {
            $message = $event->message;
            $to = collect($message->getTo())->first();
            if (! $to) return;

            $headers = $message->getHeaders();
            $notificationClass = $headers->has('X-Notification-Type')
                ? $headers->get('X-Notification-Type')->getBodyAsString()
                : null;

            $user = User::where('email', $to->getAddress())->first();

            EmailLog::create([
                'to_address' => $to->getAddress(),
                'to_name' => $to->getName() ?: null,
                'subject' => $message->getSubject() ?: '(no subject)',
                'notification_class' => $notificationClass,
                'user_id' => $user?->id,
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Never let logging failures break email sending
            Log::warning('EmailLog persistence failed: ' . $e->getMessage());
        }
    }
}
