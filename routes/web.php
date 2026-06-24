<?php

use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstructorProfileController;
use App\Http\Controllers\InstructorSearchController;
use App\Http\Controllers\Instructor\DashboardController as InstructorDashboard;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SuburbController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Support subdomain — registered FIRST so it matches before the main / route
|--------------------------------------------------------------------------
| Laravel matches routes in registration order — the first matching route
| wins regardless of domain. If support.* requests aren't caught here, they
| fall through to the main domain's `Route::get('/', ...)` below.
|
| The /support/* prefix fallback is registered later at the bottom of the file.
*/
if ($supportDomain = config('app.support_domain')) {
    Route::domain($supportDomain)->name('support.')->group(function () {
        $c = App\Http\Controllers\Support\SupportController::class;
        $req = App\Http\Controllers\Support\SupportRequestController::class;
        Route::get('/', [$c, 'home'])->name('home');
        Route::get('/search', [$c, 'search'])->name('search');
        Route::get('/submit-request', [$req, 'show'])->name('request.show');
        Route::post('/submit-request', [$req, 'store'])->name('request.store');
        Route::get('/categories/{category:slug}', [$c, 'category'])->name('category');
        Route::get('/sections/{section:slug}', [$c, 'section'])->name('section');
        Route::get('/articles/{article:slug}', [$c, 'article'])->name('article');
        Route::post('/articles/{article:slug}/feedback', [$c, 'feedback'])->name('article.feedback');
    });
}

Route::get('/', function () {
    return view('frontend.home');
});
Route::get('/find-instructor', function () {
    return view('find-instructor');
})->name('find-instructor');

Route::get('/find-instructor/results', function (\Illuminate\Http\Request $request) {
    $suburbId = (string) $request->query('suburb_id', '');
    $q = trim((string) $request->query('q', ''));

    // If absolutely nothing was provided, send back to the search form.
    if ($suburbId === '' && $q === '') {
        return redirect()->route('find-instructor');
    }

    return view('find-instructor-results', [
        'suburb_id' => $suburbId,
        'q' => $q,
        'transmission' => $request->query('transmission', ''),
        'test_pre_booked' => $request->boolean('test_pre_booked'),
    ]);
})->name('find-instructor.results');

Route::get('/instructors/{instructorProfile}', function (App\Models\InstructorProfile $instructorProfile) {
    // If this instructor has a friendly slug, send the visitor to the pretty URL
    // so links shared from the old numeric URL still work + the address bar looks clean.
    if ($instructorProfile->public_slug) {
        return redirect()->route('instructors.public', ['slug' => $instructorProfile->public_slug], 301);
    }
    $instructorProfile->load(['user', 'serviceAreas']);
    return view('instructor-public-show', ['instructorProfile' => $instructorProfile]);
})->name('instructors.show');

// Public shareable profile URL — instructors can put this on their CV / WhatsApp / socials
// Example: https://securelicence.com/i/john-smith
Route::get('/i/{slug}', function (string $slug) {
    $instructorProfile = App\Models\InstructorProfile::where('public_slug', $slug)
        ->with(['user', 'serviceAreas'])
        ->firstOrFail();
    return view('instructor-public-show', ['instructorProfile' => $instructorProfile]);
})->name('instructors.public');

// Static pages
Route::get('/about', fn () => view('frontend.pages.about'))->name('about');
Route::get('/contact', fn () => view('frontend.pages.contact'))->name('contact');
Route::post('/contact', [App\Http\Controllers\ContactController::class, 'send'])->name('contact.send');
Route::get('/terms-and-conditions', fn () => view('frontend.pages.terms'))->name('terms');
Route::get('/privacy-policy', fn () => view('frontend.pages.privacy'))->name('privacy');
// /support is handled by the support routes group further down — see SupportController.
// Previously this line redirected to /contact; removed because it intercepted
// the new help center routes before they could match.

// ── Stripe payment routes ──
// Webhook is unauthenticated + CSRF-exempted (Stripe doesn't send our cookies).
// The signature verification inside the handler is what authenticates it.
Route::post('/stripe/webhook', [App\Http\Controllers\PaymentController::class, 'webhook'])
    ->name('stripe.webhook');

Route::middleware('auth')->group(function () {
    Route::get('/pay/{booking}/checkout', [App\Http\Controllers\PaymentController::class, 'checkout'])->name('stripe.checkout');
    Route::get('/pay/{booking}/success',  [App\Http\Controllers\PaymentController::class, 'success'])->name('stripe.success');
    Route::get('/pay/{booking}/cancel',   [App\Http\Controllers\PaymentController::class, 'cancel'])->name('stripe.cancel');
});

// Policies hub and individual policies
Route::prefix('policies')->name('policies.')->group(function () {
    Route::get('/',                     fn () => view('frontend.policies.index'))->name('index');
    Route::get('/instructor-code-of-conduct', fn () => view('frontend.policies.instructor-conduct'))->name('instructor-conduct');
    Route::get('/learner-code-of-conduct',    fn () => view('frontend.policies.learner-conduct'))->name('learner-conduct');
    Route::get('/complaint-handling',   fn () => view('frontend.policies.complaint-handling'))->name('complaint-handling');
    Route::get('/refund-and-cancellation', fn () => view('frontend.policies.refund-cancellation'))->name('refund-cancellation');
    Route::get('/safety',               fn () => view('frontend.policies.safety'))->name('safety');
    Route::get('/dispute-resolution',   fn () => view('frontend.policies.dispute-resolution'))->name('dispute-resolution');
});
Route::get('/driving-test-packages', fn () => view('frontend.pages.driving-test-packages'))->name('driving-test-packages');
Route::get('/international-licence-conversions', fn () => view('frontend.pages.international-licence'))->name('international-licence');
Route::get('/refresher-lessons', fn () => view('frontend.pages.refresher-lessons'))->name('refresher-lessons');
// City-specific landing pages (Sydney, Melbourne, Brisbane, Perth, Adelaide, Hobart, Canberra)
Route::get('/driving-lessons/{city}', [App\Http\Controllers\CityLandingController::class, 'show'])->name('city.landing');
Route::get('/prices-and-packages', fn () => view('frontend.pages.prices-packages'))->name('prices-packages');
// Industry Insights (dynamic, admin-managed)
Route::get('/industry-insights', [App\Http\Controllers\IndustryInsightController::class, 'index'])->name('industry-insights');
// Newsletter landing — MUST be registered before the {slug} route so "newsletter" isn't treated as a slug.
Route::get('/industry-insights/newsletter', [App\Http\Controllers\NewsletterController::class, 'show'])->name('industry-insights.newsletter');
Route::post('/industry-insights/newsletter/subscribe', [App\Http\Controllers\NewsletterController::class, 'subscribe'])->name('industry-insights.newsletter.subscribe');
Route::get('/industry-insights/{slug}', [App\Http\Controllers\IndustryInsightController::class, 'show'])->name('industry-insights.show');
Route::get('/instruct-with-us', fn () => view('frontend.pages.instruct-with-us'))->name('instruct-with-us');
// Public instructor application form — submitting does NOT create an account.
// Admin reviews documents, then approves (which spins up an InstructorInvite) or rejects.
// "For Instructors" feature landing pages (linked from the mega-menu)
Route::get('/for-instructors/lead-generation', fn () => view('frontend.pages.instructors.lead-generation'))->name('for-instructors.lead-generation');
Route::get('/for-instructors/work-whenever-you-want', fn () => view('frontend.pages.instructors.work-whenever-you-want'))->name('for-instructors.work-whenever-you-want');
Route::get('/for-instructors/flexible-commitment', fn () => view('frontend.pages.instructors.flexible-commitment'))->name('for-instructors.flexible-commitment');
Route::get('/for-instructors/your-listing-profile', fn () => view('frontend.pages.instructors.your-listing-profile'))->name('for-instructors.your-listing-profile');
Route::get('/for-instructors/reputation-management', fn () => view('frontend.pages.instructors.reputation-management'))->name('for-instructors.reputation-management');
Route::get('/for-instructors/white-glove-concierge', fn () => view('frontend.pages.instructors.white-glove-concierge'))->name('for-instructors.white-glove-concierge');
Route::get('/for-instructors/tools-you-already-know', fn () => view('frontend.pages.instructors.tools-you-already-know'))->name('for-instructors.tools-you-already-know');
Route::get('/for-instructors/calendar-scheduling', fn () => view('frontend.pages.instructors.calendar-scheduling'))->name('for-instructors.calendar-scheduling');
Route::get('/for-instructors/payments-payouts', fn () => view('frontend.pages.instructors.payments-payouts'))->name('for-instructors.payments-payouts');
Route::get('/for-instructors/automated-reminders', fn () => view('frontend.pages.instructors.automated-reminders'))->name('for-instructors.automated-reminders');
Route::get('/for-instructors/no-show-protection', fn () => view('frontend.pages.instructors.no-show-protection'))->name('for-instructors.no-show-protection');
Route::get('/for-instructors/lesson-catalog', fn () => view('frontend.pages.instructors.lesson-catalog'))->name('for-instructors.lesson-catalog');
Route::get('/for-instructors/website-booking-link', fn () => view('frontend.pages.instructors.website-booking-link'))->name('for-instructors.website-booking-link');
Route::get('/for-instructors/learner-management', fn () => view('frontend.pages.instructors.learner-management'))->name('for-instructors.learner-management');

