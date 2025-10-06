<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VenueController;
use App\Http\Controllers\Api\DealController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\DrinkTypeController;
use App\Http\Controllers\Api\MusicGenreController;
use App\Http\Controllers\Api\AppDataController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication routes
Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

// Public routes (no authentication required)
Route::prefix('v1')->group(function () {
    
    // All app data in one call (perfect for Figma Make)
    Route::get('/data', [AppDataController::class, 'allData']);
    
    // Venues
    Route::get('/venues', [VenueController::class, 'index']);
    Route::get('/venues/featured', [VenueController::class, 'featured']);
    Route::get('/venues/sponsored', [VenueController::class, 'sponsored']);
    Route::get('/venues/new', [VenueController::class, 'new']);
    Route::get('/venues/nearby', [VenueController::class, 'nearby']);
    Route::get('/venues/by-music-genre/{genre}', [VenueController::class, 'byMusicGenre']);
    Route::get('/venues/by-drink-type/{drinkType}', [VenueController::class, 'byDrinkType']);
    Route::get('/venues/{venue}', [VenueController::class, 'show']);
    
    // Deals
    Route::get('/deals', [DealController::class, 'index']);
    Route::get('/deals/current', [DealController::class, 'current']);
    Route::get('/deals/expiring/{hours}', [DealController::class, 'expiring']);
    Route::get('/deals/{deal}', [DealController::class, 'show']);
    
    // Events
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/upcoming', [EventController::class, 'upcoming']);
    Route::get('/events/today', [EventController::class, 'today']);
    Route::get('/events/{event}', [EventController::class, 'show']);
    
    // Reviews
    Route::get('/reviews/venue/{venue}', [ReviewController::class, 'byVenue']);
    Route::get('/reviews/{review}', [ReviewController::class, 'show']);
    
    // Supporting data (locations, drink types, music genres)
    Route::get('/locations', [LocationController::class, 'index']);
    Route::get('/locations/{location}', [LocationController::class, 'show']);
    
    Route::get('/drink-types', [DrinkTypeController::class, 'index']);
    Route::get('/drink-types/popular', [DrinkTypeController::class, 'popular']);
    Route::get('/drink-types/{drinkType}', [DrinkTypeController::class, 'show']);
    
    Route::get('/music-genres', [MusicGenreController::class, 'index']);
    Route::get('/music-genres/popular', [MusicGenreController::class, 'popular']);
    Route::get('/music-genres/{musicGenre}', [MusicGenreController::class, 'show']);
});

// Authenticated routes (mobile app users)
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    
    // User profile
    Route::get('/user', [AuthController::class, 'user']);
    
    // Reviews (authenticated users can create reviews)
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{review}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);
    
    // User favorites (if implemented)
    Route::get('/user/favorites/venues', function (Request $request) {
        // TODO: Implement user favorites
        return response()->json(['message' => 'Feature coming soon']);
    });
});

// Admin/Venue Moderator routes (strict authentication)
Route::middleware(['auth:sanctum', 'verified'])->prefix('v1/admin')->group(function () {
    
    // CRUD endpoints for authenticated venue moderators and admins
    Route::apiResource('venues', VenueController::class)->except(['index', 'show']);
    Route::apiResource('deals', DealController::class)->except(['index', 'show']);
    Route::apiResource('events', EventController::class)->except(['index', 'show']);
    Route::apiResource('reviews', ReviewController::class);
    
    // Admin-only routes
    Route::apiResource('locations', LocationController::class);
    Route::apiResource('drink-types', DrinkTypeController::class);
    Route::apiResource('music-genres', MusicGenreController::class);
});