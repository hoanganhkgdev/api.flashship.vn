<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function getBalance(Request $request)
    {
        return response()->json([
            'balance' => (float) $request->user()->balance,
        ]);
    }

    public function topup(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
        ]);

        $user = $request->user();

        return DB::transaction(function () use ($user, $request) {
            $user->increment('balance', $request->amount);

            WalletTransaction::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'type' => 'topup',
                'description' => 'Nạp tiền vào tài khoản',
            ]);

            return response()->json([
                'message' => 'Nạp tiền thành công!',
                'new_balance' => (float) $user->balance,
            ]);
        });
    }

    public function getTransactions(Request $request)
    {
        $transactions = WalletTransaction::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return response()->json($transactions);
    }
}
