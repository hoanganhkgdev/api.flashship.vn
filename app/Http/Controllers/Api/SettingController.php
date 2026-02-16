<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function bikeSettings()
    {
        $settings = Setting::where('group', 'bike')->pluck('value', 'key');

        return response()->json([
            'base_price' => (int) ($settings['bike_base_price'] ?? 13000),
            'additional_km_price' => (int) ($settings['bike_additional_km_price'] ?? 5000),
            'base_distance' => (int) ($settings['bike_base_distance'] ?? 3),
        ]);
    }

    public function foodSettings()
    {
        $settings = Setting::where('group', 'food')->pluck('value', 'key');

        return response()->json([
            'base_price' => (int) ($settings['food_base_price'] ?? 13000),
            'additional_km_price' => (int) ($settings['food_additional_km_price'] ?? 4000),
            'base_distance' => (int) ($settings['food_base_distance'] ?? 3),
            'vat_rate' => (int) ($settings['food_vat_rate'] ?? 10),
        ]);
    }
}