// Instructor signup now happens entirely on the support "Submit a request" form
// (the "I am a driving instructor interested in joining…" option). The old standalone
// /apply-as-instructor page redirects there so any saved link still works.
Route::get('/apply-as-instructor', fn () => redirect()->route('support.request.show', ['topic' => 'instructor']))->name('instructor-application.show');
Route::post('/apply-as-instructor', [App\Http\Controllers\InstructorApplicationController::class, 'store'])->name('instructor-application.store');
// Instructor Academy permanently removed — Secure Licence is NOT an RTO (Registered
// Training Organisation). Running a training academy requires separate RTO certification
// and is out of scope for this platform.
Route::get('/gift-vouchers', fn () => view('frontend.pages.gift-vouchers'))->name('gift-vouchers');
// FAQs — listing + per-question detail pages
Route::get('/faqs', [App\Http\Controllers\FaqController::class, 'index'])->name('faqs.index');
Route::get('/faqs/{slug}', [App\Http\Controllers\FaqController::class, 'show'])->name('faqs.show');
Route::get('/practice-test', [App\Http\Controllers\PracticeTestController::class, 'index'])->name('practice-test');
Route::get('/practice-test/{state}/test', [App\Http\Controllers\PracticeTestController::class, 'quiz'])->name('practice-test.quiz');
Route::get('/practice-test/{state}', [App\Http\Controllers\PracticeTestController::class, 'state'])->name('practice-test.state');

// Blog (public)
Route::get('/blog', [App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');

// Lesson confirmation (token-authenticated, no login needed — anti-chargeback proof)
Route::get('/lesson-confirmation/{token}', [App\Http\Controllers\LessonConfirmationController::class, 'show'])->name('lesson-confirmation.show');
Route::post('/lesson-confirmation/{token}', [App\Http\Controllers\LessonConfirmationController::class, 'confirm'])->name('lesson-confirmation.confirm');

// Public instructor invite accept link (sent in email — no login required)
Route::get('/instructor-invite/{token}', [App\Http\Controllers\Instructor\LearnersController::class, 'acceptInvite'])->name('instructor-invite.accept');

// Calendar ICS feeds (token-authenticated, no login needed)
Route::get('/calendar/instructor/{token}/feed.ics', [App\Http\Controllers\CalendarFeedController::class, 'instructorFeed'])->name('calendar.instructor.feed');
Route::get('/calendar/learner/{token}/feed.ics', [App\Http\Controllers\CalendarFeedController::class, 'learnerFeed'])->name('calendar.learner.feed');

// Google Calendar two-way sync (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/google-calendar/connect', [GoogleCalendarController::class, 'connect'])->name('google-calendar.connect');
    Route::get('/google-calendar/callback', [GoogleCalendarController::class, 'callback'])->name('google-calendar.callback');
    Route::post('/google-calendar/disconnect', [GoogleCalendarController::class, 'disconnect'])->name('google-calendar.disconnect');
    Route::get('/api/google-calendar/status', [GoogleCalendarController::class, 'status'])->name('google-calendar.status');
    Route::post('/api/google-calendar/sync', [GoogleCalendarController::class, 'syncNow'])->name('google-calendar.sync');
});

// ─── SEO: dynamic robots.txt + sitemap.xml (admin-controlled via SiteSetting) ───
Route::get('/robots.txt', function () {
    $mode = \App\Models\SiteSetting::get('seo_robots_mode', 'index_follow');
    $sitemapEnabled = \App\Models\SiteSetting::get('seo_sitemap_enabled', true);

    if ($mode === 'noindex_nofollow') {
        $body = "User-agent: *\nDisallow: /\n";
    } else {
        $body  = "User-agent: *\n";
        $body .= "Disallow: /admin\n";
        $body .= "Disallow: /instructor\n";
        $body .= "Disallow: /learner\n";
        $body .= "Disallow: /api\n";
        $body .= "Disallow: /login\n";
        $body .= "Disallow: /register\n";
        $body .= "Disallow: /password\n";
        $body .= "Allow: /\n";
        if ($sitemapEnabled) {
            $body .= "\nSitemap: ".url('/sitemap.xml')."\n";
        }
    }

    return response($body, 200, ['Content-Type' => 'text/plain']);
})->name('robots');

Route::get('/sitemap.xml', function () {
    if (! \App\Models\SiteSetting::get('seo_sitemap_enabled', true)) {
        abort(404);
    }

    $urls = collect();
    $base = rtrim(\App\Models\SiteSetting::get('seo_canonical_host', '') ?: url('/'), '/');

    // Static public pages — keep this list in sync as new pages are added
    $staticPaths = [
        '/'                              => ['priority' => '1.0', 'changefreq' => 'weekly'],
        '/find-instructor'               => ['priority' => '0.9', 'changefreq' => 'weekly'],
        '/prices-and-packages'           => ['priority' => '0.8', 'changefreq' => 'monthly'],
        '/driving-test-packages'         => ['priority' => '0.8', 'changefreq' => 'monthly'],
        '/refresher-lessons'             => ['priority' => '0.7', 'changefreq' => 'monthly'],
        '/international-licence-conversions' => ['priority' => '0.7', 'changefreq' => 'monthly'],
        '/instruct-with-us'              => ['priority' => '0.7', 'changefreq' => 'monthly'],
        '/practice-test'                 => ['priority' => '0.7', 'changefreq' => 'monthly'],
        '/blog'                          => ['priority' => '0.8', 'changefreq' => 'daily'],
        '/industry-insights'             => ['priority' => '0.7', 'changefreq' => 'weekly'],
        '/about'                         => ['priority' => '0.5', 'changefreq' => 'yearly'],
        '/contact'                       => ['priority' => '0.5', 'changefreq' => 'yearly'],
    ];
    foreach ($staticPaths as $path => $meta) {
        $urls->push([
            'loc'        => $base.$path,
            'changefreq' => $meta['changefreq'],
            'priority'   => $meta['priority'],
            'lastmod'    => now()->toAtomString(),
        ]);
    }

    // City landing pages
    foreach (['sydney', 'melbourne', 'brisbane', 'perth', 'adelaide', 'hobart', 'canberra'] as $citySlug) {
        $urls->push([
            'loc' => $base.'/driving-lessons/'.$citySlug,
            'changefreq' => 'weekly',
            'priority' => '0.8',
            'lastmod' => now()->toAtomString(),
        ]);
    }

    // State practice-test pages
    foreach (['nsw', 'vic', 'qld', 'wa', 'sa', 'tas', 'act'] as $stateSlug) {
        $urls->push([
            'loc' => $base.'/practice-test/'.$stateSlug,
            'changefreq' => 'monthly',
            'priority' => '0.6',
            'lastmod' => now()->toAtomString(),
        ]);
    }

    // Blog posts
    try {
        \App\Models\BlogPost::published()->select('slug', 'updated_at')->get()->each(function ($p) use ($urls, $base) {
            $urls->push([
                'loc' => $base.'/blog/'.$p->slug,
                'changefreq' => 'monthly',
                'priority' => '0.6',
                'lastmod' => $p->updated_at?->toAtomString(),
            ]);
        });
    } catch (\Throwable $e) {}

    // Industry insights
    try {
        \App\Models\IndustryInsight::published()->select('slug', 'updated_at')->get()->each(function ($p) use ($urls, $base) {
            $urls->push([
                'loc' => $base.'/industry-insights/'.$p->slug,
                'changefreq' => 'monthly',
                'priority' => '0.5',
                'lastmod' => $p->updated_at?->toAtomString(),
            ]);
        });
    } catch (\Throwable $e) {}

    // Public instructor profiles
    try {
        \App\Models\InstructorProfile::where('is_active', true)
            ->where('verification_status', 'verified')
            ->whereNotNull('public_slug')
            ->select('public_slug', 'updated_at')
            ->get()
            ->each(function ($p) use ($urls, $base) {
                $urls->push([
                    'loc' => $base.'/i/'.$p->public_slug,
                    'changefreq' => 'weekly',
                    'priority' => '0.6',
                    'lastmod' => $p->updated_at?->toAtomString(),
                ]);
            });
    } catch (\Throwable $e) {}

    $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
    foreach ($urls as $u) {
        $xml .= "  <url>\n";
        $xml .= "    <loc>".htmlspecialchars($u['loc'])."</loc>\n";
        if (! empty($u['lastmod']))    $xml .= "    <lastmod>{$u['lastmod']}</lastmod>\n";
        if (! empty($u['changefreq'])) $xml .= "    <changefreq>{$u['changefreq']}</changefreq>\n";
        if (! empty($u['priority']))   $xml .= "    <priority>{$u['priority']}</priority>\n";
        $xml .= "  </url>\n";
    }
    $xml .= "</urlset>\n";

    return response($xml, 200, ['Content-Type' => 'application/xml']);
})->name('sitemap');

