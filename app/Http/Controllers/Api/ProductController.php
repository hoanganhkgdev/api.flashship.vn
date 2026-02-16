<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['serviceCategory', 'store', 'optionGroups.options'])
            ->where('is_available', true);

        if ($request->has('service_category_slug')) {
            $query->whereHas('serviceCategory', function ($q) use ($request) {
                $q->where('slug', $request->service_category_slug);
            });
        }

        if ($request->has('service_slug')) {
            $query->whereHas('serviceCategory.service', function ($q) use ($request) {
                $q->where('slug', $request->service_slug);
            });
        }

        $products = $query->paginate(20);
        return ProductResource::collection($products);
    }
}
