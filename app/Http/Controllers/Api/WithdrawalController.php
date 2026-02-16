<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $withdrawals = Withdrawal::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($withdrawals);
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:50000',
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'account_holder' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $user = $request->user();

        if ($user->balance < $request->amount) {
            return response()->json([
                'message' => 'Số dư không đủ để thực hiện đi lệnh rút tiền.',
            ], 400);
        }

        try {
            return DB::transaction(function () use ($request, $user) {
                $withdrawal = Withdrawal::create([
                    'user_id' => $user->id,
                    'amount' => $request->amount,
                    'bank_name' => $request->bank_name,
                    'account_number' => $request->account_number,
                    'account_holder' => $request->account_holder,
                    'notes' => $request->notes,
                    'status' => 'pending',
                ]);

                // Record transaction
                WalletTransaction::create([
                    'user_id' => $user->id,
                    'amount' => -$request->amount,
                    'type' => 'withdrawal',
                    'description' => 'Rút tiền về tài khoản ' . $request->bank_name,
                    'reference_id' => $withdrawal->id,
                ]);

                return response()->json([
                    'message' => 'Yêu cầu rút tiền đã được gửi thành công. Vui lòng chờ admin phê duyệt.',
                    'withdrawal' => $withdrawal,
                    'new_balance' => (float) $user->balance,
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra, vui lòng thử lại sau.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
