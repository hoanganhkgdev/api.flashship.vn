<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = Promotion::where('expires_at', '>', Carbon::now())
            ->where(function ($query) {
                $query->whereNull('usage_limit')
                    ->orWhereRaw('used_count < usage_limit');
            })
            ->get();

        return response()->json($promotions);
    }

    public function validateCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'order_amount' => 'required|numeric'
        ]);

        $promotion = Promotion::where('code', $request->code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$promotion) {
            return response()->json(['message' => 'Mã giảm giá không tồn tại hoặc đã hết hạn.'], 404);
        }

        if ($promotion->usage_limit && $promotion->used_count >= $promotion->usage_limit) {
            return response()->json(['message' => 'Mã giảm giá đã hết lượt sử dụng.'], 400);
        }

        if ($request->order_amount < $promotion->min_order_amount) {
            return response()->json([
                'message' => 'Đơn hàng tối thiểu ' . number_format($promotion->min_order_amount) . 'đ để sử dụng mã này.'
            ], 400);
        }

        $discount = 0;
        if ($promotion->discount_type === 'fixed') {
            $discount = $promotion->discount_value;
        } else {
            $discount = ($request->order_amount * $promotion->discount_value) / 100;
            if ($promotion->max_discount_amount && $discount > $promotion->max_discount_amount) {
                $discount = $promotion->max_discount_amount;
            }
        }

        return response()->json([
            'valid' => true,
            'promotion' => $promotion,
            'discount_amount' => (float) $discount
        ]);
    }
}
