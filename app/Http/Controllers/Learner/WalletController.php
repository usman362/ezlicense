<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\LearnerTransaction;
use App\Models\LearnerWallet;
use App\Notifications\WalletCredited;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    /**
     * Add credit to wallet (simulated payment processing).
     * In production, this would integrate with Stripe/PayPal via SiteSetting keys.
     */
    public function addCredit(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user->isLearner()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:50|max:2000',
            'payment_method' => 'required|in:card,paypal',
        ]);

        $creditAmount = (float) $validated['amount'];
        $discount = 0;

        // Apply package discounts
        if ($creditAmount == 250) {
            $discount = 10;
        } elseif ($creditAmount == 500) {
            $discount = 30;
        }

        $chargeAmount = $creditAmount - $discount;

        // Check if payment gateway is configured
        $stripeKey = \App\Models\SiteSetting::get('stripe_secret_key');
        $paypalId = \App\Models\SiteSetting::get('paypal_client_id');

        if ($validated['payment_method'] === 'card' && empty($stripeKey)) {
            // Demo mode: process without real gateway
            \Illuminate\Support\Facades\Log::info('Wallet credit added (demo mode - no Stripe key configured)', [
                'user_id' => $user->id,
                'credit' => $creditAmount,
                'charged' => $chargeAmount,
            ]);
        } elseif ($validated['payment_method'] === 'paypal' && empty($paypalId)) {
            \Illuminate\Support\Facades\Log::info('Wallet credit added (demo mode - no PayPal key configured)', [
                'user_id' => $user->id,
                'credit' => $creditAmount,
                'charged' => $chargeAmount,
            ]);
        }

        // Update wallet balance
        $wallet = LearnerWallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'non_refundable_credit' => 0]
        );

        $wallet->balance = (float) $wallet->balance + $creditAmount;
        if ($discount > 0) {
            $wallet->non_refundable_credit = (float) $wallet->non_refundable_credit + $discount;
        }
        $wallet->save();

        // Record transaction
        LearnerTransaction::create([
            'user_id' => $user->id,
            'type' => LearnerTransaction::TYPE_CREDIT_PURCHASE,
            'description' => 'Wallet top-up — $' . number_format($creditAmount, 2) . ' credit' . ($discount > 0 ? ' (incl. $' . number_format($discount, 2) . ' bonus)' : ''),
            'amount' => $creditAmount,
            'balance_after' => $wallet->balance,
        ]);

        // Send notification
        try {
            $user->notify(new WalletCredited($creditAmount, (float) $wallet->balance));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Wallet credit notification failed: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Credit added successfully!',
            'data' => [
                'credit_added' => $creditAmount,
                'amount_charged' => $chargeAmount,
                'discount' => $discount,
                'new_balance' => (float) $wallet->balance,
                'new_balance_display' => '$' . number_format((float) $wallet->balance, 2),
            ],
        ]);
    }

    /**
     * Get wallet summary: balance, non-refundable credit, saved payment method placeholder.
     */
    public function show(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user->isLearner()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $wallet = LearnerWallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'non_refundable_credit' => 0]
        );

        return response()->json([
            'data' => [
                'balance' => (float) $wallet->balance,
                'balance_display' => '$' . number_format((float) $wallet->balance, 2),
                'non_refundable_credit' => (float) $wallet->non_refundable_credit,
                'non_refundable_credit_display' => '$' . number_format((float) $wallet->non_refundable_credit, 2),
                'saved_payment_method' => null, // placeholder: no saved card
            ],
        ]);
    }

    /**
     * List transactions for the authenticated learner.
     */
    public function transactions(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user->isLearner()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $transactions = LearnerTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $items = $transactions->getCollection()->map(function (LearnerTransaction $t) {
            return [
                'id' => $t->id,
                'transaction_id' => '#' . $t->id,
                'description' => $t->description,
                'date' => $t->created_at->format('D, d M Y'),
                'amount' => (float) $t->amount,
                'amount_display' => ($t->amount >= 0 ? '+' : '') . '$' . number_format(abs((float) $t->amount), 2),
                'balance_after' => (float) $t->balance_after,
                'balance_after_display' => '$' . number_format((float) $t->balance_after, 2),
            ];
        });
        $transactions->setCollection($items);

        return response()->json($transactions);
    }
}
