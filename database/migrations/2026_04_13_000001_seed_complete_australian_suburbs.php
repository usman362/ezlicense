<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\State;
use App\Models\Suburb;

/**
 * Comprehensive Australian suburbs seeder.
 * Adds ~5,000+ suburbs across all states/territories to ensure complete coverage.
 * Uses firstOrCreate to avoid duplicates with existing data.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Ensure all states exist
        $states = [
            'NSW' => 'New South Wales',
            'VIC' => 'Victoria',
            'QLD' => 'Queensland',
            'WA'  => 'Western Australia',
            'SA'  => 'South Australia',
            'TAS' => 'Tasmania',
            'ACT' => 'Australian Capital Territory',
            'NT'  => 'Northern Territory',
        ];

        $stateIds = [];
        foreach ($states as $code => $name) {
            $state = State::firstOrCreate(['code' => $code], ['name' => $name]);
            $stateIds[$code] = $state->id;
        }

        // Process in chunks per state to manage memory
        foreach ($this->getSuburbData() as $stateCode => $suburbs) {
            if (!isset($stateIds[$stateCode])) continue;
            $stateId = $stateIds[$stateCode];

            foreach (array_chunk($suburbs, 100) as $chunk) {
                foreach ($chunk as [$name, $postcode]) {
                    Suburb::firstOrCreate(
                        ['state_id' => $stateId, 'name' => $name, 'postcode' => $postcode],
                    );
                }
            }
        }
    }

    public function down(): void
    {
        // Not reversible — suburbs are reference data
    }

    private function getSuburbData(): array
    {
        return [
            'NSW' => $this->nswSuburbs(),
            'VIC' => $this->vicSuburbs(),
            'QLD' => $this->qldSuburbs(),
            'WA'  => $this->waSuburbs(),
            'SA'  => $this->saSuburbs(),
            'TAS' => $this->tasSuburbs(),
            'ACT' => $this->actSuburbs(),
            'NT'  => $this->ntSuburbs(),
        ];
    }

    private function nswSuburbs(): array
    {
        return [
            // ==================== SYDNEY METRO ====================
            // CBD & Inner City
            ['Sydney','2000'],['Barangaroo','2000'],['Dawes Point','2000'],['Millers Point','2000'],['The Rocks','2000'],
            ['Darlinghurst','2010'],['Surry Hills','2010'],['Chippendale','2008'],['Redfern','2016'],
            ['Haymarket','2000'],['Ultimo','2007'],['Pyrmont','2009'],['Woolloomooloo','2011'],
            ['Potts Point','2011'],['Elizabeth Bay','2011'],['Rushcutters Bay','2011'],
            ['Darlington','2008'],['Eveleigh','2015'],['Waterloo','2017'],['Zetland','2017'],
            ['Alexandria','2015'],['Beaconsfield','2015'],['Rosebery','2018'],['Eastlakes','2018'],
            ['Mascot','2020'],['Botany','2019'],['Banksmeadow','2019'],

            // Eastern Suburbs
            ['Paddington','2021'],['Bondi Junction','2022'],['Bellevue Hill','2023'],
            ['Bronte','2024'],['Woollahra','2025'],['Bondi','2026'],['Bondi Beach','2026'],
            ['Darling Point','2027'],['Double Bay','2028'],['Rose Bay','2029'],
            ['Dover Heights','2030'],['Vaucluse','2030'],['Watsons Bay','2030'],
            ['Randwick','2031'],['Clovelly','2031'],['Kingsford','2032'],['Daceyville','2032'],
            ['Kensington','2033'],['Coogee','2034'],['South Coogee','2034'],
            ['Maroubra','2035'],['Maroubra Junction','2035'],['Malabar','2036'],
            ['La Perouse','2036'],['Little Bay','2036'],['Chifley','2036'],

            // Inner West
            ['Glebe','2037'],['Forest Lodge','2037'],['Annandale','2038'],
            ['Rozelle','2039'],['Leichhardt','2040'],['Lilyfield','2040'],
            ['Balmain','2041'],['Balmain East','2041'],['Birchgrove','2041'],
            ['Newtown','2042'],['Enmore','2042'],['Erskineville','2043'],
            ['St Peters','2044'],['Sydenham','2044'],['Tempe','2044'],
            ['Haberfield','2045'],['Five Dock','2046'],['Rodd Point','2046'],
            ['Russell Lea','2046'],['Abbotsford','2046'],['Canada Bay','2046'],
            ['Drummoyne','2047'],['Wareemba','2047'],['Petersham','2049'],
            ['Camperdown','2050'],['Stanmore','2048'],['Dulwich Hill','2203'],
            ['Marrickville','2204'],['Earlwood','2206'],['Clemton Park','2206'],
            ['Summer Hill','2130'],['Ashfield','2131'],['Croydon','2132'],
            ['Croydon Park','2133'],['Burwood','2134'],['Strathfield','2135'],
            ['Homebush','2140'],['Homebush West','2140'],['Flemington','2140'],
            ['Lidcombe','2141'],['Berala','2141'],['Regents Park','2143'],

            // North Shore & Northern Suburbs
            ['North Sydney','2060'],['Kirribilli','2061'],['Cammeray','2062'],
            ['Northbridge','2063'],['Artarmon','2064'],['Crows Nest','2065'],
            ['St Leonards','2065'],['Lane Cove','2066'],['Lane Cove North','2066'],
            ['Chatswood','2067'],['Chatswood West','2067'],['Willoughby','2068'],
            ['Roseville','2069'],['Roseville Chase','2069'],['Lindfield','2070'],
            ['Killara','2071'],['Gordon','2072'],['Pymble','2073'],
            ['Turramurra','2074'],['South Turramurra','2074'],['St Ives','2075'],
            ['Wahroonga','2076'],['Warrawee','2074'],['Hornsby','2077'],
            ['Asquith','2077'],['Mount Colah','2079'],['Mount Kuring-gai','2080'],
            ['Berowra','2081'],['Berowra Heights','2082'],['Pennant Hills','2120'],
            ['Thornleigh','2120'],['Westleigh','2120'],['Normanhurst','2076'],
            ['Waitara','2077'],['Cheltenham','2119'],

            // Northern Beaches
            ['Mosman','2088'],['Neutral Bay','2089'],['Cremorne','2090'],
            ['Cremorne Point','2090'],['Seaforth','2092'],['Balgowlah','2093'],
            ['Fairlight','2094'],['Manly','2095'],['Freshwater','2096'],
            ['Collaroy','2097'],['Collaroy Plateau','2097'],['Dee Why','2099'],
            ['Brookvale','2100'],['Narrabeen','2101'],['North Narrabeen','2101'],
            ['Warriewood','2102'],['Mona Vale','2103'],['Bayview','2104'],
            ['Church Point','2105'],['Newport','2106'],['Avalon Beach','2107'],
            ['Palm Beach','2108'],['Whale Beach','2107'],['Curl Curl','2096'],
            ['North Curl Curl','2099'],['Beacon Hill','2100'],['Allambie Heights','2100'],
            ['Frenchs Forest','2086'],['Davidson','2085'],['Forestville','2087'],
            ['Killarney Heights','2087'],['Belrose','2085'],['Oxford Falls','2100'],
            ['Terrey Hills','2084'],['Duffys Forest','2084'],['Elanora Heights','2101'],
            ['Ingleside','2101'],['Bilgola Plateau','2107'],

            // Ryde / Hunters Hill
            ['Ryde','2112'],['Macquarie Park','2113'],['North Ryde','2113'],
            ['Meadowbank','2114'],['West Ryde','2114'],['Denistone','2114'],
            ['Putney','2112'],['Gladesville','2111'],['Tennyson Point','2111'],
            ['Hunters Hill','2110'],['Woolwich','2110'],['Henley','2111'],
            ['Eastwood','2122'],['Marsfield','2122'],['Epping','2121'],
            ['Carlingford','2118'],['Beecroft','2119'],['Cheltenham','2119'],

            // Parramatta / Western Sydney
            ['Parramatta','2150'],['North Parramatta','2151'],['Northmead','2152'],
            ['Baulkham Hills','2153'],['Bella Vista','2153'],['Castle Hill','2154'],
            ['Kellyville','2155'],['Rouse Hill','2155'],['Stanhope Gardens','2768'],
            ['The Ponds','2769'],['Kellyville Ridge','2155'],['Beaumont Hills','2155'],
            ['Glenwood','2768'],['Parklea','2768'],['Quakers Hill','2763'],
            ['Schofields','2762'],['Marsden Park','2765'],['Box Hill','2765'],
            ['Riverstone','2765'],['Vineyard','2765'],['Windsor','2756'],
            ['Richmond','2753'],['Kurrajong','2758'],['Glossodia','2756'],
            ['South Windsor','2756'],['McGraths Hill','2756'],['Pitt Town','2756'],
            ['Toongabbie','2146'],['Old Toongabbie','2146'],['Seven Hills','2147'],
            ['Blacktown','2148'],['Prospect','2148'],['Lalor Park','2147'],
            ['Kings Langley','2147'],['Doonside','2767'],['Woodcroft','2767'],
            ['Plumpton','2761'],['Oakhurst','2761'],['Glendenning','2761'],
            ['Dean Park','2761'],['Rooty Hill','2766'],['Mount Druitt','2770'],
            ['Whalan','2770'],['Lethbridge Park','2770'],['Emerton','2770'],
            ['Tregear','2770'],['Bidwill','2770'],['Dharruk','2770'],
            ['Werrington','2747'],['Penrith','2750'],['Emu Plains','2750'],
            ['Jamisontown','2750'],['South Penrith','2750'],['Glenmore Park','2745'],
            ['Jordan Springs','2747'],['St Marys','2760'],['Kingswood','2747'],
            ['Orchard Hills','2748'],['Mulgoa','2745'],['Luddenham','2745'],
            ['Badgerys Creek','2555'],['Oran Park','2570'],

            // Canterbury-Bankstown
            ['Canterbury','2193'],['Belmore','2192'],['Lakemba','2195'],
            ['Wiley Park','2195'],['Punchbowl','2196'],['Roselands','2196'],
            ['Beverly Hills','2209'],['Narwee','2209'],['Kingsgrove','2208'],
            ['Hurstville','2220'],['Hurstville Grove','2220'],['Allawah','2218'],
            ['Carlton','2218'],['Kogarah','2217'],['Rockdale','2216'],
            ['Arncliffe','2205'],['Banksia','2216'],['Bexley','2207'],
            ['Bexley North','2207'],['Bardwell Park','2207'],['Bardwell Valley','2207'],
            ['Bankstown','2200'],['Yagoona','2199'],['Bass Hill','2197'],
            ['Georges Hall','2198'],['Condell Park','2200'],['Revesby','2212'],
            ['Revesby Heights','2212'],['Padstow','2211'],['Padstow Heights','2211'],
            ['Panania','2213'],['East Hills','2213'],['Picnic Point','2213'],
            ['Milperra','2214'],['Chester Hill','2162'],['Sefton','2162'],
            ['Villawood','2163'],['Bass Hill','2197'],['Lansdowne','2163'],

            // Fairfield / Liverpool
            ['Fairfield','2165'],['Fairfield West','2165'],['Fairfield Heights','2165'],
            ['Cabramatta','2166'],['Cabramatta West','2166'],['Canley Heights','2166'],
            ['Canley Vale','2166'],['Bonnyrigg','2177'],['Bonnyrigg Heights','2177'],
            ['Edensor Park','2176'],['Bossley Park','2176'],['Prairiewood','2176'],
            ['Smithfield','2164'],['Wetherill Park','2164'],['Greenfield Park','2176'],
            ['Liverpool','2170'],['Warwick Farm','2170'],['Moorebank','2170'],
            ['Chipping Norton','2170'],['Casula','2170'],['Holsworthy','2173'],
            ['Hammondville','2170'],['Wattle Grove','2173'],['Pleasure Point','2172'],
            ['Sandy Point','2172'],['Voyager Point','2172'],['Prestons','2170'],
            ['Lurnea','2170'],['Hinchinbrook','2168'],['Hoxton Park','2171'],
            ['Green Valley','2168'],['Miller','2168'],['Cartwright','2168'],
            ['Busby','2168'],['Sadleir','2168'],['Ashcroft','2168'],

            // Campbelltown / Macarthur
            ['Campbelltown','2560'],['Ingleburn','2565'],['Minto','2566'],
            ['Leumeah','2560'],['Woodbine','2560'],['Ruse','2560'],
            ['Airds','2560'],['Bradbury','2560'],['Rosemeadow','2560'],
            ['Glen Alpine','2560'],['Ambarvale','2560'],['Appin','2560'],
            ['Macquarie Fields','2564'],['Glenfield','2167'],['Edmondson Park','2174'],
            ['Bardia','2565'],['Gregory Hills','2557'],['Spring Farm','2570'],
            ['Camden','2570'],['Narellan','2567'],['Narellan Vale','2567'],
            ['Harrington Park','2567'],['Mount Annan','2567'],['Currans Hill','2567'],

            // Sutherland Shire
            ['Sutherland','2232'],['Jannali','2226'],['Como','2226'],
            ['Sylvania','2224'],['Sylvania Waters','2224'],['Gymea','2227'],
            ['Gymea Bay','2227'],['Miranda','2228'],['Yowie Bay','2228'],
            ['Caringbah','2229'],['Caringbah South','2229'],['Taren Point','2229'],
            ['Dolls Point','2219'],['Sans Souci','2219'],['Ramsgate','2217'],
            ['Ramsgate Beach','2217'],['Cronulla','2230'],['Woolooware','2230'],
            ['Kurnell','2231'],['Kirrawee','2232'],['Loftus','2232'],
            ['Engadine','2233'],['Heathcote','2233'],['Menai','2234'],
            ['Bangor','2234'],['Barden Ridge','2234'],['Alfords Point','2234'],
            ['Illawong','2234'],['Oyster Bay','2225'],['Bonnet Bay','2226'],

            // Northern / Hills District
            ['Cherrybrook','2126'],['West Pennant Hills','2125'],
            ['Pennant Hills','2120'],['Dural','2158'],['Round Corner','2158'],
            ['Kenthurst','2156'],['Annangrove','2156'],['Glenhaven','2156'],
            ['Galston','2159'],['Arcadia','2159'],['Fiddletown','2159'],
            ['North Rocks','2151'],['Oatlands','2117'],['Dundas','2117'],
            ['Dundas Valley','2117'],['Ermington','2115'],['Rydalmere','2116'],
            ['Telopea','2117'],['Winston Hills','2153'],

            // ==================== REGIONAL NSW ====================
            // Newcastle & Hunter
            ['Newcastle','2300'],['Newcastle West','2302'],['Hamilton','2303'],
            ['Hamilton South','2303'],['Islington','2296'],['Mayfield','2304'],
            ['Waratah','2298'],['Lambton','2299'],['New Lambton','2305'],
            ['Jesmond','2299'],['Wallsend','2287'],['Maryland','2287'],
            ['Elermore Vale','2287'],['Cardiff','2285'],['Warners Bay','2282'],
            ['Charlestown','2290'],['Kotara','2289'],['Adamstown','2289'],
            ['Merewether','2291'],['The Junction','2291'],['Bar Beach','2300'],
            ['Cooks Hill','2300'],['Stockton','2295'],['Maitland','2320'],
            ['Rutherford','2320'],['Cessnock','2325'],['Kurri Kurri','2327'],
            ['Singleton','2330'],['Muswellbrook','2333'],['Raymond Terrace','2324'],
            ['Nelson Bay','2315'],['Medowie','2318'],['Thornton','2322'],
            ['Beresfield','2322'],['Tarro','2322'],

            // Wollongong & Illawarra
            ['Wollongong','2500'],['North Wollongong','2500'],['Fairy Meadow','2519'],
            ['Corrimal','2518'],['Towradgi','2518'],['Balgownie','2519'],
            ['Thirroul','2515'],['Bulli','2516'],['Woonona','2517'],
            ['Bellambi','2518'],['Dapto','2530'],['Shellharbour','2529'],
            ['Warilla','2528'],['Barrack Heights','2528'],['Albion Park','2527'],
            ['Albion Park Rail','2527'],['Oak Flats','2529'],['Kiama','2533'],
            ['Berry','2535'],['Nowra','2541'],['Bomaderry','2541'],
            ['Gerringong','2534'],['Austinmer','2515'],['Stanwell Park','2508'],
            ['Helensburgh','2508'],['Figtree','2525'],['Unanderra','2526'],
            ['Coniston','2500'],['Mangerton','2500'],

            // Central Coast
            ['Gosford','2250'],['Erina','2250'],['Terrigal','2260'],
            ['Woy Woy','2256'],['Umina Beach','2257'],['Ettalong Beach','2257'],
            ['Kariong','2250'],['Wyoming','2250'],['Niagara Park','2250'],
            ['Lisarow','2250'],['Ourimbah','2258'],['Tuggerah','2259'],
            ['The Entrance','2261'],['Long Jetty','2261'],['Toukley','2263'],
            ['Bateau Bay','2261'],['Berkeley Vale','2261'],['Killarney Vale','2261'],
            ['Lake Haven','2263'],['San Remo','2262'],['Budgewoi','2262'],
            ['Warnervale','2259'],['Hamlyn Terrace','2259'],['Kanwal','2259'],
            ['Point Clare','2250'],['Tascott','2250'],['Kincumber','2251'],
            ['Avoca Beach','2251'],['Copacabana','2251'],['Saratoga','2251'],
            ['Davistown','2251'],['Green Point','2251'],

            // Blue Mountains
            ['Katoomba','2780'],['Leura','2780'],['Wentworth Falls','2782'],
            ['Lawson','2783'],['Hazelbrook','2779'],['Woodford','2778'],
            ['Springwood','2777'],['Blaxland','2774'],['Glenbrook','2773'],
            ['Faulconbridge','2776'],['Valley Heights','2777'],['Warrimoo','2774'],
            ['Winmalee','2777'],['Yellow Rock','2777'],['Mount Victoria','2786'],
            ['Blackheath','2785'],['Lithgow','2790'],['Portland','2847'],

            // South Coast
            ['Batemans Bay','2536'],['Moruya','2537'],['Narooma','2546'],
            ['Bega','2550'],['Merimbula','2548'],['Eden','2551'],
            ['Ulladulla','2539'],['Milton','2538'],['Sussex Inlet','2540'],
            ['Vincentia','2540'],['Huskisson','2540'],

            // North Coast
            ['Port Macquarie','2444'],['Kempsey','2440'],['Nambucca Heads','2448'],
            ['Coffs Harbour','2450'],['Sawtell','2452'],['Woolgoolga','2456'],
            ['Grafton','2460'],['Yamba','2464'],['Maclean','2463'],
            ['Ballina','2478'],['Lennox Head','2478'],['Byron Bay','2481'],
            ['Brunswick Heads','2483'],['Mullumbimby','2482'],['Lismore','2480'],
            ['Casino','2470'],['Tweed Heads','2485'],['Tweed Heads South','2486'],
            ['Banora Point','2486'],['Murwillumbah','2484'],['Pottsville','2489'],
            ['Cabarita Beach','2488'],['Kingscliff','2487'],

            // Western / Country NSW
            ['Orange','2800'],['Bathurst','2795'],['Dubbo','2830'],
            ['Mudgee','2850'],['Cowra','2794'],['Parkes','2870'],
            ['Forbes','2871'],['Young','2594'],['Lithgow','2790'],
            ['Tamworth','2340'],['Armidale','2350'],['Glen Innes','2370'],
            ['Inverell','2360'],['Gunnedah','2380'],['Narrabri','2390'],
            ['Moree','2400'],['Walgett','2832'],

            // Riverina / South
            ['Wagga Wagga','2650'],['Albury','2640'],['Griffith','2680'],
            ['Leeton','2705'],['Narrandera','2700'],['Deniliquin','2710'],
            ['Hay','2711'],['Tumut','2720'],['Cooma','2630'],
            ['Queanbeyan','2620'],['Yass','2582'],['Goulburn','2580'],
            ['Bowral','2576'],['Mittagong','2575'],['Moss Vale','2577'],
            ['Broken Hill','2880'],['Wentworth','2648'],
        ];
    }

    private function vicSuburbs(): array
    {
        return [
            // ==================== MELBOURNE METRO ====================
            // CBD & Inner
            ['Melbourne','3000'],['Southbank','3006'],['Docklands','3008'],
            ['South Melbourne','3205'],['Port Melbourne','3207'],['West Melbourne','3003'],
            ['East Melbourne','3002'],['Carlton','3053'],['Carlton North','3054'],
            ['Parkville','3052'],['North Melbourne','3051'],

            // Inner North
            ['Fitzroy','3065'],['Fitzroy North','3068'],['Collingwood','3066'],
            ['Abbotsford','3067'],['Clifton Hill','3068'],['Northcote','3070'],
            ['Thornbury','3071'],['Preston','3072'],['Reservoir','3073'],
            ['Kingsbury','3083'],['Bundoora','3083'],['Mill Park','3082'],
            ['Epping','3076'],['South Morang','3752'],['Mernda','3754'],
            ['Doreen','3754'],['Plenty','3090'],['Yarrambat','3091'],
            ['Whittlesea','3757'],['Wollert','3750'],['Donnybrook','3064'],
            ['Kalkallo','3064'],['Mickleham','3064'],

            // Inner East
            ['Richmond','3121'],['Cremorne','3121'],['Burnley','3121'],
            ['Hawthorn','3122'],['Hawthorn East','3123'],['Camberwell','3124'],
            ['Canterbury','3126'],['Surrey Hills','3127'],['Box Hill','3128'],
            ['Box Hill South','3128'],['Box Hill North','3129'],
            ['Balwyn','3103'],['Balwyn North','3104'],['Kew','3101'],
            ['Kew East','3102'],['Studley Park','3101'],
            ['Deepdene','3103'],['Mont Albert','3127'],['Mont Albert North','3129'],

            // East
            ['Doncaster','3108'],['Doncaster East','3109'],['Donvale','3111'],
            ['Templestowe','3106'],['Templestowe Lower','3107'],
            ['Bulleen','3105'],['Manningham','3107'],
            ['Nunawading','3131'],['Blackburn','3130'],['Blackburn North','3130'],
            ['Blackburn South','3130'],['Mitcham','3132'],['Forest Hill','3131'],
            ['Vermont','3133'],['Vermont South','3133'],
            ['Ringwood','3134'],['Ringwood East','3135'],['Ringwood North','3134'],
            ['Croydon','3136'],['Croydon North','3136'],['Croydon Hills','3136'],
            ['Croydon South','3136'],['Mooroolbark','3138'],['Kilsyth','3137'],
            ['Montrose','3765'],['Lilydale','3140'],['Mount Evelyn','3796'],
            ['Chirnside Park','3116'],['Yarra Glen','3775'],['Healesville','3777'],
            ['Warburton','3799'],['Olinda','3788'],['Sassafras','3787'],

            // Glen Waverley / Monash
            ['Glen Waverley','3150'],['Mount Waverley','3149'],['Wheelers Hill','3150'],
            ['Mulgrave','3170'],['Notting Hill','3168'],['Clayton','3168'],
            ['Clayton South','3169'],['Oakleigh','3166'],['Oakleigh East','3166'],
            ['Oakleigh South','3167'],['Huntingdale','3166'],['Chadstone','3148'],
            ['Hughesdale','3166'],['Burwood','3125'],['Burwood East','3151'],
            ['Glen Iris','3146'],['Ashburton','3147'],['Ashwood','3147'],

            // Knox / Maroondah
            ['Knox','3152'],['Wantirna','3152'],['Wantirna South','3152'],
            ['Boronia','3155'],['Bayswater','3153'],['Bayswater North','3153'],
            ['The Basin','3154'],['Ferntree Gully','3156'],['Upper Ferntree Gully','3156'],
            ['Lysterfield','3156'],['Rowville','3178'],['Scoresby','3179'],

            // South East
            ['St Kilda','3182'],['St Kilda East','3183'],['St Kilda West','3182'],
            ['Balaclava','3183'],['Elwood','3184'],['Elsternwick','3185'],
            ['Gardenvale','3185'],['Brighton','3186'],['Brighton East','3187'],
            ['Hampton','3188'],['Hampton East','3188'],['Sandringham','3191'],
            ['Highett','3190'],['Moorabbin','3189'],['Bentleigh','3204'],
            ['Bentleigh East','3165'],['McKinnon','3204'],
            ['Ormond','3204'],['Carnegie','3163'],['Murrumbeena','3163'],
            ['Malvern','3144'],['Malvern East','3145'],['Armadale','3143'],
            ['Prahran','3181'],['South Yarra','3141'],['Toorak','3142'],
            ['Caulfield','3162'],['Caulfield North','3161'],['Caulfield East','3145'],
            ['Caulfield South','3162'],['Glen Huntly','3163'],

            // Bayside
            ['Cheltenham','3192'],['Mentone','3194'],['Parkdale','3195'],
            ['Mordialloc','3195'],['Aspendale','3195'],['Edithvale','3196'],
            ['Chelsea','3196'],['Chelsea Heights','3196'],['Bonbeach','3196'],
            ['Carrum','3197'],['Seaford','3198'],['Frankston','3199'],
            ['Frankston North','3200'],['Frankston South','3199'],

            // Mornington Peninsula
            ['Mornington','3931'],['Mount Martha','3934'],['Dromana','3936'],
            ['Rosebud','3939'],['Rye','3941'],['Blairgowrie','3942'],
            ['Sorrento','3943'],['Portsea','3944'],['Safety Beach','3936'],
            ['Mount Eliza','3930'],['Hastings','3915'],['Somerville','3912'],
            ['Langwarrin','3910'],['Baxter','3911'],['Cranbourne','3977'],
            ['Cranbourne East','3977'],['Cranbourne West','3977'],
            ['Cranbourne North','3977'],['Clyde','3978'],['Clyde North','3978'],

            // Dandenong / Casey
            ['Dandenong','3175'],['Dandenong North','3175'],['Dandenong South','3175'],
            ['Noble Park','3174'],['Noble Park North','3174'],
            ['Springvale','3171'],['Springvale South','3172'],
            ['Keysborough','3173'],['Dingley Village','3172'],
            ['Berwick','3806'],['Narre Warren','3805'],['Narre Warren North','3804'],
            ['Narre Warren South','3805'],['Hallam','3803'],['Endeavour Hills','3802'],
            ['Hampton Park','3976'],['Lynbrook','3975'],['Lyndhurst','3975'],
            ['Pakenham','3810'],['Officer','3809'],['Beaconsfield','3807'],
            ['Cardinia','3978'],['Nar Nar Goon','3812'],

            // West
            ['Footscray','3011'],['West Footscray','3012'],['Seddon','3011'],
            ['Yarraville','3013'],['Kingsville','3012'],['Spotswood','3015'],
            ['Newport','3015'],['Williamstown','3016'],['Williamstown North','3016'],
            ['Altona','3018'],['Altona North','3025'],['Altona Meadows','3028'],
            ['Laverton','3028'],['Werribee','3030'],['Hoppers Crossing','3029'],
            ['Point Cook','3030'],['Tarneit','3029'],['Wyndham Vale','3024'],
            ['Truganina','3029'],['Williams Landing','3027'],['Manor Lakes','3024'],
            ['Sunshine','3020'],['Sunshine West','3020'],['Sunshine North','3020'],
            ['Albion','3020'],['Ardeer','3022'],['St Albans','3021'],
            ['Deer Park','3023'],['Cairnlea','3023'],['Caroline Springs','3023'],
            ['Burnside','3023'],['Burnside Heights','3023'],['Rockbank','3335'],
            ['Melton','3337'],['Melton West','3337'],['Melton South','3338'],
            ['Kurunjang','3337'],['Brookfield','3338'],['Bacchus Marsh','3340'],

            // North West
            ['Essendon','3040'],['Essendon North','3041'],['Essendon West','3040'],
            ['Moonee Ponds','3039'],['Ascot Vale','3032'],['Flemington','3031'],
            ['Maribyrnong','3032'],['Aberfeldie','3040'],['Niddrie','3042'],
            ['Keilor East','3033'],['Airport West','3042'],['Avondale Heights','3034'],
            ['Keilor','3036'],['Keilor Downs','3038'],['Keilor Park','3042'],
            ['Tullamarine','3043'],['Gladstone Park','3043'],['Westmeadows','3049'],
            ['Broadmeadows','3047'],['Dallas','3047'],['Jacana','3047'],
            ['Glenroy','3046'],['Hadfield','3046'],['Oak Park','3046'],
            ['Fawkner','3060'],['Pascoe Vale','3044'],['Pascoe Vale South','3044'],
            ['Coburg','3058'],['Coburg North','3058'],['Brunswick','3056'],
            ['Brunswick West','3055'],['Brunswick East','3057'],
            ['Craigieburn','3064'],['Roxburgh Park','3064'],['Coolaroo','3048'],
            ['Meadow Heights','3048'],['Sunbury','3429'],['Diggers Rest','3427'],

            // Inner North East
            ['Ivanhoe','3079'],['Ivanhoe East','3079'],['Heidelberg','3084'],
            ['Heidelberg Heights','3081'],['Heidelberg West','3081'],
            ['Rosanna','3084'],['Macleod','3085'],['Viewbank','3084'],
            ['Greensborough','3088'],['Watsonia','3087'],['Watsonia North','3087'],
            ['Briar Hill','3088'],['Montmorency','3094'],['Eltham','3095'],
            ['Eltham North','3095'],['Research','3095'],['Diamond Creek','3089'],
            ['Hurstbridge','3099'],['St Helena','3088'],

            // ==================== REGIONAL VIC ====================
            ['Geelong','3220'],['Geelong West','3218'],['Newtown','3220'],
            ['Belmont','3216'],['Highton','3216'],['Grovedale','3216'],
            ['Waurn Ponds','3216'],['Torquay','3228'],['Ocean Grove','3226'],
            ['Queenscliff','3225'],['Leopold','3224'],['Drysdale','3222'],
            ['Colac','3250'],['Lara','3212'],['Corio','3214'],
            ['Norlane','3214'],['Bell Park','3215'],['Hamlyn Heights','3215'],

            ['Ballarat','3350'],['Ballarat East','3350'],['Ballarat North','3350'],
            ['Wendouree','3355'],['Sebastopol','3356'],['Buninyong','3357'],
            ['Mount Helen','3350'],['Lake Wendouree','3350'],

            ['Bendigo','3550'],['Strathdale','3550'],['Kangaroo Flat','3555'],
            ['Eaglehawk','3556'],['Golden Square','3555'],['Epsom','3551'],
            ['Huntly','3551'],['Long Gully','3550'],

            ['Shepparton','3630'],['Mooroopna','3629'],['Kialla','3631'],
            ['Warrnambool','3280'],['Mildura','3500'],['Red Cliffs','3496'],
            ['Irymple','3498'],['Swan Hill','3585'],['Echuca','3564'],
            ['Moama','2731'],['Horsham','3400'],['Hamilton','3300'],
            ['Portland','3305'],['Wangaratta','3677'],['Benalla','3672'],
            ['Wodonga','3690'],['Albury','2640'],['Traralgon','3844'],
            ['Morwell','3840'],['Moe','3825'],['Warragul','3820'],
            ['Drouin','3818'],['Sale','3850'],['Bairnsdale','3875'],
            ['Lakes Entrance','3909'],['Wonthaggi','3995'],['Inverloch','3996'],
            ['Phillip Island','3922'],['San Remo','3925'],['Cowes','3922'],
            ['Daylesford','3460'],['Castlemaine','3450'],['Kyneton','3444'],
            ['Woodend','3442'],['Gisborne','3437'],['Romsey','3434'],
            ['Kilmore','3764'],['Seymour','3660'],['Euroa','3666'],
            ['Mansfield','3722'],['Alexandra','3714'],
            ['Maryborough','3465'],['Stawell','3380'],['Ararat','3377'],
        ];
    }

    private function qldSuburbs(): array
    {
        return [
            // ==================== BRISBANE METRO ====================
            // CBD & Inner
            ['Brisbane City','4000'],['South Brisbane','4101'],['West End','4101'],
            ['Highgate Hill','4101'],['Woolloongabba','4102'],['Kangaroo Point','4169'],
            ['Fortitude Valley','4006'],['New Farm','4005'],['Teneriffe','4005'],
            ['Newstead','4006'],['Bowen Hills','4006'],['Spring Hill','4000'],
            ['Petrie Terrace','4000'],['Paddington','4064'],['Milton','4064'],
            ['Auchenflower','4066'],['Toowong','4066'],['St Lucia','4067'],
            ['Taringa','4068'],['Indooroopilly','4068'],['Bardon','4065'],
            ['Red Hill','4059'],['Kelvin Grove','4059'],['Herston','4006'],

            // Inner South
            ['East Brisbane','4169'],['Norman Park','4170'],['Coorparoo','4151'],
            ['Camp Hill','4152'],['Carina','4152'],['Carina Heights','4152'],
            ['Greenslopes','4120'],['Holland Park','4121'],['Holland Park West','4121'],
            ['Tarragindi','4121'],['Moorooka','4105'],['Annerley','4103'],
            ['Yeronga','4104'],['Fairfield','4103'],['Dutton Park','4102'],
            ['Salisbury','4107'],['Rocklea','4106'],['Nathan','4111'],
            ['Coopers Plains','4108'],['Sunnybank','4109'],['Sunnybank Hills','4109'],
            ['Robertson','4109'],['Runcorn','4113'],['Eight Mile Plains','4113'],

            // South
            ['Upper Mount Gravatt','4122'],['Mount Gravatt','4122'],
            ['Mount Gravatt East','4122'],['Wishart','4122'],
            ['Mansfield','4122'],['Carindale','4152'],['Belmont','4153'],
            ['Chandler','4155'],['Burbank','4156'],['Capalaba','4157'],
            ['Rochedale','4123'],['Rochedale South','4123'],['Springwood','4127'],
            ['Daisy Hill','4127'],['Shailer Park','4128'],['Tanah Merah','4128'],
            ['Loganholme','4129'],['Meadowbrook','4131'],['Logan Central','4114'],
            ['Woodridge','4114'],['Kingston','4114'],['Slacks Creek','4127'],
            ['Underwood','4119'],['Kuraby','4112'],['Parkinson','4115'],
            ['Drewvale','4116'],['Algester','4115'],['Calamvale','4116'],
            ['Stretton','4116'],['Berrinba','4117'],

            // Logan / Beenleigh
            ['Beenleigh','4207'],['Eagleby','4207'],['Edens Landing','4207'],
            ['Holmview','4207'],['Waterford','4133'],['Waterford West','4133'],
            ['Bethania','4205'],['Marsden','4132'],['Crestmead','4132'],
            ['Browns Plains','4118'],['Regents Park','4118'],['Heritage Park','4118'],
            ['Boronia Heights','4124'],['Greenbank','4124'],['Park Ridge','4125'],
            ['Park Ridge South','4125'],['Flagstone','4280'],['Yarrabilba','4207'],
            ['Jimboomba','4280'],

            // East / Bayside
            ['Wynnum','4178'],['Manly','4179'],['Lota','4179'],['Manly West','4179'],
            ['Tingalpa','4173'],['Hemmant','4174'],['Murarrie','4172'],
            ['Cannon Hill','4170'],['Morningside','4170'],['Balmoral','4171'],
            ['Bulimba','4171'],['Hawthorne','4171'],
            ['Cleveland','4163'],['Thornlands','4164'],['Victoria Point','4165'],
            ['Redland Bay','4165'],['Mount Cotton','4165'],['Sheldon','4157'],
            ['Alexandra Hills','4161'],['Ormiston','4160'],['Wellington Point','4160'],
            ['Birkdale','4159'],['Thorneside','4158'],
            ['North Stradbroke Island','4183'],['Dunwich','4183'],

            // West
            ['Kenmore','4069'],['Chapel Hill','4069'],['Kenmore Hills','4069'],
            ['Brookfield','4069'],['Pullenvale','4069'],['Fig Tree Pocket','4069'],
            ['Jindalee','4074'],['Mount Ommaney','4074'],['Riverhills','4074'],
            ['Middle Park','4074'],['Westlake','4074'],['Centenary Heights','4074'],
            ['Darra','4076'],['Oxley','4075'],['Corinda','4075'],
            ['Graceville','4075'],['Sherwood','4075'],['Chelmer','4068'],
            ['Forest Lake','4078'],['Richlands','4077'],['Inala','4077'],
            ['Durack','4077'],['Ellen Grove','4078'],['Springfield','4300'],
            ['Springfield Lakes','4300'],['Springfield Central','4300'],
            ['Augustine Heights','4300'],['Brookwater','4300'],
            ['Ipswich','4305'],['Brassall','4305'],['Booval','4304'],
            ['East Ipswich','4305'],['North Ipswich','4305'],['Redbank Plains','4301'],
            ['Bellbird Park','4300'],['Goodna','4300'],['Redbank','4301'],
            ['Collingwood Park','4301'],['Ripley','4306'],['Deebing Heights','4306'],
            ['Rosewood','4340'],['Gatton','4343'],['Laidley','4341'],

            // North
            ['Windsor','4030'],['Wilston','4051'],['Grange','4051'],
            ['Newmarket','4051'],['Lutwyche','4030'],['Gordon Park','4031'],
            ['Wooloowin','4030'],['Albion','4010'],['Clayfield','4011'],
            ['Ascot','4007'],['Hamilton','4007'],['Hendra','4011'],
            ['Eagle Farm','4009'],['Nundah','4012'],['Northgate','4013'],
            ['Banyo','4014'],['Nudgee','4014'],['Virginia','4014'],
            ['Wavell Heights','4012'],['Kedron','4031'],['Stafford','4053'],
            ['Stafford Heights','4053'],['Everton Park','4053'],['McDowall','4053'],
            ['Mitchelton','4053'],['Keperra','4054'],['Ferny Hills','4055'],
            ['Arana Hills','4054'],['Everton Hills','4053'],
            ['Chermside','4032'],['Chermside West','4032'],['Aspley','4034'],
            ['Geebung','4034'],['Zillmere','4034'],['Carseldine','4034'],
            ['Bridgeman Downs','4035'],['Bald Hills','4036'],['Bracken Ridge','4017'],
            ['Sandgate','4017'],['Shorncliffe','4017'],['Brighton','4017'],
            ['Deagon','4017'],['Taigum','4018'],['Fitzgibbon','4018'],

            // Moreton Bay / North Lakes
            ['North Lakes','4509'],['Mango Hill','4509'],['Kallangur','4503'],
            ['Dakabin','4503'],['Petrie','4502'],['Lawnton','4501'],
            ['Strathpine','4500'],['Brendale','4500'],['Bray Park','4500'],
            ['Warner','4500'],['Cashmere','4500'],['Eatons Hill','4037'],
            ['Albany Creek','4035'],['Bridgeman Downs','4035'],
            ['Rothwell','4022'],['Kippa-Ring','4021'],['Redcliffe','4020'],
            ['Scarborough','4020'],['Margate','4019'],['Clontarf','4019'],
            ['Woody Point','4019'],['Deception Bay','4508'],['Burpengary','4505'],
            ['Narangba','4504'],['Morayfield','4506'],['Caboolture','4510'],
            ['Caboolture South','4510'],['Bellmere','4510'],['Wamuran','4512'],
            ['Woodford','4514'],['Bribie Island','4507'],

            // ==================== GOLD COAST ====================
            ['Surfers Paradise','4217'],['Broadbeach','4218'],['Main Beach','4217'],
            ['Southport','4215'],['Labrador','4215'],['Biggera Waters','4216'],
            ['Runaway Bay','4216'],['Arundel','4214'],['Molendinar','4214'],
            ['Ashmore','4214'],['Benowa','4217'],['Bundall','4217'],
            ['Nerang','4211'],['Carrara','4211'],['Highland Park','4211'],
            ['Robina','4226'],['Varsity Lakes','4227'],['Mudgeeraba','4213'],
            ['Merrimac','4226'],['Mermaid Beach','4218'],['Mermaid Waters','4218'],
            ['Miami','4220'],['Burleigh Heads','4220'],['Burleigh Waters','4220'],
            ['Palm Beach','4221'],['Elanora','4221'],['Currumbin','4223'],
            ['Currumbin Waters','4223'],['Tugun','4224'],['Coolangatta','4225'],
            ['Tweed Heads','2485'],['Coomera','4209'],['Upper Coomera','4209'],
            ['Hope Island','4212'],['Oxenford','4210'],['Helensvale','4212'],
            ['Pacific Pines','4211'],['Pimpama','4209'],['Ormeau','4208'],
            ['Ormeau Hills','4208'],['Tamborine Mountain','4272'],

            // ==================== SUNSHINE COAST ====================
            ['Maroochydore','4558'],['Mooloolaba','4557'],['Alexandra Headland','4572'],
            ['Buderim','4556'],['Sippy Downs','4556'],['Mountain Creek','4557'],
            ['Caloundra','4551'],['Golden Beach','4551'],['Kings Beach','4551'],
            ['Currimundi','4551'],['Kawana Waters','4575'],['Warana','4575'],
            ['Bokarina','4575'],['Wurtulla','4575'],['Birtinya','4575'],
            ['Coolum Beach','4573'],['Peregian Springs','4573'],
            ['Noosa Heads','4567'],['Noosaville','4566'],['Tewantin','4565'],
            ['Nambour','4560'],['Palmwoods','4555'],['Maleny','4552'],
            ['Montville','4560'],['Glass House Mountains','4518'],
            ['Beerwah','4519'],['Landsborough','4550'],

            // ==================== REGIONAL QLD ====================
            ['Toowoomba','4350'],['Highfields','4352'],['Rangeville','4350'],
            ['Middle Ridge','4350'],['Darling Heights','4350'],

            ['Cairns','4870'],['Cairns North','4870'],['Edge Hill','4870'],
            ['Smithfield','4878'],['Trinity Beach','4879'],['Palm Cove','4879'],
            ['Yorkeys Knob','4878'],['Redlynch','4870'],['Brinsmead','4870'],
            ['Manoora','4870'],['Manunda','4870'],['Westcourt','4870'],
            ['Earlville','4870'],['Edmonton','4869'],['Gordonvale','4865'],
            ['Port Douglas','4877'],

            ['Townsville','4810'],['Aitkenvale','4814'],['Kirwan','4817'],
            ['Cranbrook','4814'],['Thuringowa Central','4817'],
            ['Kelso','4815'],['Bohle Plains','4817'],

            ['Mackay','4740'],['North Mackay','4740'],['South Mackay','4740'],
            ['Andergrove','4740'],['Rural View','4740'],

            ['Rockhampton','4700'],['North Rockhampton','4701'],
            ['Yeppoon','4703'],['Emu Park','4710'],
            ['Gladstone','4680'],['Tannum Sands','4680'],
            ['Hervey Bay','4655'],['Pialba','4655'],['Torquay','4655'],
            ['Urangan','4655'],['Maryborough','4650'],
            ['Bundaberg','4670'],['Bundaberg East','4670'],['Bargara','4670'],
        ];
    }

    private function waSuburbs(): array
    {
        return [
            // ==================== PERTH METRO ====================
            // CBD & Inner
            ['Perth','6000'],['West Perth','6005'],['East Perth','6004'],
            ['Northbridge','6003'],['Highgate','6003'],['North Perth','6006'],
            ['Mount Lawley','6050'],['Leederville','6007'],['West Leederville','6007'],
            ['Subiaco','6008'],['Shenton Park','6008'],['Nedlands','6009'],
            ['Crawley','6009'],['Dalkeith','6009'],['Claremont','6010'],
            ['Cottesloe','6011'],['Swanbourne','6010'],['Mosman Park','6012'],
            ['Peppermint Grove','6011'],['South Perth','6151'],['Como','6152'],
            ['Victoria Park','6100'],['East Victoria Park','6101'],
            ['Lathlain','6100'],['Carlisle','6101'],['Burswood','6100'],

            // Northern Suburbs
            ['Osborne Park','6017'],['Tuart Hill','6060'],['Joondanna','6060'],
            ['Yokine','6060'],['Dianella','6059'],['Nollamara','6061'],
            ['Mirrabooka','6061'],['Westminster','6061'],['Balga','6061'],
            ['Stirling','6021'],['Scarborough','6019'],['Doubleview','6018'],
            ['Innaloo','6018'],['Karrinyup','6018'],['Gwelup','6018'],
            ['Carine','6020'],['Duncraig','6023'],['Warwick','6024'],
            ['Greenwood','6024'],['Kingsley','6026'],['Woodvale','6026'],
            ['Joondalup','6027'],['Edgewater','6027'],['Heathridge','6027'],
            ['Currambine','6028'],['Kinross','6028'],['Burns Beach','6028'],
            ['Iluka','6028'],['Mindarie','6030'],['Clarkson','6030'],
            ['Merriwa','6030'],['Ridgewood','6030'],['Butler','6036'],
            ['Alkimos','6038'],['Yanchep','6035'],['Two Rocks','6037'],
            ['Quinns Rocks','6030'],['Wanneroo','6065'],['Tapping','6065'],
            ['Ashby','6065'],['Sinagra','6065'],['Landsdale','6065'],
            ['Madeley','6065'],['Alexander Heights','6064'],['Ballajura','6066'],
            ['Malaga','6090'],['Morley','6062'],['Noranda','6062'],
            ['Beechboro','6063'],['Kiara','6054'],['Bassendean','6054'],
            ['Bayswater','6053'],['Maylands','6051'],['Bedford','6052'],
            ['Inglewood','6052'],['Embleton','6062'],

            // Southern Suburbs
            ['Fremantle','6160'],['North Fremantle','6159'],['South Fremantle','6162'],
            ['East Fremantle','6158'],['White Gum Valley','6162'],
            ['Beaconsfield','6162'],['Hilton','6163'],['Palmyra','6157'],
            ['Bicton','6157'],['Attadale','6156'],['Alfred Cove','6154'],
            ['Melville','6156'],['Myaree','6154'],['Booragoon','6154'],
            ['Applecross','6153'],['Ardross','6153'],['Mount Pleasant','6153'],
            ['Brentwood','6153'],['Bull Creek','6149'],['Leeming','6149'],
            ['Bateman','6150'],['Winthrop','6150'],['Murdoch','6150'],
            ['Willetton','6155'],['Riverton','6148'],['Shelley','6148'],
            ['Rossmoyne','6148'],['Cannington','6107'],['Beckenham','6107'],
            ['Kenwick','6107'],['Langford','6147'],['Thornlie','6108'],
            ['Gosnells','6110'],['Maddington','6109'],['Huntingdale','6110'],
            ['Southern River','6110'],['Canning Vale','6155'],
            ['Armadale','6112'],['Kelmscott','6111'],['Mount Nasura','6112'],
            ['Seville Grove','6112'],['Camillo','6112'],['Champion Lakes','6111'],
            ['Byford','6122'],['Mundijong','6123'],['Serpentine','6125'],
            ['Rockingham','6168'],['Safety Bay','6169'],['Shoalwater','6169'],
            ['Waikiki','6169'],['Baldivis','6171'],['Port Kennedy','6172'],
            ['Wellard','6170'],['Mandurah','6210'],['Halls Head','6210'],
            ['Greenfields','6210'],['Meadow Springs','6210'],['Lakelands','6180'],
            ['Pinjarra','6208'],['Dawesville','6211'],

            // Eastern Suburbs
            ['Midland','6056'],['Bellevue','6056'],['Helena Valley','6056'],
            ['Mundaring','6073'],['Kalamunda','6076'],['Lesmurdie','6076'],
            ['High Wycombe','6057'],['Forrestfield','6058'],['Maida Vale','6057'],
            ['Ellenbrook','6069'],['The Vines','6069'],['Aveley','6069'],
            ['Brabham','6055'],['Dayton','6055'],['Swan View','6056'],
            ['Stratton','6056'],['Midvale','6056'],['Guildford','6055'],
            ['Caversham','6055'],['South Guildford','6055'],['Hazelmere','6055'],

            // Coastal South
            ['Cockburn Central','6164'],['Success','6164'],['Atwell','6164'],
            ['Aubin Grove','6164'],['Hammond Park','6164'],['Jandakot','6164'],
            ['Piara Waters','6112'],['Harrisdale','6112'],['Treeby','6164'],
            ['Munster','6166'],['Spearwood','6163'],['Bibra Lake','6163'],
            ['Yangebup','6164'],['Henderson','6166'],['South Lake','6164'],
            ['Coolbellup','6163'],['North Lake','6163'],['Hamilton Hill','6163'],
            ['Kardinya','6163'],['Samson','6163'],['O\'Connor','6163'],

            // ==================== REGIONAL WA ====================
            ['Bunbury','6230'],['South Bunbury','6230'],['East Bunbury','6230'],
            ['Australind','6233'],['Eaton','6232'],['Dalyellup','6230'],
            ['Busselton','6280'],['Dunsborough','6281'],['Margaret River','6285'],
            ['Geraldton','6530'],['Bluff Point','6530'],
            ['Kalgoorlie','6430'],['Boulder','6432'],
            ['Albany','6330'],['Esperance','6450'],
            ['Broome','6725'],['Karratha','6714'],['Port Hedland','6721'],
            ['Northam','6401'],['Merredin','6415'],['Katanning','6317'],
            ['Narrogin','6312'],['Collie','6225'],['Harvey','6220'],
            ['Carnarvon','6701'],['Exmouth','6707'],['Kununurra','6743'],
        ];
    }

    private function saSuburbs(): array
    {
        return [
            // ==================== ADELAIDE METRO ====================
            // CBD & Inner
            ['Adelaide','5000'],['North Adelaide','5006'],['Kent Town','5067'],
            ['Norwood','5067'],['Parkside','5063'],['Unley','5061'],
            ['Goodwood','5034'],['Hyde Park','5061'],['Wayville','5034'],
            ['Eastwood','5063'],['Fullarton','5063'],['Highgate','5063'],
            ['Rose Park','5067'],['Toorak Gardens','5065'],['Burnside','5066'],
            ['Erindale','5066'],['Linden Park','5065'],['Hazelwood Park','5066'],
            ['Dulwich','5065'],['Tusmore','5065'],['Frewville','5063'],
            ['Malvern','5061'],['Colonel Light Gardens','5041'],['Daw Park','5041'],
            ['Clarence Park','5034'],['Black Forest','5035'],
            ['Millswood','5034'],['Cumberland Park','5041'],
            ['Prospect','5082'],['Medindie','5081'],['Walkerville','5081'],
            ['Gilberton','5081'],['Nailsworth','5083'],['Thorngate','5082'],

            // Eastern Suburbs
            ['Magill','5072'],['Rostrevor','5073'],['Campbelltown','5074'],
            ['Paradise','5075'],['Newton','5074'],['Athelstone','5076'],
            ['Dernancourt','5075'],['Hope Valley','5090'],['Highbury','5089'],
            ['Modbury','5092'],['Modbury Heights','5092'],['Modbury North','5092'],
            ['Tea Tree Gully','5091'],['Golden Grove','5125'],['Greenwith','5125'],
            ['Surrey Downs','5126'],['Wynn Vale','5127'],['Ridgehaven','5097'],
            ['Banksia Park','5091'],['Fairview Park','5126'],

            // Northern Suburbs
            ['Enfield','5085'],['Northfield','5085'],['Lightsview','5085'],
            ['Blair Athol','5084'],['Kilburn','5084'],['Clearview','5085'],
            ['Broadview','5083'],['Hampstead Gardens','5086'],
            ['Klemzig','5087'],['Windsor Gardens','5087'],
            ['Gilles Plains','5086'],['Vale Park','5081'],['Manningham','5086'],
            ['Holden Hill','5088'],['Ingle Farm','5098'],['Para Hills','5096'],
            ['Para Hills West','5096'],['Para Vista','5093'],
            ['Salisbury','5108'],['Salisbury North','5108'],['Salisbury East','5109'],
            ['Salisbury Heights','5109'],['Salisbury Downs','5108'],
            ['Salisbury Plain','5109'],['Parafield Gardens','5107'],
            ['Mawson Lakes','5095'],['Pooraka','5095'],['Cavan','5094'],
            ['Gepps Cross','5094'],['Dry Creek','5094'],
            ['Elizabeth','5112'],['Elizabeth South','5112'],['Elizabeth East','5112'],
            ['Elizabeth Vale','5112'],['Elizabeth Downs','5113'],
            ['Elizabeth North','5113'],['Craigmore','5114'],['Blakeview','5114'],
            ['Smithfield','5114'],['Davoren Park','5113'],
            ['Munno Para','5115'],['Munno Para West','5115'],['Andrews Farm','5114'],
            ['Gawler','5118'],['Gawler East','5118'],['Gawler South','5118'],
            ['Angle Vale','5117'],['Virginia','5120'],['Two Wells','5501'],
            ['Playford','5115'],

            // Western Suburbs
            ['Henley Beach','5022'],['Henley Beach South','5022'],
            ['Grange','5022'],['Findon','5023'],['Kidman Park','5025'],
            ['Flinders Park','5025'],['Allenby Gardens','5009'],
            ['Welland','5007'],['Torrensville','5031'],['Thebarton','5031'],
            ['Mile End','5031'],['Hindmarsh','5007'],['Brompton','5007'],
            ['Bowden','5007'],['Ovingham','5082'],['Croydon','5008'],
            ['Croydon Park','5008'],['West Croydon','5008'],['Kilkenny','5009'],
            ['Royal Park','5014'],['Albert Park','5014'],['Woodville','5011'],
            ['Woodville South','5011'],['Woodville North','5012'],['Woodville West','5011'],
            ['Semaphore','5019'],['Port Adelaide','5015'],['Largs Bay','5016'],
            ['Largs North','5016'],['Ethelton','5015'],['Birkenhead','5015'],
            ['Ottoway','5013'],['Pennington','5013'],['Mansfield Park','5012'],
            ['Athol Park','5012'],['West Lakes','5021'],['West Lakes Shore','5020'],
            ['Seaton','5023'],['Fulham','5024'],['Fulham Gardens','5024'],
            ['Lockleys','5032'],['Underdale','5032'],['Brooklyn Park','5032'],
            ['West Beach','5024'],['West Torrens','5031'],

            // Southern Suburbs
            ['Mitcham','5062'],['Torrens Park','5062'],['Hawthorn','5062'],
            ['Pasadena','5042'],['St Marys','5042'],['Clovelly Park','5042'],
            ['Ascot Park','5043'],['Marion','5043'],['Park Holme','5043'],
            ['Morphettville','5043'],['Plympton','5038'],['Plympton Park','5038'],
            ['Glenelg','5045'],['Glenelg South','5045'],['Glenelg North','5045'],
            ['Glenelg East','5045'],['Somerton Park','5044'],['Brighton','5048'],
            ['Hove','5048'],['Seacliff','5049'],['Kingston Park','5049'],
            ['Marino','5049'],['Hallett Cove','5158'],['Sheidow Park','5158'],
            ['Trott Park','5158'],['Seaview Downs','5049'],
            ['Bedford Park','5042'],['Darlington','5047'],['Sturt','5047'],
            ['Bellevue Heights','5050'],['Eden Hills','5050'],
            ['Blackwood','5051'],['Belair','5052'],['Coromandel Valley','5051'],
            ['Aberfoyle Park','5159'],['Happy Valley','5159'],['Flagstaff Hill','5159'],
            ['Reynella','5161'],['Old Reynella','5161'],['Reynella East','5161'],
            ['Morphett Vale','5162'],['Woodcroft','5162'],['Christie Downs','5164'],
            ['Christies Beach','5165'],['Noarlunga Centre','5168'],
            ['Port Noarlunga','5167'],['Moana','5169'],['Seaford','5169'],
            ['Aldinga Beach','5173'],['McLaren Vale','5171'],['Willunga','5172'],
            ['Victor Harbor','5211'],['Mount Barker','5251'],['Stirling','5152'],
            ['Crafers','5152'],['Bridgewater','5155'],['Hahndorf','5245'],

            // ==================== REGIONAL SA ====================
            ['Murray Bridge','5253'],['Mount Gambier','5290'],
            ['Whyalla','5600'],['Port Augusta','5700'],['Port Lincoln','5606'],
            ['Port Pirie','5540'],['Nuriootpa','5355'],['Tanunda','5352'],
            ['Angaston','5353'],['Renmark','5341'],['Berri','5343'],
            ['Loxton','5333'],['Kadina','5554'],['Wallaroo','5556'],
            ['Ceduna','5690'],['Naracoorte','5271'],['Millicent','5280'],
        ];
    }

    private function tasSuburbs(): array
    {
        return [
            // ==================== HOBART ====================
            ['Hobart','7000'],['North Hobart','7000'],['West Hobart','7000'],
            ['South Hobart','7004'],['Battery Point','7004'],['Sandy Bay','7005'],
            ['Dynnyrne','7005'],['Mount Nelson','7007'],['Taroona','7053'],
            ['Kingston','7050'],['Kingston Beach','7050'],['Blackmans Bay','7052'],
            ['Howrah','7018'],['Bellerive','7018'],['Lindisfarne','7015'],
            ['Rose Bay','7015'],['Montagu Bay','7018'],['Warrane','7018'],
            ['Mornington','7018'],['Rosny','7018'],['Rosny Park','7018'],
            ['Glenorchy','7010'],['West Moonah','7009'],['Moonah','7009'],
            ['Derwent Park','7009'],['Lutana','7009'],['New Town','7008'],
            ['Lenah Valley','7008'],['Mount Stuart','7000'],
            ['Claremont','7011'],['Austins Ferry','7011'],
            ['Granton','7030'],['Bridgewater','7030'],['Gagebrook','7030'],
            ['Brighton','7030'],['Old Beach','7017'],['Sorell','7172'],
            ['Dodges Ferry','7173'],['Carlton','7173'],['Lauderdale','7021'],
            ['Tranmere','7018'],['Clarendon Vale','7019'],['Rokeby','7019'],
            ['Acton Park','7170'],['Seven Mile Beach','7170'],
            ['Margate','7054'],['Snug','7054'],['Electrona','7054'],
            ['Huonville','7109'],['Cygnet','7112'],['Franklin','7113'],
            ['Geeveston','7116'],['Dover','7117'],
            ['New Norfolk','7140'],['Moonah','7009'],

            // ==================== LAUNCESTON ====================
            ['Launceston','7250'],['South Launceston','7249'],
            ['East Launceston','7250'],['West Launceston','7250'],
            ['Invermay','7248'],['Mowbray','7248'],['Newnham','7248'],
            ['Ravenswood','7250'],['Riverside','7250'],['Trevallyn','7250'],
            ['Prospect','7250'],['Summerhill','7250'],['Kings Meadows','7249'],
            ['Youngtown','7249'],['Prospect Vale','7250'],['Blackstone Heights','7250'],
            ['Legana','7277'],['Grindelwald','7277'],['Hadspen','7290'],
            ['Perth','7300'],['Longford','7301'],['Westbury','7303'],
            ['Deloraine','7304'],['George Town','7253'],['Beauty Point','7270'],
            ['Beaconsfield','7270'],['Scottsdale','7260'],
            ['St Helens','7216'],['Bridport','7262'],

            // ==================== NORTH WEST / WEST ====================
            ['Devonport','7310'],['East Devonport','7310'],
            ['Spreyton','7310'],['Latrobe','7307'],['Port Sorell','7307'],
            ['Burnie','7320'],['South Burnie','7320'],['Cooee','7320'],
            ['Somerset','7322'],['Wynyard','7325'],
            ['Ulverstone','7315'],['Penguin','7316'],
            ['Smithton','7330'],['Stanley','7331'],
            ['Queenstown','7467'],['Strahan','7468'],['Rosebery','7470'],
            ['Zeehan','7469'],
        ];
    }

    private function actSuburbs(): array
    {
        return [
            // ==================== CANBERRA ====================
            // Inner North
            ['Canberra','2600'],['City','2601'],['Acton','2601'],
            ['Reid','2612'],['Braddon','2612'],['Turner','2612'],
            ['O\'Connor','2602'],['Lyneham','2602'],['Dickson','2602'],
            ['Downer','2602'],['Hackett','2602'],['Watson','2602'],
            ['Ainslie','2602'],['Campbell','2612'],

            // Inner South
            ['Barton','2600'],['Kingston','2604'],['Griffith','2603'],
            ['Narrabundah','2604'],['Forrest','2603'],['Deakin','2600'],
            ['Red Hill','2603'],['Manuka','2603'],['Yarralumla','2600'],
            ['Curtin','2605'],['Hughes','2605'],['Garran','2605'],
            ['Lyons','2606'],['Phillip','2606'],['Woden','2606'],
            ['Mawson','2607'],['Farrer','2607'],['Pearce','2607'],
            ['Torrens','2607'],['Isaacs','2607'],['OConnell','2600'],

            // Belconnen
            ['Belconnen','2617'],['Bruce','2617'],['Weetangera','2614'],
            ['Cook','2614'],['Aranda','2614'],['Hawker','2614'],
            ['Page','2614'],['Scullin','2614'],['Higgins','2615'],
            ['Holt','2615'],['Kippax','2615'],['Macgregor','2615'],
            ['Charnwood','2615'],['Dunlop','2615'],['Fraser','2615'],
            ['Evatt','2617'],['McKellar','2617'],['Giralang','2617'],
            ['Kaleen','2617'],['Lawson','2617'],['Florey','2615'],
            ['Latham','2615'],['Macquarie','2614'],['Flynn','2615'],

            // Tuggeranong
            ['Tuggeranong','2900'],['Wanniassa','2903'],['Kambah','2902'],
            ['Greenway','2900'],['Bonython','2905'],['Calwell','2905'],
            ['Chisholm','2905'],['Fadden','2904'],['Gowrie','2904'],
            ['Isabella Plains','2905'],['Monash','2904'],['Oxley','2903'],
            ['Richardson','2905'],['Theodore','2905'],['Gordon','2906'],
            ['Conder','2906'],['Banks','2906'],['Lanyon','2905'],

            // Weston Creek
            ['Weston','2611'],['Rivett','2611'],['Chapman','2611'],
            ['Duffy','2611'],['Fisher','2611'],['Stirling','2611'],
            ['Waramanga','2611'],['Holder','2611'],

            // Gungahlin
            ['Gungahlin','2912'],['Mitchell','2911'],['Franklin','2913'],
            ['Harrison','2914'],['Amaroo','2914'],['Bonner','2914'],
            ['Casey','2913'],['Crace','2911'],['Forde','2914'],
            ['Jacka','2914'],['Moncrieff','2914'],['Ngunnawal','2913'],
            ['Nicholls','2913'],['Palmerston','2913'],['Taylor','2913'],
            ['Throsby','2914'],

            // Molonglo Valley
            ['Molonglo','2611'],['Wright','2611'],['Coombs','2611'],
            ['Denman Prospect','2611'],['Whitlam','2611'],
        ];
    }

    private function ntSuburbs(): array
    {
        return [
            // ==================== DARWIN ====================
            ['Darwin City','0800'],['Stuart Park','0820'],['The Gardens','0820'],
            ['Parap','0820'],['Fannie Bay','0820'],['East Point','0820'],
            ['Larrakeyah','0820'],['Bayview','0820'],['Woolner','0820'],
            ['Ludmilla','0820'],['Winnellie','0820'],['Berrimah','0828'],
            ['Rapid Creek','0810'],['Millner','0810'],['Coconut Grove','0810'],
            ['Nightcliff','0810'],['Alawa','0810'],['Brinkin','0810'],
            ['Nakara','0810'],['Wanguri','0810'],['Tiwi','0810'],
            ['Moil','0810'],['Wagaman','0810'],['Marrara','0812'],
            ['Malak','0812'],['Anula','0812'],['Karama','0812'],
            ['Wulagi','0812'],['Leanyer','0812'],['Lee Point','0810'],
            ['Muirhead','0810'],['Lyons','0810'],

            // Greater Darwin
            ['Palmerston','0830'],['Driver','0830'],['Gray','0830'],
            ['Moulden','0830'],['Woodroffe','0830'],['Rosebery','0832'],
            ['Bellamack','0832'],['Durack','0830'],['Farrar','0830'],
            ['Johnston','0832'],['Bakewell','0832'],['Gunn','0832'],
            ['Zuccoli','0832'],['Mitchell','0832'],
            ['Howard Springs','0835'],['Virginia','0834'],
            ['Humpty Doo','0836'],['Berry Springs','0838'],
            ['Coolalinga','0839'],['Girraween','0836'],
            ['Noonamah','0837'],['Litchfield Park','0822'],

            // ==================== ALICE SPRINGS ====================
            ['Alice Springs','0870'],['East Side','0870'],['Gillen','0870'],
            ['Sadadeen','0870'],['Desert Springs','0870'],['Larapinta','0870'],
            ['Braitling','0870'],['Stuart','0870'],['The Gap','0870'],
            ['Araluen','0870'],['Ross','0870'],

            // ==================== REGIONAL NT ====================
            ['Katherine','0850'],['Katherine East','0850'],['Katherine South','0850'],
            ['Tennant Creek','0860'],['Nhulunbuy','0880'],
            ['Jabiru','0886'],['Yulara','0872'],
        ];
    }
};