Auth::routes();

// Dedicated login URLs for top bar (split-screen UI)
Route::get('/learner/login', function () {
    return view('auth.learner-login');
})->name('learner.login')->middleware('guest');
Route::get('/instructor/login', function () {
    return view('auth.instructor-login');
})->name('instructor.login')->middleware('guest');

// Instructor invite — magic-link signup (single-use, 7-day expiry)
Route::middleware('guest')->group(function () {
    Route::get('/instructor/invite/{token}', [App\Http\Controllers\InstructorInviteController::class, 'show'])->name('instructor.invite.show');
    Route::post('/instructor/invite/{token}/register', [App\Http\Controllers\InstructorInviteController::class, 'register'])->name('instructor.invite.register');
});

Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('auth');

// Admin panel
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Users management
    Route::get('/users', [App\Http\Controllers\Admin\UsersController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [App\Http\Controllers\Admin\UsersController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/json', [App\Http\Controllers\Admin\UsersController::class, 'showJson'])->name('users.show-json');
    Route::patch('/users/{user}/toggle-active', [App\Http\Controllers\Admin\UsersController::class, 'toggleActive'])->name('users.toggle-active');
    Route::patch('/users/{user}/update-role', [App\Http\Controllers\Admin\UsersController::class, 'updateRole'])->name('users.update-role');
    Route::post('/users/{user}/notes', [App\Http\Controllers\Admin\UsersController::class, 'storeNote'])->name('users.notes.store');
    Route::delete('/users/notes/{userAdminNote}', [App\Http\Controllers\Admin\UsersController::class, 'deleteNote'])->name('users.notes.destroy');
    Route::patch('/users/notes/{userAdminNote}/toggle-pin', [App\Http\Controllers\Admin\UsersController::class, 'toggleNotePin'])->name('users.notes.toggle-pin');

    // Instructors management
    Route::get('/instructors', [App\Http\Controllers\Admin\InstructorsController::class, 'index'])->name('instructors.index');
    Route::get('/instructors/{instructorProfile}', [App\Http\Controllers\Admin\InstructorsController::class, 'show'])->name('instructors.show');
    Route::patch('/instructors/{instructorProfile}/update-verification', [App\Http\Controllers\Admin\InstructorsController::class, 'updateVerification'])->name('instructors.update-verification');
    Route::patch('/instructors/{instructorProfile}/toggle-active', [App\Http\Controllers\Admin\InstructorsController::class, 'toggleActive'])->name('instructors.toggle-active');
    Route::patch('/instructors/documents/{instructorDocument}/status', [App\Http\Controllers\Admin\InstructorsController::class, 'updateDocumentStatus'])->name('instructors.update-document-status');
    Route::patch('/instructors/reviews/{review}/approve', [App\Http\Controllers\Admin\InstructorsController::class, 'approveReview'])->name('instructors.approve-review');
    Route::patch('/instructors/reviews/{review}/reject', [App\Http\Controllers\Admin\InstructorsController::class, 'rejectReview'])->name('instructors.reject-review');
    Route::delete('/instructors/reviews/{review}', [App\Http\Controllers\Admin\InstructorsController::class, 'deleteReview'])->name('instructors.delete-review');
    Route::patch('/instructors/reviews/{review}/toggle-visibility', [App\Http\Controllers\Admin\InstructorsController::class, 'toggleReviewVisibility'])->name('instructors.toggle-review-visibility');

    // Instructor rating adjustment
    Route::post('/instructors/{instructorProfile}/adjust-rating', [App\Http\Controllers\Admin\InstructorsController::class, 'adjustRating'])->name('instructors.adjust-rating');

    // Instructor audit/history — blocks
    Route::post('/instructors/{instructorProfile}/blocks', [App\Http\Controllers\Admin\InstructorsController::class, 'storeBlock'])->name('instructors.blocks.store');
    Route::patch('/instructors/blocks/{instructorBlock}/lift', [App\Http\Controllers\Admin\InstructorsController::class, 'liftBlock'])->name('instructors.blocks.lift');

    // Instructor audit/history — warnings
    Route::post('/instructors/{instructorProfile}/warnings', [App\Http\Controllers\Admin\InstructorsController::class, 'storeWarning'])->name('instructors.warnings.store');
    Route::delete('/instructors/warnings/{instructorWarning}', [App\Http\Controllers\Admin\InstructorsController::class, 'deleteWarning'])->name('instructors.warnings.destroy');

    // Instructor audit/history — complaints
    Route::post('/instructors/{instructorProfile}/complaints', [App\Http\Controllers\Admin\InstructorsController::class, 'storeComplaint'])->name('instructors.complaints.store');
    Route::patch('/instructors/complaints/{instructorComplaint}', [App\Http\Controllers\Admin\InstructorsController::class, 'updateComplaintStatus'])->name('instructors.complaints.update');
    Route::delete('/instructors/complaints/{instructorComplaint}', [App\Http\Controllers\Admin\InstructorsController::class, 'deleteComplaint'])->name('instructors.complaints.destroy');

    // Instructor audit/history — admin notes
    Route::post('/instructors/{instructorProfile}/notes', [App\Http\Controllers\Admin\InstructorsController::class, 'storeNote'])->name('instructors.notes.store');
    Route::delete('/instructors/notes/{instructorAdminNote}', [App\Http\Controllers\Admin\InstructorsController::class, 'deleteNote'])->name('instructors.notes.destroy');
    Route::patch('/instructors/notes/{instructorAdminNote}/toggle-pin', [App\Http\Controllers\Admin\InstructorsController::class, 'toggleNotePin'])->name('instructors.notes.toggle-pin');

    // Instructor audit/history — correspondence log
    Route::post('/instructors/{instructorProfile}/correspondences', [App\Http\Controllers\Admin\InstructorsController::class, 'storeCorrespondence'])->name('instructors.correspondences.store');
    Route::delete('/instructors/correspondences/{instructorCorrespondence}', [App\Http\Controllers\Admin\InstructorsController::class, 'deleteCorrespondence'])->name('instructors.correspondences.destroy');

    // Gift Vouchers management
    Route::get('/gift-vouchers', [App\Http\Controllers\Admin\GiftVouchersController::class, 'index'])->name('gift-vouchers.index');
    Route::get('/gift-vouchers/create', [App\Http\Controllers\Admin\GiftVouchersController::class, 'create'])->name('gift-vouchers.create');
    Route::post('/gift-vouchers', [App\Http\Controllers\Admin\GiftVouchersController::class, 'store'])->name('gift-vouchers.store');
    Route::get('/gift-vouchers/{giftVoucher}', [App\Http\Controllers\Admin\GiftVouchersController::class, 'show'])->name('gift-vouchers.show');
    Route::get('/gift-vouchers/{giftVoucher}/edit', [App\Http\Controllers\Admin\GiftVouchersController::class, 'edit'])->name('gift-vouchers.edit');
    Route::put('/gift-vouchers/{giftVoucher}', [App\Http\Controllers\Admin\GiftVouchersController::class, 'update'])->name('gift-vouchers.update');
    Route::patch('/gift-vouchers/{giftVoucher}/cancel', [App\Http\Controllers\Admin\GiftVouchersController::class, 'cancel'])->name('gift-vouchers.cancel');

    // Bookings management
    Route::get('/bookings', [App\Http\Controllers\Admin\BookingsController::class, 'index'])->name('bookings.index');
    Route::patch('/bookings/{booking}/update-status', [App\Http\Controllers\Admin\BookingsController::class, 'updateStatus'])->name('bookings.update-status');
    Route::post('/bookings/{booking}/refund', [App\Http\Controllers\Admin\BookingsController::class, 'refund'])->name('bookings.refund');
    Route::post('/bookings/{booking}/hold-payment', [App\Http\Controllers\Admin\BookingsController::class, 'holdPayment'])->name('bookings.hold-payment');
    Route::post('/bookings/{booking}/release-payment', [App\Http\Controllers\Admin\BookingsController::class, 'releasePayment'])->name('bookings.release-payment');

    // Coupons / Promo codes management
    Route::get('/coupons', [App\Http\Controllers\Admin\CouponsController::class, 'index'])->name('coupons.index');
    Route::get('/coupons/create', [App\Http\Controllers\Admin\CouponsController::class, 'create'])->name('coupons.create');
    Route::post('/coupons', [App\Http\Controllers\Admin\CouponsController::class, 'store'])->name('coupons.store');
    Route::get('/coupons/{coupon}/edit', [App\Http\Controllers\Admin\CouponsController::class, 'edit'])->name('coupons.edit');
    Route::put('/coupons/{coupon}', [App\Http\Controllers\Admin\CouponsController::class, 'update'])->name('coupons.update');
    Route::patch('/coupons/{coupon}/toggle', [App\Http\Controllers\Admin\CouponsController::class, 'toggle'])->name('coupons.toggle');
    Route::delete('/coupons/{coupon}', [App\Http\Controllers\Admin\CouponsController::class, 'destroy'])->name('coupons.destroy');

    // Centralized Reviews management — full control panel across all instructors
    Route::get('/reviews', [App\Http\Controllers\Admin\ReviewsController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/bulk', [App\Http\Controllers\Admin\ReviewsController::class, 'bulk'])->name('reviews.bulk');
    Route::post('/reviews/{review}/approve', [App\Http\Controllers\Admin\ReviewsController::class, 'approve'])->name('reviews.approve');
    Route::post('/reviews/{review}/reject', [App\Http\Controllers\Admin\ReviewsController::class, 'reject'])->name('reviews.reject');
    Route::post('/reviews/{review}/toggle-visibility', [App\Http\Controllers\Admin\ReviewsController::class, 'toggleVisibility'])->name('reviews.toggle-visibility');
    Route::delete('/reviews/{review}', [App\Http\Controllers\Admin\ReviewsController::class, 'destroy'])->name('reviews.destroy');

    // Calendar view
    Route::get('/calendar', fn () => view('admin.calendar'))->name('calendar');

    // Payouts management
    Route::get('/payouts', [App\Http\Controllers\Admin\PayoutsController::class, 'index'])->name('payouts.index');
    Route::get('/payouts/export-csv', [App\Http\Controllers\Admin\PayoutsController::class, 'exportCsv'])->name('payouts.export-csv');
    Route::post('/payouts/generate', [App\Http\Controllers\Admin\PayoutsController::class, 'generate'])->name('payouts.generate');
    Route::post('/payouts/bulk-approve', [App\Http\Controllers\Admin\PayoutsController::class, 'bulkApprove'])->name('payouts.bulk-approve');
    Route::post('/payouts/bulk-mark-paid', [App\Http\Controllers\Admin\PayoutsController::class, 'bulkMarkPaid'])->name('payouts.bulk-mark-paid');
    Route::get('/payouts/{instructorPayout}', [App\Http\Controllers\Admin\PayoutsController::class, 'show'])->name('payouts.show');
    Route::patch('/payouts/{instructorPayout}/approve', [App\Http\Controllers\Admin\PayoutsController::class, 'approve'])->name('payouts.approve');
    Route::patch('/payouts/{instructorPayout}/mark-paid', [App\Http\Controllers\Admin\PayoutsController::class, 'markPaid'])->name('payouts.mark-paid');
    Route::patch('/payouts/{instructorPayout}/mark-failed', [App\Http\Controllers\Admin\PayoutsController::class, 'markFailed'])->name('payouts.mark-failed');
    Route::post('/payouts/{instructorPayout}/notes', [App\Http\Controllers\Admin\PayoutsController::class, 'addNote'])->name('payouts.add-note');

    // Blog management
    Route::get('/blog', [App\Http\Controllers\Admin\BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/create', [App\Http\Controllers\Admin\BlogController::class, 'create'])->name('blog.create');
    Route::post('/blog', [App\Http\Controllers\Admin\BlogController::class, 'store'])->name('blog.store');
    Route::get('/blog/{blogPost}/edit', [App\Http\Controllers\Admin\BlogController::class, 'edit'])->name('blog.edit');
    Route::put('/blog/{blogPost}', [App\Http\Controllers\Admin\BlogController::class, 'update'])->name('blog.update');
    Route::get('/blog/categories', [App\Http\Controllers\Admin\BlogController::class, 'categories'])->name('blog.categories');

    // Instructor applications (public-form pipeline) — admin reviews docs then approves/rejects
    Route::get('/instructor-applications', [App\Http\Controllers\Admin\InstructorApplicationController::class, 'index'])->name('instructor-applications.index');
    Route::get('/instructor-applications/{instructorApplication}', [App\Http\Controllers\Admin\InstructorApplicationController::class, 'show'])->name('instructor-applications.show');
    Route::post('/instructor-applications/{instructorApplication}/under-review', [App\Http\Controllers\Admin\InstructorApplicationController::class, 'markUnderReview'])->name('instructor-applications.under-review');
    Route::post('/instructor-applications/{instructorApplication}/approve', [App\Http\Controllers\Admin\InstructorApplicationController::class, 'approve'])->name('instructor-applications.approve');
    Route::post('/instructor-applications/{instructorApplication}/reject', [App\Http\Controllers\Admin\InstructorApplicationController::class, 'reject'])->name('instructor-applications.reject');

    // Instructor invites (magic-link onboarding)
    Route::get('/instructor-invites', [App\Http\Controllers\Admin\InstructorInviteController::class, 'index'])->name('instructor-invites.index');
    Route::post('/instructor-invites', [App\Http\Controllers\Admin\InstructorInviteController::class, 'store'])->name('instructor-invites.store');
    Route::post('/instructor-invites/{instructorInvite}/resend', [App\Http\Controllers\Admin\InstructorInviteController::class, 'resend'])->name('instructor-invites.resend');
    Route::patch('/instructor-invites/{instructorInvite}/cancel', [App\Http\Controllers\Admin\InstructorInviteController::class, 'cancel'])->name('instructor-invites.cancel');
    Route::delete('/instructor-invites/{instructorInvite}', [App\Http\Controllers\Admin\InstructorInviteController::class, 'destroy'])->name('instructor-invites.destroy');

    // Industry Insights management (clones blog structure)
    Route::get('/industry-insights/categories', [App\Http\Controllers\Admin\IndustryInsightController::class, 'categories'])->name('industry-insights.categories');
    Route::get('/industry-insights', [App\Http\Controllers\Admin\IndustryInsightController::class, 'index'])->name('industry-insights.index');
    Route::get('/industry-insights/create', [App\Http\Controllers\Admin\IndustryInsightController::class, 'create'])->name('industry-insights.create');
    Route::post('/industry-insights', [App\Http\Controllers\Admin\IndustryInsightController::class, 'store'])->name('industry-insights.store');
    Route::get('/industry-insights/{industryInsight}/edit', [App\Http\Controllers\Admin\IndustryInsightController::class, 'edit'])->name('industry-insights.edit');
    Route::put('/industry-insights/{industryInsight}', [App\Http\Controllers\Admin\IndustryInsightController::class, 'update'])->name('industry-insights.update');

    // Practice test questions
    Route::get('/practice-questions', [App\Http\Controllers\Admin\PracticeQuestionController::class, 'index'])->name('practice-questions.index');
    Route::post('/practice-questions/counts', [App\Http\Controllers\Admin\PracticeQuestionController::class, 'updateCounts'])->name('practice-questions.counts');
    Route::get('/practice-questions/create', [App\Http\Controllers\Admin\PracticeQuestionController::class, 'create'])->name('practice-questions.create');
    Route::post('/practice-questions', [App\Http\Controllers\Admin\PracticeQuestionController::class, 'store'])->name('practice-questions.store');
    Route::get('/practice-questions/{practiceQuestion}/edit', [App\Http\Controllers\Admin\PracticeQuestionController::class, 'edit'])->name('practice-questions.edit');
    Route::put('/practice-questions/{practiceQuestion}', [App\Http\Controllers\Admin\PracticeQuestionController::class, 'update'])->name('practice-questions.update');
    Route::patch('/practice-questions/{practiceQuestion}/toggle', [App\Http\Controllers\Admin\PracticeQuestionController::class, 'toggle'])->name('practice-questions.toggle');
    Route::delete('/practice-questions/{practiceQuestion}', [App\Http\Controllers\Admin\PracticeQuestionController::class, 'destroy'])->name('practice-questions.destroy');

    // Newsletter subscribers
    Route::get('/newsletter', [App\Http\Controllers\Admin\NewsletterController::class, 'index'])->name('newsletter.index');
    Route::get('/newsletter/export', [App\Http\Controllers\Admin\NewsletterController::class, 'export'])->name('newsletter.export');
    Route::patch('/newsletter/{subscriber}/toggle', [App\Http\Controllers\Admin\NewsletterController::class, 'toggle'])->name('newsletter.toggle');
    Route::delete('/newsletter/{subscriber}', [App\Http\Controllers\Admin\NewsletterController::class, 'destroy'])->name('newsletter.destroy');

    // FAQs (dynamic, admin-managed)
    Route::get('/faqs', [App\Http\Controllers\Admin\FaqController::class, 'index'])->name('faqs.index');
    Route::get('/faqs/create', [App\Http\Controllers\Admin\FaqController::class, 'create'])->name('faqs.create');
    Route::post('/faqs', [App\Http\Controllers\Admin\FaqController::class, 'store'])->name('faqs.store');
    Route::get('/faqs/{faq}/edit', [App\Http\Controllers\Admin\FaqController::class, 'edit'])->name('faqs.edit');
    Route::put('/faqs/{faq}', [App\Http\Controllers\Admin\FaqController::class, 'update'])->name('faqs.update');
    Route::patch('/faqs/{faq}/toggle', [App\Http\Controllers\Admin\FaqController::class, 'toggle'])->name('faqs.toggle');
    Route::delete('/faqs/{faq}', [App\Http\Controllers\Admin\FaqController::class, 'destroy'])->name('faqs.destroy');

    // Settings
    Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/seed', [App\Http\Controllers\Admin\SettingsController::class, 'seed'])->name('settings.seed');
});
Route::put('/user/profile', function (\Illuminate\Http\Request $request) {
    $user = $request->user();
    $isPersonalDetails = $request->has('first_name') || $request->has('postcode') || $request->has('current_password');
    if ($isPersonalDetails) {
        $rules = [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'preferred_first_name' => 'nullable|string|max:100',
            'gender' => 'required|in:male,female,other,prefer_not_to_say',
            'phone' => 'required|string|max:20',
            'postcode' => 'required|string|max:10',
            'current_password' => 'required|string|current_password',
            'new_password' => 'nullable|string|min:8|confirmed',
            'accepts_female_learners_only' => 'nullable|boolean',
        ];
        $validated = $request->validate($rules);
        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->preferred_first_name = $validated['preferred_first_name'] ?? null;
        $user->gender = $validated['gender'];
        $user->phone = $validated['phone'];
        $user->postcode = $validated['postcode'];
        $user->name = trim($validated['first_name'] . ' ' . $validated['last_name']);
        if (! empty($validated['new_password'])) {
            $user->password = $validated['new_password'];
        }
        $user->save();

        // Female-only safety preference (only applies to female-gendered instructors)
        if ($user->isInstructor() && $user->instructorProfile) {
            $femaleOnly = $request->boolean('accepts_female_learners_only');
            // Force-disable if user changed gender away from female
            if ($validated['gender'] !== 'female') {
                $femaleOnly = false;
            }
            $user->instructorProfile->update(['accepts_female_learners_only' => $femaleOnly]);
        }

        return response()->json(['message' => 'Saved.']);
    }
    $request->validate(['name' => 'required|string|max:255', 'phone' => 'nullable|string|max:20']);
    $user->update($request->only('name', 'phone'));
    return response()->json(['message' => 'Updated']);
})->name('user.profile.update')->middleware('auth');

// ── Learner booking flow — accessible to GUESTS and authenticated learners ──
// Guest bookings auto-create an account after successful payment (EasyLicence-style)
Route::prefix('learner')->name('learner.')->group(function () {
    // Step 2: Choose lesson amount (hours package with bulk discount)
    Route::get('/bookings/amount', [App\Http\Controllers\Learner\BookingController::class, 'amount'])->name('bookings.amount');
    Route::post('/bookings/amount', [App\Http\Controllers\Learner\BookingController::class, 'storeAmount'])->name('bookings.amount.store');
    // Step 2b: Add Driving Test Package upsell (Add or Skip)
    Route::get('/bookings/test-package', [App\Http\Controllers\Learner\BookingController::class, 'testPackage'])->name('bookings.test-package');
    Route::post('/bookings/test-package', [App\Http\Controllers\Learner\BookingController::class, 'storeTestPackage'])->name('bookings.test-package.store');
    // Step 3: Book your lessons (schedule individual lessons from purchased hours)
    Route::get('/bookings/new', [App\Http\Controllers\Learner\BookingController::class, 'create'])->name('bookings.new');
    Route::post('/bookings/continue', [App\Http\Controllers\Learner\BookingController::class, 'continueToPayment'])->name('bookings.continue');
    // Step 4: Learner Registration — collect personal details + (guest) password
    Route::get('/bookings/details', [App\Http\Controllers\Learner\BookingController::class, 'details'])->name('bookings.details');
    Route::post('/bookings/details', [App\Http\Controllers\Learner\BookingController::class, 'storeDetails'])->name('bookings.details.store');
    // Step 5: Payment
    Route::get('/bookings/payment', [App\Http\Controllers\Learner\BookingController::class, 'payment'])->name('bookings.payment');
    // Coupon application (AJAX) — accessible to guests + auth learners during checkout
    Route::post('/bookings/coupon/apply', [App\Http\Controllers\Learner\BookingController::class, 'applyCoupon'])->name('bookings.coupon.apply');
    Route::post('/bookings/coupon/remove', [App\Http\Controllers\Learner\BookingController::class, 'removeCoupon'])->name('bookings.coupon.remove');
});

// ── Learner authenticated routes ──
Route::middleware(['auth', 'role:learner'])->prefix('learner')->name('learner.')->group(function () {
    Route::get('/dashboard', fn () => view('learner.pages.dashboard'))->name('dashboard');
    Route::get('/calendar', fn () => view('learner.pages.calendar'))->name('calendar');
    Route::get('/wallet', fn () => view('learner.pages.wallet'))->name('wallet');
    Route::get('/wallet/add-credit', fn () => view('learner.pages.wallet-add-credit'))->name('wallet.add-credit');

    // ── Receipts ──
    Route::get('/receipts', [App\Http\Controllers\Learner\ReceiptsController::class, 'index'])->name('receipts');
    Route::get('/receipts/{booking}', [App\Http\Controllers\Learner\ReceiptsController::class, 'show'])->name('receipts.show');
    Route::get('/receipts/{booking}/download', [App\Http\Controllers\Learner\ReceiptsController::class, 'download'])->name('receipts.download');

    // ── Invite Friends (referral) ──
    Route::get('/invite-friends', [App\Http\Controllers\Learner\InviteController::class, 'index'])->name('invite');
    Route::post('/invite-friends/send', [App\Http\Controllers\Learner\InviteController::class, 'send'])->name('invite.send');

    // ── Give Feedback ──
    Route::get('/feedback', [App\Http\Controllers\Learner\FeedbackController::class, 'index'])->name('feedback');
    Route::post('/feedback', [App\Http\Controllers\Learner\FeedbackController::class, 'store'])->name('feedback.store');

    // ── Support ──
    Route::get('/support', [App\Http\Controllers\Learner\SupportController::class, 'index'])->name('support');
    Route::post('/support', [App\Http\Controllers\Learner\SupportController::class, 'send'])->name('support.send');
});

Route::middleware(['auth', 'role:instructor', 'instructor.onboarded'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::get('/onboarding/pending', fn () => view('instructor.pages.onboarding-pending'))->name('onboarding.pending');
    Route::get('/dashboard', fn () => view('instructor.pages.dashboard'))->name('dashboard');
    Route::get('/calendar', fn () => view('instructor.pages.calendar'))->name('calendar');
    Route::get('/learners', fn () => view('instructor.pages.learners'))->name('learners');
    Route::get('/reports', fn () => view('instructor.pages.reports'))->name('reports');

    // ── Statements (weekly/fortnightly/monthly PDF) ──
    Route::get('/statements', [App\Http\Controllers\Instructor\StatementsController::class, 'index'])->name('statements');
    Route::get('/statements/{key}', [App\Http\Controllers\Instructor\StatementsController::class, 'show'])->name('statements.show')->where('key', '\d{4}-\d{2}-\d{2}');
    Route::get('/statements/{key}/download', [App\Http\Controllers\Instructor\StatementsController::class, 'download'])->name('statements.download')->where('key', '\d{4}-\d{2}-\d{2}');
    Route::get('/notifications', function (\Illuminate\Http\Request $request) {
        $user = auth()->user();
        $tab = $request->query('tab', 'notifications'); // notifications | proposals

        if ($tab === 'proposals') {
            $query = \App\Models\Booking::with(['learner:id,name,email,phone', 'suburb.state'])
                ->where('instructor_id', $user->id)
                ->where('status', \App\Models\Booking::STATUS_PROPOSED);

            if ($lq = $request->query('learner_q')) {
                $query->whereHas('learner', function ($q) use ($lq) {
                    $q->where('name', 'like', "%{$lq}%")
                      ->orWhere('email', 'like', "%{$lq}%")
                      ->orWhere('phone', 'like', "%{$lq}%");
                });
            }
            if ($status = $request->query('proposal_status')) {
                if ($status === 'expiring')   $query->where('proposal_expires_at', '<=', now()->addHours(24));
                if ($status === 'expired')    $query->where('proposal_expires_at', '<', now());
                if ($status === 'fresh')      $query->where('proposal_expires_at', '>', now()->addHours(24));
            }
            $sort = $request->query('sort', 'recent');
            if ($sort === 'oldest')   $query->oldest();
            elseif ($sort === 'expiry') $query->orderBy('proposal_expires_at', 'asc');
            else                       $query->latest();

            return view('instructor.pages.notifications', [
                'tab'           => 'proposals',
                'proposals'     => $query->paginate(20)->withQueryString(),
                'notifications' => null,
            ]);
        }

        // Booking Notifications tab
        $notifications = $user->notifications()->latest()->paginate(20)->withQueryString();
        return view('instructor.pages.notifications', [
            'tab'           => 'notifications',
            'notifications' => $notifications,
            'proposals'     => null,
        ]);
    })->name('notifications');
    Route::post('/notifications/mark-all-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'All notifications marked as read.');
    })->name('notifications.mark-all-read');
    Route::post('/notifications/mark-selected-read', function (\Illuminate\Http\Request $request) {
        $ids = (array) $request->input('ids', []);
        auth()->user()->notifications()->whereIn('id', $ids)->whereNull('read_at')->update(['read_at' => now()]);
        return back()->with('success', count($ids) . ' notification(s) marked as read.');
    })->name('notifications.mark-selected-read');

    // Instructor support — categorised "Submit a request" form
    Route::get('/support', fn () => view('instructor.pages.support'))->name('support');
    Route::post('/support', [App\Http\Controllers\Instructor\SupportController::class, 'submit'])->name('support.submit');
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/personal-details', function () {
            $postcodes = \App\Models\Suburb::select('postcode')->distinct()->orderBy('postcode')->pluck('postcode');
            return view('instructor.settings.personal-details', ['postcodes' => $postcodes]);
        })->name('personal-details');
        Route::get('/profile', fn () => view('instructor.settings.profile'))->name('profile');
        Route::get('/vehicle', fn () => view('instructor.settings.vehicle'))->name('vehicle');
        Route::get('/service-area', fn () => view('instructor.settings.service-area'))->name('service-area');
        Route::get('/opening-hours', fn () => view('instructor.settings.opening-hours'))->name('opening-hours');
        Route::get('/calendar-settings', fn () => view('instructor.settings.calendar-settings'))->name('calendar-settings');
        Route::get('/pricing', fn () => view('instructor.settings.pricing'))->name('pricing');
        Route::get('/documents', fn () => view('instructor.settings.documents'))->name('documents');
        Route::get('/banking', fn () => view('instructor.settings.banking'))->name('banking');
        Route::get('/guide', fn () => view('instructor.settings.guide'))->name('guide');
    });
});

