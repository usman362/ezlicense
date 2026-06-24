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
            'testNameShort' => 'DKT',
            'description' => 'The NSW Driver Knowledge Test (DKT) is a computer-based test you must pass to get your learner licence. It covers road rules, traffic signs, and safe driving practices.',
            'aboutText' => 'The NSW DKT consists of 45 questions. You need to answer at least 12 out of 15 general knowledge questions and 29 out of 30 road safety questions correctly. The test is available at Service NSW centres.',
            'passScore' => 80,
            'questionCount' => 45,
            'authority' => 'Service NSW',
            'handbookName' => "Road Users' Handbook",
            'handbookUrl' => 'https://www.transport.nsw.gov.au/operations/roads-and-waterways/safety/handbooks-and-resources/road-users-handbook',
            'whatText' => 'The Driver Knowledge Test is a computer-based test booked through Service NSW. It has 45 multiple-choice questions split into 15 general knowledge questions and 30 road-safety questions, all drawn from the NSW road rules.',
            'passText' => "To pass you need to answer at least 12 of 15 general knowledge questions and 29 of 30 road-safety questions correctly. Study the Road Users' Handbook cover to cover before you sit the test.",
            'handbookText' => "The Road Users' Handbook is published by Transport for NSW. It covers every road rule, signal and safety practice you'll be tested on. Read it in full before booking your DKT.",
        ],
        'vic' => [
            'code' => 'VIC',
            'name' => 'Victoria',
            'testName' => 'Learner Permit Knowledge Test',
            'testNameShort' => 'LPKT',
            'description' => 'The VIC Learner Permit Knowledge Test assesses your understanding of Victorian road rules and safe driving practices.',
            'aboutText' => 'The Victorian learner permit test is a computer-based multiple choice test available at VicRoads customer service centres. You need to correctly answer 78% of questions to pass.',
            'passScore' => 78,
            'questionCount' => 32,
            'authority' => 'VicRoads',
            'handbookName' => 'Road to Solo Driving',
            'handbookUrl' => 'https://www.vicroads.vic.gov.au/licences/your-ls/your-learner-handbooks',
            'whatText' => "The Learner Permit Knowledge Test is a 32-question computer-based test you sit at a VicRoads Customer Service Centre. Questions are drawn from all four chapters of the official 'Road to Solo Driving' handbook.",
            'passText' => "You'll need to answer 25 of 32 questions correctly to pass. Working through every chapter of the 'Road to Solo Driving' handbook is the best way to prepare.",
            'handbookText' => "The 'Road to Solo Driving' handbook is published by VicRoads. It contains every road rule and safe-driving principle you need to know to pass the Learner Permit test and hold a Victorian licence.",
        ],
        'qld' => [
            'code' => 'QLD',
            'name' => 'Queensland',
            'testName' => 'Road Rules Test',
            'testNameShort' => 'RRT',
            'description' => 'The Queensland Road Rules Test checks your knowledge of road rules, road signs, and safe driving practices before you can get your learner licence.',
            'aboutText' => 'The QLD written road rules test is available at Queensland Transport and Main Roads customer service centres. The test is multiple choice and covers general road rules and road safety.',
            'passScore' => 80,
            'questionCount' => 30,
            'authority' => 'TMR Queensland',
            'handbookName' => "Your Keys to Driving in Queensland",
            'handbookUrl' => 'https://www.qld.gov.au/transport/licensing/driver-licensing/applying/learner/get/keys',
            'whatText' => 'The Queensland Road Rules Test is a 30-question test made up of 10 give-way questions and 20 general road-rule questions. Any of these can appear in your real learner test.',
            'passText' => "You need to answer at least 24 of 30 questions correctly (80%). The 'Your Keys to Driving in Queensland' handbook covers every rule you'll be tested on.",
            'handbookText' => "Queensland's official learner handbook is 'Your Keys to Driving in Queensland', published by the Department of Transport and Main Roads. It's the definitive guide to the rules you'll need to pass.",
        ],
        'wa' => [
            'code' => 'WA',
            'name' => 'Western Australia',
            'testName' => 'Road Rules Theory Test',
            'testNameShort' => 'TT',
            'description' => 'The WA Computerised Theory Test assesses your knowledge of road rules and traffic signs in Western Australia.',
            'aboutText' => 'The WA theory test consists of 30 multiple choice questions. You need to correctly answer at least 24 questions (80%) to pass. The test is conducted at Department of Transport licensing centres.',
            'passScore' => 80,
            'questionCount' => 30,
            'authority' => 'Transport WA',
            'handbookName' => 'Drive Safe',
            'handbookUrl' => 'https://www.transport.wa.gov.au/licensing/learn-to-drive.asp',
            'whatText' => "Western Australia's Road Rules Theory Test is a 30-question multiple-choice quiz covering road rules and safe driving practices. Questions are drawn from the 'Drive Safe' handbook.",
            'passText' => "You'll need to answer at least 24 of 30 questions correctly (80%) to pass. Reading the 'Drive Safe' handbook in full is essential preparation.",
            'handbookText' => "'Drive Safe' is published by the WA Department of Transport. It explains every rule, sign and safe-driving practice you'll need for the Theory Test and life on WA roads.",
        ],
        'sa' => [
            'code' => 'SA',
            'name' => 'South Australia',
            'testName' => 'Learner Theory Test',
            'testNameShort' => 'LTT',
            'description' => "The SA Learner's Theory Test assesses your knowledge of road rules and safe driving practices in South Australia.",
            'aboutText' => 'The South Australian learner theory test is a computer-based test available at Service SA centres. It covers road rules, traffic signs, and safe driving practices.',
            'passScore' => 80,
            'questionCount' => 50,
            'authority' => 'Service SA',
            'handbookName' => "The Driver's Handbook",
            'handbookUrl' => 'https://www.sa.gov.au/topics/driving-and-transport/licences/learners-permit',
            'whatText' => "South Australia's Learner Theory Test (LTT) has 50 questions — 8 give-way questions and 42 general knowledge questions — covering road rules, signs and safe-driving practices.",
            'passText' => "You'll need to get the give-way questions all correct, plus at least 35 of 42 general knowledge questions, to pass. 'The Driver's Handbook' is your study guide.",
            'handbookText' => "'The Driver's Handbook' is published by the South Australian Government. It covers every rule and safe-driving principle you'll be tested on for the LTT.",
        ],
        'tas' => [
            'code' => 'TAS',
            'name' => 'Tasmania',
            'testName' => 'Driver Knowledge Test',
            'testNameShort' => 'DKT',
            'description' => 'The Tasmanian Driver Knowledge Test assesses your understanding of road rules and traffic signs before you can obtain a learner licence.',
            'aboutText' => 'The TAS knowledge test is available at Service Tasmania shops. You must correctly answer the required number of questions to pass and obtain your learner licence.',
            'passScore' => 80,
            'questionCount' => 35,
            'authority' => 'Transport Tasmania',
            'handbookName' => 'Driver Handbook',
            'handbookUrl' => 'https://www.transport.tas.gov.au/licensing/getting_a_drivers_licence',
            'whatText' => "Tasmania's Driver Knowledge Test has 35 questions spread across 4 sections — Tasmanian road rules, general road safety, traffic rules and general knowledge.",
            'passText' => 'You need at least 80% across all sections to pass. Study the Tasmanian Driver Handbook in full to cover every topic.',
            'handbookText' => "The Tasmanian Driver Handbook is published by the Department of State Growth. It's the official guide to road rules and safe driving in Tasmania.",
        ],
        'act' => [
            'code' => 'ACT',
            'name' => 'Australian Capital Territory',
            'testName' => 'Road Rules Knowledge Test',
            'testNameShort' => 'RRKT',
            'description' => 'The ACT Road Rules Knowledge Test covers road rules, road signs, and safe driving knowledge required for your ACT learner licence.',
            'aboutText' => 'The ACT road rules test is a computer-based test available at Access Canberra service centres. You must pass the test before you can apply for your learner licence.',
            'passScore' => 80,
            'questionCount' => 35,
            'authority' => 'Access Canberra',
            'handbookName' => 'Road Rules Handbook',
            'handbookUrl' => 'https://www.accesscanberra.act.gov.au/s/article/driver-licences-tab-getting-a-licence',
            'whatText' => "The ACT's Road Rules Knowledge Test contains 35 questions split into 6 sections — from general road rules and give-way laws to how to protect vulnerable road users.",
            'passText' => 'You need at least 80% to pass the RRKT. Read the ACT Road Rules Handbook in full to prepare for every section.',
            'handbookText' => "The ACT Road Rules Handbook is published by Access Canberra. It's the definitive guide to every rule, sign and safe-driving practice in the Territory.",
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
        // Map each state to display-friendly listing data. Question counts come
        // from each state's published handbook (Service NSW, VicRoads, etc.).
        $listing = [
            'nsw' => [
                'name'      => 'New South Wales',
                'testName'  => 'Driver Knowledge Test (DKT)',
                'blurb'     => 'The DKT is the learner test you need to pass to prove your road-rules knowledge. It contains 45 questions split between 15 general knowledge and 30 road-safety questions.',
            ],
            'vic' => [
                'name'      => 'Victoria',
                'testName'  => 'Learner Permit Knowledge Test',
                'blurb'     => 'Victoria\'s Learner Permit Knowledge Test has 32 questions drawn from all four chapters of the official "Road to Solo Driving" handbook.',
            ],
            'qld' => [
                'name'      => 'Queensland',
                'testName'  => 'Road Rules Test',
                'blurb'     => 'The Queensland Road Rules Test mixes 10 give-way questions with 20 general road-rule questions — any of which can be asked when you sit your real learner test.',
            ],
            'wa' => [
                'name'      => 'Western Australia',
                'testName'  => 'Road Rules Theory Test',
                'blurb'     => 'Western Australia\'s theory test is a 30-question multiple-choice quiz based on the "Drive Safe" handbook. You need 80% to pass.',
            ],
            'sa' => [
                'name'      => 'South Australia',
                'testName'  => 'Learner Theory Test (LTT)',
                'blurb'     => 'The LTT covers South Australian road rules across 50 questions — 8 give-way questions and 42 general knowledge questions.',
            ],
            'tas' => [
                'name'      => 'Tasmania',
                'testName'  => 'Driver Knowledge Test (DKT)',
                'blurb'     => 'The Tasmanian DKT has 35 questions across 4 sections — Tasmanian road rules, general road safety, traffic rules and general knowledge.',
            ],
            'act' => [
                'name'      => 'Australian Capital Territory',
                'testName'  => 'Road Rules Knowledge Test (RRKT)',
                'blurb'     => 'The ACT RRKT contains 35 questions split across 6 sections — from general road rules and give-way laws to how to protect vulnerable road users.',
            ],
        ];

        return view('frontend.pages.practice-test', ['listing' => $listing]);
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
            'stateSlug'      => $state,
            'stateCode'      => $config['code'],
            'stateName'      => $config['name'],
            'testName'       => $config['testName'],
            'testNameShort'  => $config['testNameShort'],
            'description'    => $config['description'],
            'aboutText'      => $config['aboutText'],
            'passScore'      => $config['passScore'],
            'questionCount'  => $config['questionCount'],
            'authority'      => $config['authority'],
            'handbookName'   => $config['handbookName'],
            'handbookUrl'    => $config['handbookUrl'],
            'whatText'       => $config['whatText'],
            'passText'       => $config['passText'],
            'handbookText'   => $config['handbookText'],
            'questions'      => $questions,
        ]);
    }

    /**
     * The interactive 2-section practice test.
     *   GET /practice-test/{state}/test
     */
    public function quiz(string $state): View
    {
        $state = strtolower($state);
        if (! isset(self::$states[$state])) {
            abort(404, 'State not found.');
        }
        $config = self::$states[$state];

        $sections = [];
        foreach ([
            \App\Models\PracticeQuestion::SECTION_GENERAL,
            \App\Models\PracticeQuestion::SECTION_ROAD_SAFETY,
        ] as $sectionKey) {
            // Only this state's own questions (plus any marked "All states").
            $qs = \App\Models\PracticeQuestion::active()
                ->forState($state)
                ->where('section', $sectionKey)
                ->inRandomOrder()
                ->get();

            if ($qs->isEmpty()) {
                continue;
            }

            $sections[] = [
                'key'      => $sectionKey,
                'label'    => \App\Models\PracticeQuestion::sectionLabel($sectionKey),
                'passMark' => (int) ceil($qs->count() * 0.8),
                'count'    => $qs->count(),
                'questions' => $qs->map(fn ($q) => [
                    'id'          => $q->id,
                    'question'    => $q->question,
                    'image'       => $q->image_url,
                    'options'     => $q->options,
                    'correct'     => $q->correct_index,
                    'explanation' => $q->explanation,
                ])->values(),
            ];
        }

        return view('frontend.pages.practice-test-quiz', [
            'stateSlug' => $state,
            'stateCode' => $config['code'],
            'stateName' => $config['name'],
            'testName'  => $config['testName'],
            'sections'  => $sections,
            'totalQuestions' => array_sum(array_column($sections, 'count')),
        ]);
    }
}
