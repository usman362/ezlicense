<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstructorPayout;
use App\Services\PayoutService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayoutsController extends Controller
{
    public function __construct(private PayoutService $payoutService) {}

    public function index(Request $request)
    {
        $query = InstructorPayout::with(['instructorProfile.user']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($search = $request->input('search')) {
            $query->whereHas('instructorProfile.user', fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
        }

        $payouts = $query->orderByDesc('period_start')->paginate(30)->withQueryString();

        $summaryPending  = InstructorPayout::pending()->sum('net_amount');
        $summaryApproved = InstructorPayout::approved()->sum('net_amount');
        $summaryPaidWeek = InstructorPayout::paid()->where('paid_at', '>=', now()->startOfWeek())->sum('net_amount');
        $pendingCount    = InstructorPayout::pending()->count();
        $missingBank     = InstructorPayout::pending()
            ->whereHas('instructorProfile', fn ($q) => $q->whereNull('bank_account_number')->orWhere('bank_account_number', ''))
            ->count();

        return view('admin.payouts.index', compact(
            'payouts', 'summaryPending', 'summaryApproved', 'summaryPaidWeek', 'pendingCount', 'missingBank',
        ));
    }

    public function show(InstructorPayout $instructorPayout)
    {
        $instructorPayout->load(['instructorProfile.user', 'items.booking.learner', 'approver']);
        return view('admin.payouts.show', ['payout' => $instructorPayout]);
    }

    public function approve(InstructorPayout $instructorPayout)
    {
        try {
            $this->payoutService->approvePayout($instructorPayout, Auth::user());
            return redirect()->back()->with('message', 'Payout approved.');
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function markPaid(Request $request, InstructorPayout $instructorPayout)
    {
        try {
            $this->payoutService->markAsPaid($instructorPayout, $request->input('payment_reference'));
            return redirect()->back()->with('message', 'Payout marked as paid.');
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function markFailed(Request $request, InstructorPayout $instructorPayout)
    {
        $request->validate(['failure_reason' => 'required|string|max:500']);
        $this->payoutService->markAsFailed($instructorPayout, $request->input('failure_reason'));
        return redirect()->back()->with('message', 'Payout marked as failed.');
    }

    public function addNote(Request $request, InstructorPayout $instructorPayout)
    {
        $request->validate(['admin_notes' => 'required|string|max:5000']);
        $instructorPayout->update(['admin_notes' => $request->input('admin_notes')]);
        return redirect()->back()->with('message', 'Notes updated.');
    }

    public function bulkApprove(Request $request)
    {
        $ids = $request->input('payout_ids', []);
        $count = $this->payoutService->bulkApprove($ids, Auth::user());
        return redirect()->back()->with('message', "{$count} payout(s) approved.");
    }

    public function bulkMarkPaid(Request $request)
    {
        $ids = $request->input('payout_ids', []);
        $count = $this->payoutService->bulkMarkPaid($ids, $request->input('payment_reference'));
        return redirect()->back()->with('message', "{$count} payout(s) marked as paid.");
    }

    public function generate(Request $request)
    {
        $weekEnding = null;
        if ($request->input('week_ending')) {
            $weekEnding = Carbon::parse($request->input('week_ending'), 'Australia/Sydney');
        }
        $count = $this->payoutService->generateWeeklyPayouts($weekEnding);
        return redirect()->back()->with('message', "{$count} payout(s) generated.");
    }

    public function exportCsv(Request $request)
    {
        $query = InstructorPayout::with(['instructorProfile.user']);
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        $payouts = $query->orderByDesc('period_start')->get();

        $headers = ['Reference', 'Instructor', 'Period', 'Bookings', 'Gross', 'Service Fee', 'Processing Fee', 'Net', 'Status', 'Paid At', 'Payment Ref'];

        return response()->streamDownload(function () use ($payouts, $headers) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers);
            foreach ($payouts as $p) {
                fputcsv($out, [
                    $p->reference,
                    $p->instructorProfile?->user?->name ?? '—',
                    $p->periodLabel(),
                    $p->bookings_count,
                    $p->gross_amount,
                    $p->service_fee_total,
                    $p->processing_fee_total,
                    $p->net_amount,
                    $p->status,
                    $p->paid_at?->format('Y-m-d'),
                    $p->payment_reference ?? '',
                ]);
            }
            fclose($out);
        }, 'payouts-' . date('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