// API-style JSON routes (session auth for same-origin JS)
Route::prefix('api')->middleware('web')->group(function () {
    Route::get('suburbs/search', [SuburbController::class, 'search'])->name('api.suburbs.search');
    Route::get('suburbs/postcode/{postcode}', [SuburbController::class, 'byPostcode'])->name('api.suburbs.postcode');
    Route::get('instructors', [InstructorSearchController::class, 'index'])->name('api.instructors.index');
    Route::get('instructors/{instructorProfile}', [InstructorProfileController::class, 'show'])->name('api.instructors.show');
    Route::get('instructors/{instructorProfile}/availability/dates', [AvailabilityController::class, 'dates'])->name('api.availability.dates');
    Route::get('instructors/{instructorProfile}/availability/slots', [AvailabilityController::class, 'slots'])->name('api.availability.slots');

    // Learner booking payment — accepts guest OR authenticated users
    // (Guests get an account auto-created after successful payment)
    Route::post('learner/bookings/pay', [App\Http\Controllers\Learner\BookingController::class, 'processPayment'])->name('api.learner.bookings.pay');

    // Admin blog API routes
    Route::middleware(['auth', 'role:admin'])->prefix('admin/blog')->group(function () {
        Route::get('list', [App\Http\Controllers\Admin\BlogController::class, 'list']);
        Route::delete('{blogPost}', [App\Http\Controllers\Admin\BlogController::class, 'destroy']);
        Route::patch('{blogPost}/toggle-featured', [App\Http\Controllers\Admin\BlogController::class, 'toggleFeatured']);
        Route::get('categories/list', [App\Http\Controllers\Admin\BlogController::class, 'categoryList']);
        Route::post('categories', [App\Http\Controllers\Admin\BlogController::class, 'categoryStore']);
        Route::put('categories/{blogCategory}', [App\Http\Controllers\Admin\BlogController::class, 'categoryUpdate']);
        Route::delete('categories/{blogCategory}', [App\Http\Controllers\Admin\BlogController::class, 'categoryDestroy']);
    });

    // Admin industry-insights API routes (clones blog API)
    Route::middleware(['auth', 'role:admin'])->prefix('admin/industry-insights')->group(function () {
        Route::get('list', [App\Http\Controllers\Admin\IndustryInsightController::class, 'list']);
        Route::delete('{industryInsight}', [App\Http\Controllers\Admin\IndustryInsightController::class, 'destroy']);
        Route::patch('{industryInsight}/toggle-featured', [App\Http\Controllers\Admin\IndustryInsightController::class, 'toggleFeatured']);
        Route::get('categories/list', [App\Http\Controllers\Admin\IndustryInsightController::class, 'categoryList']);
        Route::post('categories', [App\Http\Controllers\Admin\IndustryInsightController::class, 'categoryStore']);
        Route::put('categories/{industryInsightCategory}', [App\Http\Controllers\Admin\IndustryInsightController::class, 'categoryUpdate']);
        Route::delete('categories/{industryInsightCategory}', [App\Http\Controllers\Admin\IndustryInsightController::class, 'categoryDestroy']);
    });

    // Admin calendar API (all bookings across all instructors)
    Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
        Route::get('calendar/bookings', [App\Http\Controllers\Admin\BookingsController::class, 'calendarBookings'])->name('api.admin.calendar.bookings');
    });

    // ── Vehicle makes & models (public lookups) ────────────
    Route::get('vehicle-makes', [App\Http\Controllers\Api\VehicleController::class, 'makes'])->name('api.vehicle-makes');
    Route::get('vehicle-makes/{vehicleMake}/models', [App\Http\Controllers\Api\VehicleController::class, 'models'])->name('api.vehicle-models');
    Route::get('mechanic-service-types', [App\Http\Controllers\Api\ServiceJobController::class, 'serviceTypes'])->name('api.mechanic-service-types');

    Route::middleware('auth')->group(function () {
        // ── Customer vehicles ────────────────────────────────
        Route::get('my-vehicles', [App\Http\Controllers\Api\VehicleController::class, 'index'])->name('api.vehicles.index');
        Route::post('my-vehicles', [App\Http\Controllers\Api\VehicleController::class, 'store'])->name('api.vehicles.store');
        Route::put('my-vehicles/{vehicle}', [App\Http\Controllers\Api\VehicleController::class, 'update'])->name('api.vehicles.update');
        Route::delete('my-vehicles/{vehicle}', [App\Http\Controllers\Api\VehicleController::class, 'destroy'])->name('api.vehicles.destroy');

        // ── Service job requests (customer submits, provider accepts/rejects) ──
        Route::post('mechanic/{serviceProvider}/job-request', [App\Http\Controllers\Api\ServiceJobController::class, 'submitJobRequest'])->name('api.mechanic.job-request');
        Route::get('mechanic/pending-jobs', [App\Http\Controllers\Api\ServiceJobController::class, 'pendingJobs'])->name('api.mechanic.pending-jobs');
        Route::get('mechanic/my-jobs', [App\Http\Controllers\Api\ServiceJobController::class, 'myJobs'])->name('api.mechanic.my-jobs');
        Route::patch('mechanic/jobs/{serviceBooking}/accept', [App\Http\Controllers\Api\ServiceJobController::class, 'acceptJob'])->name('api.mechanic.accept-job');
        Route::patch('mechanic/jobs/{serviceBooking}/reject', [App\Http\Controllers\Api\ServiceJobController::class, 'rejectJob'])->name('api.mechanic.reject-job');

        Route::get('bookings', [BookingController::class, 'index'])->name('api.bookings.index');
        Route::post('bookings', [BookingController::class, 'store'])->name('api.bookings.store');
        Route::get('bookings/{booking}', [BookingController::class, 'show'])->name('api.bookings.show');
        Route::put('bookings/{booking}/reschedule', [BookingController::class, 'reschedule'])->name('api.bookings.reschedule');
        Route::put('bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('api.bookings.cancel');
        Route::put('bookings/{booking}/accept', [BookingController::class, 'acceptProposed'])->name('api.bookings.accept');
        Route::post('reviews', [ReviewController::class, 'store'])->name('api.reviews.store');
        Route::patch('reviews/{review}/google-prompted', [ReviewController::class, 'markGooglePrompted'])->name('api.reviews.google-prompted');
        Route::put('bookings/{booking}/complete', [BookingController::class, 'complete'])->name('api.bookings.complete');
        Route::post('bookings/{booking}/arrived', [BookingController::class, 'markArrived'])->name('api.bookings.arrived');
        Route::post('bookings/{booking}/start-lesson', [BookingController::class, 'startLesson'])->name('api.bookings.start-lesson');
        Route::post('bookings/{booking}/end-lesson', [BookingController::class, 'endLesson'])->name('api.bookings.end-lesson');

        // Gift Vouchers API
        Route::post('gift-vouchers/purchase', [App\Http\Controllers\GiftVoucherController::class, 'purchase'])->name('api.gift-vouchers.purchase');
        Route::post('gift-vouchers/{giftVoucher}/confirm-payment', [App\Http\Controllers\GiftVoucherController::class, 'confirmPayment'])->name('api.gift-vouchers.confirm-payment');
        Route::post('gift-vouchers/redeem', [App\Http\Controllers\GiftVoucherController::class, 'redeem'])->name('api.gift-vouchers.redeem');
        Route::post('gift-vouchers/check', [App\Http\Controllers\GiftVoucherController::class, 'check'])->name('api.gift-vouchers.check');

        // Calendar sync
        Route::get('calendar/subscribe-urls', [App\Http\Controllers\CalendarFeedController::class, 'generateToken'])->name('api.calendar.subscribe');
        Route::post('calendar/regenerate-token', [App\Http\Controllers\CalendarFeedController::class, 'regenerateToken'])->name('api.calendar.regenerate');
        Route::get('bookings/{booking}/download-ics', [App\Http\Controllers\CalendarFeedController::class, 'downloadBooking'])->name('api.bookings.download-ics');

        Route::middleware('role:learner')->prefix('learner')->name('api.learner.')->group(function () {
            Route::get('dashboard', [App\Http\Controllers\Learner\DashboardController::class, 'index'])->name('dashboard');
            Route::get('wallet', [App\Http\Controllers\Learner\WalletController::class, 'show'])->name('wallet.show');
            Route::get('wallet/transactions', [App\Http\Controllers\Learner\WalletController::class, 'transactions'])->name('wallet.transactions');
            Route::post('wallet/add-credit', [App\Http\Controllers\Learner\WalletController::class, 'addCredit'])->name('wallet.add-credit');
        });

        Route::middleware('role:instructor')->prefix('instructor')->name('api.instructor.')->group(function () {
            Route::get('profile', [InstructorDashboard::class, 'profile'])->name('profile');
            Route::put('profile', [InstructorDashboard::class, 'updateProfile'])->name('profile.update');
            Route::post('profile/photo', [InstructorDashboard::class, 'uploadProfilePhoto'])->name('profile.photo');
            Route::post('profile/vehicle-photo', [InstructorDashboard::class, 'uploadVehiclePhoto'])->name('profile.vehicle-photo');
            Route::get('learners', [App\Http\Controllers\Instructor\LearnersController::class, 'index'])->name('learners');
            Route::get('learners/pending-invites', [App\Http\Controllers\Instructor\LearnersController::class, 'pendingInvites'])->name('learners.pending-invites');
            Route::get('learners/{user}', [App\Http\Controllers\Instructor\LearnersController::class, 'show'])->name('learners.show');
            Route::post('learners/invite', [App\Http\Controllers\Instructor\LearnersController::class, 'invite'])->name('learners.invite');
            Route::post('learners/invite/{invite}/resend', [App\Http\Controllers\Instructor\LearnersController::class, 'resendInvite'])->name('learners.invite.resend');
            Route::delete('learners/invite/{invite}', [App\Http\Controllers\Instructor\LearnersController::class, 'cancelInvite'])->name('learners.invite.cancel');
            Route::post('booking-proposals', [App\Http\Controllers\Instructor\BookingProposalController::class, 'store'])->name('booking-proposals.store');
            Route::put('profile/service-areas', [InstructorDashboard::class, 'updateServiceAreas'])->name('profile.service-areas');
            Route::put('profile/availability', [InstructorDashboard::class, 'updateAvailability'])->name('profile.availability');
            Route::put('profile/calendar-settings', [InstructorDashboard::class, 'updateCalendarSettings'])->name('profile.calendar-settings');
            Route::get('documents', [App\Http\Controllers\Instructor\DocumentsController::class, 'index'])->name('documents.index');
            Route::post('documents', [App\Http\Controllers\Instructor\DocumentsController::class, 'store'])->name('documents.store');
            Route::put('profile/banking', [InstructorDashboard::class, 'updateBanking'])->name('profile.banking');
            Route::get('reports', [App\Http\Controllers\Instructor\ReportsController::class, 'index'])->name('reports.index');
            Route::get('reports/fy-statement/{year}/download', [App\Http\Controllers\Instructor\ReportsController::class, 'downloadFinancialYearStatement'])->name('reports.fy-download');
        });
    });
});

