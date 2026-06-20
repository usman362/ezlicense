<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NewsletterController extends Controller
{
    public function index(Request $request)
    {
        $q = NewsletterSubscriber::query()->latest('subscribed_at');

        if ($search = trim((string) $request->query('q'))) {
            $q->where(function ($w) use ($search) {
                $w->where('email', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('state', 'like', "%{$search}%");
            });
        }

        if (($status = $request->query('status')) === 'active') {
            $q->where('is_active', true);
        } elseif ($status === 'unsubscribed') {
            $q->where('is_active', false);
        }

        $subscribers = $q->paginate(25)->withQueryString();

        $stats = [
            'total'        => NewsletterSubscriber::count(),
            'active'       => NewsletterSubscriber::where('is_active', true)->count(),
            'unsubscribed' => NewsletterSubscriber::where('is_active', false)->count(),
            'this_month'   => NewsletterSubscriber::where('subscribed_at', '>=', now()->startOfMonth())->count(),
        ];

        return view('admin.pages.newsletter.index', compact('subscribers', 'stats'));
    }

    public function export(Request $request): StreamedResponse
    {
        $rows = NewsletterSubscriber::where('is_active', true)->orderBy('subscribed_at')->get();

        $filename = 'newsletter-subscribers-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Email', 'First name', 'Last name', 'State', 'Source', 'Subscribed at']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->email,
                    $r->first_name,
                    $r->last_name,
                    $r->state,
                    $r->source,
                    optional($r->subscribed_at)->format('Y-m-d H:i'),
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function toggle(NewsletterSubscriber $subscriber)
    {
        $subscriber->update(['is_active' => ! $subscriber->is_active]);

        return back()->with('message', 'Subscriber ' . ($subscriber->is_active ? 're-activated' : 'unsubscribed') . '.');
    }

    public function destroy(NewsletterSubscriber $subscriber)
    {
        $subscriber->delete();

        return back()->with('message', 'Subscriber removed.');
    }
}
