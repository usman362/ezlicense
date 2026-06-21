<?php

namespace Database\Seeders;

use App\Models\PracticeQuestion;
use Illuminate\Database\Seeder;

class PracticeQuestionSeeder extends Seeder
{
    public function run(): void
    {
        // [question, [options], correctIndex, explanation]
        $general = [
            ['What does a red traffic light mean?', ['Stop and wait behind the stop line', 'Slow down and proceed with caution', 'Stop only if there is traffic', 'Speed up to clear the intersection'], 0, 'A red light always means stop. Wait behind the stop line until the light turns green.'],
            ['When approaching a roundabout, you must give way to:', ['Vehicles on your left', 'Vehicles already in the roundabout', 'Vehicles on your right only', 'No one if you arrived first'], 1, 'You must give way to any vehicle already in the roundabout before entering.'],
            ['What is the default speed limit in a built-up area unless otherwise signed?', ['40 km/h', '50 km/h', '60 km/h', '80 km/h'], 1, 'In built-up areas the default limit is 50 km/h unless a sign shows otherwise.'],
            ['A broken white centre line on the road means:', ['You may overtake if it is safe', 'No overtaking allowed', 'Road works ahead', 'One-way traffic'], 0, 'A broken (dashed) centre line means you may cross to overtake when it is safe and legal.'],
            ['What should you do when you see an amber (yellow) traffic light?', ['Speed up to get through', 'Stop safely if you can', 'Continue at the same speed', 'Flash your headlights'], 1, 'Amber means stop if you can do so safely. Only continue if stopping would be dangerous.'],
            ['At an intersection with a stop sign, you must:', ['Slow down and give way', 'Stop completely, then proceed when safe', 'Stop only if other vehicles are present', 'Give way to vehicles on your left'], 1, 'A stop sign requires a complete stop behind the line before proceeding when safe.'],
            ['A solid white line on your side of the centre line means:', ['You may cross it to overtake', 'You must not cross the line', 'You may turn right across it', 'The road is one-way'], 1, 'You must not cross a solid line on your side of the road, except to enter or leave the road.'],
            ['What does a yellow diamond-shaped sign indicate?', ['A regulatory rule you must obey', 'A warning of conditions ahead', 'A freeway entrance', 'A parking zone'], 1, 'Diamond-shaped yellow signs are warning signs alerting you to hazards or changes ahead.'],
            ['When can you legally make a U-turn?', ['Anywhere, as long as it is quick', 'Only where it is safe and not prohibited by a sign or line', 'Only on freeways', 'Never in a built-up area'], 1, 'You may make a U-turn where you have a clear view and it is not prohibited by a sign or unbroken line.'],
            ['What must you do before changing lanes?', ['Sound your horn', 'Check mirrors, signal, and check your blind spot', 'Speed up immediately', 'Turn on your hazard lights'], 1, 'Always mirror, signal, and head-check your blind spot before changing lanes.'],
            ['Headlights must be turned on:', ['Only on freeways', 'Between sunset and sunrise and in poor visibility', 'Only when it is raining', 'Only in tunnels'], 1, 'Use headlights from sunset to sunrise and whenever visibility is reduced.'],
            ['A flashing yellow arrow at traffic lights means:', ['You must stop', 'You may proceed but must give way', 'The lights are broken', 'Turn around'], 1, 'A flashing yellow arrow means you may proceed in that direction but must give way to pedestrians and other traffic.'],
            ['What is the minimum following distance recommended in good conditions?', ['Half a second', 'At least 2 seconds', '10 metres at any speed', 'One car length'], 1, 'Keep at least a 2-second gap behind the vehicle in front; increase it in poor conditions.'],
            ['When parking on a hill facing downhill, you should turn your wheels:', ['Towards the kerb', 'Away from the kerb', 'Straight ahead', 'It does not matter'], 0, 'Facing downhill, turn your wheels towards the kerb so the car rolls into it if the brakes fail.'],
            ['A green traffic light means:', ['Go if the way is clear', 'Stop', 'Give way to all traffic', 'Prepare to stop'], 0, 'Green means you may proceed if the intersection is clear and it is safe to do so.'],
        ];

        $roadSafety = [
            ['You must keep at least how many seconds gap behind the vehicle in front in good conditions?', ['1 second', '2 seconds', '3 seconds', '5 seconds'], 1, 'A 2-second gap gives you time to react and stop safely in normal conditions.'],
            ['What is the legal blood alcohol limit for learner and provisional drivers?', ['0.02', '0.05', '0.00', '0.08'], 2, 'Learner and P-plate drivers must have a zero (0.00) blood alcohol concentration.'],
            ['When can you use a hand-held mobile phone while driving?', ['When stopped at traffic lights', 'When using hands-free and fully licensed', 'Any time if you are careful', 'Never while the vehicle is moving or stopped in traffic'], 3, 'Hand-held phone use is illegal whenever you are in control of a moving or stationary vehicle in traffic.'],
            ['You want to park your vehicle for a short time at night. You should:', ['Leave your headlights on high beam', 'Park on the footpath', 'Pick a visible position or leave the parking or hazard lights on', 'Park anywhere quickly'], 2, 'At night, park where your vehicle is visible, or use parking/hazard lights so others can see you.'],
            ['If one or two of your wheels run off the edge of the roadway, you should:', ['Brake hard immediately', 'Ease off the accelerator and steer back gently when safe', 'Swerve back onto the road quickly', 'Accelerate to regain control'], 1, 'Ease off the accelerator, keep control, and steer back gently when it is safe.'],
            ['What must you do when you see a school zone sign during its operating times?', ['Maintain the normal speed limit', 'Reduce to the posted school-zone speed limit', 'Sound your horn', 'Stop completely'], 1, 'Obey the reduced school-zone speed limit during the posted times to protect children.'],
            ['When driving in fog, you should:', ['Use high beam headlights', 'Use low beam headlights or fog lights and slow down', 'Turn off all lights', 'Speed up to get through quickly'], 1, 'Use low beam or fog lights in fog; high beam reflects back and reduces visibility.'],
            ['You are approaching a pedestrian crossing and a person is waiting to cross. You should:', ['Speed up to pass before they step out', 'Slow down and be prepared to stop to give way', 'Sound your horn', 'Continue at the same speed'], 1, 'Slow down and give way to pedestrians at or approaching a crossing.'],
            ['Seatbelts must be worn by:', ['Only the driver', 'Only front-seat passengers', 'The driver and all passengers where belts are fitted', 'Only on freeways'], 2, 'The driver and every passenger must wear a fitted seatbelt; the driver is responsible for under-16s.'],
            ['When an emergency vehicle approaches with lights and sirens, you should:', ['Speed up and clear the area', 'Move left and give way when safe', 'Stop immediately in your lane', 'Ignore it if you have right of way'], 1, 'Move to the left and give way to emergency vehicles when it is safe to do so.'],
            ['You should not drive when you are tired because:', ['It uses more fuel', 'Fatigue slows your reactions and impairs judgement', 'It is illegal to drive after 9pm', 'Your insurance is void'], 1, 'Fatigue dramatically slows reaction time and judgement, increasing crash risk.'],
            ['When driving past a cyclist, you must leave a minimum gap of:', ['No gap is required', 'At least 1 metre in 60 km/h zones or less', '5 metres at all times', 'Half a metre'], 1, 'Leave at least 1 metre when passing a cyclist in 60 km/h or lower zones (1.5 m above that).'],
            ['If you are involved in a crash where someone is injured, you must:', ['Leave immediately', 'Stop, help, and report it to police', 'Only exchange details if asked', 'Wait a week then report it'], 1, 'You must stop, give assistance, and report any injury crash to police.'],
            ['In wet conditions you should:', ['Drive at the normal speed limit', 'Increase your following distance and reduce speed', 'Brake harder than usual', 'Use high beam'], 1, 'Wet roads increase stopping distance, so slow down and leave a bigger gap.'],
            ['Before opening your car door into traffic, you should:', ['Open it quickly', 'Check mirrors and over your shoulder for cyclists and traffic', 'Sound your horn', 'Open it only on the kerb side'], 1, 'Always check mirrors and look over your shoulder for cyclists and traffic before opening a door.'],
        ];

        // firstOrCreate: only inserts questions that don't already exist (by section + question).
        // Re-running is a no-op on existing rows — admin edits are never overwritten.
        $order = 0;
        foreach ($general as [$q, $opts, $correct, $exp]) {
            PracticeQuestion::firstOrCreate(
                ['section' => PracticeQuestion::SECTION_GENERAL, 'question' => $q],
                ['options' => $opts, 'correct_index' => $correct, 'explanation' => $exp, 'is_active' => true, 'sort_order' => $order++],
            );
        }
        $order = 0;
        foreach ($roadSafety as [$q, $opts, $correct, $exp]) {
            PracticeQuestion::firstOrCreate(
                ['section' => PracticeQuestion::SECTION_ROAD_SAFETY, 'question' => $q],
                ['options' => $opts, 'correct_index' => $correct, 'explanation' => $exp, 'is_active' => true, 'sort_order' => $order++],
            );
        }
    }
}
