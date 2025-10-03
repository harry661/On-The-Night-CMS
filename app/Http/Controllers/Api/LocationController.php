<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    /**
     * Display a listing of locations.
     */
    public function index(): JsonResponse
    {
        $locations = Location::active()
                           ->withCount('venues')
                           ->orderBy('name')
                           ->get();

        return response()->json([
            'data' => LocationResource::collection($locations)
        ]);
    }

    /**
     * Store a newly created location.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:50',
            'country' => 'required|string|max:50',
        ]);

        $location = Location::create($request->validated());

        return response()->json([
            'data' => new LocationResource($location)
        ], 201);
    }

    /**
     * Display the specified location.
     */
    public function show(Location $location): JsonResponse
    {
        $location->load(['venues' => function ($query) {
            $query->active()->with(['location', 'drinkTypes', 'musicGenres'])->limit(20);
        }]);

        return response()->json([
            'data' => new LocationResource($location)
        ]);
    }

    /**
     * Update the specified location.
     */
    public function update(Request $request, Location $location): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:100',
            'state' => 'sometimes|string|max:50',
            'country' => 'sometimes|string|max:50',
            'is_active' => 'sometimes|boolean',
        ]);

        $location->update(collect($request->validated())->filter()->toArray());

        return response()->json([
            'data' => new LocationResource($location)
        ]);
    }

    /**
     * Remove the specified location.
     */
    public function destroy(Location $location): JsonResponse
    {
        $location->delete();

        return response()->json([
            'message' => 'Location deleted successfully'
        ]);
    }
}