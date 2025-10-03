<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'review_text' => $this->review_text,
            'rating' => $this->rating,
            'reviewer_name' => $this->reviewer_name ?: 'Anonymous',
            'reviewer_email' => $this->reviewer_email,
            'is_approved' => $this->is_approved,
            'images' => $this->getAllMediaUrls(),
            'venue' => new VenueResource($this->whenLoaded('venue')),
            'venue_id' => $this->venue_id,
            'venue_name' => $this->when($this->relationLoaded('venue'), $this->venue->name),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Get all media URLs for this review
     */
    private function getAllMediaUrls(): array
    {
        $urls = [];
        
        foreach ($this->getMedia('review_images') as $media) {
            $urls[] = [
                'url' => $media->getUrl(),
                'thumbnail' => $media->getUrl('thumb'),
                'preview' => $media->getUrl('preview'),
                'collection' => 'review_images',
            ];
        }
        
        foreach ($this->getMedia('review_gallery') as $media) {
            $urls[] = [
                'url' => $media->getUrl(),
                'thumbnail' => $media->getUrl('thumb'),
                'preview' => $media->getUrl('preview'),
                'collection' => 'review_gallery',
            ];
        }
        
        return $urls;
    }
}