<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Service;
use App\Models\User;
use App\Models\Promotion;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'service_slug' => 'required|string|exists:services,slug',
            'total_amount' => 'required|numeric',
            'shipping_fee' => 'nullable|numeric',
            'subtotal' => 'nullable|numeric',
            'pickup_address' => 'required|string',
            'dropoff_address' => 'required|string',
            'payment_method' => 'required|string|in:cash,flash_pay',
            'promotion_id' => 'nullable|exists:promotions,id',

            // Food/Mart specific
            'store_id' => 'required_if:service_slug,do-an,di-cho,dac-san|exists:stores,id',
            'items' => 'required_if:service_slug,do-an,di-cho,dac-san|array',
            'items.*.product_id' => 'required_if:service_slug,do-an,di-cho,dac-san|exists:products,id',
            'items.*.quantity' => 'required_if:service_slug,do-an,di-cho,dac-san|integer|min:1',
            'items.*.price' => 'required_if:service_slug,do-an,di-cho,dac-san|numeric',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $user = $request->user();
                $discountAmount = 0;
                $promotion = null;

                if ($request->promotion_id) {
                    $promotion = Promotion::find($request->promotion_id);
                    // Server side validation
                    if ($promotion && $promotion->expires_at > Carbon::now() && $request->total_amount >= $promotion->min_order_amount) {
                        if ($promotion->discount_type === 'fixed') {
                            $discountAmount = $promotion->discount_value;
                        } else {
                            $discountAmount = ($request->total_amount * $promotion->discount_value) / 100;
                            if ($promotion->max_discount_amount && $discountAmount > $promotion->max_discount_amount) {
                                $discountAmount = $promotion->max_discount_amount;
                            }
                        }
                        $promotion->increment('used_count');
                    }
                }

                $subtotal = $request->subtotal ?? ($request->total_amount - ($request->shipping_fee ?? 0));
                $shippingFee = $request->shipping_fee ?? 0;
                $finalAmount = ($subtotal + $shippingFee) - $discountAmount;
                $vatAmount = 0;

                // Calculate VAT for food if needed
                if (in_array($request->service_slug, ['food', 'do-an'])) {
                    $vatRate = (int) (\App\Models\Setting::where('key', 'food_vat_rate')->value('value') ?? 10);
                    $vatAmount = ($finalAmount * $vatRate) / 100;
                    $finalAmount += $vatAmount;
                }

                // Check balance if payment method is flash_pay
                if ($request->payment_method === 'flash_pay') {
                    if ($user->balance < $finalAmount) {
                        throw new \Exception('Số dư ví FlashShip Pay không đủ. Vui lòng nạp thêm tiền.');
                    }
                    // Deduct balance
                    $user->decrement('balance', $finalAmount);
                }

                $service = Service::where('slug', $request->service_slug)->first();

                $order = Order::create([
                    'user_id' => $user->id,
                    'service_id' => $service?->id ?? 1,
                    'store_id' => $request->store_id,
                    'promotion_id' => $request->promotion_id,
                    'discount_amount' => $discountAmount,
                    'status' => 'pending',
                    'payment_method' => $request->payment_method,
                    'total_amount' => $finalAmount,
                    'shipping_fee' => $shippingFee,
                    'vat_amount' => $vatAmount,
                    'pickup_address' => $request->pickup_address,
                    'dropoff_address' => $request->dropoff_address,
                    'notes' => $request->notes,
                    'pickup_lat' => $request->pickup_lat,
                    'pickup_lng' => $request->pickup_lng,
                    'dropoff_lat' => $request->dropoff_lat,
                    'dropoff_lng' => $request->dropoff_lng,
                ]);

                if (in_array($request->service_slug, ['do-an', 'di-cho', 'dac-san']) && $request->has('items')) {
                    foreach ($request->items as $item) {
                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'],
                            'price' => $item['price'],
                            'options' => $item['options'] ?? null,
                        ]);
                    }
                }

                // Notify all drivers about new order
                \App\Services\FCMService::notifyDrivers(
                    'Đơn hàng mới! 🚀',
                    'Có đơn hàng ' . ($service->name ?? 'mới') . ' đang chờ bạn. Nhận ngay!',
                    ['order_id' => (string) $order->id, 'type' => 'new_order']
                );

                return response()->json([
                    'message' => 'Order created successfully',
                    'order' => new OrderResource($order->load(['items.product', 'store', 'service'])),
                    'new_balance' => (float) $user->balance,
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create order',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with(['store', 'items.product', 'service'])
            ->latest()
            ->get();

        return OrderResource::collection($orders);
    }

    public function show(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id && (!$request->user()->role === 'driver' || $order->driver_id !== $request->user()->id)) {
            // Added driver check to allow drivers to see order details too
        }

        // Simpler check for now to allow both customer and assigned driver
        return new OrderResource($order->load(['store', 'items.product', 'driver', 'service']));
    }

    public function updateLocation(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'address' => 'nullable|string',
        ]);

        $order->update([
            'dropoff_lat' => $request->lat,
            'dropoff_lng' => $request->lng,
            'dropoff_address' => $request->address ?? $order->dropoff_address,
        ]);

        return response()->json([
            'message' => 'Location updated successfully',
            'dropoff_lat' => (float) $order->dropoff_lat,
            'dropoff_lng' => (float) $order->dropoff_lng,
        ]);
    }
}
