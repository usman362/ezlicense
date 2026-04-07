<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'Plumber', 'icon' => 'wrench', 'commission_rate' => 10, 'description' => 'Plumbing repairs, installations and emergency call-outs.'],
            ['name' => 'Electrician', 'icon' => 'plug', 'commission_rate' => 10, 'description' => 'Licensed electrical work, wiring, lighting and safety inspections.'],
            ['name' => 'Carpenter', 'icon' => 'hammer', 'commission_rate' => 10, 'description' => 'Custom woodwork, framing, repairs and installations.'],
            ['name' => 'Painter', 'icon' => 'paintbrush', 'commission_rate' => 10, 'description' => 'Interior and exterior painting services.'],
            ['name' => 'Cleaner', 'icon' => 'sparkles', 'commission_rate' => 10, 'description' => 'Residential and commercial cleaning.'],
            ['name' => 'Gardener', 'icon' => 'leaf', 'commission_rate' => 10, 'description' => 'Lawn care, landscaping and garden maintenance.'],
            ['name' => 'Air Conditioning', 'icon' => 'snowflake', 'commission_rate' => 10, 'description' => 'AC installation, repair and servicing.'],
            ['name' => 'Locksmith', 'icon' => 'key', 'commission_rate' => 10, 'description' => '24/7 emergency lockouts, lock changes and key cutting.'],
        ];

        foreach ($items as $i => $item) {
            ServiceCategory::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($item['name'])],
                array_merge($item, ['display_order' => $i, 'is_active' => true])
            );
        }
    }
}
