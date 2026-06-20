<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            // ── Page 1 ──
            ['Lessons & Pricing', 'How much do driving lessons cost?', '<p>Driving lesson prices on Secure Licence are set by each individual instructor, so they can vary depending on where you\'re located, your chosen transmission (manual or auto), and the instructor you select.</p><p>The best way to find out is to enter your suburb in our search tool and compare lesson costs instantly. You\'ll see available instructors, their pricing, ratings, and car details — all in one spot. You always see the exact price up front before you book, and on packages of 5+ lessons the card-processing fee is waived.</p>'],
            ['Lessons & Pricing', 'Do you offer any special lessons to prepare for the driving test?', '<p>Yes. Many instructors offer a dedicated <strong>Test-Day Package</strong> that includes a pre-test warm-up lesson, use of the instructor\'s vehicle for the test itself, and pickup/drop-off. It\'s the most popular option for learners sitting their test within the next few weeks.</p><p>You can also book regular lessons focused on the test routes and manoeuvres used in your area.</p>'],
            ['Lessons & Pricing', 'How many driving lessons do I need?', '<p>It depends entirely on your starting point. Complete beginners often need 15–25 hours of professional instruction, while learners with plenty of supervised practice may only need a handful of lessons before their test.</p><p>Your instructor will give you an honest assessment after the first lesson and recommend a plan that suits your goals and budget.</p>'],
            ['Lessons & Pricing', 'Can driving lessons count towards my logbook hours?', '<p>In most states, professional driving lessons with an accredited instructor count for <strong>bonus logbook hours</strong> (for example, the "3-for-1" structured lesson scheme in NSW). The exact rules vary by state.</p><p>Your instructor will record and sign your logbook for every lesson so the hours are properly credited.</p>'],
            ['Booking & Account', 'How do I book a driving lesson?', '<p>Search your suburb, compare verified instructors, pick a time that suits you, and pay securely by card — all in under 60 seconds. Your lesson is confirmed instantly and appears in your dashboard.</p><p>You\'ll receive a confirmation by email and SMS, plus a reminder before the lesson.</p>'],
            ['Booking & Account', 'How do I contact my driving instructor?', '<p>Once your booking is confirmed, your instructor\'s contact details are available in your dashboard, and they\'ll typically reach out to confirm the pickup location. You can message them about timing, the pickup address, or anything else you need before the lesson.</p>'],
            ['Booking & Account', 'How do I find a driving instructor?', '<p>Enter your suburb or postcode on the homepage and we\'ll show you verified instructors in your area, with ratings, reviews, pricing, transmission type and live availability. Filter by what matters to you and book the one that fits.</p>'],
            ['Getting Started', 'Can I take driving lessons if I have never driven before?', '<p>Absolutely — many of our learners start with zero experience. Our instructors specialise in first-time drivers and will begin with the basics in a quiet, low-pressure environment before building up to busier roads.</p>'],
            ['Payments', 'Which payment methods do you accept?', '<p>We accept all major <strong>credit and debit cards</strong> (Visa, Mastercard, American Express) through our secure Stripe payment system. Payment is taken at the time of booking, so there\'s no cash to organise on the day.</p>'],
            ['Getting Started', 'Can I book driving lessons if I already have my driver\'s licence?', '<p>Yes. Plenty of fully-licensed drivers book <strong>refresher lessons</strong> — whether they haven\'t driven in years, are nervous on highways or in heavy traffic, or want to learn a manual after driving automatic. Just choose a refresher lesson when you book.</p>'],

            // ── Page 2 ──
            ['Payments', 'How do I buy a driving lesson package & secure my discount?', '<p>When you book, choose a multi-lesson package instead of a single lesson. Packages are priced at a discount, and on packages of 5 or more lessons we also waive the card-processing fee — so the more lessons you bundle, the more you save.</p><p>You pay once at checkout and your lessons are stored against your account, ready to schedule whenever it suits you.</p>'],
            ['Booking & Account', 'What if there are no available driving instructors in my area?', '<p>If no instructors currently cover your suburb, register your interest and we\'ll notify you as soon as one becomes available nearby. Our network is growing constantly. You can also <a href="/contact">contact our team</a> and we\'ll do our best to help match you with someone in range.</p>'],
            ['Booking & Account', 'Can I have a different pickup & drop off address for my driving lesson?', '<p>Yes. When booking, you can set a different pickup and drop-off location — for example, picked up from home and dropped at work or school — as long as both are within your instructor\'s service area.</p>'],
            ['Lessons & Pricing', 'Where will my driving instructor take me to learn to drive?', '<p>Your instructor tailors each lesson to your level. Beginners start in quiet streets and car parks, then progress to busier roads, roundabouts, highways and the local test routes as your confidence grows.</p>'],
            ['Booking & Account', 'Where will my driving lessons be held?', '<p>Lessons start from your nominated pickup address and take place on real roads around your area. Your instructor chooses routes appropriate to your skill level and the conditions you\'ll face on test day.</p>'],
            ['Payments', 'Do I pay extra to use the driving instructor\'s vehicle in my driving lessons?', '<p>No. The lesson price includes use of the instructor\'s fully-insured, dual-control vehicle. There are no hidden vehicle hire fees for standard lessons.</p>'],
            ['Booking & Account', 'When should I book driving lessons?', '<p>As early as you can. Popular instructors and after-school/weekend slots fill quickly, especially in the lead-up to test dates. Booking ahead secures your preferred time and instructor.</p>'],
            ['Booking & Account', 'How do I buy or book more driving lessons with Secure Licence?', '<p>Log in to your dashboard, choose your instructor, and book additional lessons or a new package in a few clicks. Returning learners can re-book the same instructor directly from their lesson history.</p>'],
            ['Lessons & Pricing', 'How long are your driving lessons?', '<p>Lesson durations are set by each instructor and commonly range from 1 hour up to longer 1.5, 2, 3, 4 or 5-hour sessions and full test-day packages. You choose the duration that suits you when you book.</p>'],
            ['Getting Started', 'Can I take refresher driving lessons?', '<p>Yes. Refresher lessons are perfect if you\'re licensed but out of practice, nervous in certain conditions, or returning to driving after a break. Just select a refresher lesson when booking.</p>'],

            // ── Page 3 ──
            ['Booking & Account', 'Can I change instructors?', '<p>Of course. You\'re never locked in to one instructor. If you\'d prefer a different teaching style or availability, simply book your next lesson with another instructor in your area.</p>'],
            ['About Secure Licence', 'Is Secure Licence a driving school?', '<p>Secure Licence is an online marketplace that connects learners with independent, accredited driving instructors — not a traditional driving school. We bring transparency, choice and easy online booking, while your lessons are delivered by qualified local instructors.</p>'],
            ['About Secure Licence', 'Do your vehicles have dual controls?', '<p>Yes. Instructors on Secure Licence teach in dual-control vehicles fitted with a passenger-side brake, so your instructor can keep every lesson safe while you learn.</p>'],
            ['Lessons & Pricing', 'Can I book driving lessons to learn how to drive manual?', '<p>Yes. Filter instructors by <strong>manual</strong> transmission to find those who teach in a manual vehicle. It\'s a great option if you want a full (non-automatic) licence or plan to drive manual cars.</p>'],
            ['About Secure Licence', 'Where does Secure Licence offer driving lessons?', '<p>Secure Licence connects learners with instructors right across Australia — including Sydney, Melbourne, Brisbane, Perth, Adelaide, Hobart, Canberra and many regional centres. Enter your suburb to see who\'s available near you.</p>'],
        ];

        foreach ($faqs as $i => [$category, $question, $answer]) {
            Faq::updateOrCreate(
                ['slug' => Str::slug($question)],
                [
                    'question'     => $question,
                    'category'     => $category,
                    'answer'       => $answer,
                    'is_published' => true,
                    'sort_order'   => $i + 1,
                ]
            );
        }
    }
}
