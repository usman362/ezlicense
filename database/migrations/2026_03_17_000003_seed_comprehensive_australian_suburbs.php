<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\State;
use App\Models\Suburb;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure NT state exists
        State::firstOrCreate(['code' => 'NT'], ['name' => 'Northern Territory']);

        $suburbsByState = [
            'NSW' => [
                // Sydney CBD & Inner
                ['Sydney','2000'],['Surry Hills','2010'],['Darlinghurst','2010'],['Pyrmont','2009'],['Ultimo','2007'],['Haymarket','2000'],['The Rocks','2000'],['Millers Point','2000'],
                // Eastern Suburbs
                ['Bondi','2026'],['Bondi Junction','2022'],['Bronte','2024'],['Coogee','2034'],['Maroubra','2035'],['Randwick','2031'],['Kensington','2033'],['Double Bay','2028'],['Rose Bay','2029'],['Vaucluse','2030'],['Paddington','2021'],['Woollahra','2025'],
                // Inner West
                ['Newtown','2042'],['Marrickville','2204'],['Enmore','2042'],['Petersham','2049'],['Leichhardt','2040'],['Balmain','2041'],['Rozelle','2039'],['Annandale','2038'],['Glebe','2037'],['Camperdown','2050'],['Summer Hill','2130'],['Ashfield','2131'],['Burwood','2134'],['Strathfield','2135'],['Concord','2137'],['Five Dock','2046'],['Drummoyne','2047'],
                // North Shore
                ['Chatswood','2067'],['North Sydney','2060'],['Mosman','2088'],['Neutral Bay','2089'],['Cremorne','2090'],['Crows Nest','2065'],['St Leonards','2065'],['Artarmon','2064'],['Lane Cove','2066'],['Willoughby','2068'],['Roseville','2069'],['Lindfield','2070'],['Killara','2071'],['Gordon','2072'],['Pymble','2073'],['Turramurra','2074'],['Wahroonga','2076'],['Hornsby','2077'],
                // Northern Beaches
                ['Manly','2095'],['Dee Why','2099'],['Brookvale','2100'],['Frenchs Forest','2086'],['Mona Vale','2103'],['Newport','2106'],['Avalon','2107'],['Palm Beach','2108'],['Narrabeen','2101'],['Collaroy','2097'],['Freshwater','2096'],
                // Western Sydney
                ['Parramatta','2150'],['Blacktown','2148'],['Penrith','2750'],['Liverpool','2170'],['Campbelltown','2560'],['Bankstown','2200'],['Auburn','2144'],['Granville','2142'],['Merrylands','2160'],['Fairfield','2165'],['Cabramatta','2166'],['Wetherill Park','2164'],['Mount Druitt','2770'],['Rooty Hill','2766'],['Rouse Hill','2155'],['Castle Hill','2154'],['Baulkham Hills','2153'],['Kellyville','2155'],['Bella Vista','2153'],['Epping','2121'],['Eastwood','2122'],['Ryde','2112'],['Macquarie Park','2113'],['Marsfield','2122'],['Carlingford','2118'],['Beecroft','2119'],
                // South/South West Sydney
                ['Hurstville','2220'],['Kogarah','2217'],['Rockdale','2216'],['Sans Souci','2219'],['Miranda','2228'],['Caringbah','2229'],['Cronulla','2230'],['Sutherland','2232'],['Menai','2234'],['Engadine','2233'],['Penshurst','2222'],['Revesby','2212'],['Padstow','2211'],['East Hills','2213'],['Ingleburn','2565'],['Leppington','2179'],['Narellan','2567'],['Camden','2570'],
                // Regional NSW
                ['Newcastle','2300'],['Wollongong','2500'],['Central Coast','2250'],['Gosford','2250'],['Maitland','2320'],['Cessnock','2325'],['Tamworth','2340'],['Orange','2800'],['Bathurst','2795'],['Dubbo','2830'],['Wagga Wagga','2650'],['Albury','2640'],['Coffs Harbour','2450'],['Port Macquarie','2444'],['Lismore','2480'],['Byron Bay','2481'],['Tweed Heads','2485'],['Armidale','2350'],['Nowra','2541'],['Kiama','2533'],['Berry','2535'],['Shellharbour','2529'],['Lake Macquarie','2283'],['Charlestown','2290'],['Belmont','2280'],['Broken Hill','2880'],['Griffith','2680'],['Queanbeyan','2620'],
            ],
            'VIC' => [
                // Melbourne CBD & Inner
                ['Melbourne','3000'],['Southbank','3006'],['Docklands','3008'],['South Melbourne','3205'],['Port Melbourne','3207'],['West Melbourne','3003'],
                // Inner East
                ['Richmond','3121'],['Collingwood','3066'],['Fitzroy','3065'],['Carlton','3053'],['Brunswick','3056'],['Northcote','3070'],['Thornbury','3071'],['Preston','3072'],['Reservoir','3073'],['Heidelberg','3084'],['Ivanhoe','3079'],
                // East
                ['Hawthorn','3122'],['Camberwell','3124'],['Kew','3101'],['Balwyn','3103'],['Box Hill','3128'],['Doncaster','3108'],['Templestowe','3106'],['Ringwood','3134'],['Croydon','3136'],['Nunawading','3131'],['Blackburn','3130'],['Mitcham','3132'],['Burwood','3125'],['Glen Waverley','3150'],['Mount Waverley','3149'],['Oakleigh','3166'],['Clayton','3168'],['Monash','3152'],['Rowville','3178'],['Knox','3152'],['Ferntree Gully','3156'],['Bayswater','3153'],['Lilydale','3140'],['Mooroolbark','3138'],
                // South East
                ['St Kilda','3182'],['Brighton','3186'],['Elsternwick','3185'],['Caulfield','3162'],['Malvern','3144'],['Prahran','3181'],['South Yarra','3141'],['Toorak','3142'],['Bentleigh','3204'],['Moorabbin','3189'],['Cheltenham','3192'],['Mentone','3194'],['Mordialloc','3195'],['Chelsea','3196'],['Frankston','3199'],['Mornington','3931'],['Dandenong','3175'],['Noble Park','3174'],['Springvale','3171'],['Keysborough','3173'],['Cranbourne','3977'],['Berwick','3806'],['Narre Warren','3805'],['Pakenham','3810'],['Officer','3809'],['Clyde','3978'],
                // West
                ['Footscray','3011'],['Yarraville','3013'],['Williamstown','3016'],['Werribee','3030'],['Hoppers Crossing','3029'],['Point Cook','3030'],['Tarneit','3029'],['Wyndham Vale','3024'],['Truganina','3029'],['Sunshine','3020'],['St Albans','3021'],['Deer Park','3023'],['Caroline Springs','3023'],['Melton','3337'],['Bacchus Marsh','3340'],
                // North
                ['Coburg','3058'],['Pascoe Vale','3044'],['Essendon','3040'],['Moonee Ponds','3039'],['Broadmeadows','3047'],['Craigieburn','3064'],['Sunbury','3429'],['Epping','3076'],['South Morang','3752'],['Mill Park','3082'],['Bundoora','3083'],['Greensborough','3088'],['Eltham','3095'],['Diamond Creek','3089'],['Whittlesea','3757'],['Doreen','3754'],['Mernda','3754'],
                // Regional VIC
                ['Geelong','3220'],['Ballarat','3350'],['Bendigo','3550'],['Shepparton','3630'],['Warrnambool','3280'],['Mildura','3500'],['Traralgon','3844'],['Sale','3850'],['Wonthaggi','3995'],['Horsham','3400'],['Wangaratta','3677'],['Wodonga','3690'],['Echuca','3564'],['Swan Hill','3585'],['Castlemaine','3450'],['Daylesford','3460'],
            ],
            'QLD' => [
                // Brisbane CBD & Inner
                ['Brisbane City','4000'],['South Brisbane','4101'],['West End','4101'],['Fortitude Valley','4006'],['New Farm','4005'],['Kangaroo Point','4169'],['Spring Hill','4000'],['Woolloongabba','4102'],
                // Inner South
                ['Annerley','4103'],['Tarragindi','4121'],['Holland Park','4121'],['Mount Gravatt','4122'],['Upper Mount Gravatt','4122'],['Sunnybank','4109'],['Sunnybank Hills','4109'],['Eight Mile Plains','4113'],['Carindale','4152'],['Coorparoo','4151'],['Camp Hill','4152'],
                // South
                ['Logan Central','4114'],['Springwood','4127'],['Beenleigh','4207'],['Browns Plains','4118'],['Marsden','4132'],['Waterford','4133'],['Loganholme','4129'],
                // North
                ['Chermside','4032'],['Nundah','4012'],['Kedron','4031'],['Stafford','4053'],['Aspley','4034'],['Zillmere','4034'],['Bracken Ridge','4017'],['Sandgate','4017'],['Shorncliffe','4017'],['Northgate','4013'],['Banyo','4014'],['Nudgee','4014'],
                // North West
                ['The Gap','4061'],['Ashgrove','4060'],['Bardon','4065'],['Paddington','4064'],['Red Hill','4059'],['Kelvin Grove','4059'],['Mitchelton','4053'],['Everton Park','4053'],['Ferny Hills','4055'],['Arana Hills','4054'],
                // West
                ['Indooroopilly','4068'],['St Lucia','4067'],['Toowong','4066'],['Taringa','4068'],['Chapel Hill','4069'],['Kenmore','4069'],['Fig Tree Pocket','4069'],['Jindalee','4074'],['Ipswich','4305'],['Springfield','4300'],
                // East/Bayside
                ['Wynnum','4178'],['Manly','4179'],['Cleveland','4163'],['Capalaba','4157'],['Redland Bay','4165'],['Victoria Point','4165'],
                // North Side/Moreton Bay
                ['North Lakes','4509'],['Caboolture','4510'],['Redcliffe','4020'],['Petrie','4502'],['Strathpine','4500'],['Pine Rivers','4500'],['Deception Bay','4508'],['Bribie Island','4507'],
                // Gold Coast
                ['Surfers Paradise','4217'],['Southport','4215'],['Broadbeach','4218'],['Burleigh Heads','4220'],['Coolangatta','4225'],['Robina','4226'],['Nerang','4211'],['Coomera','4209'],['Helensvale','4212'],['Runaway Bay','4216'],['Labrador','4215'],['Palm Beach','4221'],['Varsity Lakes','4227'],['Mudgeeraba','4213'],['Ormeau','4208'],
                // Sunshine Coast
                ['Maroochydore','4558'],['Caloundra','4551'],['Noosa Heads','4567'],['Mooloolaba','4557'],['Nambour','4560'],['Buderim','4556'],['Sippy Downs','4556'],['Kawana','4575'],
                // Regional QLD
                ['Toowoomba','4350'],['Cairns','4870'],['Townsville','4810'],['Mackay','4740'],['Rockhampton','4700'],['Bundaberg','4670'],['Gladstone','4680'],['Hervey Bay','4655'],['Maryborough','4650'],['Mount Isa','4825'],
            ],
            'WA' => [
                // Perth CBD & Inner
                ['Perth','6000'],['East Perth','6004'],['West Perth','6005'],['Northbridge','6003'],['Subiaco','6008'],['Leederville','6007'],
                // North
                ['Joondalup','6027'],['Wanneroo','6065'],['Stirling','6021'],['Scarborough','6019'],['Osborne Park','6017'],['Innaloo','6018'],['Dianella','6059'],['Morley','6062'],['Bayswater','6053'],['Bassendean','6054'],['Midland','6056'],['Ellenbrook','6069'],['Clarkson','6030'],['Butler','6036'],['Alkimos','6038'],['Yanchep','6035'],['Two Rocks','6037'],
                // South
                ['Fremantle','6160'],['Rockingham','6168'],['Mandurah','6210'],['Victoria Park','6100'],['South Perth','6151'],['Como','6152'],['Applecross','6153'],['Booragoon','6154'],['Canning Vale','6155'],['Armadale','6112'],['Gosnells','6110'],['Maddington','6109'],['Thornlie','6108'],['Riverton','6148'],['Willetton','6155'],['Bull Creek','6149'],['Murdoch','6150'],['Cockburn','6164'],
                // East
                ['Belmont','6104'],['Kalamunda','6076'],['Mundaring','6073'],['Forrestfield','6058'],['High Wycombe','6057'],
                // Regional WA
                ['Bunbury','6230'],['Geraldton','6530'],['Kalgoorlie','6430'],['Albany','6330'],['Broome','6725'],['Karratha','6714'],['Port Hedland','6721'],['Busselton','6280'],['Margaret River','6285'],
            ],
            'SA' => [
                // Adelaide CBD & Inner
                ['Adelaide','5000'],['North Adelaide','5006'],['Kent Town','5067'],['Norwood','5067'],['Kensington','5068'],
                // East
                ['Burnside','5066'],['Magill','5072'],['Campbelltown','5074'],['Paradise','5075'],['Modbury','5092'],['Tea Tree Gully','5091'],['Golden Grove','5125'],
                // North
                ['Prospect','5082'],['Enfield','5085'],['Mawson Lakes','5095'],['Salisbury','5108'],['Elizabeth','5112'],['Gawler','5118'],['Smithfield','5114'],['Playford','5114'],
                // West
                ['Henley Beach','5022'],['Glenelg','5045'],['Brighton','5048'],['Marion','5043'],['Port Adelaide','5015'],['West Lakes','5021'],['Semaphore','5019'],
                // South
                ['Mitcham','5062'],['Unley','5061'],['Goodwood','5034'],['Morphett Vale','5162'],['Noarlunga','5168'],['Seaford','5169'],['Aldinga','5173'],['Mount Barker','5251'],['Victor Harbor','5211'],['Murray Bridge','5253'],
                // Regional SA
                ['Mount Gambier','5290'],['Whyalla','5600'],['Port Augusta','5700'],['Port Lincoln','5606'],['Port Pirie','5540'],['Berri','5343'],['Renmark','5341'],
            ],
            'TAS' => [
                // Hobart & South
                ['Hobart','7000'],['Sandy Bay','7005'],['Glenorchy','7010'],['Moonah','7009'],['New Town','7008'],['Kingston','7050'],['Bellerive','7018'],['Rosny Park','7018'],['Howrah','7018'],['Clarence','7018'],['Brighton','7030'],['Sorell','7172'],
                // North
                ['Launceston','7250'],['Kings Meadows','7249'],['Mowbray','7248'],['Newnham','7248'],['Riverside','7250'],['Invermay','7248'],
                // North West
                ['Devonport','7310'],['Burnie','7320'],['Ulverstone','7315'],['Wynyard','7325'],
            ],
            'ACT' => [
                // Canberra regions
                ['Canberra','2600'],['Civic','2601'],['Barton','2600'],['Kingston','2604'],['Manuka','2603'],['Griffith','2603'],['Red Hill','2603'],['Deakin','2600'],
                ['Belconnen','2617'],['Bruce','2617'],['Macquarie','2614'],['Aranda','2614'],['Cook','2614'],['Weetangera','2614'],
                ['Woden','2606'],['Phillip','2606'],['Curtin','2605'],['Garran','2605'],['Hughes','2605'],['Lyons','2606'],
                ['Tuggeranong','2900'],['Kambah','2902'],['Wanniassa','2903'],['Greenway','2900'],['Bonython','2905'],['Gordon','2906'],['Conder','2906'],
                ['Gungahlin','2912'],['Harrison','2914'],['Franklin','2913'],['Amaroo','2914'],['Casey','2913'],['Ngunnawal','2913'],
                ['Weston Creek','2611'],['Molonglo Valley','2611'],['Fyshwick','2609'],['Mitchell','2911'],['Queanbeyan','2620'],
            ],
            'NT' => [
                ['Darwin','0800'],['Palmerston','0830'],['Casuarina','0810'],['Stuart Park','0820'],['Nightcliff','0810'],['Rapid Creek','0810'],['Fannie Bay','0820'],['The Gardens','0820'],['Parap','0820'],['Woolner','0820'],
                ['Alice Springs','0870'],['Katherine','0850'],['Tennant Creek','0860'],['Nhulunbuy','0880'],
            ],
        ];

        foreach ($suburbsByState as $stateCode => $suburbs) {
            $state = State::where('code', $stateCode)->first();
            if (! $state) continue;

            foreach ($suburbs as [$name, $postcode]) {
                Suburb::firstOrCreate(
                    ['state_id' => $state->id, 'name' => $name, 'postcode' => $postcode],
                );
            }
        }
    }

    public function down(): void
    {
        // Keep suburbs — no rollback for data seeding
    }
};
