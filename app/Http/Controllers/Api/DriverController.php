<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Driver;
use App\Http\Resources\OrderResource;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DriverController extends Controller
{
    public function getAvailableOrders(Request $request)
    {
        $orders = Order::where('status', 'pending')
            ->whereNull('driver_id')
            ->with(['store', 'items.product', 'service'])
            ->latest()
            ->get();

        return OrderResource::collection($orders);
    }

    public function acceptOrder(Request $request, Order $order)
    {
        if ($order->driver_id) {
            return response()->json(['message' => 'Đơn hàng đã có tài xế nhận.'], 400);
        }

        $order->update([
            'driver_id' => $request->user()->id,
            'status' => 'accepted'
        ]);

        // Notify customer
        $customer = $order->user;
        if ($customer && $customer->fcm_token) {
            \App\Services\FCMService::send(
                $customer->fcm_token,
                'FlashShip: Tài xế đã nhận đơn! 🛵',
                'Tài xế ' . $request->user()->name . ' đang chuẩn bị đến lấy hàng.',
                ['order_id' => (string) $order->id, 'type' => 'order_accepted']
            );
        }

        return new OrderResource($order->load(['store', 'items.product', 'service']));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:arrived_at_pickup,picked_up,arrived_at_dropoff,completed,cancelled'
        ]);

        if ($order->driver_id !== $request->user()->id) {
            return response()->json(['message' => 'Bạn không có quyền cập nhật đơn hàng này.'], 403);
        }

        $oldStatus = $order->status;

        if ($request->status === 'completed' && $order->status !== 'completed') {
            DB::transaction(function () use ($order, $request) {
                // ... same commission logic ...
                $commissionRate = 0.20;
                $shippingFee = $order->shipping_fee ?? 0;
                $vatAmount = $order->vat_amount ?? 0;

                $feeToDeduct = ($shippingFee * $commissionRate) + $vatAmount;

                if ($feeToDeduct > 0) {
                    $driverUser = $request->user();
                    $driverUser->decrement('balance', $feeToDeduct);

                    WalletTransaction::create([
                        'user_id' => $driverUser->id,
                        'amount' => -$feeToDeduct,
                        'type' => 'commission',
                        'description' => 'Phí hệ thống & VAT cho đơn hàng #' . $order->id,
                        'reference_id' => $order->id,
                    ]);
                }

                $order->update(['status' => 'completed']);
            });
        } else {
            $order->update(['status' => $request->status]);
        }

        // Notify customer about status change
        $statusMessages = [
            'arrived_at_pickup' => 'Tài xế đã đến điểm lấy hàng.',
            'picked_up' => 'Đơn hàng của bạn đang trên đường giao.',
            'arrived_at_dropoff' => 'Tài xế đã đến điểm giao hàng. Vui lòng nhận hàng!',
            'completed' => 'Chuyến đi đã hoàn tất. Cảm ơn bạn đã sử dụng FlashShip!',
            'cancelled' => 'Đơn hàng của bạn đã bị hủy.',
        ];

        if (isset($statusMessages[$request->status])) {
            $customer = $order->user;
            if ($customer && $customer->fcm_token) {
                \App\Services\FCMService::send(
                    $customer->fcm_token,
                    'Cập nhật đơn hàng #' . $order->id,
                    $statusMessages[$request->status],
                    ['order_id' => (string) $order->id, 'status' => $request->status, 'type' => 'order_status_update']
                );
            }
        }

        return new OrderResource($order->load(['store', 'items.product', 'service']));
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        Driver::updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'current_lat' => $request->lat,
                'current_lng' => $request->lng,
            ]
        );

        return response()->json(['message' => 'Cập nhật vị trí thành công.']);
    }

    public function toggleStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|string|in:online,offline'
        ]);

        $driver = Driver::updateOrCreate(
            ['user_id' => $request->user()->id],
            ['status' => $request->status]
        );

        return response()->json([
            'message' => 'Cập nhật trạng thái thành công.',
            'status' => $driver->status
        ]);
    }

    public function getActiveOrder(Request $request)
    {
        $order = Order::where('driver_id', $request->user()->id)
            ->whereIn('status', ['accepted', 'arrived_at_pickup', 'picked_up', 'arrived_at_dropoff'])
            ->with(['store', 'items.product', 'service', 'user'])
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Không có đơn hàng đang xử lý.'], 404);
        }

        return new OrderResource($order);
    }

    public function getOrderHistory(Request $request)
    {
        $orders = Order::where('driver_id', $request->user()->id)
            ->whereIn('status', ['completed', 'cancelled'])
            ->with(['store', 'service', 'user'])
            ->latest()
            ->paginate(20);

        return OrderResource::collection($orders);
    }

    public function getNearbyDrivers(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radius' => 'nullable|numeric', // in km
        ]);

        $lat = $request->lat;
        $lng = $request->lng;
        $radius = $request->radius ?? 5;

        // Fetch all online drivers and calculate distance in PHP
        // This is safe for a reasonable number of drivers and avoids SQL math function issues (especially in SQLite)
        $allDrivers = Driver::where('status', 'online')
            ->whereNotNull('current_lat')
            ->whereNotNull('current_lng')
            ->get(['id', 'user_id', 'current_lat', 'current_lng']);

        $nearbyDrivers = $allDrivers->filter(function ($driver) use ($lat, $lng, $radius) {
            $distance = $this->calculateDistance($lat, $lng, $driver->current_lat, $driver->current_lng);
            $driver->distance = $distance;
            return $distance <= $radius;
        })->values();

        return response()->json([
            'data' => $nearbyDrivers
        ]);
    }

    public function updateVehicle(Request $request)
    {
        $request->validate([
            'vehicle_type' => 'required|string',
            'license_plate' => 'required|string',
            'vehicle_brand' => 'nullable|string',
            'vehicle_model' => 'nullable|string',
            'vehicle_color' => 'nullable|string',
        ]);

        $driver = Driver::updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'vehicle_type' => $request->vehicle_type,
                'license_plate' => $request->license_plate,
                'vehicle_brand' => $request->vehicle_brand,
                'vehicle_model' => $request->vehicle_model,
                'vehicle_color' => $request->vehicle_color,
            ]
        );

        $user = $request->user()->load('driver');
        if ($user->avatar) {
            $user->avatar = asset('storage/' . $user->avatar);
        }

        return response()->json([
            'message' => 'Cập nhật thông tin phương tiện thành công.',
            'driver' => $driver,
            'user' => $user
        ]);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
