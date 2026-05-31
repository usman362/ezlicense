<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Models\SupportRequest;
use App\Models\User;
use App\Notifications\SupportRequestSubmittedAdmin;
use App\Notifications\SupportRequestReceivedUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Public "Submit a Request" form.
 *   GET  /submit-request
 *   POST /submit-request
 */
class SupportRequestController extends Controller
{
    private const TOPICS = [
        'booking'       => 'Booking issue (cancel, reschedule, refund)',
        'payment'       => 'Payment / refund query',
        'account'       => 'Account access (login, password)',
        'instructor'    => 'Instructor sign-up / verification',
        'complaint'     => 'Complaint about a lesson or instructor',
        'feature'       => 'Feature request or feedback',
        'other'         => 'Other',
    ];

    public function show(Request $request): View
    {
        return view('support.submit-request', [
            'topics'     => self::TOPICS,
            'prefillTopic' => $request->query('topic'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:150'],
            'email'   => ['required', 'email', 'max:191'],
            'phone'   => ['nullable', 'string', 'max:30'],
            'role'    => ['nullable', Rule::in(['learner', 'instructor', 'other'])],
            'topic'   => ['required', Rule::in(array_keys(self::TOPICS))],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            // honeypot
            'website' => ['nullable', 'string', 'max:0'],
        ]);

        // Honeypot trip
        if (! empty($request->input('website'))) {
            return back()->with('message', 'Thanks! We\'ll be in touch.');
        }

        $req = SupportRequest::create([
            'name'    => $data['name'],
            'email'   => $data['email'],
            'phone'   => $data['phone'] ?? null,
            'role'    => $data['role'] ?? null,
            'topic'   => $data['topic'],
            'subject' => $data['subject'],
            'message' => $data['message'],
            'status'  => SupportRequest::STATUS_NEW,
            'user_id' => Auth::id(),
        ]);

        // Notify admin (silent failure — request still saved)
        try {
            $adminEmail = SiteSetting::get('admin_notification_email', 'admin@securelicence.com');
            Notification::route('mail', $adminEmail)->notify(new SupportRequestSubmittedAdmin($req));
        } catch (\Throwable $e) {
            Log::warning("Support request admin email failed: " . $e->getMessage());
        }

        // Auto-ack to user
        try {
            Notification::route('mail', $req->email)->notify(new SupportRequestReceivedUser($req));
        } catch (\Throwable $e) {
            Log::warning("Support request user ack email failed: " . $e->getMessage());
        }

        return redirect()
            ->route('support.request.show')
            ->with('message', "Thanks! Your request has been submitted. Reference: <strong>{$req->reference}</strong>. We've sent a confirmation to your email and will respond within 1 business day.");
    }
}
