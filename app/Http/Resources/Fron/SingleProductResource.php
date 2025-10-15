<?php

namespace App\Http\Resources\Fron;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SingleProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'store' => $this->store->name ?? null,
            'category' => $this->category->name ?? null,
            'name' => $this->name,
            'slug' => $this->slug,
            'disc' => $this->disc,
            'image_url' => $this->image_url,
            'price' => $this->price,
            'compare_price' => $this->compare_price,
            'rating' => $this->rating,
            'type' => $this->type,
            'options' => $this->options,
            'tags' => TagNameResource::collection($this->tags),
            'sameProducts' => $this->sameProds,
        ];
    }
}
