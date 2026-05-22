<?php

namespace App\Http\Controllers;

use App\Models\InstructorInvite;
use App\Models\InstructorProfile;
use App\Models\User;
use App\Notifications\InstructorSignupAdminAlert;
use App\Notifications\WelcomeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class InstructorInviteController extends Controller
{
    /**
     * Show the invite landing + registration form.
     */
    public function show(string $token)
    {
        $invite = InstructorInvite::where('token', $token)->first();

        if (! $invite) {
            return view('auth.invite-status', [
                'state'   => 'not_found',
                'heading' => 'Invite not found',
                'message' => "This invite link doesn't look right. Please check the URL or ask the admin for a new invite.",
            ]);
        }

        $invite->syncStatus();

        // Already accepted → bounce to login (magic link "single use" enforcement)
        if ($invite->status === InstructorInvite::STATUS_ACCEPTED) {
            return view('auth.invite-status', [
                'state'   => 'accepted',
                'heading' => 'Invite already used',
                'message' => 'This invite has already been redeemed. Please log in with the email + password you set, or contact support if this wasn\'t you.',
                'loginUrl'=> route('instructor.login'),
            ]);
        }

        if ($invite->status === InstructorInvite::STATUS_CANCELLED) {
            return view('auth.invite-status', [
                'state'   => 'cancelled',
                'heading' => 'Invite cancelled',
                'message' => 'This invitation has been cancelled by the admin. Please contact us if you believe this is a mistake.',
            ]);
        }

        if (! $invite->isUsable()) {
            return view('auth.invite-status', [
                'state'   => 'expired',
                'heading' => 'Invite expired',
                'message' => 'This invite link has expired. Please ask the admin to send you a fresh invite.',
            ]);
        }

        return view('auth.instructor-invite', compact('invite'));
    }

    /**
     * Accept the invite → create User + InstructorProfile → auto-login → redirect.
     */
    public function register(Request $request, string $token)
    {
        $invite = InstructorInvite::where('token', $token)->first();
        if (! $invite || ! $invite->isUsable()) {
            return redirect()->route('instructor.invite.show', ['token' => $token]);
        }

        $data = $request->validate([
            'first_name'            => 'required|string|max:100',
            'last_name'             => 'required|string|max:100',
            'phone'                 => 'required|string|max:30',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
            'accept_terms'          => 'required|accepted',
        ]);

        // Email is read-only on the form — always use the invite's email server-side
        $email = $invite->email;

        // If a User already exists with this email, fail loudly
        if (User::where('email', $email)->exists()) {
            return back()->withErrors([
                'password' => 'An account already exists with this email. Please log in instead.',
            ])->withInput();
        }

        try {
            $user = DB::transaction(function () use ($invite, $data, $email) {
                $user = User::create([
                    'name'       => trim($data['first_name'] . ' ' . $data['last_name']),
                    'first_name' => $data['first_name'],
                    'last_name'  => $data['last_name'],
                    'email'      => $email,
                    'phone'      => $data['phone'],
                    'role'       => User::ROLE_INSTRUCTOR,
                    'password'   => Hash::make($data['password']),
                    'is_active'  => true,
                    'email_verified_at' => now(), // Magic-link signup ⇒ email is implicitly verified
                ]);

                // Create instructor profile shell (admin will verify docs later)
                InstructorProfile::firstOrCreate(
                    ['user_id' => $user->id],
                    ['is_active' => false] // Active only after admin approves docs
                );

                $invite->markAccepted($user);

                return $user;
            });
        } catch (\Throwable $e) {
            Log::error('Instructor invite registration failed: ' . $e->getMessage());
            return back()->withErrors([
                'password' => 'Something went wrong creating your account. Please try again or contact support.',
            ])->withInput();
        }

        // Welcome email (silent failure)
        try { $user->notify(new WelcomeNotification($user)); } catch (\Throwable $e) {}

        // Alert all admins (silent failure)
        try {
            foreach (User::where('role', User::ROLE_ADMIN)->get() as $admin) {
                $admin->notify(new InstructorSignupAdminAlert($user, $user->instructorProfile));
            }
        } catch (\Throwable $e) {}

        // Magic-link UX: auto-login + send straight to docs upload
        Auth::login($user, remember: true);

        return redirect()->route('instructor.documents.index')
            ->with('message', 'Welcome to Secure Licence! Upload your documents below to get verified.');
    }
}
