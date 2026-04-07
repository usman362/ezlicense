<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\State;
use App\Models\Suburb;

return new class extends Migration
{
    /**
     * Add missing Sydney suburbs that were identified from client demo.
     * These suburbs exist on the SecureLicences platform but were missing from our seed data.
     */
    public function up(): void
    {
        $nswState = State::where('code', 'NSW')->first();
        if (!$nswState) return;

        $suburbs = [
            // South West Sydney - Canterbury-Bankstown area (missing from original seed)
            ['name' => 'Bass Hill', 'postcode' => '2197'],
            ['name' => 'Yagoona', 'postcode' => '2199'],
            ['name' => 'Villawood', 'postcode' => '2163'],
            ['name' => 'Chester Hill', 'postcode' => '2162'],
            ['name' => 'Sefton', 'postcode' => '2162'],
            ['name' => 'Regents Park', 'postcode' => '2143'],
            ['name' => 'Berala', 'postcode' => '2141'],
            ['name' => 'Lidcombe', 'postcode' => '2141'],
            ['name' => 'Homebush', 'postcode' => '2140'],
            ['name' => 'Homebush West', 'postcode' => '2140'],
            ['name' => 'Birrong', 'postcode' => '2143'],
            ['name' => 'Condell Park', 'postcode' => '2200'],
            ['name' => 'Lansdowne', 'postcode' => '2163'],
            ['name' => 'Canley Heights', 'postcode' => '2166'],
            ['name' => 'Canley Vale', 'postcode' => '2166'],
            ['name' => 'Carramar', 'postcode' => '2163'],

            // Wider Western Sydney gaps
            ['name' => 'Guildford', 'postcode' => '2161'],
            ['name' => 'Greystanes', 'postcode' => '2145'],
            ['name' => 'Wentworthville', 'postcode' => '2145'],
            ['name' => 'Westmead', 'postcode' => '2145'],
            ['name' => 'Northmead', 'postcode' => '2152'],
            ['name' => 'Toongabbie', 'postcode' => '2146'],
            ['name' => 'Old Toongabbie', 'postcode' => '2146'],
            ['name' => 'Seven Hills', 'postcode' => '2147'],
            ['name' => 'Lalor Park', 'postcode' => '2147'],
            ['name' => 'Kings Langley', 'postcode' => '2147'],
            ['name' => 'Quakers Hill', 'postcode' => '2763'],
            ['name' => 'Stanhope Gardens', 'postcode' => '2768'],
            ['name' => 'The Ponds', 'postcode' => '2769'],
            ['name' => 'Schofields', 'postcode' => '2762'],
            ['name' => 'Riverstone', 'postcode' => '2765'],
            ['name' => 'Marsden Park', 'postcode' => '2765'],
            ['name' => 'Jordan Springs', 'postcode' => '2747'],
            ['name' => 'Glenmore Park', 'postcode' => '2745'],
            ['name' => 'Emu Plains', 'postcode' => '2750'],

            // Hills District gaps
            ['name' => 'Dural', 'postcode' => '2158'],
            ['name' => 'Cherrybrook', 'postcode' => '2126'],
            ['name' => 'West Pennant Hills', 'postcode' => '2125'],
            ['name' => 'Pennant Hills', 'postcode' => '2120'],
            ['name' => 'Thornleigh', 'postcode' => '2120'],
            ['name' => 'Normanhurst', 'postcode' => '2076'],
            ['name' => 'Waitara', 'postcode' => '2077'],
            ['name' => 'Asquith', 'postcode' => '2077'],

            // South Sydney / St George gaps
            ['name' => 'Arncliffe', 'postcode' => '2205'],
            ['name' => 'Wolli Creek', 'postcode' => '2205'],
            ['name' => 'Tempe', 'postcode' => '2044'],
            ['name' => 'Mascot', 'postcode' => '2020'],
            ['name' => 'Botany', 'postcode' => '2019'],
            ['name' => 'Alexandria', 'postcode' => '2015'],
            ['name' => 'Zetland', 'postcode' => '2017'],
            ['name' => 'Waterloo', 'postcode' => '2017'],
            ['name' => 'Redfern', 'postcode' => '2016'],
            ['name' => 'Erskineville', 'postcode' => '2043'],
            ['name' => 'St Peters', 'postcode' => '2044'],
            ['name' => 'Sydenham', 'postcode' => '2044'],
            ['name' => 'Dulwich Hill', 'postcode' => '2203'],
            ['name' => 'Canterbury', 'postcode' => '2193'],
            ['name' => 'Campsie', 'postcode' => '2194'],
            ['name' => 'Belmore', 'postcode' => '2192'],
            ['name' => 'Lakemba', 'postcode' => '2195'],
            ['name' => 'Wiley Park', 'postcode' => '2195'],
            ['name' => 'Punchbowl', 'postcode' => '2196'],
            ['name' => 'Roselands', 'postcode' => '2196'],
            ['name' => 'Beverly Hills', 'postcode' => '2209'],
            ['name' => 'Kingsgrove', 'postcode' => '2208'],
            ['name' => 'Bexley', 'postcode' => '2207'],
            ['name' => 'Carlton', 'postcode' => '2218'],
            ['name' => 'Allawah', 'postcode' => '2218'],
            ['name' => 'Mortdale', 'postcode' => '2223'],
            ['name' => 'Oatley', 'postcode' => '2223'],
            ['name' => 'Lugarno', 'postcode' => '2210'],
            ['name' => 'Peakhurst', 'postcode' => '2210'],
            ['name' => 'Riverwood', 'postcode' => '2210'],

            // Sutherland Shire gaps
            ['name' => 'Gymea', 'postcode' => '2227'],
            ['name' => 'Kirrawee', 'postcode' => '2232'],
            ['name' => 'Jannali', 'postcode' => '2226'],
            ['name' => 'Como', 'postcode' => '2226'],
            ['name' => 'Sylvania', 'postcode' => '2224'],
            ['name' => 'Taren Point', 'postcode' => '2229'],
            ['name' => 'Woolooware', 'postcode' => '2230'],
            ['name' => 'Kurnell', 'postcode' => '2231'],

            // South West growth areas
            ['name' => 'Gregory Hills', 'postcode' => '2557'],
            ['name' => 'Oran Park', 'postcode' => '2570'],
            ['name' => 'Spring Farm', 'postcode' => '2570'],
            ['name' => 'Harrington Park', 'postcode' => '2567'],
            ['name' => 'Mount Annan', 'postcode' => '2567'],
            ['name' => 'Currans Hill', 'postcode' => '2567'],
            ['name' => 'Macquarie Fields', 'postcode' => '2564'],
            ['name' => 'Minto', 'postcode' => '2566'],
            ['name' => 'Leumeah', 'postcode' => '2560'],
            ['name' => 'Rosemeadow', 'postcode' => '2560'],
            ['name' => 'Ambarvale', 'postcode' => '2560'],
            ['name' => 'Edmondson Park', 'postcode' => '2174'],
            ['name' => 'Bardia', 'postcode' => '2565'],
        ];

        foreach ($suburbs as $suburb) {
            Suburb::firstOrCreate(
                ['state_id' => $nswState->id, 'name' => $suburb['name'], 'postcode' => $suburb['postcode']],
            );
        }
    }

    public function down(): void
    {
        $nswState = State::where('code', 'NSW')->first();
        if (!$nswState) return;

        $names = [
            'Bass Hill', 'Yagoona', 'Villawood', 'Chester Hill', 'Sefton', 'Regents Park',
            'Berala', 'Lidcombe', 'Homebush', 'Homebush West', 'Birrong', 'Condell Park',
            'Lansdowne', 'Canley Heights', 'Canley Vale', 'Carramar', 'Guildford',
            'Greystanes', 'Wentworthville', 'Westmead', 'Northmead', 'Toongabbie',
            'Old Toongabbie', 'Seven Hills', 'Lalor Park', 'Kings Langley', 'Quakers Hill',
            'Stanhope Gardens', 'The Ponds', 'Schofields', 'Riverstone', 'Marsden Park',
            'Jordan Springs', 'Glenmore Park', 'Emu Plains', 'Dural', 'Cherrybrook',
            'West Pennant Hills', 'Pennant Hills', 'Thornleigh', 'Normanhurst', 'Waitara',
            'Asquith', 'Arncliffe', 'Wolli Creek', 'Tempe', 'Mascot', 'Botany', 'Alexandria',
            'Zetland', 'Waterloo', 'Redfern', 'Erskineville', 'St Peters', 'Sydenham',
            'Dulwich Hill', 'Canterbury', 'Campsie', 'Belmore', 'Lakemba', 'Wiley Park',
            'Punchbowl', 'Roselands', 'Beverly Hills', 'Kingsgrove', 'Bexley', 'Carlton',
            'Allawah', 'Mortdale', 'Oatley', 'Lugarno', 'Peakhurst', 'Riverwood', 'Gymea',
            'Kirrawee', 'Jannali', 'Como', 'Sylvania', 'Taren Point', 'Woolooware', 'Kurnell',
            'Gregory Hills', 'Oran Park', 'Spring Farm', 'Harrington Park', 'Mount Annan',
            'Currans Hill', 'Macquarie Fields', 'Minto', 'Leumeah', 'Rosemeadow', 'Ambarvale',
            'Edmondson Park', 'Bardia',
        ];

        Suburb::where('state_id', $nswState->id)->whereIn('name', $names)->delete();
    }
};
