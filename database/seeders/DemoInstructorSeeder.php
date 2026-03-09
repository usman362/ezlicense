<?php

namespace Database\Seeders;

use App\Models\InstructorProfile;
use App\Models\InstructorAvailabilitySlot;
use App\Models\State;
use App\Models\Suburb;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoInstructorSeeder extends Seeder
{
    /**
     * Create a demo instructor so you can test search and booking flow.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'instructor@demo.com'],
            [
                'name' => 'Demo Instructor',
                'password' => bcrypt('password'),
                'role' => User::ROLE_INSTRUCTOR,
                'phone' => '0400000000',
            ]
        );

        $profile = InstructorProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'bio' => 'Friendly driving instructor with 10+ years experience. Patient and calm.',
                'transmission' => 'both',
                'vehicle_make' => 'Toyota',
                'vehicle_model' => 'Corolla',
                'vehicle_year' => 2022,
                'vehicle_safety_rating' => '5 star',
                'lesson_price' => 65,
                'test_package_price' => 150,
                'lesson_duration_minutes' => 60,
                'offers_test_package' => true,
                'is_active' => true,
            ]
        );

        // Service areas: attach first 5 NSW suburbs
        $nsw = State::where('code', 'NSW')->first();
        if ($nsw) {
            $suburbIds = Suburb::where('state_id', $nsw->id)->limit(5)->pluck('id');
            $profile->serviceAreas()->sync($suburbIds);
        }

        // Availability: Mon–Fri 9:00–17:00
        if ($profile->availabilitySlots()->count() === 0) {
            foreach ([1, 2, 3, 4, 5] as $day) { // Mon=1 ... Fri=5
                InstructorAvailabilitySlot::create([
                    'instructor_profile_id' => $profile->id,
                    'day_of_week' => $day,
                    'start_time' => '09:00',
                    'end_time' => '17:00',
                ]);
            }
        }

        $this->command->info('Demo instructor: instructor@demo.com / password');
    }
}
