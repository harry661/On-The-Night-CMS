<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DrinkTypeResource;
use App\Models\DrinkType;
use Illuminate\Http\JsonResponse;

class DrinkTypeController extends Controller
{
    /**
     * Display a listing of drink types.
     */
    public function index(): JsonResponse
    {
        $drinkTypes = DrinkType::withCount('venues')
                             ->orderBy('category')
                             ->orderBy('name')
                             ->get();

        return response()->json([
            'data' => DrinkTypeResource::collection($drinkTypes)
        ]);
    }

    /**
     * Display popular drink types.
     */
    public function popular(): JsonResponse
    {
        $drinkTypes = DrinkType::popular()
                             ->withCount('venues')
                             ->orderBy('name')
                             ->get();

        return response()->json([
            'data' => DrinkTypeResource::collection($drinkTypes)
        ]);
    }

    /**
     * Store a newly created drink type.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_popular' => 'boolean',
        ]);

        $drinkType = DrinkType::create($request->validated());

        return response()->json([
            'data' => new DrinkTypeResource($drinkType)
        ], 201);
    }

    /**
     * Display the specified drink type.
     */
    public function show(DrinkType $drinkType): JsonResponse
    {
        $drinkType->load(['venues' => function ($query) {
            $query->active()->with(['location', 'drinkTypes', 'musicGenres'])->limit(20);
        }]);

        return response()->json([
            'data' => new DrinkTypeResource($drinkType)
        ]);
    }

    /**
     * Update the specified drink type.
     */
    public function update(Request $request, DrinkType $drinkType): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'category' => 'sometimes|string|max:100',
            'description' => 'sometimes|string',
            'is_popular' => 'sometimes|boolean',
        ]);

        $drinkType->update(collect($request->validated())->filter()->toArray());

        return response()->json([
            'data' => new DrinkTypeResource($drinkType)
        ]);
    }

    /**
     * Remove the specified drink type.
     */
    public function destroy(DrinkType $drinkType): JsonResponse
    {
        $drinkType->delete();

        return response()->json([
            'message' => 'Drink type deleted successfully'
        ]);
    }
}