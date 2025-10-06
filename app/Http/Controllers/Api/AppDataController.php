<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use App\Models\Deal;
use App\Models\Event;
use App\Models\Location;
use App\Models\DrinkType;
use App\Models\MusicGenre;
use Illuminate\Http\Request;

class AppDataController extends Controller
{
    /**
     * Get all app data in a single API call
     * Perfect for Figma Make integration
     */
    public function allData(Request $request)
    {
        try {
            // Get all active venues with relationships
            $venues = Venue::with([
                'venueType',
                'drinkTypes',
                'musicGenres', 
                'location',
                'reviews' => function($query) {
                    $query->where('is_approved', true);
                }
            ])
            ->where('is_active', true)
            ->get()
            ->map(function($venue) {
                return [
                    'id' => $venue->id,
                    'name' => $venue->name,
                    'description' => $venue->description,
                    'address' => $venue->full_address,
                    'phone' => $venue->phone,
                    'email' => $venue->email,
                    'website' => $venue->website,
                    'opening_hours' => $venue->opening_hours,
                    'amenities' => $venue->amenities,
                    'status' => $venue->status,
                    'location' => $venue->location ? [
                        'name' => $venue->location->name,
                        'city' => $venue->location->city,
                    ] : null,
                    'drink_types' => $venue->drinkTypes->map(function($drink) {
                        return [
                            'name' => $drink->name,
                            'category' => $drink->category,
                        ];
                    }),
                    'music_genres' => $venue->musicGenres->map(function($music) {
                        return [
                            'name' => $music->name,
                            'category' => $music->category,
                        ];
                    }),
                    'images' => collect($venue->getMedia('venue_images'))->map(function($media) {
                        return [
                            'url' => $media->getUrl(),
                            'thumbnail' => $media->getUrl('thumb'),
                            'preview' => $media->getUrl('preview'),
                        ];
                    }),
                    'reviews' => [
                        'count' => $venue->reviews->count(),
                        'average_rating' => $venue->reviews->avg('rating') ?? 0,
                    ],
                ];
            });

            // Get current active deals
            $currentDeals = Deal::with('venue')
                ->where('is_active', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->get()
                ->map(function($deal) {
                    return [
                        'id' => $deal->id,
                        'title' => $deal->title,
                        'deal_type' => $deal->deal_type,
                        'description' => $deal->description,
                        'venue_name' => $deal->venue->name ?? 'Unknown',
                        'start_date' => $deal->start_date,
                        'end_date' => $deal->end_date,
                        'images' => collect($deal->getMedia('images'))->map(function($media) {
                            return [
                                'url' => $media->getUrl(),
                                'thumbnail' => $media->getUrl('thumb'),
                            ];
                        }),
                    ];
                });

            // Get upcoming events
            $upcomingEvents = Event::with('venue')
                ->where('is_active', true)
                ->where('start_date', '>', now())
                ->orderBy('start_date', 'asc')
                ->get()
                ->map(function($event) {
                    return [
                        'id' => $event->id,
                        'title' => $event->title,
                        'event_type' => $event->event_type,
                        'description' => $event->description,
                        'venue_name' => $event->venue->name ?? 'Unknown',
                        'start_date' => $event->start_date,
                        'end_date' => $event->end_date,
                        'ticket_price' => $event->ticket_price,
                        'sold_out' => $event->sold_out,
                        'images' => collect($event->getMedia('images'))->map(function($media) {
                            return [
                                'url' => $media->getUrl(),
                                'thumbnail' => $media->getUrl('thumb'),
                            ];
                        }),
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'All app data retrieved successfully',
                'data' => [
                    'venues' => $venues,
                    'current_deals' => $currentDeals,
                    'upcoming_events' => $upcomingEvents,
                ],
                'meta' => [
                    'generated_at' => now()->toISOString(),
                    'venue_count' => $venues->count(),
                    'current_deals_count' => $currentDeals->count(),
                    'upcoming_events_count' => $upcomingEvents->count(),
                    'version' => '1.0',
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving app data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}