<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    /**
     * Display a listing of events.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Event::with(['venue.location'])
                    ->active()
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
                    ->when($request->filled('event_type'), function ($query) use ($request) {
                        $query->where('event_type', $request->input('event_type'));
                    })
                    ->when($request->filled('featured_only') && $request->boolean('featured_only'), function ($query) {
                        $query->where('featured', true);
                    })
                    ->when($request->filled('sold_out') && $request->boolean('sold_out'), function ($query) {
                        $query->where('sold_out', true);
                    })
                    ->when($request->filled('search'), function ($query) use ($request) {
                        $search = $request->input('search');
                        $query->where('title', 'like', "%{$search}%")
                              ->orWhere('description', 'like', "%{$search}%");
                    });

        // Default to upcoming events
        if (!$request->filled('show_all') || !$request->boolean('show_all')) {
            $query->where('end_date', '>=', now());
        }

        $perPage = $request->input('per_page', 20);
        $events = $query->orderBy('start_date', 'asc')->paginate($perPage);

        return response()->json([
            'data' => EventResource::collection($events),
            'meta' => [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
                'from' => $events->firstItem(),
                'to' => $events->lastItem(),
            ],
            'links' => [
                'first' => $events->url(1),
                'last' => $events->url($events->lastPage()),
                'prev' => $events->previousPageUrl(),
                'next' => $events->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event): JsonResponse
    {
        $event->load(['venue.location']);

        return response()->json([
            'data' => new EventResource($event)
        ]);
    }

    /**
     * Get upcoming events.
     */
    public function upcoming(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 10);
        
        $events = Event::with(['venue.location'])
                    ->active()
                    ->where('start_date', '>', now())
                    ->where('featured', true)
                    ->orderBy('start_date', 'asc')
                    ->limit($limit)
                    ->get();

        return response()->json([
            'data' => EventResource::collection($events)
        ]);
    }

    /**
     * Get events happening today.
     */
    public function today(Request $request): JsonResponse
    {
        $today = now()->startOfDay();
        $tomorrow = now()->addDay()->startOfDay();
        
        $events = Event::with(['venue.location'])
                    ->active()
                    ->whereBetween('start_date', [$today, $tomorrow])
                    ->orderBy('start_date', 'asc')
                    ->get();

        return response()->json([
            'data' => EventResource::collection($events),
            'meta' => [
                'date' => $today->format('Y-m-d'),
                'found_count' => $events->count(),
            ]
        ]);
    }

    /**
     * Store a newly created event.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_type' => 'required|string',
            'start_date' => 'required|date|after_or_equal:now',
            'end_date' => 'required|date|after:start_date',
            'ticket_price' => 'sometimes|numeric|min:0',
            'venue_id' => 'required|exists:venues,id',
        ]);

        $event = Event::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
            'is_active' => true,
            'sold_out' => false,
        ]);

        return response()->json([
            'data' => new EventResource($event)
        ], 201);
    }

    /**
     * Update the specified event.
     */
    public function update(Request $request, Event $event): JsonResponse
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'event_type' => 'sometimes|string',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'ticket_price' => 'sometimes|numeric|min:0',
            'is_active' => 'sometimes|boolean',
            'sold_out' => 'sometimes|boolean',
        ]);

        $event->update($request->validated());

        return response()->json([
            'data' => new EventResource($event)
        ]);
    }

    /**
     * Remove the specified event.
     */
    public function destroy(Event $event): JsonResponse
    {
        $event->delete();

        return response()->json([
            'message' => 'Event deleted successfully'
        ]);
    }
}