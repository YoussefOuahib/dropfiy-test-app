<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'products' => ProductResource::collection($this->whenLoaded('products', function () {
                return $this->products->filter(function ($product) {
                    return $product->is_active;
                });
            })),
            'total_products' => $this->products()->count(),
            'last_synced_at' => $this->last_synced_at,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}