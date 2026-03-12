<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class DynamicMailConfigProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Only override mail config if site_settings table exists and has SMTP settings
        try {
            if (! Schema::hasTable('site_settings')) {
                return;
            }

            $smtpHost = SiteSetting::get('smtp_host');
            if (empty($smtpHost)) {
                return;
            }

            Config::set('mail.default', 'smtp');
            Config::set('mail.mailers.smtp.host', $smtpHost);
            Config::set('mail.mailers.smtp.port', SiteSetting::get('smtp_port', 587));
            Config::set('mail.mailers.smtp.username', SiteSetting::get('smtp_username'));
            Config::set('mail.mailers.smtp.password', SiteSetting::get('smtp_password'));
            Config::set('mail.mailers.smtp.encryption', SiteSetting::get('smtp_encryption', 'tls'));

            $fromAddress = SiteSetting::get('mail_from_address');
            $fromName = SiteSetting::get('mail_from_name', SiteSetting::get('site_name', 'EzLicence'));
            if ($fromAddress) {
                Config::set('mail.from.address', $fromAddress);
                Config::set('mail.from.name', $fromName);
            }
        } catch (\Throwable $e) {
            // Silently fail — database may not be migrated yet
        }
    }
}
