<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VenueResource;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VenueController extends Controller
{
    /**
     * Display a listing of venues with filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Venue::with(['location', 'drinkTypes', 'musicGenres', 'reviews'])
                    ->active()
                    ->when($request->filled('search'), function ($query) use ($request) {
                        $search = $request->input('search');
                        $query->where('name', 'like', "%{$search}%")
                              ->orWhere('description', 'like', "%{$search}%")
                              ->orWhere('address', 'like', "%{$search}%");
                    })
                    ->when($request->filled('location_id'), function ($query) use ($request) {
                        $query->where('location_id', $request->input('location_id'));
                    })
                    ->when($request->filled('location_name'), function ($query) use ($request) {
                        $query->whereHas('location', function ($q) use ($request) {
                            $q->where('name', 'like', "%{$request->input('location_name')}%");
                        });
                    })
                    ->when($request->filled('city'), function ($query) use ($request) {
                        $query->where('city', 'like', "%{$request->input('city')}%");
                    })
                    ->when($request->filled('status'), function ($query) use ($request) {
                        $query->where('status', $request->input('status'));
                    })
                    ->when($request->filled('music_genres'), function ($query) use ($request) {
                        $musicGenres = is_array($request->input('music_genres')) 
                            ? $request->input('music_genres') 
                            : explode(',', $request->input('music_genres'));
                        $query->whereHas('musicGenres', function ($q) use ($musicGenres) {
                            $q->whereIn('name', $musicGenres);
                        });
                    })
                    ->when($request->filled('drink_types'), function ($query) use ($request) {
                        $drinkTypes = is_array($request->input('drink_types')) 
                            ? $request->input('drink_types') 
                            : explode(',', $request->input('drink_types'));
                        $query->whereHas('drinkTypes', function ($q) use ($drinkTypes) {
                            $q->whereIn('name', $drinkTypes);
                        });
                    })
                    ->when($request->filled('featured_only') && $request->boolean('featured_only'), function ($query) {
                        $query->where('status', 'featured');
                    })
                    ->when($request->filled('new_only') && $request->boolean('new_only'), function ($query) {
                        $query->where('status', 'new');
                    })
                    ->when($request->filled('sponsored_only') && $request->boolean('sponsored_only'), function ($query) {
                        $query->where('status', 'sponsored');
                    });

        // Nearby venues using geolocation
        if ($request->filled('user_lat') && $request->filled('user_lon')) {
            $radius = $request->input('radius', 10); // Default 10km radius
            $lat = $request->input('user_lat');
            $lon = $request->input('user_lon');
            
            $query->havingRaw('(
                6371 * acos(
                    cos(radians(?))
                    * cos(radians(latitude))
                    * cos(radians(?) - radians(longitude))
                    + sin(radians(?)) * sin(radians(latitude))
                )
            ) <= ?', [
                $lat, $lat, $lon, $lat, $lat, $radius
            ]);
            
            $query->orderByRaw('(
                6371 * acos(
                    cos(radians(?))
                    * cos(radians(latitude))
                    * cos(radians(?) - radians(longitude))
                    + sin(radians(?)) * sin(radians(latitude))
                )
            )', [$lat, $lat, $lon, $lat, $lat]);
        } else {
            $query->orderBy('status', 'desc') // Featured first
                   ->orderBy('created_at', 'desc');
        }

        $perPage = $request->input('per_page', 20);
        $venues = $query->paginate($perPage);

        return response()->json([
            'data' => VenueResource::collection($venues),
            'meta' => [
                'current_page' => $venues->currentPage(),
                'last_page' => $venues->lastPage(),
                'per_page' => $venues->perPage(),
                'total' => $venues->total(),
                'from' => $venues->firstItem(),
                'to' => $venues->lastItem(),
            ],
            'links' => [
                'first' => $venues->url(1),
                'last' => $venues->url($venues->lastPage()),
                'prev' => $venues->previousPageUrl(),
                'next' => $venues->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Display the specified venue.
     */
    public function show(Request $request, Venue $venue): JsonResponse
    {
        $venue->load(['location', 'drinkTypes', 'musicGenres', 'reviews' => function ($query) {
            $query->where('is_approved', true)->latest()->limit(10);
        }, 'deals' => function ($query) {
            $query->active()->where('end_date', '>=', now())->latest();
        }, 'events' => function ($query) {
            $query->active()->where('end_date', '>=', now())->latest()->limit(5);
        }]);

        return response()->json([
            'data' => new VenueResource($venue)
        ]);
    }

    /**
     * Get featured venues.
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 10);
        
        $venues = Venue::where('status', 'featured')
                     ->where('is_active', true)
                     ->with(['location', 'drinkTypes', 'musicGenres'])
                     ->orderBy('created_at', 'desc')
                     ->limit($limit)
                     ->get();

        return response()->json([
            'data' => VenueResource::collection($venues)
        ]);
    }

    /**
     * Get nearby venues based on location.
     */
    public function nearby(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'sometimes|numeric|min:0.1|max:100',
        ]);

        $radius = $request->input('radius', 10); // 10km default
        $lat = $request->input('latitude');
        $lon = $request->input('longitude');

        $venues = Venue::with(['location', 'drinkTypes', 'musicGenres'])
                     ->active()
                     ->havingRaw('(
                         6371 * acos(
                            cos(radians(?))
                            * cos(radians(latitude))
                            * cos(radians(?) - radians(longitude))
                            + sin(radians(?)) * sin(radians(latitude))
                         )
                     ) <= ?', [$lat, $lon, $lat, $radius])
                     ->orderByRaw('(
                         6371 * acos(
                            cos(radians(?))
                            * cos(radians(latitude))
                            * cos(radians(?) - radians(longitude))
                            + sin(radians(?)) * sin(radians(latitude))
                         )
                     )', [$lat, $lon, $lat])
                     ->limit($request->input('limit', 20))
                     ->get();

        return response()->json([
            'data' => VenueResource::collection($venues),
            'meta' => [
                'center_latitude' => $lat,
                'center_longitude' => $lon,
                'radius_km' => $radius,
                'found_count' => $venues->count(),
            ]
        ]);
    }

    /**
     * Get venues by music genre.
     */
    public function byMusicGenre(Request $request, string $genre): JsonResponse
    {
        $perPage = $request->input('per_page', 20);
        
        $venues = Venue::with(['location', 'musicGenres', 'drinkTypes'])
                     ->active()
                     ->whereHas('musicGenres', function ($query) use ($genre) {
                         $query->where('name', 'like', "%{$genre}%");
                     })
                     ->orderBy('status', 'desc')
                     ->orderBy('created_at', 'desc')
                     ->paginate($perPage);

        return response()->json([
            'data' => VenueResource::collection($venues),
            'meta' => [
                'current_page' => $venues->currentPage(),
                'last_page' => $venues->lastPage(),
                'per_page' => $venues->perPage(),
                'total' => $venues->total(),
            ]
        ]);
    }

    /**
     * Get venues by drink type.
     */
    public function byDrinkType(Request $request, string $drinkType): JsonResponse
    {
        $perPage = $request->input('per_page', 20);
        
        $venues = Venue::with(['location', 'drinkTypes', 'musicGenres'])
                     ->active()
                     ->whereHas('drinkTypes', function ($query) use ($drinkType) {
                         $query->where('name', 'like', "%{$drinkType}%");
                     })
                     ->orderBy('status', 'desc')
                     ->orderBy('created_at', 'desc')
                     ->paginate($perPage);

        return response()->json([
            'data' => VenueResource::collection($venues),
            'meta' => [
                'current_page' => $venues->currentPage(),
                'last_page' => $venues->lastPage(),
                'per_page' => $venues->perPage(),
                'total' => $venues->total(),
            ]
        ]);
    }
}