<?php

namespace App\Providers;

use App\Listeners\LogSentEmail;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Render all paginators with Bootstrap 5 markup (the UI is Bootstrap; the
        // Laravel default is Tailwind, which renders broken/unstyled).
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        // NOTE: LogSentEmail::handleSent is auto-discovered by Laravel (app/Listeners,
        // method starts with "handle" + type-hints the event), so it is registered
        // automatically. Do NOT also Event::listen() it here or it fires twice.
    }
}
