<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->price,
            'image' => $this->image ? (filter_var($this->image, FILTER_VALIDATE_URL) ? $this->image : url('storage/' . $this->image)) : null,
            'is_available' => (bool) $this->is_available,
            'category' => [
                'id' => $this->serviceCategory?->id,
                'name' => $this->serviceCategory?->name,
                'slug' => $this->serviceCategory?->slug,
            ],
            'store' => [
                'id' => $this->store->id,
                'name' => $this->store->name,
                'logo' => $this->store->logo ? (filter_var($this->store->logo, FILTER_VALIDATE_URL) ? $this->store->logo : url('storage/' . $this->store->logo)) : null,
                'rating' => (float) $this->store->rating,
            ],
            'option_groups' => $this->whenLoaded('optionGroups', function () {
                return $this->optionGroups->map(function ($group) {
                    return [
                        'id' => $group->id,
                        'name' => $group->name,
                        'is_required' => (bool) $group->is_required,
                        'max_selectable' => (int) $group->max_selectable,
                        'options' => $group->options->map(function ($option) {
                            return [
                                'id' => $option->id,
                                'name' => $option->name,
                                'price' => (float) $option->price,
                                'is_available' => (bool) $option->is_available,
                            ];
                        }),
                    ];
                });
            }),
        ];
    }
}
