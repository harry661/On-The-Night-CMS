<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DealResource;
use App\Models\Deal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DealController extends Controller
{
    /**
     * Display a listing of current deals.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Deal::with(['venue.location'])
                    ->active()
                    ->where('end_date', '>=', now())
                    ->when($request->filled('venue_id'), function ($query) use ($request) {
                        $query->where('venue_id', $request->input('venue_id'));
                    })
                    ->when($request->filled('venue_name'), function ($query) use ($request) {
                        $query->whereHas('venue', function ($q) use ($request) {
                            $q->where('name', 'like', "%{$request->input('venue_name')}%");
                        });
                    })
                    ->when($request->filled('location_id'), function ($query) use ($request) {
                        $query->whereHas('venue', function ($q) use ($request) {
                            $q->where('location_id', $request->input('location_id'));
                        });
                    })
                    ->when($request->filled('deal_type'), function ($query) use ($request) {
                        $query->where('deal_type', 'like', "%{$request->input('deal_type')}%");
                    })
                    ->when($request->filled('featured_only') && $request->boolean('featured_only'), function ($query) {
                        $query->where('featured', true);
                    })
                    ->when($request->filled('search'), function ($query) use ($request) {
                        $search = $request->input('search');
                        $query->where('title', 'like', "%{$search}%")
                              ->orWhere('description', 'like', "%{$search}%");
                    });

        $perPage = $request->input('per_page', 20);
        $deals = $query->orderBy('end_date', 'asc')->paginate($perPage);

        return response()->json([
            'content' => DealResource::collection($deals),
            'meta' => [
                'current_page' => $deals->currentPage(),
                'last_page' => $deals->lastPage(),
                'per_page' => $deals->perPage(),
                'total' => $deals->total(),
                'from' => $deals->firstItem(),
                'to' => $deals->lastItem(),
            ],
            'links' => [
                'first' => $deals->url(1),
                'last' => $deals->url($deals->lastPage()),
                'prev' => $deals->previousPageUrl(),
                'next' => $deals->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Display the specified deal.
     */
    public function show(Deal $deal): JsonResponse
    {
        $deal->load(['venue.location']);

        return response()->json([
            'data' => new DealResource($deal)
        ]);
    }

    /**
     * Get current active deals.
     */
    public function current(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 10);
        
        $deals = Deal::with(['venue.location'])
                    ->active()
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->where('featured', true)
                    ->orderBy('end_date', 'asc')
                    ->limit($limit)
                    ->get();


        return response()->json([
            'data' => DealResource::collection($deals)
        ]);
    }

    /**
     * Get deals expiring within specified hours.
     */
    public function expiring(Request $request, int $hours): JsonResponse
    {
        $deals = Deal::with(['venue.location'])
                    ->active()
                    ->where('end_date', '>=', now())
                    ->where('end_date', '<=', now()->addHours($hours))
                    ->orderBy('end_date', 'asc')
                    ->get();

        return response()->json([
            'data' => DealResource::collection($deals),
            'meta' => [
                'expiring_within_hours' => $hours,
                'found_count' => $deals->count(),
            ]
        ]);
    }

    /**
     * Store a newly created deal.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deal_type' => 'required|string|max:100',
            'start_date' => 'required|date|after_or_equal:now',
            'end_date' => 'required|date|after:start_date',
            'venue_id' => 'required|exists:venues,id',
        ]);

        $deal = Deal::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
            'is_active' => true,
        ]);

        return response()->json([
            'data' => new DealResource($deal)
        ], 201);
    }

    /**
     * Update the specified deal.
     */
    public function update(Request $request, Deal $deal): JsonResponse
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'deal_type' => 'sometimes|string|max:100',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'is_active' => 'sometimes|boolean',
        ]);

        $deal->update($request->validated());

        return response()->json([
            'data' => new DealResource($deal)
        ]);
    }

    /**
     * Remove the specified deal.
     */
    public function destroy(Deal $deal): JsonResponse
    {
        $deal->delete();

        return response()->json([
            'message' => 'Deal deleted successfully'
        ]);
    }
}