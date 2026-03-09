<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\LearnerTransaction;
use App\Models\LearnerWallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
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
