<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    /**
     * Display reviews for a specific venue.
     */
    public function byVenue(Request $request, int $venueId): JsonResponse
    {
        $query = Review::with(['venue'])
                    ->where('venue_id', $venueId)
                    ->where('is_approved', true)
                    ->when($request->filled('rating'), function ($query) use ($request) {
                        $query->where('rating', $request->input('rating'));
                    })
                    ->when($request->filled('search'), function ($query) use ($request) {
                        $search = $request->input('search');
                        $query->where('review_text', 'like', "%{$search}%");
                    });

        $perPage = $request->input('per_page', 20);
        $reviews = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'data' => ReviewResource::collection($reviews),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ]
        ]);
    }

    /**
     * Display the specified review.
     */
    public function show(Review $review): JsonResponse
    {
        $review->load(['venue']);

        return response()->json([
            'data' => new ReviewResource($review)
        ]);
    }

    /**
     * Store a newly created review.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'venue_id' => 'required|exists:venues,id',
            'review_text' => 'required|string|max:2000',
            'rating' => 'required|integer|min:1|max:5',
            'reviewer_name' => 'sometimes|string|max:255',
            'reviewer_email' => 'sometimes|email|max:255',
        ]);

        // Check if user has already reviewed this venue
        $existingReview = Review::where('venue_id', $request->venue_id)
                               ->where('user_id', auth()->id())
                               ->first();

        if ($existingReview) {
            return response()->json([
                'message' => 'You have already reviewed this venue'
            ], 422);
        }

        $review = Review::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'data' => new ReviewResource($review)
        ], 201);
    }

    /**
     * Update the specified review.
     */
    public function update(Request $request, Review $review): JsonResponse
    {
        // Only allow review owner or admin to update
        if ($review->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'review_text' => 'sometimes|string|max:2000',
            'rating' => 'sometimes|integer|min:1|max:5',
            'reviewer_name' => 'sometimes|string|max:255',
            'reviewer_email' => 'sometimes|email|max:255',
            'is_approved' => 'sometimes|boolean', // Admin only
        ]);

        $review->update(collect($request->validated())->except('is_approved')->filter()->toArray());

        // Only admins can approve reviews
        if ($request->has('is_approved') && auth()->user()->isAdmin()) {
            $review->update(['is_approved' => $request->boolean('is_approved')]);
        }

        return response()->json([
            'data' => new ReviewResource($review)
        ]);
    }

    /**
     * Remove the specified review.
     */
    public function destroy(Review $review): JsonResponse
    {
        // Only allow review owner or admin to delete
        if ($review->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $review->delete();

        return response()->json([
            'message' => 'Review deleted successfully'
        ]);
    }
}