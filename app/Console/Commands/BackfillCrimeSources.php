<?php

namespace App\Console\Commands;

use App\Jobs\SearchKlatenSources;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class BackfillCrimeSources extends Command
{
    protected $signature = 'crime:sources:backfill {--start-year=} {--dry-run}';

    protected $description = 'Search one bounded historical year for public Klaten sources';

    public function handle(): int
    {
        $configuredYear = (int) config('crime-timeline.backfill_start_year');
        $startYear = (int) ($this->option('start-year') ?: $configuredYear);
        $currentYear = (int) now('Asia/Jakarta')->year;
        if ($startYear < 2000 || $startYear > $currentYear) {
            $this->error('Start year outside allowed range.');

            return self::FAILURE;
        }

        $cursor = (int) Cache::get('crime-timeline.backfill_year', $startYear);
        if ($cursor < $startYear || $cursor > $currentYear) {
            $cursor = $startYear;
        }
        $window = ['start' => $cursor.'-01-01', 'end' => $cursor === $currentYear ? now('Asia/Jakarta')->toDateString() : $cursor.'-12-31'];
        if ($this->option('dry-run')) {
            $this->table(['Start', 'End'], [array_values($window)]);

            return self::SUCCESS;
        }

        SearchKlatenSources::dispatch($window['start'], $window['end'], 'backfill');
        Cache::put('crime-timeline.backfill_year', $cursor === $currentYear ? $startYear : $cursor + 1);
        $this->info('One bounded search window queued.');

        return self::SUCCESS;
    }
}
