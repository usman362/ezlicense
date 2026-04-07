<?php

namespace App\Http\Controllers;

use App\Models\GiftVoucher;
use App\Models\WalletTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GiftVoucherController extends Controller
{
    /**
     * Purchase a gift voucher.
     */
    public function purchase(Request $request): JsonResponse
    {
        $request->validate([
            'voucher_type' => 'required|in:1hour,5hour,custom',
            'custom_amount' => 'required_if:voucher_type,custom|nullable|numeric|min:50|max:5000',
            'recipient_name' => 'required|string|max:100',
            'recipient_email' => 'required|email|max:255',
            'personal_message' => 'nullable|string|max:500',
            'purchaser_name' => 'nullable|string|max:100',
            'purchaser_email' => 'nullable|email|max:255',
        ]);

        $type = $request->input('voucher_type');
        $amount = $type === 'custom'
            ? (float) $request->input('custom_amount')
            : (GiftVoucher::PRICES[$type] ?? 0);

        if ($amount <= 0) {
            return response()->json(['message' => 'Invalid voucher amount.'], 422);
        }

        $user = Auth::user();

        $voucher = GiftVoucher::create([
            'code' => GiftVoucher::generateCode(),
            'purchaser_id' => $user?->id,
            'purchaser_name' => $request->input('purchaser_name', $user?->name),
            'purchaser_email' => $request->input('purchaser_email', $user?->email),
            'recipient_name' => $request->input('recipient_name'),
            'recipient_email' => $request->input('recipient_email'),
            'personal_message' => $request->input('personal_message'),
            'amount' => $amount,
            'remaining_amount' => $amount,
            'voucher_type' => $type,
            'status' => GiftVoucher::STATUS_PENDING,
            'expires_at' => now()->addYear(),
        ]);

        return response()->json([
            'data' => [
                'id' => $voucher->id,
                'code' => $voucher->code,
                'amount' => $voucher->amount,
                'status' => $voucher->status,
                'expires_at' => $voucher->expires_at->toIso8601String(),
            ],
            'message' => 'Voucher created. Please complete payment.',
        ], 201);
    }

    /**
     * Simulate payment confirmation (in production, this would be a webhook).
     */
    public function confirmPayment(Request $request, GiftVoucher $giftVoucher): JsonResponse
    {
        if ($giftVoucher->status !== GiftVoucher::STATUS_PENDING) {
            return response()->json(['message' => 'Voucher is not pending payment.'], 422);
        }

        $giftVoucher->update([
            'status' => GiftVoucher::STATUS_ACTIVE,
            'payment_method' => $request->input('payment_method', 'card'),
            'payment_reference' => $request->input('payment_reference', 'manual-' . now()->timestamp),
            'paid_at' => now(),
        ]);

        // TODO: Send email to recipient with voucher code

        return response()->json([
            'data' => [
                'id' => $giftVoucher->id,
                'code' => $giftVoucher->code,
                'status' => $giftVoucher->status,
            ],
            'message' => 'Payment confirmed. Voucher is now active.',
        ]);
    }

    /**
     * Redeem a voucher code — adds credit to learner wallet.
     */
    public function redeem(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|max:16',
        ]);

        $user = Auth::user();
        $voucher = GiftVoucher::where('code', $request->input('code'))->first();

        if (!$voucher) {
            return response()->json(['message' => 'Invalid voucher code.'], 404);
        }

        if ($voucher->isExpired()) {
            $voucher->update(['status' => GiftVoucher::STATUS_EXPIRED]);
            return response()->json(['message' => 'This voucher has expired.'], 422);
        }

        if (!$voucher->isActive()) {
            return response()->json(['message' => 'This voucher is not available for redemption. Status: ' . $voucher->status], 422);
        }

        $creditAmount = $voucher->remaining_amount;

        DB::transaction(function () use ($voucher, $user, $creditAmount) {
            // Add credit to user wallet
            $user->increment('wallet_balance', $creditAmount);

            // Record wallet transaction
            WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'credit',
                'amount' => $creditAmount,
                'description' => 'Gift voucher redeemed: ' . $voucher->code,
                'reference_type' => 'gift_voucher',
                'reference_id' => $voucher->id,
            ]);

            // Update voucher
            $voucher->update([
                'redeemer_id' => $user->id,
                'remaining_amount' => 0,
                'status' => GiftVoucher::STATUS_REDEEMED,
                'redeemed_at' => now(),
            ]);
        });

        return response()->json([
            'data' => [
                'credited_amount' => $creditAmount,
                'new_wallet_balance' => $user->fresh()->wallet_balance,
                'voucher_code' => $voucher->code,
            ],
            'message' => '$' . number_format($creditAmount, 2) . ' has been added to your wallet.',
        ]);
    }

    /**
     * Check voucher status without redeeming.
     */
    public function check(Request $request): JsonResponse
    {
        $request->validate(['code' => 'required|string|max:16']);

        $voucher = GiftVoucher::where('code', $request->input('code'))->first();

        if (!$voucher) {
            return response()->json(['message' => 'Invalid voucher code.'], 404);
        }

        return response()->json([
            'data' => [
                'code' => $voucher->code,
                'amount' => $voucher->amount,
                'remaining_amount' => $voucher->remaining_amount,
                'status' => $voucher->status,
                'status_label' => GiftVoucher::statusLabels()[$voucher->status] ?? $voucher->status,
                'expires_at' => $voucher->expires_at?->toIso8601String(),
                'is_active' => $voucher->isActive(),
            ],
        ]);
    }
}
