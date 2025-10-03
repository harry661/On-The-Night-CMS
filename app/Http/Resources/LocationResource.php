<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
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
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'is_active' => $this->is_active,
            'full_address' => $this->full_address,
            'venues_count' => $this->when(isset($this->venues_count), $this->venues_count),
            'venues' => VenueResource::collection($this->whenLoaded('venues')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}