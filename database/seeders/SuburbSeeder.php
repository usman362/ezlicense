<?php

namespace Database\Seeders;

use App\Models\State;
use App\Models\Suburb;
use Illuminate\Database\Seeder;

class SuburbSeeder extends Seeder
{
    /**
     * Seed sample suburbs (Sydney, Melbourne, Brisbane areas).
     * Add more via CSV or external source for production.
     */
    public function run(): void
    {
        $suburbsByState = [
            'NSW' => [
                ['name' => 'Sydney', 'postcode' => '2000'],
                ['name' => 'Parramatta', 'postcode' => '2150'],
                ['name' => 'Liverpool', 'postcode' => '2170'],
                ['name' => 'Penrith', 'postcode' => '2750'],
                ['name' => 'Newcastle', 'postcode' => '2300'],
                ['name' => 'Wollongong', 'postcode' => '2500'],
                ['name' => 'Central Coast', 'postcode' => '2250'],
                ['name' => 'Bondi Junction', 'postcode' => '2022'],
                ['name' => 'Chatswood', 'postcode' => '2067'],
                ['name' => 'Hurstville', 'postcode' => '2220'],
            ],
            'VIC' => [
                ['name' => 'Melbourne', 'postcode' => '3000'],
                ['name' => 'Geelong', 'postcode' => '3220'],
                ['name' => 'Bendigo', 'postcode' => '3550'],
                ['name' => 'Box Hill', 'postcode' => '3128'],
                ['name' => 'Dandenong', 'postcode' => '3175'],
                ['name' => 'Frankston', 'postcode' => '3199'],
                ['name' => 'Ringwood', 'postcode' => '3134'],
                ['name' => 'Richmond', 'postcode' => '3121'],
                ['name' => 'Footscray', 'postcode' => '3011'],
                ['name' => 'Preston', 'postcode' => '3072'],
            ],
            'QLD' => [
                ['name' => 'Brisbane', 'postcode' => '4000'],
                ['name' => 'Gold Coast', 'postcode' => '4217'],
                ['name' => 'Sunshine Coast', 'postcode' => '4558'],
                ['name' => 'Toowoomba', 'postcode' => '4350'],
                ['name' => 'Cairns', 'postcode' => '4870'],
                ['name' => 'Townsville', 'postcode' => '4810'],
                ['name' => 'Ipswich', 'postcode' => '4305'],
                ['name' => 'Logan Central', 'postcode' => '4114'],
                ['name' => 'Southport', 'postcode' => '4215'],
                ['name' => 'Coffs Harbour', 'postcode' => '2450'],
            ],
            'WA' => [
                ['name' => 'Perth', 'postcode' => '6000'],
                ['name' => 'Fremantle', 'postcode' => '6160'],
                ['name' => 'Joondalup', 'postcode' => '6027'],
                ['name' => 'Mandurah', 'postcode' => '6210'],
            ],
            'SA' => [
                ['name' => 'Adelaide', 'postcode' => '5000'],
                ['name' => 'Mount Gambier', 'postcode' => '5290'],
            ],
            'TAS' => [
                ['name' => 'Hobart', 'postcode' => '7000'],
                ['name' => 'Launceston', 'postcode' => '7250'],
            ],
            'ACT' => [
                ['name' => 'Canberra', 'postcode' => '2600'],
            ],
        ];

        foreach ($suburbsByState as $stateCode => $suburbs) {
            $state = State::where('code', $stateCode)->first();
            if (! $state) {
                continue;
            }
            foreach ($suburbs as $sub) {
                Suburb::firstOrCreate(
                    ['state_id' => $state->id, 'postcode' => $sub['postcode']],
                    ['name' => $sub['name']]
                );
            }
        }
    }
}
