<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstructorInvite;
use App\Models\User;
use App\Notifications\InstructorInviteNotification;
use Illuminate\Http\Request;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class InstructorInviteController extends Controller
{
    /* ─── List ─── */
    public function index(Request $request)
    {
        $q = InstructorInvite::with(['inviter', 'registeredUser'])
            ->latest();

        if ($status = $request->query('status')) {
            $q->where('status', $status);
        }
        if ($search = $request->query('q')) {
            $q->where(function ($w) use ($search) {
                $w->where('email', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $invites = $q->paginate(20)->withQueryString();

        // Sync expiry status on the page we're showing (cheap, only 20 rows)
        $invites->getCollection()->each->syncStatus();

        $stats = [
            'pending'   => InstructorInvite::where('status', InstructorInvite::STATUS_PENDING)->where('expires_at', '>', now())->count(),
            'accepted'  => InstructorInvite::where('status', InstructorInvite::STATUS_ACCEPTED)->count(),
            'expired'   => InstructorInvite::where(function ($q) {
                                $q->where('status', InstructorInvite::STATUS_EXPIRED)
                                  ->orWhere(function ($w) {
                                      $w->where('status', InstructorInvite::STATUS_PENDING)
                                        ->where('expires_at', '<=', now());
                                  });
                           })->count(),
            'cancelled' => InstructorInvite::where('status', InstructorInvite::STATUS_CANCELLED)->count(),
        ];

        return view('admin.pages.instructor-invites.index', compact('invites', 'stats'));
    }

    /* ─── Create ─── */
    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'nullable|string|max:100',
            'email'         => 'required|email|max:255',
            'phone'         => 'nullable|string|max:30',
            'personal_note' => 'nullable|string|max:500',
        ]);

        $email = strtolower(trim($data['email']));

        // Block invites to existing instructor accounts
        $existing = User::where('email', $email)->first();
        if ($existing && $existing->role === User::ROLE_INSTRUCTOR) {
            return back()->withErrors([
                'email' => 'An instructor account already exists with this email.',
            ])->withInput();
        }

        // Block duplicate pending invites for the same email
        $duplicate = InstructorInvite::where('email', $email)
            ->where('status', InstructorInvite::STATUS_PENDING)
            ->where('expires_at', '>', now())
            ->first();
        if ($duplicate) {
            return back()->withErrors([
                'email' => 'A pending invite already exists for this email. Resend it from the list instead.',
            ])->withInput();
        }

        $invite = InstructorInvite::create([
            'first_name'         => $data['first_name'],
            'last_name'          => $data['last_name'] ?? null,
            'email'              => $email,
            'phone'              => $data['phone'] ?? null,
            'personal_note'      => $data['personal_note'] ?? null,
            'invited_by_user_id' => auth()->id(),
        ]);

        // Send email (silent failure — admin can resend if it bounces)
        try {
            Notification::route('mail', $email)
                ->notify(new InstructorInviteNotification($invite));
        } catch (\Throwable $e) {
            Log::warning('Instructor invite email failed (id=' . $invite->id . '): ' . $e->getMessage());
        }

        return redirect()->route('admin.instructor-invites.index')
            ->with('message', 'Invite sent to ' . $email);
    }

    /* ─── Resend ─── */
    public function resend(InstructorInvite $instructorInvite)
    {
        if ($instructorInvite->status === InstructorInvite::STATUS_ACCEPTED) {
            return back()->withErrors(['resend' => 'This invite has already been accepted.']);
        }

        // Generate fresh token + reset expiry so it's usable again
        $instructorInvite->update([
            'token'        => InstructorInvite::generateToken(),
            'status'       => InstructorInvite::STATUS_PENDING,
            'expires_at'   => now()->addDays(InstructorInvite::DEFAULT_EXPIRY_DAYS),
            'last_sent_at' => now(),
            'send_count'   => $instructorInvite->send_count + 1,
        ]);

        try {
            Notification::route('mail', $instructorInvite->email)
                ->notify(new InstructorInviteNotification($instructorInvite->fresh()));
        } catch (\Throwable $e) {
            Log::warning('Instructor invite resend failed (id=' . $instructorInvite->id . '): ' . $e->getMessage());
        }

        return back()->with('message', 'Invite re-sent to ' . $instructorInvite->email);
    }

    /* ─── Cancel ─── */
    public function cancel(InstructorInvite $instructorInvite)
    {
        if ($instructorInvite->status === InstructorInvite::STATUS_ACCEPTED) {
            return back()->withErrors(['cancel' => 'Already accepted — can\'t cancel.']);
        }
        $instructorInvite->update(['status' => InstructorInvite::STATUS_CANCELLED]);
        return back()->with('message', 'Invite cancelled.');
    }

    /* ─── Delete ─── */
    public function destroy(InstructorInvite $instructorInvite)
    {
        $instructorInvite->delete();
        return back()->with('message', 'Invite deleted.');
    }
}
