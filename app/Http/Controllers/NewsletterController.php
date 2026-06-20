<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

/**
 * Industry Insights — monthly newsletter for instructors.
 *   GET  /industry-insights/newsletter
 *   POST /industry-insights/newsletter/subscribe
 */
class NewsletterController extends Controller
{
    public function show()
    {
        return view('frontend.pages.industry-insights-newsletter');
    }

    public function subscribe(Request $request)
    {
        $data = $request->validate([
            'email'      => ['required', 'email', 'max:191'],
            'first_name' => ['nullable', 'string', 'max:100'],
            'last_name'  => ['nullable', 'string', 'max:100'],
            'state'      => ['nullable', 'string', 'max:10'],
            // honeypot
            'website'    => ['nullable', 'string', 'max:0'],
        ]);

        if (! empty($request->input('website'))) {
            return back()->with('newsletter_success', 'Thanks! You\'re subscribed.');
        }

        NewsletterSubscriber::updateOrCreate(
            ['email' => strtolower(trim($data['email']))],
            [
                'first_name' => $data['first_name'] ?? null,
                'last_name'  => $data['last_name'] ?? null,
                'state'      => $data['state'] ?? null,
                'source'     => 'industry-insights',
                'is_active'  => true,
            ]
        );

        return back()->with('newsletter_success',
            'You\'re in! Look out for the next Industry Insights on the first Tuesday of the month.');
    }
}
