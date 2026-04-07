<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PracticeTestController extends Controller
{
    /**
     * State-specific test configurations.
     */
    private static array $states = [
        'nsw' => [
            'code' => 'NSW',
            'name' => 'New South Wales',
            'testName' => 'Driver Knowledge Test',
            'description' => 'The NSW Driver Knowledge Test (DKT) is a computer-based test you must pass to get your learner licence. It covers road rules, traffic signs, and safe driving practices.',
            'aboutText' => 'The NSW DKT consists of 45 questions. You need to answer at least 12 out of 15 general knowledge questions and 29 out of 30 road safety questions correctly. The test is available at Service NSW centres.',
            'passScore' => 80,
        ],
        'vic' => [
            'code' => 'VIC',
            'name' => 'Victoria',
            'testName' => 'Learner Permit Knowledge Test',
            'description' => 'The VIC Learner Permit Knowledge Test assesses your understanding of Victorian road rules and safe driving practices.',
            'aboutText' => 'The Victorian learner permit test is a computer-based multiple choice test available at VicRoads customer service centres. You need to correctly answer 78% of questions to pass.',
            'passScore' => 78,
        ],
        'qld' => [
            'code' => 'QLD',
            'name' => 'Queensland',
            'testName' => 'Road Rules Test',
            'description' => 'The Queensland Road Rules Test checks your knowledge of road rules, road signs, and safe driving practices before you can get your learner licence.',
            'aboutText' => 'The QLD written road rules test is available at Queensland Transport and Main Roads customer service centres. The test is multiple choice and covers general road rules and road safety.',
            'passScore' => 80,
        ],
        'wa' => [
            'code' => 'WA',
            'name' => 'Western Australia',
            'testName' => 'Road Rules Theory Test',
            'description' => 'The WA Computerised Theory Test assesses your knowledge of road rules and traffic signs in Western Australia.',
            'aboutText' => 'The WA theory test consists of 30 multiple choice questions. You need to correctly answer at least 24 questions (80%) to pass. The test is conducted at Department of Transport licensing centres.',
            'passScore' => 80,
        ],
        'sa' => [
            'code' => 'SA',
            'name' => 'South Australia',
            'testName' => 'Learner Theory Test',
            'description' => 'The SA Learner\'s Theory Test assesses your knowledge of road rules and safe driving practices in South Australia.',
            'aboutText' => 'The South Australian learner theory test is a computer-based test available at Service SA centres. It covers road rules, traffic signs, and safe driving practices.',
            'passScore' => 80,
        ],
        'tas' => [
            'code' => 'TAS',
            'name' => 'Tasmania',
            'testName' => 'Driver Knowledge Test',
            'description' => 'The Tasmanian Driver Knowledge Test assesses your understanding of road rules and traffic signs before you can obtain a learner licence.',
            'aboutText' => 'The TAS knowledge test is available at Service Tasmania shops. You must correctly answer the required number of questions to pass and obtain your learner licence.',
            'passScore' => 80,
        ],
        'act' => [
            'code' => 'ACT',
            'name' => 'Australian Capital Territory',
            'testName' => 'Road Rules Knowledge Test',
            'description' => 'The ACT Road Rules Knowledge Test covers road rules, road signs, and safe driving knowledge required for your ACT learner licence.',
            'aboutText' => 'The ACT road rules test is a computer-based test available at Access Canberra service centres. You must pass the test before you can apply for your learner licence.',
            'passScore' => 80,
        ],
    ];

    /**
     * Sample questions per state (in production, these would come from a database).
     */
    private static function questionsForState(string $stateCode): array
    {
        $common = [
            ['question' => 'What does a red traffic light mean?', 'options' => ['Stop and wait behind the stop line', 'Slow down and proceed with caution', 'Stop only if there is traffic', 'Speed up to clear the intersection'], 'correct' => 0],
            ['question' => 'When approaching a roundabout, you must give way to:', 'options' => ['Vehicles on your left', 'Vehicles already in the roundabout', 'Vehicles on your right only', 'No one if you arrived first'], 'correct' => 1],
            ['question' => 'What is the default speed limit in a built-up area unless otherwise signed?', 'options' => ['40 km/h', '50 km/h', '60 km/h', '80 km/h'], 'correct' => 1],
            ['question' => 'You must keep at least how many seconds gap behind the vehicle in front?', 'options' => ['1 second', '2 seconds', '3 seconds', '5 seconds'], 'correct' => 2],
            ['question' => 'A broken white centre line on the road means:', 'options' => ['You may overtake if safe', 'No overtaking allowed', 'Road works ahead', 'One way traffic'], 'correct' => 0],
            ['question' => 'What should you do when you see an amber (yellow) traffic light?', 'options' => ['Speed up to get through', 'Stop safely if you can', 'Continue at the same speed', 'Flash your headlights'], 'correct' => 1],
            ['question' => 'At an intersection with a stop sign, you must:', 'options' => ['Slow down and give way', 'Stop completely, then proceed when safe', 'Stop only if there are other vehicles', 'Give way to vehicles on your left'], 'correct' => 1],
            ['question' => 'What is the legal blood alcohol limit for learner drivers?', 'options' => ['0.02', '0.05', '0.00', '0.08'], 'correct' => 2],
            ['question' => 'When can you use a mobile phone while driving?', 'options' => ['When stopped at traffic lights', 'When using hands-free and fully licensed', 'Any time if you are careful', 'Never under any circumstances'], 'correct' => 1],
            ['question' => 'A solid white line on your side of the centre line means:', 'options' => ['You may cross to overtake', 'You must not cross the line', 'You may turn right across it', 'The road is one way'], 'correct' => 1],
        ];

        return $common;
    }

    public function index(): View
    {
        return view('frontend.pages.practice-test');
    }

    public function state(string $state): View
    {
        $state = strtolower($state);

        if (! isset(self::$states[$state])) {
            abort(404, 'State not found.');
        }

        $config = self::$states[$state];
        $questions = self::questionsForState($state);

        return view('frontend.pages.practice-test-state', [
            'stateCode' => $config['code'],
            'stateName' => $config['name'],
            'testName' => $config['testName'],
            'description' => $config['description'],
            'aboutText' => $config['aboutText'],
            'passScore' => $config['passScore'],
            'questionCount' => count($questions),
            'questions' => $questions,
        ]);
    }
}
