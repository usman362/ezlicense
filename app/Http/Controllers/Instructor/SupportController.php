<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SupportController extends Controller
{
    /**
     * Handle a "Submit a request" form from the instructor support page.
     * Saves attachments to Spaces, emails the support team, and bounces back
     * with a success flash.
     */
    public function submit(Request $request)
    {
        $data = $request->validate([
            'category'     => ['required', 'string', 'max:50'],
            'sub_category' => ['nullable', 'string', 'max:100'],
            'subject'      => ['nullable', 'string', 'max:150'],
            'order_number' => ['nullable', 'string', 'max:50'],
            'transmission' => ['nullable', 'in:auto,manual,both'],
            'postcode'     => ['nullable', 'string', 'max:10'],
            'message'      => ['required', 'string', 'min:10', 'max:5000'],
            'attachments'  => ['nullable', 'array', 'max:5'],
            'attachments.*'=> ['file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:5120'],
        ]);

        $user = Auth::user();

        // Save any attachments to Spaces (private)
        $uploaded = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                try {
                    $path = $file->store('support-tickets/'.$user->id, ['disk' => 'spaces', 'visibility' => 'private']);
                    $uploaded[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                    ];
                } catch (\Throwable $e) {
                    Log::warning('Support ticket attachment upload failed: '.$e->getMessage());
                }
            }
        }

        // Email the support team
        try {
            $body = "New support request from instructor:\n\n"
                  . "Name: ".$user->name."\n"
                  . "Email: ".$user->email."\n"
                  . "Phone: ".($user->phone ?: '—')."\n"
                  . "Category: ".$data['category']."\n"
                  . "Sub-category: ".($data['sub_category'] ?? '—')."\n"
                  . "Subject: ".($data['subject'] ?: '—')."\n"
                  . "Order/Booking #: ".($data['order_number'] ?: '—')."\n"
                  . "Transmission: ".($data['transmission'] ?? '—')."\n"
                  . "Postcode: ".($data['postcode'] ?? '—')."\n"
                  . "Attachments: ".count($uploaded)."\n"
                  . "\n----\n\n"
                  . $data['message'];

            Mail::raw($body, function ($m) use ($user, $data) {
                $m->to(config('mail.from.address', 'support@securelicences.com.au'))
                  ->replyTo($user->email, $user->name)
                  ->subject('[Instructor Support] '.ucwords(str_replace('_', ' ', $data['category'])).': '.($data['subject'] ?: 'New request'));
            });
        } catch (\Throwable $e) {
            Log::error('Support ticket email failed: '.$e->getMessage());
            return back()->withInput()->with('error', "We couldn't send your request right now. Please try again, or email us directly at support@securelicences.com.au.");
        }

        return redirect()->route('instructor.support')
            ->with('success', "Thanks! Your request has been received — we'll reply within 1-2 business days.");
    }
}
