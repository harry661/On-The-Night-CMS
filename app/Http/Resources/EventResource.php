<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            'event_type' => $this->event_type,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'ticket_price' => $this->ticket_price,
            'is_active' => $this->is_active,
            'is_featured' => $this->featured ?? false,
            'sold_out' => $this->sold_out,
            'is_upcoming' => $this->is_active && $this->start_date > now(),
            'is_current' => $this->is_active && $this->start_date <= now() && $this->end_date >= now(),
            'has_ended' => $this->end_date < now(),
            'time_until_start' => $this->timeUntilStart(),
            'venue' => new VenueResource($this->whenLoaded('venue')),
            'venue_id' => $this->venue_id,
            'venue_name' => $this->when($this->relationLoaded('venue'), $this->venue->name),
            'images' => $this->getAllMediaUrls(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Get all media URLs for this event with placeholder fallback
     */
    private function getAllMediaUrls(): array
    {
        $urls = [];
        
        foreach ($this->getMedia('event_images') as $media) {
            $urls[] = [
                'url' => $media->getUrl(),
                'thumbnail' => $media->getUrl('thumb'),
                'preview' => $media->getUrl('preview'),
                'collection' => 'event_images',
            ];
        }
        
        foreach ($this->getMedia('event_gallery') as $media) {
            $urls[] = [
                'url' => $media->getUrl(),
                'thumbnail' => $media->getUrl('thumb'),
                'preview' => $media->getUrl('preview'),
                'collection' => 'event_gallery',
            ];
        }
        
        // If no images found, provide placeholder
        if (empty($urls)) {
            $urls[] = [
                'url' => asset('images/event-placeholder.jpg'),
                'thumbnail' => asset('images/event-placeholder.jpg'),
                'preview' => asset('images/event-placeholder.jpg'),
                'collection' => 'placeholder',
            ];
        }
        
        return $urls;
    }

    /**
     * Calculate time until event starts in human readable format
     */
    private function timeUntilStart(): ?string
    {
        if ($this->start_date <= now()) {
            return null;
        }

        $diff = $this->start_date->diff(now());
        
        if ($diff->days > 0) {
            return $diff->days . ' days';
        } elseif ($diff->h > 0) {
            return $diff->h . ' hours';
        } elseif ($diff->i > 0) {
            return $diff->i . ' minutes';
        } else {
            return 'Soon';
        }
    }
}