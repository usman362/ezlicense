<?php

use App\Http\Middleware\EnsureInstructorOnboarded;
use App\Http\Middleware\EnsureUserRole;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => EnsureUserRole::class,
            'instructor.onboarded' => EnsureInstructorOnboarded::class,
        ]);

        // Stripe webhook is server-to-server and uses signature verification
        // instead of CSRF tokens. Exempt it from CSRF middleware.
        $middleware->validateCsrfTokens(except: [
            'stripe/webhook',
        ]);
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Generate instructor payouts every Monday at 02:00 AEST
        $schedule->command('payouts:generate')
            ->weeklyOn(1, '02:00')
            ->timezone('Australia/Sydney')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/payouts.log'));

        // Send lesson confirmation reminders every 4 hours (anti-chargeback)
        $schedule->command('confirmations:remind --hours=4 --max-reminders=3')
            ->everyFourHours()
            ->timezone('Australia/Sydney')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/confirmations.log'));

        // Send 24-hour lesson reminders (hourly check, sends once per booking)
        $schedule->command('lessons:remind-24h')
            ->hourly()
            ->timezone('Australia/Sydney')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/lesson-reminders.log'));

        // Release pending payments past the 24h dispute window — runs hourly,
        // idempotent. Once released, bookings become eligible for the next
        // weekly payout run on Monday 02:00.
        // Hours value lives in SiteSetting `payment_hold_hours` (default 24, admin-tunable).
        $schedule->command('payments:release-pending')
            ->hourly()
            ->timezone('Australia/Sydney')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/payments-release.log'));

        // Email instructors their weekly statement every Sunday at 18:00 AEST
        // (the day after a Sun-Sat period ends). For fortnightly/4-weekly
        // instructors, the command itself checks if their period ended.
        $schedule->command('statements:send-weekly')
            ->weeklyOn(0, '18:00')      // 0 = Sunday
            ->timezone('Australia/Sydney')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/statements.log'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
