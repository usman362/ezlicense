<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\InstructorProfile;
use App\Models\Suburb;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoLearnerSeeder extends Seeder
{
    /**
     * Create a demo learner with bookings for the demo instructor (for testing Learners page).
     */
    public function run(): void
    {
        $instructor = User::where('email', 'instructor@demo.com')->first();
        if (! $instructor) {
            $this->command->warn('Demo instructor not found. Run DemoInstructorSeeder first.');

            return;
        }

        $learner = User::firstOrCreate(
            ['email' => 'learner@demo.com'],
            [
                'name' => 'Aaron G.',
                'password' => bcrypt('password'),
                'role' => User::ROLE_LEARNER,
                'phone' => '0412345678',
            ]
        );

        $profile = InstructorProfile::where('user_id', $instructor->id)->first();
        $suburbId = $profile
            ? $profile->serviceAreas()->first()?->id
            : Suburb::whereHas('state', fn ($q) => $q->where('code', 'NSW'))->value('id');

        if (! $suburbId) {
            $this->command->warn('No suburb found for booking.');

            return;
        }

        // One completed booking (shows hours completed)
        if (Booking::where('instructor_id', $instructor->id)->where('learner_id', $learner->id)->where('status', Booking::STATUS_COMPLETED)->count() === 0) {
            Booking::create([
                'learner_id' => $learner->id,
                'instructor_id' => $instructor->id,
                'suburb_id' => $suburbId,
                'type' => Booking::TYPE_LESSON,
                'transmission' => 'auto',
                'scheduled_at' => now()->subDays(7),
                'duration_minutes' => 60,
                'amount' => 65,
                'status' => Booking::STATUS_COMPLETED,
            ]);
        }

        // One upcoming confirmed booking (shows in Upcoming)
        if (Booking::where('instructor_id', $instructor->id)->where('learner_id', $learner->id)->where('status', Booking::STATUS_CONFIRMED)->where('scheduled_at', '>', now())->count() === 0) {
            Booking::create([
                'learner_id' => $learner->id,
                'instructor_id' => $instructor->id,
                'suburb_id' => $suburbId,
                'type' => Booking::TYPE_LESSON,
                'transmission' => 'auto',
                'scheduled_at' => now()->addDays(2)->setTime(10, 0),
                'duration_minutes' => 60,
                'amount' => 65,
                'status' => Booking::STATUS_CONFIRMED,
            ]);
        }

        $this->command->info('Demo learner: learner@demo.com / password (linked to demo instructor)');
    }
}
