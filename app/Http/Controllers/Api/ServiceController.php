<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Get list of all active services.
     */
    public function index()
    {
        $services = Service::where('is_active', true)
            ->orderBy('id', 'asc')
            ->get();

        return ServiceResource::collection($services);
    }

    /**
     * Get details of a single service.
     */
    public function show($slug)
    {
        $service = Service::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return new ServiceResource($service);
    }
}
