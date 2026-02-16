<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Http\Resources\StoreResource;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Get list of all active stores.
     */
    public function index(Request $request)
    {
        $query = Store::with('service')->where('is_active', true);

        if ($request->has('service_slug')) {
            $query->whereHas('service', function ($q) use ($request) {
                $q->where('slug', $request->service_slug);
            });
        }

        if ($request->has('service_category_slug')) {
            $query->whereHas('serviceCategories', function ($q) use ($request) {
                $q->where('slug', $request->service_category_slug);
            });
        }

        $stores = $query->get();
        return StoreResource::collection($stores);
    }

    /**
     * Get details of a single store with categories and products.
     */
    public function show($id)
    {
        $store = Store::with([
            'service',
            'products.serviceCategory',
            'products.optionGroups.options'
        ])->findOrFail($id);
        return new StoreResource($store);
    }

    /**
     * Get featured stores (for homepage).
     */
    public function featured(Request $request)
    {
        $query = Store::with('service')
            ->where('is_active', true)
            ->where('is_featured', true);

        if ($request->has('service_slug')) {
            $query->whereHas('service', function ($q) use ($request) {
                $q->where('slug', $request->service_slug);
            });
        }

        $stores = $query->take(10)->get();
        return StoreResource::collection($stores);
    }
}