/*
|--------------------------------------------------------------------------
| Service Provider Marketplace (Plumber, Electrician, etc.)
|--------------------------------------------------------------------------
*/

/*
| ──────────────────────────────────────────────────────────────────
| DISABLED for Phase 1 launch — Public services browse + bookings.
| We're focused only on learner / driving instructor flows for now.
| ──────────────────────────────────────────────────────────────────
| Route::get('/become-a-provider', [App\Http\Controllers\ServiceController::class, 'becomeProvider'])->name('services.become-provider');
| Route::get('/services', [App\Http\Controllers\ServiceController::class, 'categories'])->name('services.categories');
| Route::get('/services/{slug}', [App\Http\Controllers\ServiceController::class, 'browse'])->name('services.browse');
| Route::get('/services/{slug}/{provider}', [App\Http\Controllers\ServiceController::class, 'show'])->name('services.show');
|
| Route::middleware(['auth'])->group(function () {
|     Route::get('/services/{provider}/book', [App\Http\Controllers\ServiceBookingController::class, 'create'])->name('service-bookings.create');
|     Route::post('/services/{provider}/book', [App\Http\Controllers\ServiceBookingController::class, 'store'])->name('service-bookings.store');
|     Route::get('/my-service-bookings', [App\Http\Controllers\ServiceBookingController::class, 'index'])->name('service-bookings.index');
|     Route::get('/service-bookings/{serviceBooking}', [App\Http\Controllers\ServiceBookingController::class, 'show'])->name('service-bookings.show');
| });
*/

