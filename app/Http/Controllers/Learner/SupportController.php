<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class SupportController extends Controller
{
    public function index(): View
    {
        return view('learner.pages.support');
    }

    public function send(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Rate limit: 5 support messages per hour per user
        $key = 'learner-support:' . $user->id;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->with('error', 'You\'ve sent a lot of support messages recently. Please wait before sending more.');
        }
        RateLimiter::hit($key, 3600);

        $validated = $request->validate([
            'subject' => 'required|string|max:120',
            'message' => 'required|string|min:10|max:2000',
        ]);

        // Persist to contact_messages so admins can see in one place
        try {
            ContactMessage::create([
                'name'    => $user->name,
                'email'   => $user->email,
                'phone'   => $user->phone,
                'subject' => '[Learner] ' . $validated['subject'],
                'message' => $validated['message'],
                'ip_address' => $request->ip(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Learner support message persist failed: ' . $e->getMessage());
        }

        // Notify all admins
        try {
            $adminEmails = User::where('role', User::ROLE_ADMIN)
                ->where('is_active', true)
                ->pluck('email')
                ->all();

            if ($adminEmails) {
                Mail::raw(
                    "Support request from a logged-in learner.\n\n"
                    . "From: {$user->name} <{$user->email}>" . ($user->phone ? " ({$user->phone})" : '') . "\n"
                    . "Subject: {$validated['subject']}\n\n"
                    . "Message:\n{$validated['message']}",
                    function ($m) use ($adminEmails, $user, $validated) {
                        $m->to($adminEmails)
                          ->replyTo($user->email, $user->name)
                          ->subject('[Learner Support] ' . $validated['subject']);
                    }
                );
            }
        } catch (\Throwable $e) {
            Log::warning('Learner support email failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Thanks! Your message has been sent. We\'ll reply to ' . $user->email . ' within 1-2 business days.');
    }
}
