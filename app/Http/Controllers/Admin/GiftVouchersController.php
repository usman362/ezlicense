<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GiftVoucher;
use Illuminate\Http\Request;

class GiftVouchersController extends Controller
{
    public function index(Request $request)
    {
        $query = GiftVoucher::with(['purchaser', 'redeemer']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%")
                  ->orWhere('recipient_email', 'like', "%{$search}%")
                  ->orWhere('purchaser_name', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $vouchers = $query->orderByDesc('created_at')->paginate(30)->withQueryString();

        $stats = [
            'total_sold' => GiftVoucher::whereIn('status', ['active', 'redeemed', 'partially_redeemed'])->sum('amount'),
            'total_redeemed' => GiftVoucher::whereIn('status', ['redeemed'])->sum('amount'),
            'active_count' => GiftVoucher::whereIn('status', ['active', 'partially_redeemed'])->count(),
            'pending_count' => GiftVoucher::where('status', 'pending')->count(),
        ];

        return view('admin.gift-vouchers.index', ['vouchers' => $vouchers, 'stats' => $stats]);
    }

    public function create()
    {
        return view('admin.gift-vouchers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'voucher_type' => 'required|in:1hour,5hour,custom',
            'custom_amount' => 'required_if:voucher_type,custom|nullable|numeric|min:50|max:5000',
            'recipient_name' => 'required|string|max:100',
            'recipient_email' => 'required|email|max:255',
            'personal_message' => 'nullable|string|max:500',
        ]);

        $type = $request->input('voucher_type');
        $amount = $type === 'custom'
            ? (float) $request->input('custom_amount')
            : (GiftVoucher::PRICES[$type] ?? 0);

        $voucher = GiftVoucher::create([
            'code' => GiftVoucher::generateCode(),
            'purchaser_name' => 'Admin',
            'purchaser_email' => auth()->user()->email,
            'recipient_name' => $request->input('recipient_name'),
            'recipient_email' => $request->input('recipient_email'),
            'personal_message' => $request->input('personal_message'),
            'amount' => $amount,
            'remaining_amount' => $amount,
            'voucher_type' => $type,
            'status' => GiftVoucher::STATUS_ACTIVE,
            'paid_at' => now(),
            'payment_method' => 'admin',
            'payment_reference' => 'admin-created',
            'expires_at' => now()->addYear(),
        ]);

        return redirect()->route('admin.gift-vouchers.index')->with('message', 'Gift voucher ' . $voucher->code . ' created and activated.');
    }

    public function show(GiftVoucher $giftVoucher)
    {
        $giftVoucher->load(['purchaser', 'redeemer']);
        return view('admin.gift-vouchers.show', ['voucher' => $giftVoucher]);
    }

    public function edit(GiftVoucher $giftVoucher)
    {
        return view('admin.gift-vouchers.edit', ['voucher' => $giftVoucher]);
    }

    public function update(Request $request, GiftVoucher $giftVoucher)
    {
        $data = $request->validate([
            'recipient_name' => 'required|string|max:100',
            'recipient_email' => 'required|email|max:255',
            'personal_message' => 'nullable|string|max:500',
            'expires_at' => 'nullable|date|after:today',
        ]);

        $giftVoucher->update($data);

        return redirect()->route('admin.gift-vouchers.index')
            ->with('message', 'Voucher ' . $giftVoucher->code . ' updated.');
    }

    public function cancel(GiftVoucher $giftVoucher)
    {
        if (in_array($giftVoucher->status, [GiftVoucher::STATUS_REDEEMED])) {
            return redirect()->back()->with('message', 'Cannot cancel a fully redeemed voucher.');
        }

        $giftVoucher->update(['status' => GiftVoucher::STATUS_CANCELLED]);

        return redirect()->back()->with('message', 'Voucher ' . $giftVoucher->code . ' cancelled.');
    }
}
