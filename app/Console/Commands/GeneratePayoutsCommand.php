<?php

namespace App\Console\Commands;

use App\Services\PayoutService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GeneratePayoutsCommand extends Command
{
    protected $signature = 'payouts:generate {--week-ending= : Saturday date for the payout period (YYYY-MM-DD). Defaults to last Saturday.}';

    protected $description = 'Generate weekly instructor payout records for completed bookings';

    public function handle(PayoutService $service): int
    {
        $weekEnding = null;
        if ($this->option('week-ending')) {
            $weekEnding = Carbon::parse($this->option('week-ending'), 'Australia/Sydney');
            if ($weekEnding->dayOfWeek !== Carbon::SATURDAY) {
                $this->error('--week-ending must be a Saturday.');
                return self::FAILURE;
            }
        }

        $this->info('Generating weekly payouts...');
        $count = $service->generateWeeklyPayouts($weekEnding);
        $this->info("Done. {$count} payout(s) generated.");

        return self::SUCCESS;
    }
}
