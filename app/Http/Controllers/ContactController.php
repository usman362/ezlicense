<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class ContactController extends Controller
{
    /**
     * Store a new contact message.
     * Rate-limited: 3 messages per 10 minutes per IP.
     */
    public function send(Request $request): JsonResponse
    {
        // Rate limiting by IP
        $key = 'contact-form:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'message' => "Too many messages. Please try again in {$seconds} seconds.",
            ], 429);
        }

        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'phone'   => 'nullable|string|max:20',
            'subject' => 'required|string|max:100',
            'message' => 'required|string|max:2000',
        ]);

        RateLimiter::hit($key, 600); // 10 minute window

        $contact = ContactMessage::create([
            ...$validated,
            'ip_address' => $request->ip(),
        ]);

        // Send notification email to admin
        $adminEmail = SiteSetting::get('support_email', 'support@securelicences.com.au');

        try {
            Mail::raw(
                "New contact form message:\n\n" .
                "Name: {$contact->name}\n" .
                "Email: {$contact->email}\n" .
                "Phone: " . ($contact->phone ?: 'N/A') . "\n" .
                "Subject: {$contact->subject}\n\n" .
                "Message:\n{$contact->message}\n\n" .
                "IP: {$contact->ip_address}\n" .
                "Sent: {$contact->created_at->format('d M Y, H:i')}",
                function ($m) use ($adminEmail, $contact) {
                    $m->to($adminEmail)
                      ->replyTo($contact->email, $contact->name)
                      ->subject("Contact Form: {$contact->subject}");
                }
            );
        } catch (\Throwable $e) {
            // Log but don't fail — the message is saved in DB
            \Log::warning('Contact form email failed: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Thank you! Your message has been sent.',
        ]);
    }
}
