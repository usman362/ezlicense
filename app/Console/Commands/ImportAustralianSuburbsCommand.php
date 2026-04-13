<?php

namespace App\Console\Commands;

use App\Models\State;
use App\Models\Suburb;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class ImportAustralianSuburbsCommand extends Command
{
    protected $signature = 'suburbs:import
                            {--source=geonames : Data source (geonames)}
                            {--fresh : Delete all existing suburbs first and reimport}
                            {--dry-run : Show what would be imported without saving}';

    protected $description = 'Import ALL Australian suburbs/postcodes from Geonames (16,000+ localities)';

    /**
     * Geonames AU postal code data columns (tab-separated):
     * 0: country_code   1: postal_code   2: place_name
     * 3: admin_name1 (state full name)   4: admin_code1 (state code)
     * 5: admin_name2   6: admin_code2   7: admin_name3   8: admin_code3
     * 9: latitude   10: longitude   11: accuracy
     */
    private const GEONAMES_URL = 'https://download.geonames.org/export/zip/AU.zip';

    // Map Geonames state codes to our state codes
    private const STATE_MAP = [
        'NSW' => 'NSW',
        'VIC' => 'VIC',
        'QLD' => 'QLD',
        'WA'  => 'WA',
        'SA'  => 'SA',
        'TAS' => 'TAS',
        'ACT' => 'ACT',
        'NT'  => 'NT',
        // Geonames sometimes uses full names
        'New South Wales'              => 'NSW',
        'Victoria'                     => 'VIC',
        'Queensland'                   => 'QLD',
        'Western Australia'            => 'WA',
        'South Australia'              => 'SA',
        'Tasmania'                     => 'TAS',
        'Australian Capital Territory' => 'ACT',
        'Northern Territory'           => 'NT',
    ];

    private array $stateCache = [];
    private int $created = 0;
    private int $updated = 0;
    private int $skipped = 0;

    public function handle(): int
    {
        $this->info('');
        $this->info('╔══════════════════════════════════════════════════╗');
        $this->info('║   Australian Suburbs Import (Geonames)          ║');
        $this->info('╚══════════════════════════════════════════════════╝');
        $this->info('');

        // Step 1: Ensure states exist
        $this->ensureStatesExist();

        // Step 2: Download the data
        $this->info('📥 Downloading Australian postal code data from Geonames...');
        $data = $this->downloadGeonamesData();
        if ($data === null) {
            $this->error('Failed to download data. Aborting.');
            return self::FAILURE;
        }

        $lines = explode("\n", $data);
        $totalLines = count(array_filter($lines, fn ($l) => trim($l) !== ''));
        $this->info("   Found {$totalLines} postal code entries.");

        // Step 3: Fresh import?
        if ($this->option('fresh') && !$this->option('dry-run')) {
            if ($this->confirm('⚠️  This will DELETE all existing suburbs and reimport. Continue?', false)) {
                Suburb::truncate();
                $this->warn('   Existing suburbs deleted.');
            } else {
                $this->info('   Keeping existing suburbs (merge mode).');
            }
        }

        // Step 4: Process entries
        $this->info('');
        $this->info('🔄 Processing suburbs...');
        $bar = $this->output->createProgressBar($totalLines);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% — %message%');
        $bar->setMessage('Starting...');

        $batch = [];
        $batchSize = 200;

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            $cols = explode("\t", $line);
            if (count($cols) < 10) {
                $this->skipped++;
                $bar->advance();
                continue;
            }

            $countryCode = $cols[0] ?? '';
            $postcode    = $cols[1] ?? '';
            $placeName   = $cols[2] ?? '';
            $stateName   = $cols[3] ?? '';
            $stateCode   = $cols[4] ?? '';
            $latitude    = $cols[9] ?? null;
            $longitude   = $cols[10] ?? null;

            // Skip non-AU entries (shouldn't happen but safety check)
            if ($countryCode !== 'AU') {
                $this->skipped++;
                $bar->advance();
                continue;
            }

            // Resolve state
            $resolvedCode = self::STATE_MAP[$stateCode] ?? self::STATE_MAP[$stateName] ?? null;
            if (!$resolvedCode) {
                $this->skipped++;
                $bar->advance();
                continue;
            }

            $stateId = $this->getStateId($resolvedCode, $stateName);
            if (!$stateId) {
                $this->skipped++;
                $bar->advance();
                continue;
            }

            // Clean up place name
            $placeName = $this->cleanPlaceName($placeName);
            if (empty($placeName) || empty($postcode)) {
                $this->skipped++;
                $bar->advance();
                continue;
            }

            $batch[] = [
                'state_id'  => $stateId,
                'name'      => $placeName,
                'postcode'  => $postcode,
                'latitude'  => $latitude ? round((float) $latitude, 7) : null,
                'longitude' => $longitude ? round((float) $longitude, 7) : null,
            ];

            if (count($batch) >= $batchSize) {
                $bar->setMessage("{$placeName}, {$resolvedCode} {$postcode}");
                if (!$this->option('dry-run')) {
                    $this->upsertBatch($batch);
                }
                $batch = [];
            }

            $bar->advance();
        }

        // Process remaining batch
        if (!empty($batch) && !$this->option('dry-run')) {
            $this->upsertBatch($batch);
        }

        $bar->setMessage('Done!');
        $bar->finish();

        // Step 5: Summary
        $totalSuburbs = Suburb::count();
        $this->info('');
        $this->info('');
        $this->info('✅ Import complete!');
        $this->info('');

        $this->table(
            ['Metric', 'Count'],
            [
                ['Created (new)', $this->created],
                ['Updated (existing)', $this->updated],
                ['Skipped (invalid)', $this->skipped],
                ['Total in database', $totalSuburbs],
            ]
        );

        // State breakdown
        $this->info('');
        $this->info('📊 Suburbs per state:');
        $stateCounts = Suburb::select('state_id', DB::raw('COUNT(*) as count'))
            ->groupBy('state_id')
            ->with('state')
            ->get()
            ->map(fn ($row) => [
                $row->state?->code ?? '?',
                $row->state?->name ?? '?',
                number_format($row->count),
            ]);

        $this->table(['Code', 'State', 'Suburbs'], $stateCounts->toArray());

        if ($this->option('dry-run')) {
            $this->warn('🔸 Dry run — no changes were saved.');
        }

        Log::info('Suburbs import completed', [
            'created' => $this->created,
            'updated' => $this->updated,
            'skipped' => $this->skipped,
            'total' => $totalSuburbs,
        ]);

        return self::SUCCESS;
    }

    private function downloadGeonamesData(): ?string
    {
        try {
            $response = Http::timeout(60)->get(self::GEONAMES_URL);

            if (!$response->successful()) {
                $this->error("HTTP {$response->status()} from Geonames");
                return null;
            }

            // Save ZIP to temp file
            $tmpZip = tempnam(sys_get_temp_dir(), 'geonames_au_');
            file_put_contents($tmpZip, $response->body());

            // Extract
            $zip = new ZipArchive();
            if ($zip->open($tmpZip) !== true) {
                $this->error('Failed to open ZIP file');
                unlink($tmpZip);
                return null;
            }

            $data = $zip->getFromName('AU.txt');
            $zip->close();
            unlink($tmpZip);

            if ($data === false) {
                $this->error('AU.txt not found in ZIP');
                return null;
            }

            $this->info('   ✓ Downloaded and extracted successfully.');
            return $data;

        } catch (\Throwable $e) {
            $this->error('Download failed: ' . $e->getMessage());
            return null;
        }
    }

    private function ensureStatesExist(): void
    {
        $states = [
            ['name' => 'New South Wales',              'code' => 'NSW'],
            ['name' => 'Victoria',                     'code' => 'VIC'],
            ['name' => 'Queensland',                   'code' => 'QLD'],
            ['name' => 'Western Australia',            'code' => 'WA'],
            ['name' => 'South Australia',              'code' => 'SA'],
            ['name' => 'Tasmania',                     'code' => 'TAS'],
            ['name' => 'Australian Capital Territory', 'code' => 'ACT'],
            ['name' => 'Northern Territory',           'code' => 'NT'],
        ];

        foreach ($states as $state) {
            State::firstOrCreate(['code' => $state['code']], $state);
        }
    }

    private function getStateId(string $code, string $name): ?int
    {
        if (isset($this->stateCache[$code])) {
            return $this->stateCache[$code];
        }

        $state = State::where('code', $code)->first();
        if ($state) {
            $this->stateCache[$code] = $state->id;
            return $state->id;
        }

        return null;
    }

    private function upsertBatch(array $batch): void
    {
        foreach ($batch as $row) {
            $existing = Suburb::where('state_id', $row['state_id'])
                ->where('name', $row['name'])
                ->where('postcode', $row['postcode'])
                ->first();

            if ($existing) {
                // Update lat/lng if we have better data
                if ($row['latitude'] && (!$existing->latitude || !$existing->longitude)) {
                    $existing->update([
                        'latitude' => $row['latitude'],
                        'longitude' => $row['longitude'],
                    ]);
                    $this->updated++;
                } else {
                    $this->skipped++;
                }
            } else {
                Suburb::create($row);
                $this->created++;
            }
        }
    }

    private function cleanPlaceName(string $name): string
    {
        // Trim whitespace
        $name = trim($name);

        // Title case normalization for ALL-CAPS entries
        if ($name === strtoupper($name) && strlen($name) > 3) {
            $name = mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');
        }

        return $name;
    }
}
