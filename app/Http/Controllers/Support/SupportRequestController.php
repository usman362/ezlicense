<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\InstructorApplication;
use App\Models\SiteSetting;
use App\Models\SupportRequest;
use App\Models\User;
use App\Notifications\InstructorApplicationReceived;
use App\Notifications\InstructorApplicationAdminAlert;
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

    /**
     * "Which best describes you?" — the primary selector. Nothing on the form
     * shows until one of these is chosen.
     *   mode 'support'    → opens a support ticket (name/email/phone/subject/message)
     *   mode 'instructor' → opens an instructor application, submitted right here
     */
    private const DESCRIBES = [
        'learner'           => ['label' => 'I am learning to drive',                                        'mode' => 'support',    'role' => 'learner',    'topic' => 'booking'],
        'instructor'        => ['label' => 'I am a driving instructor (already on Secure Licence)',         'mode' => 'support',    'role' => 'instructor', 'topic' => 'account'],
        'join_instructor'   => ['label' => 'I am a driving instructor interested in joining Secure Licence', 'mode' => 'instructor'],
        'become_instructor' => ['label' => 'I am interested in becoming a driving instructor',              'mode' => 'instructor'],
        'media'             => ['label' => 'I have a media / PR enquiry',                                    'mode' => 'support',    'role' => 'other',      'topic' => 'other'],
        'other'             => ['label' => 'Something else',                                                'mode' => 'support',    'role' => 'other',      'topic' => 'other'],
    ];

    public function show(Request $request): View
    {
        // Backward-compat: a ?topic=instructor link preselects the join-instructor option.
        $prefillDescribes = $request->query('topic') === 'instructor' ? 'join_instructor' : null;

        return view('support.submit-request', [
            'describes'        => self::DESCRIBES,
            'prefillDescribes' => $prefillDescribes,
            'states'           => \App\Models\State::orderBy('name')->get(['id', 'name', 'code']),
        ]);
    }

    public function store(Request $request)
    {
        // Honeypot trip — checked before validation so it never shows as a visible field error.
        if (! empty($request->input('website'))) {
            return back()->with('message', 'Thanks! We\'ll be in touch.');
        }

        $describesKey = (string) $request->input('describes_you');
        $describes    = self::DESCRIBES[$describesKey] ?? null;

        if (! $describes) {
            return back()->withInput()->withErrors(['describes_you' => 'Please choose an option.']);
        }

        return ($describes['mode'] === 'instructor')
            ? $this->storeInstructorApplication($request)
            : $this->storeSupportTicket($request, $describes);
    }

    /* ─────────── Instructor application (submitted from this form) ─────────── */

    private function storeInstructorApplication(Request $request)
    {
        $data = $request->validate([
            'describes_you'    => ['required', Rule::in(array_keys(self::DESCRIBES))],
            'first_name'       => ['required', 'string', 'max:100'],
            'last_name'        => ['required', 'string', 'max:100'],
            'email'            => ['required', 'email', 'max:191'],
            'phone'            => ['required', 'string', 'max:30'],
            'state'            => ['nullable', 'string', 'max:60'],
            'postcode'         => ['nullable', 'string', 'max:10'],
            'transmission'     => ['nullable', 'in:auto,manual,both'],
            'years_experience' => ['nullable', 'integer', 'min:0', 'max:70'],
            'message'          => ['required', 'string', 'min:10', 'max:3000'],
        ]);

        $email = strtolower(trim($data['email']));

        // Refuse duplicate open applications for the same email.
        $exists = InstructorApplication::where('email', $email)
            ->whereIn('status', [InstructorApplication::STATUS_PENDING, InstructorApplication::STATUS_UNDER_REVIEW])
            ->exists();
        if ($exists) {
            return back()->withInput()->withErrors([
                'email' => 'You already have an application in progress for this email — we\'ll be in touch once it\'s reviewed.',
            ]);
        }

        $bio = trim($data['message']);
        $meta = array_filter(['State: ' . ($data['state'] ?? ''), 'Postcode: ' . ($data['postcode'] ?? '')], fn ($p) => ! str_ends_with($p, ': '));
        if ($meta) {
            $bio .= "\n\n" . implode(' · ', $meta);
        }

        $app = InstructorApplication::create([
            'first_name'       => $data['first_name'],
            'last_name'        => $data['last_name'],
            'email'            => $email,
            'phone'            => $data['phone'],
            'years_experience' => $data['years_experience'] ?? null,
            'transmission'     => $data['transmission'] ?? null,
            'bio'              => $bio,
            'documents'        => [],
            'status'           => InstructorApplication::STATUS_PENDING,
            'applied_ip'       => $request->ip(),
            'applied_user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);

        try {
            Notification::route('mail', $email)->notify(new InstructorApplicationReceived($app));
        } catch (\Throwable $e) {
            Log::warning('Instructor application ack email failed: ' . $e->getMessage());
        }
        try {
            $adminEmail = SiteSetting::get('admin_notification_email', 'admin@securelicence.com');
            Notification::route('mail', $adminEmail)->notify(new InstructorApplicationAdminAlert($app));
        } catch (\Throwable $e) {
            Log::warning('Instructor application admin alert failed: ' . $e->getMessage());
        }

        return redirect()->route('support.request.show')->with('message',
            "Thanks! Your instructor application <strong>{$app->reference}</strong> has been received. "
            . "Our team will review it and email you the next steps — including which documents to send for verification."
        );
    }

    /* ─────────── Support ticket ─────────── */

    private function storeSupportTicket(Request $request, array $describes)
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:150'],
            'email'   => ['required', 'email', 'max:191'],
            'phone'   => ['nullable', 'string', 'max:30'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        $req = SupportRequest::create([
            'name'    => $data['name'],
            'email'   => $data['email'],
            'phone'   => $data['phone'] ?? null,
            'role'    => $describes['role'] ?? null,
            'topic'   => $describes['topic'] ?? 'other',
            'subject' => $data['subject'],
            'message' => $data['message'],
            'status'  => SupportRequest::STATUS_NEW,
            'user_id' => Auth::id(),
        ]);

        try {
            $adminEmail = SiteSetting::get('admin_notification_email', 'admin@securelicence.com');
            Notification::route('mail', $adminEmail)->notify(new SupportRequestSubmittedAdmin($req));
        } catch (\Throwable $e) {
            Log::warning('Support request admin email failed: ' . $e->getMessage());
        }
        try {
            Notification::route('mail', $req->email)->notify(new SupportRequestReceivedUser($req));
        } catch (\Throwable $e) {
            Log::warning('Support request user ack email failed: ' . $e->getMessage());
        }

        return redirect()->route('support.request.show')->with('message',
            "Thanks! Your request has been submitted. Reference: <strong>{$req->reference}</strong>. We've sent a confirmation to your email and will respond within 1 business day."
        );
    }
}
