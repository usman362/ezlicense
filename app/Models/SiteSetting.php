<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $table = 'site_settings';

    protected $fillable = ['group', 'key', 'value', 'type', 'label', 'hint'];

    /*
    |--------------------------------------------------------------------------
    | Static helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Get a setting value by key with optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = Cache::remember("site_setting.{$key}", 3600, function () use ($key) {
            return static::where('key', $key)->first();
        });

        if (! $setting) {
            return $default;
        }

        return match ($setting->type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'number'  => is_numeric($setting->value) ? (float) $setting->value : $default,
            'json'    => json_decode($setting->value, true) ?? $default,
            'secret'  => $setting->value, // stored encrypted at rest via DB, shown masked in UI
            default   => $setting->value ?? $default,
        };
    }

    /**
     * Set (upsert) a setting value.
     */
    public static function set(string $key, mixed $value, ?string $group = null, ?string $type = null, ?string $label = null, ?string $hint = null): static
    {
        $attributes = ['value' => is_array($value) ? json_encode($value) : (string) $value];

        if ($group !== null) {
            $attributes['group'] = $group;
        }
        if ($type !== null) {
            $attributes['type'] = $type;
        }
        if ($label !== null) {
            $attributes['label'] = $label;
        }
        if ($hint !== null) {
            $attributes['hint'] = $hint;
        }

        $setting = static::updateOrCreate(['key' => $key], $attributes);

        Cache::forget("site_setting.{$key}");
        Cache::forget('site_settings.all');

        return $setting;
    }

    /**
     * Get all settings grouped.
     */
    public static function allGrouped(): array
    {
        return Cache::remember('site_settings.all', 3600, function () {
            return static::orderBy('group')->orderBy('id')->get()->groupBy('group')->toArray();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Default settings seed
    |--------------------------------------------------------------------------
    */

    public static function seedDefaults(): void
    {
        $defaults = [
            // General
            ['group' => 'general', 'key' => 'site_name', 'value' => 'Secure Licences', 'type' => 'text', 'label' => 'Site Name', 'hint' => 'The name displayed across the platform'],
            ['group' => 'general', 'key' => 'site_tagline', 'value' => 'Find a driving instructor near you', 'type' => 'text', 'label' => 'Tagline', 'hint' => 'Short tagline shown on homepage'],
            ['group' => 'general', 'key' => 'support_email', 'value' => 'support@securelicences.com.au', 'type' => 'text', 'label' => 'Support Email', 'hint' => 'Displayed in footer and emails'],
            ['group' => 'general', 'key' => 'support_phone', 'value' => '1300 399 542', 'type' => 'text', 'label' => 'Support Phone', 'hint' => 'Customer support phone number'],
            ['group' => 'general', 'key' => 'timezone', 'value' => 'Australia/Sydney', 'type' => 'text', 'label' => 'Default Timezone', 'hint' => 'e.g. Australia/Sydney, Australia/Melbourne'],
            ['group' => 'general', 'key' => 'currency', 'value' => 'AUD', 'type' => 'text', 'label' => 'Currency', 'hint' => 'ISO currency code (AUD, USD, etc.)'],
            ['group' => 'general', 'key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean', 'label' => 'Maintenance Mode', 'hint' => 'Show maintenance page to visitors'],
            ['group' => 'general', 'key' => 'google_place_id', 'value' => '', 'type' => 'text', 'label' => 'Google Place ID', 'hint' => 'Google Business Place ID for review redirect (find it at https://developers.google.com/maps/documentation/places/web-service/place-id)'],
            ['group' => 'general', 'key' => 'facebook_url', 'value' => 'https://www.facebook.com/SecureLicences', 'type' => 'text', 'label' => 'Facebook URL', 'hint' => 'Facebook page URL for emails and footer'],
            ['group' => 'general', 'key' => 'instagram_url', 'value' => 'https://www.instagram.com/securelicences', 'type' => 'text', 'label' => 'Instagram URL', 'hint' => 'Instagram profile URL for emails and footer'],
            ['group' => 'general', 'key' => 'admin_notification_email', 'value' => 'admin@securelicences.com.au', 'type' => 'text', 'label' => 'Admin Notification Email', 'hint' => 'Email address that receives booking alerts (new, cancel, complete)'],

            // Payment
            ['group' => 'payment', 'key' => 'stripe_enabled', 'value' => '0', 'type' => 'boolean', 'label' => 'Enable Stripe', 'hint' => 'Toggle Stripe payment gateway'],
            ['group' => 'payment', 'key' => 'stripe_publishable_key', 'value' => '', 'type' => 'secret', 'label' => 'Stripe Publishable Key', 'hint' => 'pk_test_... or pk_live_...'],
            ['group' => 'payment', 'key' => 'stripe_secret_key', 'value' => '', 'type' => 'secret', 'label' => 'Stripe Secret Key', 'hint' => 'sk_test_... or sk_live_...'],
            ['group' => 'payment', 'key' => 'stripe_webhook_secret', 'value' => '', 'type' => 'secret', 'label' => 'Stripe Webhook Secret', 'hint' => 'whsec_...'],
            ['group' => 'payment', 'key' => 'paypal_enabled', 'value' => '0', 'type' => 'boolean', 'label' => 'Enable PayPal', 'hint' => 'Toggle PayPal payment gateway'],
            ['group' => 'payment', 'key' => 'paypal_client_id', 'value' => '', 'type' => 'secret', 'label' => 'PayPal Client ID', 'hint' => 'From PayPal Developer Dashboard'],
            ['group' => 'payment', 'key' => 'paypal_client_secret', 'value' => '', 'type' => 'secret', 'label' => 'PayPal Client Secret', 'hint' => 'From PayPal Developer Dashboard'],
            ['group' => 'payment', 'key' => 'paypal_mode', 'value' => 'sandbox', 'type' => 'text', 'label' => 'PayPal Mode', 'hint' => 'sandbox or live'],

            // Commission / Fees
            ['group' => 'commission', 'key' => 'platform_fee_percent', 'value' => '4', 'type' => 'number', 'label' => 'Platform Fee (%)', 'hint' => 'Percentage charged to learner on each booking'],
            ['group' => 'commission', 'key' => 'instructor_commission_percent', 'value' => '85', 'type' => 'number', 'label' => 'Instructor Payout (%)', 'hint' => 'Percentage of booking amount paid to instructor'],
            ['group' => 'commission', 'key' => 'cancellation_fee_percent', 'value' => '50', 'type' => 'number', 'label' => 'Late Cancellation Fee (%)', 'hint' => 'Charged if cancelled within 24 hours'],
            ['group' => 'commission', 'key' => 'min_wallet_topup', 'value' => '50', 'type' => 'number', 'label' => 'Minimum Wallet Top-up ($)', 'hint' => 'Minimum amount for wallet credit purchase'],

            // Email
            ['group' => 'email', 'key' => 'mail_from_name', 'value' => 'Secure Licences', 'type' => 'text', 'label' => 'From Name', 'hint' => 'Sender name in outgoing emails'],
            ['group' => 'email', 'key' => 'mail_from_address', 'value' => 'noreply@securelicences.com.au', 'type' => 'text', 'label' => 'From Address', 'hint' => 'Sender email address'],
            ['group' => 'email', 'key' => 'smtp_host', 'value' => '', 'type' => 'text', 'label' => 'SMTP Host', 'hint' => 'e.g. smtp.gmail.com, smtp.mailtrap.io'],
            ['group' => 'email', 'key' => 'smtp_port', 'value' => '587', 'type' => 'number', 'label' => 'SMTP Port', 'hint' => '587 for TLS, 465 for SSL'],
            ['group' => 'email', 'key' => 'smtp_username', 'value' => '', 'type' => 'secret', 'label' => 'SMTP Username', 'hint' => 'Your SMTP login'],
            ['group' => 'email', 'key' => 'smtp_password', 'value' => '', 'type' => 'secret', 'label' => 'SMTP Password', 'hint' => 'Your SMTP password'],
            ['group' => 'email', 'key' => 'smtp_encryption', 'value' => 'tls', 'type' => 'text', 'label' => 'Encryption', 'hint' => 'tls or ssl'],

            // SMS
            ['group' => 'sms', 'key' => 'sms_enabled', 'value' => '0', 'type' => 'boolean', 'label' => 'Enable SMS', 'hint' => 'Toggle SMS notifications'],
            ['group' => 'sms', 'key' => 'twilio_sid', 'value' => '', 'type' => 'secret', 'label' => 'Twilio Account SID', 'hint' => 'From Twilio Console'],
            ['group' => 'sms', 'key' => 'twilio_auth_token', 'value' => '', 'type' => 'secret', 'label' => 'Twilio Auth Token', 'hint' => 'From Twilio Console'],
            ['group' => 'sms', 'key' => 'twilio_phone_number', 'value' => '', 'type' => 'text', 'label' => 'Twilio Phone Number', 'hint' => '+61xxxxxxxxx format'],
        ];

        foreach ($defaults as $setting) {
            static::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
