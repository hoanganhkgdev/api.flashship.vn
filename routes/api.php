<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\ServiceCategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\PromotionController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\WithdrawalController;

Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{slug}', [ServiceController::class, 'show']);

Route::get('/news', [NewsController::class, 'index']);
Route::get('/news/{id}', [NewsController::class, 'show']);

Route::get('/service-categories', [ServiceCategoryController::class, 'index']);

Route::get('/products', [ProductController::class, 'index']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/user/avatar', [AuthController::class, 'updateAvatar']);
    Route::post('/user/fcm-token', [AuthController::class, 'updateFcmToken']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::post('/orders/{order}/location', [OrderController::class, 'updateLocation']);

    // Route test thông báo
    Route::get('/test-notification', function (Request $request) {
        $user = $request->user();
        if (!$user->fcm_token) {
            return response()->json(['message' => 'Bạn chưa có FCM Token. Vui lòng login lại trên app.'], 400);
        }
        \App\Services\FCMService::send(
            $user->fcm_token,
            'Chào ' . $user->name . '! 👋',
            'Đây là thông báo test từ API. Chúc bạn một ngày tốt lành! 🛵',
            ['type' => 'test']
        );
        return response()->json(['message' => 'Đã gửi thông báo test tới thiết bị của bạn.']);
    });
});


Route::get('/stores', [StoreController::class, 'index']);
Route::get('/stores/featured', [StoreController::class, 'featured']);
Route::get('/stores/{id}', [StoreController::class, 'show']);
Route::get('/stores/{id}/reviews', [ReviewController::class, 'getStoreReviews']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/settings/bike', [SettingController::class, 'bikeSettings']);
Route::get('/settings/food', [SettingController::class, 'foodSettings']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/wallet/balance', [WalletController::class, 'getBalance']);
    Route::post('/wallet/topup', [WalletController::class, 'topup']);
    Route::get('/wallet/transactions', [WalletController::class, 'getTransactions']);

    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::get('/withdrawals', [WithdrawalController::class, 'index']);
    Route::post('/withdrawals', [WithdrawalController::class, 'store']);
});

Route::get('/promotions', [PromotionController::class, 'index']);
Route::post('/promotions/validate', [PromotionController::class, 'validateCode'])->middleware('auth:sanctum');


Route::get('/drivers/nearby', [DriverController::class, 'getNearbyDrivers']);

Route::middleware(['auth:sanctum'])->prefix('driver')->group(function () {
    Route::get('/orders/available', [DriverController::class, 'getAvailableOrders']);
    Route::get('/orders/active', [DriverController::class, 'getActiveOrder']);
    Route::get('/orders/history', [DriverController::class, 'getOrderHistory']);
    Route::post('/orders/{order}/accept', [DriverController::class, 'acceptOrder']);
    Route::post('/orders/{order}/status', [DriverController::class, 'updateOrderStatus']);
    Route::post('/location', [DriverController::class, 'updateLocation']);
    Route::post('/status', [DriverController::class, 'toggleStatus']);
    Route::post('/vehicle', [DriverController::class, 'updateVehicle']);
});
