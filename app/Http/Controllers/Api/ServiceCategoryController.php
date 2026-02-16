<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Http\Resources\ServiceCategoryResource;
use Illuminate\Http\Request;

class ServiceCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceCategory::where('is_active', true)
            ->orderBy('sort_order', 'asc');

        if ($request->has('service_slug')) {
            $query->whereHas('service', function ($q) use ($request) {
                $q->where('slug', $request->service_slug);
            });
        }

        return ServiceCategoryResource::collection($query->get());
    }
}
