<?php

namespace App\Traits;

use App\Models\SiteSetting;

/**
 * Adds SMS channel to notifications when SMS is enabled and user has a phone number.
 *
 * Usage in notification via() method:
 *   return array_merge(['database', 'mail'], $this->smsChannel($notifiable));
 */
trait SendsSms
{
    /**
     * Returns ['vonage'] if SMS is enabled and the user has a phone number, else [].
     */
    protected function smsChannel(object $notifiable): array
    {
        // Check if SMS is enabled in site settings
        if (!SiteSetting::get('sms_enabled', false)) {
            return [];
        }

        // Check if the user has a phone number
        $phone = $notifiable->phone ?? null;
        if (empty($phone)) {
            return [];
        }

        return ['vonage'];
    }
}
