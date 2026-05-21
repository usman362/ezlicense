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
            ['group' => 'general', 'key' => 'google_maps_api_key', 'value' => '', 'type' => 'secret', 'label' => 'Google Maps API Key', 'hint' => 'Google Cloud Maps API key (Places library enabled). Used for address autocomplete during booking. Get one at https://console.cloud.google.com/google/maps-apis/credentials'],
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
            ['group' => 'commission', 'key' => 'gst_rate_percent', 'value' => '10', 'type' => 'number', 'label' => 'GST Rate (%)', 'hint' => 'Australian GST is 10%. Applied to platform fees on payouts (instructor must be GST-registered).'],
            ['group' => 'commission', 'key' => 'default_test_package_price', 'value' => '225', 'type' => 'number', 'label' => 'Default Test Package Price ($)', 'hint' => 'Fallback price shown if instructor has not set their own. Australian average is ~$225.'],

            // Discounts (bulk-hours tiers + booking package options)
            ['group' => 'discounts', 'key' => 'hours_discount_tiers', 'value' => '[{"hours":6,"discount_pct":5},{"hours":10,"discount_pct":10}]', 'type' => 'json', 'label' => 'Bulk Hours Discount Tiers', 'hint' => 'Defines: buy X hours → get Y% off. Add/remove tiers below. Order from smallest hours to largest.'],
            ['group' => 'discounts', 'key' => 'booking_hour_packages', 'value' => '[1,3,5,10,20]', 'type' => 'json', 'label' => 'Hour Package Options', 'hint' => 'Pre-set hour packages shown to learners on the amount-selection page (in addition to a custom input).'],

            // Referral program
            ['group' => 'referral', 'key' => 'referral_enabled', 'value' => '1', 'type' => 'boolean', 'label' => 'Enable Referral Program', 'hint' => 'Allow learners to invite friends for rewards'],
            ['group' => 'referral', 'key' => 'referral_referrer_credit', 'value' => '10', 'type' => 'number', 'label' => 'Referrer Credit ($)', 'hint' => 'Wallet credit awarded to the referrer after invitee completes their first paid booking'],
            ['group' => 'referral', 'key' => 'referral_invitee_discount_pct', 'value' => '5', 'type' => 'number', 'label' => 'Invitee Discount (%)', 'hint' => 'Percentage discount on invitee\'s first booking (set 0 to disable)'],
            ['group' => 'referral', 'key' => 'referral_invitee_discount_amount', 'value' => '0', 'type' => 'number', 'label' => 'Invitee Discount ($)', 'hint' => 'Flat dollar discount on invitee\'s first booking (alternative to %; takes precedence if both set)'],
            ['group' => 'referral', 'key' => 'referral_expiry_days', 'value' => '90', 'type' => 'number', 'label' => 'Expiry (days)', 'hint' => 'How long an invite link/discount stays valid after sign-up'],
            ['group' => 'referral', 'key' => 'referral_max_invites_per_user', 'value' => '50', 'type' => 'number', 'label' => 'Max Invites Per User', 'hint' => 'Anti-abuse cap on how many friends one learner can invite'],

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

            // SEO — site-wide meta defaults, analytics & search-engine tools
            ['group' => 'seo', 'key' => 'seo_default_title',        'value' => 'Secure Licences — Find a verified driving instructor near you', 'type' => 'text',     'label' => 'Default Page Title',        'hint' => 'Used as the <title> when a page does not set its own. Keep under ~60 characters.'],
            ['group' => 'seo', 'key' => 'seo_title_suffix',         'value' => ' | Secure Licences',                                              'type' => 'text',     'label' => 'Title Suffix',              'hint' => 'Appended to every page title (e.g. " | Secure Licences"). Leave blank to disable.'],
            ['group' => 'seo', 'key' => 'seo_default_description',  'value' => "Australia's #1 platform to find, compare and book verified driving instructors online. Transparent pricing, no booking fees.", 'type' => 'textarea', 'label' => 'Default Meta Description',  'hint' => 'Search-result snippet shown when a page does not set its own. Keep under ~160 characters.'],
            ['group' => 'seo', 'key' => 'seo_default_keywords',     'value' => 'driving lessons, driving instructors, learner driver, driving school, driving test, australia', 'type' => 'text', 'label' => 'Default Keywords',          'hint' => 'Comma-separated. Mostly ignored by Google but still parsed by some smaller search engines.'],
            ['group' => 'seo', 'key' => 'seo_og_image',             'value' => '',                                                                'type' => 'text',     'label' => 'Default Social Share Image', 'hint' => 'Full URL to a 1200x630 image used for Facebook / Twitter / LinkedIn previews when a page has no specific image.'],
            ['group' => 'seo', 'key' => 'seo_twitter_handle',       'value' => '@securelicences',                                                  'type' => 'text',     'label' => 'Twitter / X Handle',        'hint' => 'Including the @ sign — used in Twitter Card meta tags.'],
            ['group' => 'seo', 'key' => 'seo_canonical_host',       'value' => '',                                                                'type' => 'text',     'label' => 'Canonical Host',            'hint' => 'Optional. e.g. "https://www.securelicences.com.au" — used to build canonical URLs. Leave blank to use request URL.'],
            ['group' => 'seo', 'key' => 'seo_robots_mode',          'value' => 'index_follow',                                                    'type' => 'text',     'label' => 'Robots Mode',               'hint' => 'index_follow (live site), noindex_nofollow (staging — blocks all search engines).'],
            ['group' => 'seo', 'key' => 'seo_google_analytics_id',  'value' => '',                                                                'type' => 'text',     'label' => 'Google Analytics ID',       'hint' => 'GA4 measurement ID, e.g. G-XXXXXXXXXX. Loads the gtag.js snippet automatically.'],
            ['group' => 'seo', 'key' => 'seo_google_verification',  'value' => '',                                                                'type' => 'text',     'label' => 'Google Search Console',     'hint' => 'Verification token from the HTML-tag method (just the content="..." value).'],
            ['group' => 'seo', 'key' => 'seo_facebook_pixel_id',    'value' => '',                                                                'type' => 'text',     'label' => 'Facebook Pixel ID',         'hint' => '15-16 digit Pixel ID. Leave blank to disable Meta Pixel tracking.'],
            ['group' => 'seo', 'key' => 'seo_bing_verification',    'value' => '',                                                                'type' => 'text',     'label' => 'Bing Webmaster Verification', 'hint' => 'msvalidate.01 content value from Bing Webmaster Tools.'],
            ['group' => 'seo', 'key' => 'seo_org_schema_enabled',   'value' => '1',                                                               'type' => 'boolean',  'label' => 'Output Organisation Schema', 'hint' => 'Adds an Organization JSON-LD block to every page (helps Google understand your business).'],
            ['group' => 'seo', 'key' => 'seo_sitemap_enabled',      'value' => '1',                                                               'type' => 'boolean',  'label' => 'Enable Sitemap',            'hint' => 'Reference /sitemap.xml in robots.txt so search engines can discover all pages.'],

            // Branding — logos, favicon, hero colours used across the site
            ['group' => 'branding', 'key' => 'brand_logo_url',       'value' => '', 'type' => 'text',    'label' => 'Header Logo URL',  'hint' => 'Full URL to your header logo (PNG/SVG, ~180x40). Leave blank to use the default text wordmark.'],
            ['group' => 'branding', 'key' => 'brand_logo_dark_url',  'value' => '', 'type' => 'text',    'label' => 'Dark Logo URL',    'hint' => 'Optional alternate logo used on dark backgrounds (e.g. email headers).'],
            ['group' => 'branding', 'key' => 'brand_favicon_url',    'value' => '', 'type' => 'text',    'label' => 'Favicon URL',      'hint' => 'Full URL to a 32x32 PNG or ICO. Shows in browser tabs and bookmarks.'],
            ['group' => 'branding', 'key' => 'brand_touch_icon_url', 'value' => '', 'type' => 'text',    'label' => 'Apple Touch Icon', 'hint' => 'Full URL to a 180x180 PNG. Used when users save your site to home-screen on iOS.'],
            ['group' => 'branding', 'key' => 'brand_address',        'value' => '', 'type' => 'textarea','label' => 'Business Address', 'hint' => 'Used in footer, contact emails and Organization schema. e.g. "Level 1, 100 George Street, Sydney NSW 2000".'],
        ];

        foreach ($defaults as $setting) {
            static::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
