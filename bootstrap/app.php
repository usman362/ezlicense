<?php

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
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
