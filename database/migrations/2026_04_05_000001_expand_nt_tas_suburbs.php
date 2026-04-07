<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\State;
use App\Models\Suburb;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $ntState = State::where('code', 'NT')->first();
        $tasState = State::where('code', 'TAS')->first();

        // NT suburbs - Darwin area
        $ntDarwinSuburbs = [
            ['name' => 'Coconut Grove', 'postcode' => '0810'],
            ['name' => 'Millner', 'postcode' => '0810'],
            ['name' => 'Malak', 'postcode' => '0812'],
            ['name' => 'Karama', 'postcode' => '0812'],
            ['name' => 'Wulagi', 'postcode' => '0812'],
            ['name' => 'Anula', 'postcode' => '0812'],
            ['name' => 'Leanyer', 'postcode' => '0812'],
            ['name' => 'Muirhead', 'postcode' => '0810'],
            ['name' => 'Lyons', 'postcode' => '0810'],
            ['name' => 'Bayview', 'postcode' => '0820'],
            ['name' => 'Larrakeyah', 'postcode' => '0820'],
            ['name' => 'Howard Springs', 'postcode' => '0835'],
            ['name' => 'Humpty Doo', 'postcode' => '0836'],
            ['name' => 'Virginia', 'postcode' => '0834'],
            ['name' => 'Berrimah', 'postcode' => '0828'],
            ['name' => 'Winnellie', 'postcode' => '0820'],
            ['name' => 'East Point', 'postcode' => '0820'],
        ];

        // NT suburbs - Palmerston area
        $ntPalmerstonSuburbs = [
            ['name' => 'Gray', 'postcode' => '0830'],
            ['name' => 'Moulden', 'postcode' => '0830'],
            ['name' => 'Driver', 'postcode' => '0830'],
            ['name' => 'Farrar', 'postcode' => '0830'],
            ['name' => 'Durack', 'postcode' => '0830'],
            ['name' => 'Gunn', 'postcode' => '0832'],
            ['name' => 'Rosebery', 'postcode' => '0832'],
            ['name' => 'Zuccoli', 'postcode' => '0832'],
        ];

        // NT suburbs - Alice Springs area
        $ntAliceSpringsSuburbs = [
            ['name' => 'East Side', 'postcode' => '0870'],
            ['name' => 'Gillen', 'postcode' => '0870'],
            ['name' => 'Braitling', 'postcode' => '0870'],
            ['name' => 'Larapinta', 'postcode' => '0870'],
            ['name' => 'The Gap', 'postcode' => '0870'],
            ['name' => 'Araluen', 'postcode' => '0870'],
        ];

        // TAS suburbs - Hobart area
        $tasHobartSuburbs = [
            ['name' => 'Battery Point', 'postcode' => '7004'],
            ['name' => 'West Hobart', 'postcode' => '7000'],
            ['name' => 'North Hobart', 'postcode' => '7000'],
            ['name' => 'South Hobart', 'postcode' => '7004'],
            ['name' => 'Lenah Valley', 'postcode' => '7008'],
            ['name' => 'Mount Stuart', 'postcode' => '7000'],
            ['name' => 'Dynnyrne', 'postcode' => '7005'],
            ['name' => 'Taroona', 'postcode' => '7053'],
            ['name' => 'Blackmans Bay', 'postcode' => '7052'],
            ['name' => 'Howden', 'postcode' => '7054'],
            ['name' => 'Margate', 'postcode' => '7054'],
            ['name' => 'Lindisfarne', 'postcode' => '7015'],
            ['name' => 'Montrose', 'postcode' => '7010'],
            ['name' => 'Claremont', 'postcode' => '7011'],
            ['name' => 'Granton', 'postcode' => '7030'],
            ['name' => 'Bridgewater', 'postcode' => '7030'],
        ];

        // TAS suburbs - Launceston area
        $tasLauncestonSuburbs = [
            ['name' => 'East Launceston', 'postcode' => '7250'],
            ['name' => 'Trevallyn', 'postcode' => '7250'],
            ['name' => 'Prospect', 'postcode' => '7250'],
            ['name' => 'Ravenswood', 'postcode' => '7250'],
            ['name' => 'St Leonards', 'postcode' => '7250'],
            ['name' => 'Youngtown', 'postcode' => '7249'],
            ['name' => 'Legana', 'postcode' => '7277'],
            ['name' => 'Hadspen', 'postcode' => '7290'],
            ['name' => 'Perth', 'postcode' => '7300'],
            ['name' => 'Longford', 'postcode' => '7301'],
        ];

        // TAS suburbs - Regional
        $tasRegionalSuburbs = [
            ['name' => 'George Town', 'postcode' => '7253'],
            ['name' => 'Smithton', 'postcode' => '7330'],
            ['name' => 'Queenstown', 'postcode' => '7467'],
            ['name' => 'New Norfolk', 'postcode' => '7140'],
            ['name' => 'Huonville', 'postcode' => '7109'],
            ['name' => 'Deloraine', 'postcode' => '7304'],
        ];

        // Insert NT suburbs
        foreach (array_merge($ntDarwinSuburbs, $ntPalmerstonSuburbs, $ntAliceSpringsSuburbs) as $suburb) {
            Suburb::firstOrCreate(
                ['state_id' => $ntState->id, 'name' => $suburb['name'], 'postcode' => $suburb['postcode']],
            );
        }

        // Insert TAS suburbs
        foreach (array_merge($tasHobartSuburbs, $tasLauncestonSuburbs, $tasRegionalSuburbs) as $suburb) {
            Suburb::firstOrCreate(
                ['state_id' => $tasState->id, 'name' => $suburb['name'], 'postcode' => $suburb['postcode']],
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $ntState = State::where('code', 'NT')->first();
        $tasState = State::where('code', 'TAS')->first();

        // Remove NT suburbs added by this migration
        $ntSuburbNames = [
            'Coconut Grove', 'Millner', 'Malak', 'Karama', 'Wulagi', 'Anula', 'Leanyer',
            'Muirhead', 'Lyons', 'Bayview', 'Larrakeyah', 'Howard Springs', 'Humpty Doo',
            'Virginia', 'Berrimah', 'Winnellie', 'East Point', 'Gray', 'Moulden', 'Driver',
            'Farrar', 'Durack', 'Gunn', 'Rosebery', 'Zuccoli', 'East Side', 'Gillen',
            'Braitling', 'Larapinta', 'The Gap', 'Araluen',
        ];

        Suburb::where('state_id', $ntState->id)->whereIn('name', $ntSuburbNames)->delete();

        // Remove TAS suburbs added by this migration
        $tasSuburbNames = [
            'Battery Point', 'West Hobart', 'North Hobart', 'South Hobart', 'Lenah Valley',
            'Mount Stuart', 'Dynnyrne', 'Taroona', 'Blackmans Bay', 'Howden', 'Margate',
            'Lindisfarne', 'Montrose', 'Claremont', 'Granton', 'Bridgewater', 'East Launceston',
            'Trevallyn', 'Prospect', 'Ravenswood', 'St Leonards', 'Youngtown', 'Legana',
            'Hadspen', 'Perth', 'Longford', 'George Town', 'Smithton', 'Queenstown',
            'New Norfolk', 'Huonville', 'Deloraine',
        ];

        Suburb::where('state_id', $tasState->id)->whereIn('name', $tasSuburbNames)->delete();
    }
};
