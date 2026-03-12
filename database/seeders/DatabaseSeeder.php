<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            StateSeeder::class,
            SuburbSeeder::class,
            SiteSettingsSeeder::class,
            DemoInstructorSeeder::class,
            DemoLearnerSeeder::class,
        ]);

        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@ezlicence.com.au'],
            [
                'name' => 'Admin',
                'first_name' => 'Site',
                'last_name' => 'Admin',
                'password' => bcrypt('admin123'),
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
            ]
        );

        // Test learner
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'role' => User::ROLE_LEARNER,
                'is_active' => true,
            ]
        );
    }
}