/*
| ──────────────────────────────────────────────────────────────────
| DISABLED for Phase 1 launch — Service provider portal.
| Only learner / driving instructor flows are active for now.
| Re-enable when expanding to other service categories.
| ──────────────────────────────────────────────────────────────────
| Route::middleware(['auth'])->prefix('service-provider')->name('service-provider.')->group(function () {
|     Route::get('/onboarding', [App\Http\Controllers\ServiceProvider\DashboardController::class, 'onboardingCreate'])->name('onboarding.create');
|     Route::post('/onboarding', [App\Http\Controllers\ServiceProvider\DashboardController::class, 'onboardingStore'])->name('onboarding.store');
|     Route::get('/dashboard', [App\Http\Controllers\ServiceProvider\DashboardController::class, 'index'])->name('dashboard');
|     Route::get('/availability', [App\Http\Controllers\ServiceProvider\AvailabilityController::class, 'index'])->name('availability.index');
|     Route::post('/availability/slots', [App\Http\Controllers\ServiceProvider\AvailabilityController::class, 'storeSlot'])->name('availability.slots.store');
|     Route::delete('/availability/slots/{slot}', [App\Http\Controllers\ServiceProvider\AvailabilityController::class, 'destroySlot'])->name('availability.slots.destroy');
| });
*/

// Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('email-logs', [App\Http\Controllers\Admin\EmailLogsController::class, 'index'])->name('email-logs.index');
    Route::get('feedback', [App\Http\Controllers\Admin\FeedbackController::class, 'index'])->name('feedback.index');
    Route::patch('feedback/{feedback}', [App\Http\Controllers\Admin\FeedbackController::class, 'update'])->name('feedback.update');
    /*
    | ──────────────────────────────────────────────────────────────────
    | DISABLED for Phase 1 launch — Service Providers & Categories.
    | We're focused only on driving instructor / car services for now.
    | Other service categories (mechanic, panel beating, tyres, etc.)
    | will be re-enabled in a future phase. Sidebar links are also
    | commented out in resources/views/layouts/admin.blade.php.
    | ──────────────────────────────────────────────────────────────────
    | Route::resource('service-categories', App\Http\Controllers\Admin\ServiceCategoryController::class)->except(['show']);
    | Route::get('service-providers', [App\Http\Controllers\Admin\ServiceProviderController::class, 'index'])->name('service-providers.index');
    | Route::get('service-providers/create', [App\Http\Controllers\Admin\ServiceProviderController::class, 'create'])->name('service-providers.create');
    | Route::post('service-providers', [App\Http\Controllers\Admin\ServiceProviderController::class, 'store'])->name('service-providers.store');
    | Route::get('service-providers/{serviceProvider}', [App\Http\Controllers\Admin\ServiceProviderController::class, 'show'])->name('service-providers.show');
    | Route::get('service-providers/{serviceProvider}/edit', [App\Http\Controllers\Admin\ServiceProviderController::class, 'edit'])->name('service-providers.edit');
    | Route::put('service-providers/{serviceProvider}', [App\Http\Controllers\Admin\ServiceProviderController::class, 'update'])->name('service-providers.update');
    | Route::delete('service-providers/{serviceProvider}', [App\Http\Controllers\Admin\ServiceProviderController::class, 'destroy'])->name('service-providers.destroy');
    | Route::post('service-providers/{serviceProvider}/approve', [App\Http\Controllers\Admin\ServiceProviderController::class, 'approve'])->name('service-providers.approve');
    | Route::post('service-providers/{serviceProvider}/reject', [App\Http\Controllers\Admin\ServiceProviderController::class, 'reject'])->name('service-providers.reject');
    */
});

