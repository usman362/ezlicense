<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\InstructorSignupAdminAlert;
use App\Notifications\WelcomeNotification;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    protected function redirectTo(): string
    {
        $user = auth()->user();
        if ($user?->isInstructor()) {
            return '/instructor/dashboard';
        }
        if ($user?->isLearner()) {
            return '/learner/dashboard';
        }
        return '/home';
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Capture ?ref= referral code from URL into session so it survives the
     * GET form → POST submission flow. Stays in session for 24 hours.
     */
    public function showRegistrationForm(\Illuminate\Http\Request $request)
    {
        if ($request->filled('ref')) {
            $request->session()->put('referral_code', strtoupper($request->input('ref')));
        }
        return view('auth.register');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'role' => ['required', 'in:learner,instructor'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Resolve referral code (from form/session if a ?ref= was used during signup)
        $referrerId = null;
        $referralCode = $data['referral_code'] ?? request()->input('ref') ?? session('referral_code');
        if ($referralCode) {
            $referrer = User::where('referral_code', strtoupper($referralCode))->first();
            $referrerId = $referrer?->id;
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'] ?? User::ROLE_LEARNER,
            'password' => Hash::make($data['password']),
            'referred_by_user_id' => $referrerId,
            'referred_at' => $referrerId ? now() : null,
        ]);

        // Mark any matching pending invite as converted
        if ($referrerId) {
            try {
                \App\Models\ReferralInvite::where('referrer_user_id', $referrerId)
                    ->where('invitee_email', strtolower($user->email))
                    ->whereNull('signed_up_at')
                    ->update([
                        'signed_up_user_id' => $user->id,
                        'signed_up_at' => now(),
                    ]);
                session()->forget('referral_code');
            } catch (\Throwable $e) {
                Log::warning('Referral conversion update failed: ' . $e->getMessage());
            }
        }

        // ── Auto-accept any pending Instructor → Learner invite ──
        // If the user signed up via an instructor's invite link, mark it accepted
        // so they appear in that instructor's "My Learners" list immediately.
        $instructorInviteToken = session('accept_instructor_invite_token');
        if ($instructorInviteToken && $user->role === User::ROLE_LEARNER) {
            try {
                $invite = \App\Models\InstructorLearnerInvite::where('invite_token', $instructorInviteToken)
                    ->whereNull('accepted_at')
                    ->first();
                if ($invite && strtolower($invite->invitee_email) === strtolower($user->email)) {
                    $invite->update([
                        'accepted_by_user_id' => $user->id,
                        'accepted_at' => now(),
                    ]);
                }
                session()->forget('accept_instructor_invite_token');
            } catch (\Throwable $e) {
                Log::warning('Instructor invite acceptance failed: ' . $e->getMessage());
            }
        }

        // Send welcome email (fails silently — don't block registration)
        try {
            $user->notify(new WelcomeNotification($user));
        } catch (\Throwable $e) {
            Log::warning('Welcome email failed for user ' . $user->id . ': ' . $e->getMessage());
        }

        // If instructor: alert all admins so they can verify quickly
        if ($user->role === User::ROLE_INSTRUCTOR) {
            try {
                $admins = User::where('role', User::ROLE_ADMIN)->get();
                foreach ($admins as $admin) {
                    $admin->notify(new InstructorSignupAdminAlert($user, $user->instructorProfile));
                }
            } catch (\Throwable $e) {
                Log::warning('Admin instructor-signup alert failed: ' . $e->getMessage());
            }
        }

        return $user;
    }
}
