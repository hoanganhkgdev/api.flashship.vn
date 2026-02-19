<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users',
            'email' => 'nullable|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|string|in:customer,driver,merchant',
        ]);

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'customer',
        ]);

        if ($user->role === 'driver') {
            Driver::create([
                'user_id' => $user->id,
                'vehicle_type' => $request->vehicle_type,
                'license_plate' => $request->license_plate,
                'status' => 'offline',
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        if ($user->avatar) {
            $user->avatar = asset('storage/' . $user->avatar);
        }

        return response()->json([
            'message' => 'User registered successfully',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 201);
    }

    /**
     * Login user and create token.
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Thông tin đăng nhập không chính xác.',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        if ($user->avatar) {
            $user->avatar = asset('storage/' . $user->avatar);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    /**
     * Firebase Login
     */
    public function firebaseLogin(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        try {
            $auth = app('firebase.auth');
            $verifiedIdToken = $auth->verifyIdToken($request->id_token);
            $firebaseId = $verifiedIdToken->claims()->get('sub');
            $firebaseUser = $auth->getUser($firebaseId);
            $phone = $firebaseUser->phoneNumber;

            if (!$phone) {
                return response()->json(['message' => 'Phone number not found in token'], 400);
            }

            // Standardize phone number format if needed
            // Firebase usually provides +84... format
            
            $user = User::where('phone', $phone)->first();
            $isNewUser = false;

            if (!$user) {
                $user = User::create([
                    'name' => 'Khách hàng', // Default placeholder
                    'phone' => $phone,
                    'password' => Hash::make(Str::random(16)),
                    'role' => 'customer',
                ]);
                $isNewUser = true;
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            if ($user->avatar) {
                $user->avatar = asset('storage/' . $user->avatar);
            }

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
                'is_new_user' => $isNewUser,
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid token: ' . $e->getMessage()], 401);
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|nullable|email|unique:users,email,' . $user->id,
        ]);

        if ($request->has('name')) {
            $user->name = $request->name;
        }
        
        if ($request->has('email')) {
            $user->email = $request->email;
        }

        $user->save();

        if ($user->avatar) {
            $user->avatar = asset('storage/' . $user->avatar);
        }

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }

    /**
     * Logout user (Revoke the token).
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        // Xóa token khỏi database để không nhận được push thông báo sau khi logout
        $user->fcm_token = null;
        $user->save();

        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    /**
     * Update user avatar.
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $user = $request->user();

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
            $user->save();

            return response()->json([
                'message' => 'Avatar updated successfully',
                'avatar_url' => asset('storage/' . $path),
                'user' => $user,
            ]);
        }

        return response()->json(['message' => 'No file uploaded'], 400);
    }

    /**
     * Get the authenticated User.
     */
    public function me(Request $request)
    {
        $user = $request->user()->load('driver');
        if ($user->avatar) {
            $user->avatar = asset('storage/' . $user->avatar);
        }
        return response()->json($user);
    }

    /**
     * Update FCM Token
     */
    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user = $request->user();
        $user->fcm_token = $request->fcm_token;
        $user->save();

        return response()->json([
            'message' => 'FCM Token updated successfully',
        ]);
    }
}
