<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VenueResource extends JsonResource
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
            'description' => $this->description,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'full_address' => $this->full_address,
            'opening_hours' => $this->opening_hours,
            'amenities' => $this->amenities,
            'is_active' => $this->is_active,
            'status' => $this->status,
            'images' => $this->getAllMediaUrls(),
            'location' => new LocationResource($this->whenLoaded('location')),
            'drink_types' => DrinkTypeResource::collection($this->whenLoaded('drinkTypes')),
            'music_genres' => MusicGenreResource::collection($this->whenLoaded('musicGenres')),
            'deals' => DealResource::collection($this->whenLoaded('deals')),
            'events' => EventResource::collection($this->whenLoaded('events')),
            'reviews' => [
                'count' => $this->whenLoaded('reviews', function () {
                    return $this->reviews()->where('is_approved', true)->count();
                }),
                'average_rating' => $this->whenLoaded('reviews', function () {
                    return round($this->reviews()->where('is_approved', true)->avg('rating') ?: 0, 1);
                }),
                'approved_reviews' => ReviewResource::collection($this->whenLoaded('approvedReviews')),
            ],
            'distance' => $this->when($request->has('user_lat') && $request->has('user_lon'), function () use ($request) {
                return $this->calculateDistance(
                    $request->input('user_lat'),
                    $request->input('user_lon'),
                    $this->latitude,
                    $this->longitude
                );
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Get all media URLs for this venue with placeholder fallback
     */
    private function getAllMediaUrls(): array
    {
        $urls = [];
        
        // Get main venue images
        foreach ($this->getMedia('venue_images') as $media) {
            $urls[] = [
                'url' => $media->getUrl(),
                'thumbnail' => $media->getUrl('thumb'),
                'preview' => $media->getUrl('preview'),
                'collection' => 'venue_images',
            ];
        }
        
        // Get gallery images
        foreach ($this->getMedia('venue_gallery') as $media) {
            $urls[] = [
                'url' => $media->getUrl(),
                'thumbnail' => $media->getUrl('thumb'),
                'preview' => $media->getUrl('preview'),
                'collection' => 'venue_gallery',
            ];
        }
        
        // If no images found, provide placeholder
        if (empty($urls)) {
            $urls[] = [
                'url' => $this->getPlaceholderImage(),
                'thumbnail' => $this->getPlaceholderImage(),
                'preview' => $this->getPlaceholderImage(),
                'collection' => 'placeholder',
            ];
        }
        
        return $urls;
    }

    /**
     * Get placeholder image URL for venues
     */
    private function getPlaceholderImage(): string
    {
        return asset('images/venue-placeholder.jpg');
    }

    /**
     * Calculate distance between two coordinates (in kilometers)
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) {
            return 0;
        }

        $earthRadius = 6371; // Earth's radius in kilometers

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat/2) * sin($dlat/2) + cos($lat1) * cos($lat2) * sin($dlon/2) * sin($dlon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return round($earthRadius * $c, 2);
    }
}