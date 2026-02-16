<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'total_amount' => (float) $this->total_amount,
            'shipping_fee' => (float) $this->shipping_fee,
            'vat_amount' => (float) $this->vat_amount,
            'discount_amount' => (float) $this->discount_amount,
            'subtotal' => (float) ($this->total_amount - $this->shipping_fee - $this->vat_amount + $this->discount_amount),
            'pickup_address' => $this->pickup_address,
            'pickup_lat' => (float) $this->pickup_lat,
            'pickup_lng' => (float) $this->pickup_lng,
            'dropoff_address' => $this->dropoff_address,
            'dropoff_lat' => (float) $this->dropoff_lat,
            'dropoff_lng' => (float) $this->dropoff_lng,
            'payment_method' => $this->payment_method,
            'is_rated' => (bool) $this->is_rated,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'created_at_human' => $this->created_at->diffForHumans(),
            'store' => new StoreResource($this->whenLoaded('store')),
            'service' => [
                'id' => $this->service->id,
                'name' => $this->service->name,
                'slug' => $this->service->slug,
            ],
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'phone' => $this->user->phone,
            ],
            'driver' => $this->driver_id ? [
                'id' => $this->driver->id,
                'name' => $this->driver->name,
                'phone' => $this->driver->phone,
                'current_lat' => $this->driver->driver->current_lat,
                'current_lng' => $this->driver->driver->current_lng,
            ] : null,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
