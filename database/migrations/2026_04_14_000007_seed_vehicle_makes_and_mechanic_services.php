<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $this->seedVehicleMakes();
        $this->seedVehicleModels();
        $this->seedMechanicServiceTypes();
    }

    private function seedVehicleMakes(): void
    {
        $makes = [
            // ── Japanese (most popular in Australia) ─────────────
            ['name' => 'Toyota',        'country' => 'Japan',      'origin_type' => 'japanese',   'is_popular' => true,  'is_european' => false, 'display_order' => 1],
            ['name' => 'Mazda',         'country' => 'Japan',      'origin_type' => 'japanese',   'is_popular' => true,  'is_european' => false, 'display_order' => 2],
            ['name' => 'Hyundai',       'country' => 'South Korea','origin_type' => 'korean',     'is_popular' => true,  'is_european' => false, 'display_order' => 3],
            ['name' => 'Kia',           'country' => 'South Korea','origin_type' => 'korean',     'is_popular' => true,  'is_european' => false, 'display_order' => 4],
            ['name' => 'Mitsubishi',    'country' => 'Japan',      'origin_type' => 'japanese',   'is_popular' => true,  'is_european' => false, 'display_order' => 5],
            ['name' => 'Ford',          'country' => 'USA',        'origin_type' => 'american',   'is_popular' => true,  'is_european' => false, 'display_order' => 6],
            ['name' => 'Holden',        'country' => 'Australia',  'origin_type' => 'australian', 'is_popular' => true,  'is_european' => false, 'display_order' => 7],
            ['name' => 'Nissan',        'country' => 'Japan',      'origin_type' => 'japanese',   'is_popular' => true,  'is_european' => false, 'display_order' => 8],
            ['name' => 'Honda',         'country' => 'Japan',      'origin_type' => 'japanese',   'is_popular' => true,  'is_european' => false, 'display_order' => 9],
            ['name' => 'Subaru',        'country' => 'Japan',      'origin_type' => 'japanese',   'is_popular' => true,  'is_european' => false, 'display_order' => 10],
            ['name' => 'Suzuki',        'country' => 'Japan',      'origin_type' => 'japanese',   'is_popular' => true,  'is_european' => false, 'display_order' => 11],
            ['name' => 'Isuzu',         'country' => 'Japan',      'origin_type' => 'japanese',   'is_popular' => true,  'is_european' => false, 'display_order' => 12],
            ['name' => 'Volkswagen',    'country' => 'Germany',    'origin_type' => 'european',   'is_popular' => true,  'is_european' => true,  'display_order' => 13],

            // ── European / Luxury ────────────────────────────────
            ['name' => 'BMW',           'country' => 'Germany',    'origin_type' => 'european',   'is_popular' => true,  'is_european' => true,  'display_order' => 14],
            ['name' => 'Mercedes-Benz', 'country' => 'Germany',    'origin_type' => 'european',   'is_popular' => true,  'is_european' => true,  'display_order' => 15],
            ['name' => 'Audi',          'country' => 'Germany',    'origin_type' => 'european',   'is_popular' => true,  'is_european' => true,  'display_order' => 16],
            ['name' => 'Volvo',         'country' => 'Sweden',     'origin_type' => 'european',   'is_popular' => false, 'is_european' => true,  'display_order' => 17],
            ['name' => 'Peugeot',       'country' => 'France',     'origin_type' => 'european',   'is_popular' => false, 'is_european' => true,  'display_order' => 18],
            ['name' => 'Renault',       'country' => 'France',     'origin_type' => 'european',   'is_popular' => false, 'is_european' => true,  'display_order' => 19],
            ['name' => 'Fiat',          'country' => 'Italy',      'origin_type' => 'european',   'is_popular' => false, 'is_european' => true,  'display_order' => 20],
            ['name' => 'Alfa Romeo',    'country' => 'Italy',      'origin_type' => 'european',   'is_popular' => false, 'is_european' => true,  'display_order' => 21],
            ['name' => 'MINI',          'country' => 'UK',         'origin_type' => 'european',   'is_popular' => false, 'is_european' => true,  'display_order' => 22],
            ['name' => 'Land Rover',    'country' => 'UK',         'origin_type' => 'european',   'is_popular' => false, 'is_european' => true,  'display_order' => 23],
            ['name' => 'Jaguar',        'country' => 'UK',         'origin_type' => 'european',   'is_popular' => false, 'is_european' => true,  'display_order' => 24],
            ['name' => 'Porsche',       'country' => 'Germany',    'origin_type' => 'european',   'is_popular' => false, 'is_european' => true,  'display_order' => 25],
            ['name' => 'Skoda',         'country' => 'Czech Republic','origin_type' => 'european','is_popular' => false, 'is_european' => true,  'display_order' => 26],
            ['name' => 'Citroen',       'country' => 'France',     'origin_type' => 'european',   'is_popular' => false, 'is_european' => true,  'display_order' => 27],

            // ── American ─────────────────────────────────────────
            ['name' => 'Jeep',          'country' => 'USA',        'origin_type' => 'american',   'is_popular' => false, 'is_european' => false, 'display_order' => 28],
            ['name' => 'RAM',           'country' => 'USA',        'origin_type' => 'american',   'is_popular' => false, 'is_european' => false, 'display_order' => 29],
            ['name' => 'Chevrolet',     'country' => 'USA',        'origin_type' => 'american',   'is_popular' => false, 'is_european' => false, 'display_order' => 30],
            ['name' => 'Tesla',         'country' => 'USA',        'origin_type' => 'american',   'is_popular' => false, 'is_european' => false, 'display_order' => 31],

            // ── Korean ───────────────────────────────────────────
            ['name' => 'SsangYong',     'country' => 'South Korea','origin_type' => 'korean',     'is_popular' => false, 'is_european' => false, 'display_order' => 32],
            ['name' => 'Genesis',       'country' => 'South Korea','origin_type' => 'korean',     'is_popular' => false, 'is_european' => false, 'display_order' => 33],

            // ── Chinese (growing in Australia) ───────────────────
            ['name' => 'MG',            'country' => 'China',      'origin_type' => 'chinese',    'is_popular' => true,  'is_european' => false, 'display_order' => 34],
            ['name' => 'GWM',           'country' => 'China',      'origin_type' => 'chinese',    'is_popular' => false, 'is_european' => false, 'display_order' => 35],
            ['name' => 'BYD',           'country' => 'China',      'origin_type' => 'chinese',    'is_popular' => false, 'is_european' => false, 'display_order' => 36],
            ['name' => 'LDV',           'country' => 'China',      'origin_type' => 'chinese',    'is_popular' => false, 'is_european' => false, 'display_order' => 37],
            ['name' => 'Haval',         'country' => 'China',      'origin_type' => 'chinese',    'is_popular' => false, 'is_european' => false, 'display_order' => 38],

            // ── Japanese continued ───────────────────────────────
            ['name' => 'Lexus',         'country' => 'Japan',      'origin_type' => 'japanese',   'is_popular' => false, 'is_european' => false, 'display_order' => 39],
            ['name' => 'Daihatsu',      'country' => 'Japan',      'origin_type' => 'japanese',   'is_popular' => false, 'is_european' => false, 'display_order' => 40],

            // ── Other ────────────────────────────────────────────
            ['name' => 'Other',         'country' => null,         'origin_type' => 'other',      'is_popular' => false, 'is_european' => false, 'display_order' => 99],
        ];

        $now = now();
        foreach ($makes as $make) {
            DB::table('vehicle_makes')->insertOrIgnore(array_merge($make, [
                'slug' => Str::slug($make['name']),
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    private function seedVehicleModels(): void
    {
        $models = [
            // Toyota
            'Toyota' => ['Corolla', 'Camry', 'HiLux', 'RAV4', 'LandCruiser', 'Kluger', 'Yaris', 'C-HR', 'Prado', 'Fortuner', '86', 'Supra', 'HiAce'],
            // Mazda
            'Mazda' => ['Mazda3', 'CX-5', 'CX-3', 'CX-30', 'CX-8', 'CX-9', 'Mazda2', 'Mazda6', 'BT-50', 'MX-5'],
            // Hyundai
            'Hyundai' => ['i30', 'Tucson', 'Kona', 'Santa Fe', 'Accent', 'Venue', 'Staria', 'iLoad', 'Ioniq 5', 'Palisade'],
            // Kia
            'Kia' => ['Cerato', 'Sportage', 'Seltos', 'Carnival', 'Sorento', 'Stonic', 'Picanto', 'Rio', 'EV6', 'Niro'],
            // Mitsubishi
            'Mitsubishi' => ['Triton', 'ASX', 'Outlander', 'Pajero Sport', 'Eclipse Cross', 'Mirage', 'Lancer', 'Pajero'],
            // Ford
            'Ford' => ['Ranger', 'Everest', 'Focus', 'Mustang', 'Puma', 'Escape', 'Transit', 'Falcon', 'Territory', 'Fiesta'],
            // Holden
            'Holden' => ['Commodore', 'Colorado', 'Cruze', 'Captiva', 'Astra', 'Trax', 'Barina', 'Trailblazer'],
            // Nissan
            'Nissan' => ['X-Trail', 'Navara', 'Qashqai', 'Patrol', 'Juke', 'Pathfinder', 'Leaf', 'Pulsar', '370Z', 'Dualis'],
            // Honda
            'Honda' => ['Civic', 'CR-V', 'HR-V', 'Jazz', 'Accord', 'City', 'ZR-V', 'Odyssey', 'WR-V'],
            // Subaru
            'Subaru' => ['Outback', 'Forester', 'XV', 'Impreza', 'WRX', 'BRZ', 'Liberty', 'Crosstrek', 'Solterra'],
            // Suzuki
            'Suzuki' => ['Swift', 'Jimny', 'Vitara', 'Baleno', 'S-Cross', 'Ignis'],
            // Isuzu
            'Isuzu' => ['D-Max', 'MU-X'],
            // Volkswagen
            'Volkswagen' => ['Golf', 'Tiguan', 'T-Cross', 'T-Roc', 'Polo', 'Amarok', 'Passat', 'Transporter', 'Touareg', 'ID.4'],
            // BMW
            'BMW' => ['3 Series', '1 Series', 'X3', 'X5', '5 Series', 'X1', 'X7', '4 Series', 'M3', 'M4', 'iX3'],
            // Mercedes-Benz
            'Mercedes-Benz' => ['C-Class', 'A-Class', 'GLC', 'E-Class', 'GLA', 'CLA', 'GLE', 'S-Class', 'AMG GT', 'EQC'],
            // Audi
            'Audi' => ['A3', 'Q5', 'A4', 'Q3', 'Q7', 'A1', 'Q8', 'e-tron', 'RS3', 'TT'],
            // MG
            'MG' => ['ZS', 'MG3', 'HS', 'ZST', 'MG4'],
        ];

        $now = now();
        foreach ($models as $makeName => $modelNames) {
            $makeId = DB::table('vehicle_makes')->where('name', $makeName)->value('id');
            if (!$makeId) {
                continue;
            }
            foreach ($modelNames as $modelName) {
                DB::table('vehicle_models')->insertOrIgnore([
                    'vehicle_make_id' => $makeId,
                    'name' => $modelName,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    private function seedMechanicServiceTypes(): void
    {
        $types = [
            // Servicing
            ['name' => 'Logbook Service',           'category' => 'servicing',  'icon' => 'bi-journal-check',    'description' => 'Manufacturer-specified logbook service to maintain your warranty.', 'display_order' => 1],
            ['name' => 'General Service',           'category' => 'servicing',  'icon' => 'bi-wrench',           'description' => 'Standard oil change, filter replacement, and vehicle health check.', 'display_order' => 2],
            ['name' => 'Major Service',             'category' => 'servicing',  'icon' => 'bi-tools',            'description' => 'Comprehensive service including fluids, filters, spark plugs, and full inspection.', 'display_order' => 3],

            // Repairs
            ['name' => 'Brake Pads & Rotors',       'category' => 'repairs',    'icon' => 'bi-disc',             'description' => 'Brake pad replacement, rotor machining or replacement.', 'display_order' => 4],
            ['name' => 'Clutch Replacement',         'category' => 'repairs',    'icon' => 'bi-gear-wide',        'description' => 'Clutch disc, pressure plate and bearing replacement.', 'display_order' => 5],
            ['name' => 'Timing Belt/Chain',          'category' => 'repairs',    'icon' => 'bi-arrow-repeat',     'description' => 'Timing belt or chain replacement to prevent engine damage.', 'display_order' => 6],
            ['name' => 'Suspension & Steering',      'category' => 'repairs',    'icon' => 'bi-arrow-down-up',    'description' => 'Shock absorbers, struts, control arms, and steering components.', 'display_order' => 7],
            ['name' => 'Exhaust System',             'category' => 'repairs',    'icon' => 'bi-wind',             'description' => 'Exhaust repairs, muffler replacement, catalytic converter.', 'display_order' => 8],
            ['name' => 'Cooling System',             'category' => 'repairs',    'icon' => 'bi-thermometer-half', 'description' => 'Radiator, water pump, thermostat, and coolant hose repairs.', 'display_order' => 9],
            ['name' => 'Engine Repair',              'category' => 'repairs',    'icon' => 'bi-cpu',              'description' => 'Engine diagnostics, head gasket, oil leak repairs.', 'display_order' => 10],
            ['name' => 'Transmission Repair',        'category' => 'repairs',    'icon' => 'bi-gear-fill',        'description' => 'Auto/manual transmission servicing and repairs.', 'display_order' => 11],
            ['name' => 'Drive Belt',                 'category' => 'repairs',    'icon' => 'bi-circle',           'description' => 'Serpentine belt, fan belt, and accessory belt replacement.', 'display_order' => 12],
            ['name' => 'CV Shaft/Joint',             'category' => 'repairs',    'icon' => 'bi-nut',              'description' => 'CV joint boot, CV shaft replacement.', 'display_order' => 13],
            ['name' => 'Fuel System',                'category' => 'repairs',    'icon' => 'bi-fuel-pump',        'description' => 'Fuel pump, fuel filter, injector cleaning and repair.', 'display_order' => 14],

            // Electrical
            ['name' => 'Battery Replacement',        'category' => 'electrical', 'icon' => 'bi-battery-charging', 'description' => 'Car battery testing, supply and fitting.', 'display_order' => 15],
            ['name' => 'Alternator Repair',          'category' => 'electrical', 'icon' => 'bi-lightning-charge', 'description' => 'Alternator testing, repair or replacement.', 'display_order' => 16],
            ['name' => 'Starter Motor',              'category' => 'electrical', 'icon' => 'bi-power',            'description' => 'Starter motor repair or replacement.', 'display_order' => 17],
            ['name' => 'Air Conditioning',           'category' => 'electrical', 'icon' => 'bi-snow',             'description' => 'A/C regas, compressor repair, and leak detection.', 'display_order' => 18],
            ['name' => 'Electrical Diagnostics',     'category' => 'electrical', 'icon' => 'bi-diagram-3',        'description' => 'Fault code reading, wiring issues, sensor replacement.', 'display_order' => 19],

            // Tyres
            ['name' => 'Tyre Fitting',               'category' => 'tyres',      'icon' => 'bi-circle-fill',      'description' => 'Mobile tyre supply, fitting, and balancing.', 'display_order' => 20],
            ['name' => 'Flat Tyre Repair',           'category' => 'tyres',      'icon' => 'bi-exclamation-circle','description' => 'Puncture repair or spare tyre fitting.', 'display_order' => 21],
            ['name' => 'Wheel Alignment',            'category' => 'tyres',      'icon' => 'bi-bullseye',         'description' => 'Front and rear wheel alignment adjustment.', 'display_order' => 22],

            // Inspections
            ['name' => 'Pre-Purchase Inspection',    'category' => 'inspection', 'icon' => 'bi-search',           'description' => 'Comprehensive vehicle inspection before buying a used car.', 'display_order' => 23],
            ['name' => 'Roadworthy Certificate',     'category' => 'inspection', 'icon' => 'bi-shield-check',     'description' => 'Safety inspection for registration transfer (RWC/Pink Slip).', 'display_order' => 24],
            ['name' => 'Vehicle Diagnostic',         'category' => 'inspection', 'icon' => 'bi-clipboard-data',   'description' => 'Computer diagnostic scan and general health check.', 'display_order' => 25],

            // Other
            ['name' => 'Other / Custom Job',         'category' => 'other',      'icon' => 'bi-three-dots',       'description' => 'Any other mechanical work — describe your issue.', 'display_order' => 99],
        ];

        $now = now();
        foreach ($types as $type) {
            DB::table('mechanic_service_types')->insertOrIgnore(array_merge($type, [
                'slug' => \Illuminate\Support\Str::slug($type['name']),
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    public function down(): void
    {
        DB::table('mechanic_service_types')->truncate();
        DB::table('vehicle_models')->truncate();
        DB::table('vehicle_makes')->truncate();
    }
};