/*
|--------------------------------------------------------------------------
| Support — public help center
|--------------------------------------------------------------------------
| Served on the `support.` subdomain in production (e.g.
| https://support.securelicence.com), and also accessible at /support/* on
| the main domain as a fallback for local dev and discoverability.
|
| The subdomain is resolved via the SUPPORT_DOMAIN env var.
*/

// Register the same support routes either on a subdomain (production) or under
// /support on the main domain (local dev), depending on config.
// NOTE: pulled from config (not env) so it survives `php artisan config:cache`.
$supportDomain = config('app.support_domain');     // e.g. 'support.securelicence.com'

$supportRoutes = function () {
    $c = App\Http\Controllers\Support\SupportController::class;
    $req = App\Http\Controllers\Support\SupportRequestController::class;
    Route::get('/', [$c, 'home'])->name('home');
    Route::get('/search', [$c, 'search'])->name('search');
    Route::get('/submit-request', [$req, 'show'])->name('request.show');
    Route::post('/submit-request', [$req, 'store'])->name('request.store');
    Route::get('/categories/{category:slug}', [$c, 'category'])->name('category');
    Route::get('/sections/{section:slug}', [$c, 'section'])->name('section');
    Route::get('/articles/{article:slug}', [$c, 'article'])->name('article');
    Route::post('/articles/{article:slug}/feedback', [$c, 'feedback'])->name('article.feedback');
};

// Note: when SUPPORT_DOMAIN is set, the named subdomain routes are registered at
// the TOP of this file (so they match before main '/' route). Here we only register
// the /support/* prefix fallback. When SUPPORT_DOMAIN is empty, /support/* is the
// canonical home.
if ($supportDomain) {
    // Subdomain already registered at top — register /support/* fallback inline
    // WITHOUT names (names belong to the canonical subdomain routes above to avoid clash).
    Route::prefix('support')->group(function () {
        $c = App\Http\Controllers\Support\SupportController::class;
        $req = App\Http\Controllers\Support\SupportRequestController::class;
        Route::get('/', [$c, 'home']);
        Route::get('/search', [$c, 'search']);
        Route::get('/submit-request', [$req, 'show']);
        Route::post('/submit-request', [$req, 'store']);
        Route::get('/categories/{category:slug}', [$c, 'category']);
        Route::get('/sections/{section:slug}', [$c, 'section']);
        Route::get('/articles/{article:slug}', [$c, 'article']);
        Route::post('/articles/{article:slug}/feedback', [$c, 'feedback']);
    });
} else {
    // No subdomain configured — /support/* is canonical (uses named closure)
    Route::prefix('support')->name('support.')->group($supportRoutes);
}

/*
|--------------------------------------------------------------------------
| Support — admin panel CRUD
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin/support')->name('admin.support.')->group(function () {
    $c = App\Http\Controllers\Admin\Support\AdminSupportController::class;

    Route::get('/', [$c, 'dashboard'])->name('dashboard');

    // Categories
    Route::get('/categories', [$c, 'categoriesIndex'])->name('categories');
    Route::post('/categories', [$c, 'categoryStore'])->name('category.store');
    Route::put('/categories/{category}', [$c, 'categoryUpdate'])->name('category.update');
    Route::delete('/categories/{category}', [$c, 'categoryDestroy'])->name('category.destroy');

    // Sections
    Route::get('/sections', [$c, 'sectionsIndex'])->name('sections');
    Route::post('/sections', [$c, 'sectionStore'])->name('section.store');
    Route::put('/sections/{section}', [$c, 'sectionUpdate'])->name('section.update');
    Route::delete('/sections/{section}', [$c, 'sectionDestroy'])->name('section.destroy');

    // Articles
    Route::get('/articles', [$c, 'articlesIndex'])->name('articles');
    Route::get('/articles/create', [$c, 'articleCreate'])->name('article.create');
    Route::post('/articles', [$c, 'articleStore'])->name('article.store');
    Route::get('/articles/{article}/edit', [$c, 'articleEdit'])->name('article.edit');
    Route::put('/articles/{article}', [$c, 'articleUpdate'])->name('article.update');
    Route::delete('/articles/{article}', [$c, 'articleDestroy'])->name('article.destroy');
    Route::post('/articles/image-upload', [$c, 'articleImageUpload'])->name('article.image-upload');

    // Requests (Inbox)
    Route::get('/requests', [$c, 'requestsIndex'])->name('requests');
    Route::get('/requests/{request}', [$c, 'requestShow'])->name('request.show');
    Route::put('/requests/{supportRequest}', [$c, 'requestUpdate'])->name('request.update');
});

// ── Admin: Fees Dashboard (real-time P&L) ──
Route::middleware(['auth', 'role:admin'])->prefix('admin/fees-dashboard')->name('admin.fees-dashboard.')->group(function () {
    $c = App\Http\Controllers\Admin\FeesDashboardController::class;
    Route::get('/', [$c, 'index'])->name('index');
    Route::get('/export', [$c, 'export'])->name('export');
});

// ── Admin: Blocked Signups (anti-spam) ──
Route::middleware(['auth', 'role:admin'])->prefix('admin/blocked-signups')->name('admin.blocked-signups.')->group(function () {
    $c = App\Http\Controllers\Admin\BlockedSignupsController::class;
    Route::get('/', [$c, 'index'])->name('index');
    Route::post('/', [$c, 'store'])->name('store');
    Route::get('/{blockedSignup}', [$c, 'show'])->name('show');
    Route::put('/{blockedSignup}/toggle', [$c, 'toggle'])->name('toggle');
    Route::delete('/{blockedSignup}', [$c, 'destroy'])->name('destroy');
});
