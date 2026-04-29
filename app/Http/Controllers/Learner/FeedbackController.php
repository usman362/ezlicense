<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserFeedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $myFeedback = UserFeedback::where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('learner.pages.give-feedback', [
            'myFeedback' => $myFeedback,
            'categories' => UserFeedback::CATEGORIES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Rate limit: 3 submissions per 10 minutes per user
        $key = 'feedback-submit:' . $user->id;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return back()->with('error', 'You\'ve sent a lot of feedback recently. Please wait a few minutes before sending more.');
        }
        RateLimiter::hit($key, 600);

        $validated = $request->validate([
            'category'     => 'required|string|in:' . implode(',', array_keys(UserFeedback::CATEGORIES)),
            'rating'       => 'nullable|integer|min:1|max:5',
            'message'      => 'required|string|min:10|max:2000',
            'page_context' => 'nullable|string|max:255',
        ]);

        $feedback = UserFeedback::create([
            'user_id' => $user->id,
            'category' => $validated['category'],
            'rating' => $validated['rating'] ?? null,
            'message' => $validated['message'],
            'page_context' => $validated['page_context'] ?? null,
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'status' => 'new',
        ]);

        // Notify admins via email
        try {
            $adminEmails = User::where('role', User::ROLE_ADMIN)
                ->where('is_active', true)
                ->pluck('email')
                ->all();

            if ($adminEmails) {
                Mail::raw(
                    "New feedback from {$user->name} ({$user->email})\n\n"
                    . 'Category: ' . UserFeedback::CATEGORIES[$feedback->category] . "\n"
                    . ($feedback->rating ? "Rating: {$feedback->rating}/5\n" : '')
                    . "Message:\n{$feedback->message}\n\n"
                    . 'View in admin: ' . url('/admin/feedback'),
                    function ($m) use ($adminEmails, $feedback) {
                        $m->to($adminEmails)
                          ->subject('[Feedback] ' . ucfirst($feedback->category) . ' from ' . ($feedback->user?->name ?? 'a learner'));
                    }
                );
            }
        } catch (\Throwable $e) {
            Log::warning('Feedback admin notification failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Thanks for your feedback! Our team has been notified and will review it soon.');
    }
}
