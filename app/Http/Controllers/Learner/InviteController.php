<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\ReferralInvite;
use App\Models\User;
use App\Notifications\ReferralInviteNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class InviteController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        // Ensure they have a referral code (defensive — boot() should have set it)
        if (! $user->referral_code) {
            $user->referral_code = User::generateUniqueReferralCode();
            $user->save();
        }

        $invites = ReferralInvite::where('referrer_user_id', $user->id)
            ->latest()
            ->limit(50)
            ->get();

        $convertedCount = $invites->whereNotNull('signed_up_at')->count();
        $pendingCount = $invites->whereNull('signed_up_at')->count();

        return view('learner.pages.invite-friends', [
            'referralCode' => $user->referral_code,
            'referralLink' => url('/register?ref=' . $user->referral_code),
            'invites' => $invites,
            'stats' => [
                'sent' => $invites->count(),
                'converted' => $convertedCount,
                'pending' => $pendingCount,
            ],
        ]);
    }

    public function send(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Rate limit: 5 invites per minute, 30 per hour, per user
        $key = 'invite-send:' . $user->id;
        if (RateLimiter::tooManyAttempts($key, 30)) {
            return back()->with('error', 'You\'re sending too many invites. Please try again later.');
        }
        RateLimiter::hit($key, 3600);

        $validated = $request->validate([
            'invitee_email' => 'required|email|max:160',
            'invitee_name'  => 'nullable|string|max:80',
            'personal_message' => 'nullable|string|max:500',
        ]);

        $email = strtolower(trim($validated['invitee_email']));

        // Don't allow inviting yourself
        if ($email === strtolower($user->email)) {
            return back()->with('error', 'You can\'t invite yourself.');
        }

        // Check if recipient is already on the platform
        $existing = User::where('email', $email)->first();
        if ($existing) {
            return back()->with('error', $email . ' is already a Secure Licences member.');
        }

        // Avoid duplicate pending invite to same email from same referrer
        $duplicate = ReferralInvite::where('referrer_user_id', $user->id)
            ->where('invitee_email', $email)
            ->whereNull('signed_up_at')
            ->where('created_at', '>=', now()->subDays(7))
            ->exists();
        if ($duplicate) {
            return back()->with('error', 'You\'ve already sent an invite to ' . $email . ' in the last 7 days.');
        }

        $invite = ReferralInvite::create([
            'referrer_user_id' => $user->id,
            'invitee_email' => $email,
            'invitee_name' => $validated['invitee_name'] ?? null,
            'personal_message' => $validated['personal_message'] ?? null,
        ]);

        // Send invite email
        try {
            Notification::route('mail', $email)->notify(new ReferralInviteNotification($invite, $user));
            $invite->update(['email_sent_at' => now()]);
        } catch (\Throwable $e) {
            Log::warning('Referral invite email failed: ' . $e->getMessage());
            return back()->with('error', 'Could not send the invite right now. Please try again.');
        }

        return back()->with('success', 'Invite sent to ' . $email . '! 🎉');
    }
}
