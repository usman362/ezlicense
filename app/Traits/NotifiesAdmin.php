<?php

namespace App\Traits;

use App\Models\Booking;
use App\Models\SiteSetting;
use App\Models\User;
use App\Notifications\AdminBookingAlert;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Provides a simple method to notify platform admins about booking events.
 * Sends to all admin users + the configured admin_notification_email.
 */
trait NotifiesAdmin
{
    protected function notifyAdminAboutBooking(Booking $booking, string $event, ?string $extraInfo = null): void
    {
        try {
            // Get all admin users
            $admins = User::where('role', 'admin')->get();

            if ($admins->isEmpty()) {
                // Fallback: send to support email as an anonymous notifiable
                $adminEmail = SiteSetting::get('support_email', 'support@securelicences.com.au');
                Notification::route('mail', $adminEmail)
                    ->notify(new AdminBookingAlert($booking, $event, $extraInfo));
            } else {
                // Send to all admin users
                Notification::send($admins, new AdminBookingAlert($booking, $event, $extraInfo));
            }
        } catch (\Throwable $e) {
            Log::warning("Admin booking notification failed [{$event}]: " . $e->getMessage());
        }
    }
}
