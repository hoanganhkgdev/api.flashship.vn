<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'lat' => (float) $this->lat,
            'lng' => (float) $this->lng,
            'image' => $this->image
                ? (filter_var($this->image, FILTER_VALIDATE_URL)
                    ? $this->image
                    : url('storage/' . $this->image))
                : null,
            'logo' => $this->logo
                ? (filter_var($this->logo, FILTER_VALIDATE_URL)
                    ? $this->logo
                    : url('storage/' . $this->logo))
                : null,
            'is_active' => (bool) $this->is_active,
            'is_featured' => (bool) $this->is_featured,
            'rating' => (float) $this->rating,
            'reviews_count' => $this->reviews()->count(),
            'service_slug' => $this->service?->slug,
            'service' => $this->whenLoaded('service', function () {
                return new ServiceResource($this->service);
            }),
            'products' => ProductResource::collection($this->whenLoaded('products')),
        ];
    }
}
