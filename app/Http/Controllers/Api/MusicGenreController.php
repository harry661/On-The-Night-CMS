<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MusicGenreResource;
use App\Models\MusicGenre;
use Illuminate\Http\JsonResponse;

class MusicGenreController extends Controller
{
    /**
     * Display a listing of music genres.
     */
    public function index(): JsonResponse
    {
        $musicGenres = MusicGenre::withCount('venues')
                                ->orderBy('category')
                                ->orderBy('name')
                                ->get();

        return response()->json([
            'data' => MusicGenreResource::collection($musicGenres)
        ]);
    }

    /**
     * Display popular music genres.
     */
    public function popular(): JsonResponse
    {
        $musicGenres = MusicGenre::popular()
                                ->withCount('venues')
                                ->orderBy('name')
                                ->get();

        return response()->json([
            'data' => MusicGenreResource::collection($musicGenres)
        ]);
    }

    /**
     * Store a newly created music genre.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_popular' => 'boolean',
        ]);

        $musicGenre = MusicGenre::create($request->validated());

        return response()->json([
            'data' => new MusicGenreResource($musicGenre)
        ], 201);
    }

    /**
     * Display the specified music genre.
     */
    public function show(MusicGenre $musicGenre): JsonResponse
    {
        $musicGenre->load(['venues' => function ($query) {
            $query->active()->with(['location', 'drinkTypes', 'musicGenres'])->limit(20);
        }]);

        return response()->json([
            'data' => new MusicGenreResource($musicGenre)
        ]);
    }

    /**
     * Update the specified music genre.
     */
    public function update(Request $request, MusicGenre $musicGenre): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'category' => 'sometimes|string|max:100',
            'description' => 'sometimes|string',
            'is_popular' => 'sometimes|boolean',
        ]);

        $musicGenre->update(collect($request->validated())->filter()->toArray());

        return response()->json([
            'data' => new MusicGenreResource($musicGenre)
        ]);
    }

    /**
     * Remove the specified music genre.
     */
    public function destroy(MusicGenre $musicGenre): JsonResponse
    {
        $musicGenre->delete();

        return response()->json([
            'message' => 'Music genre deleted successfully'
        ]);
    }
}