<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'icon' => $this->icon
                ? (filter_var($this->icon, FILTER_VALIDATE_URL)
                    ? $this->icon
                    : url('storage/' . $this->icon))
                : null,
            'image' => $this->image
                ? (filter_var($this->image, FILTER_VALIDATE_URL)
                    ? $this->image
                    : url('storage/' . $this->image))
                : null,
            'sort_order' => $this->sort_order,
        ];
    }
}
