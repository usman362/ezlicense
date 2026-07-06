<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /** @var string */
    protected $redirectTo = '/home';

    /**
     * Show the application's login form. /login is for admin only.
     */
    public function showLoginForm()
    {
        return view('auth.admin-login');
    }

    /**
     * Redirect after login: admins to admin panel, others to their dashboards.
     */
    protected function redirectTo(): string
    {
        if (auth()->user()?->isAdmin()) {
            return '/admin/dashboard';
        }
        if (auth()->user()?->isInstructor()) {
            return '/instructor/dashboard';
        }
        return '/home';
    }

    /**
     * Reject deactivated or temporarily-blocked accounts right after they
     * authenticate — log them straight back out with a clear message.
     */
    protected function authenticated(\Illuminate\Http\Request $request, $user)
    {
        $blocked = $user->blocked_until && $user->blocked_until->isFuture();
        if (! $user->is_active || $blocked) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $message = $blocked
                ? 'Your account is temporarily suspended. Please try again later or contact support.'
                : 'This account has been deactivated. Please contact support if you think this is a mistake.';

            return redirect()->route('login')->withErrors([
                $this->username() => $message,
            ]);
        }

        return null;
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
