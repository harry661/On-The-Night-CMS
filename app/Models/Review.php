<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Review extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'review_text',
        'rating',
        'reviewer_name',
        'reviewer_email',
        'is_approved',
        'venue_id',
        'user_id',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    /**
     * Get the venue this review belongs to
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * Get the user who wrote this review
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for approved reviews
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }


    /**
     * Get the average rating for a venue
     */
    public static function averageRatingForVenue($venueId)
    {
        return static::where('venue_id', $venueId)
                    ->where('is_approved', true)
                    ->avg('rating');
    }

    /**
     * Get review count for a venue
     */
    public static function countForVenue($venueId)
    {
        return static::where('venue_id', $venueId)
                    ->where('is_approved', true)
                    ->count();
    }

    /**
     * Get approved reviews for a venue
     */
    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    /**
     * Register media collections for review images
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('review_images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->singleFile();

        $this->addMediaCollection('review_gallery')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    /**
     * Register media conversions for optimized images
    */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(200)
            ->sharpen(10);

        $this->addMediaConversion('preview')
            ->width(800)
            ->height(600)
            ->sharpen(10);
    }

    /**
     * Scope for featured reviews
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }
}
