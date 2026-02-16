<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Order;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        \Log::info('Review request:', $request->all());
        $validator = \Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            \Log::warning('Review validation failed:', $validator->errors()->toArray());
            return response()->json(['message' => 'Dữ liệu không hợp lệ', 'errors' => $validator->errors()], 422);
        }

        try {
            $order = Order::findOrFail($request->order_id);

            if ($order->user_id !== $request->user()->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            if ($order->is_rated) {
                return response()->json(['message' => 'Đơn hàng này đã được đánh giá.'], 400);
            }

            $review = Review::create([
                'order_id' => $order->id,
                'user_id' => $request->user()->id,
                'store_id' => $order->store_id,
                'driver_id' => $order->driver_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);

            $order->update(['is_rated' => true]);
            \Log::info('Review created successfully for order ' . $order->id);

            return response()->json([
                'message' => 'Cảm ơn bạn đã đánh giá!',
                'review' => $review
            ], 201);
        } catch (\Throwable $e) {
            \Log::error('Review creation failed: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi hệ thống: ' . $e->getMessage()], 500);
        }
    }

    public function getStoreReviews($storeId)
    {
        $reviews = Review::where('store_id', $storeId)
            ->with('user:id,name')
            ->latest()
            ->get();

        return response()->json($reviews);
    }
}
