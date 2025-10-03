<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DealResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'deal_type' => $this->deal_type,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_active' => $this->is_active,
            'is_featured' => $this->featured,
            'is_current' => $this->is_active && $this->start_date <= now() && $this->end_date >= now(),
            'time_remaining' => $this->timeRemaining(),
            'venue' => new VenueResource($this->whenLoaded('venue')),
            'venue_id' => $this->venue_id,
            'venue_name' => $this->when($this->relationLoaded('venue'), $this->venue->name),
            'images' => $this->getAllMediaUrls(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Get all media URLs for this deal with placeholder fallback
     */
    private function getAllMediaUrls(): array
    {
        $urls = [];
        
        foreach ($this->getMedia('deal_images') as $media) {
            $urls[] = [
                'url' => $media->getUrl(),
                'thumbnail' => $media->getUrl('thumb'),
                'preview' => $media->getUrl('preview'),
                'collection' => 'deal_images',
            ];
        }
        
        foreach ($this->getMedia('deal_gallery') as $media) {
            $urls[] = [
                'url' => $media->getUrl(),
                'thumbnail' => $media->getUrl('thumb'),
                'preview' => $media->getUrl('preview'),
                'collection' => 'deal_gallery',
            ];
        }
        
        // If no images found, provide placeholder
        if (empty($urls)) {
            $urls[] = [
                'url' => asset('images/deal-placeholder.jpg'),
                'thumbnail' => asset('images/deal-placeholder.jpg'),
                'preview' => asset('images/deal-placeholder.jpg'),
                'collection' => 'placeholder',
            ];
        }
        
        return $urls;
    }

    /**
     * Calculate time remaining in human readable format
     */
    private function timeRemaining(): ?string
    {
        if ($this->end_date <= now()) {
            return 'Expired';
        }

        $diff = $this->end_date->diff(now());
        
        if ($diff->days > 0) {
            return $diff->days . ' days left';
        } elseif ($diff->h > 0) {
            return $diff->h . ' hours left';
        } elseif ($diff->i > 0) {
            return $diff->i . ' minutes left';
        } else {
            return 'Less than a minute left';
        }
    }
}