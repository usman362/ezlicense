<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    public function run(): void
    {
        $states = [
            ['name' => 'New South Wales', 'code' => 'NSW'],
            ['name' => 'Victoria', 'code' => 'VIC'],
            ['name' => 'Queensland', 'code' => 'QLD'],
            ['name' => 'Western Australia', 'code' => 'WA'],
            ['name' => 'South Australia', 'code' => 'SA'],
            ['name' => 'Tasmania', 'code' => 'TAS'],
            ['name' => 'Australian Capital Territory', 'code' => 'ACT'],
            ['name' => 'Northern Territory', 'code' => 'NT'],
        ];

        foreach ($states as $state) {
            State::firstOrCreate(['code' => $state['code']], $state);
        }
    }
}
