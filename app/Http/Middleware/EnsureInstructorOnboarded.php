<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureInstructorOnboarded
{
    /**
     * Routes that are ALWAYS accessible, regardless of onboarding stage.
     * (Logout, the onboarding waiting page itself, support, notifications, contact modal.)
     */
    protected array $alwaysAllowed = [
        'logout',
        'instructor.onboarding.pending',
        'instructor.support',
        'instructor.support.submit',
        'instructor.notifications',
        'instructor.notifications.mark-all-read',
        'instructor.notifications.mark-selected-read',
    ];

    /**
     * Routes accessible during the "needs to upload documents" stage —
     * i.e. verification_status is 'pending' or 'rejected'.
     */
    protected array $allowedWhileUploadingDocs = [
        'instructor.settings.personal-details',
        'instructor.settings.documents',
    ];

    /**
     * Routes accessible during the "waiting for admin approval" stage —
     * i.e. verification_status is 'documents_submitted'.
     */
    protected array $allowedWhileWaiting = [
        'instructor.settings.personal-details',
        'instructor.settings.documents',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Not an instructor → pass through (admins / learners / guests handled by other middleware)
        if (! $user || ! $user->isInstructor()) {
            return $next($request);
        }

        $profile = $user->instructorProfile;

        // No profile shell yet — shouldn't normally happen; create on the fly
        if (! $profile) {
            $profile = $user->instructorProfile()->create(['is_active' => false]);
        }

        $status = $profile->verification_status ?? 'pending';

        // Already verified + active → full access
        if ($status === 'verified' && $profile->is_active) {
            return $next($request);
        }

        $currentRoute = $request->route()?->getName();

        // Always-allowed routes (logout, waiting page, support, notifications)
        if (in_array($currentRoute, $this->alwaysAllowed, true)) {
            return $next($request);
        }

        // Decide which redirect target based on status
        // - pending / rejected → docs upload page
        // - documents_submitted → waiting-for-approval page
        if (in_array($status, ['pending', 'rejected'], true)) {
            if (in_array($currentRoute, $this->allowedWhileUploadingDocs, true)) {
                return $next($request);
            }
            return redirect()->route('instructor.settings.documents')
                ->with('onboarding_notice', $status === 'rejected'
                    ? 'Your documents need attention before you can accept bookings. Please review the admin\'s feedback below and re-upload.'
                    : 'Welcome! Please upload your verification documents to start accepting bookings.');
        }

        if ($status === 'documents_submitted') {
            if (in_array($currentRoute, $this->allowedWhileWaiting, true)) {
                return $next($request);
            }
            return redirect()->route('instructor.onboarding.pending');
        }

        // Fallback (e.g. unknown status) — send to docs page
        return redirect()->route('instructor.settings.documents');
    }
}
