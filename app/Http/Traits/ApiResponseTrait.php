<?php

namespace App\Http\Traits;

trait ApiResponseTrait
{
    /**
     * Return a standardized success response
     */
    protected function successResponse($data = null, $message = 'Success', $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toISOString(),
        ], $code);
    }

    /**
     * Return a standardized error response
     */
    protected function errorResponse($message = 'Error', $data = null, $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toISOString(),
        ], $code);
    }

    /**
     * Return paginated response with consistent structure
     */
    protected function paginatedResponse($data, $message = 'Data retrieved successfully')
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data->items(),
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'from' => $data->firstItem() ?? 0,
                'to' => $data->lastItem() ?? 0,
            ],
            'links' => [
                'first' => $data->url(1),
                'last' => $data->url($data->lastPage()),
                'prev' => $data->previousPageUrl(),
                'next' => $data->nextPageUrl(),
            ],
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Return collection response with consistent structure
     */
    protected function collectionResponse($data, $message = 'Data retrieved successfully')
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => [
                'count' => $data->count(),
            ],
            'timestamp' => now()->toISOString(),
        ]);
    }
}
