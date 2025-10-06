<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Venue extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;


        protected $fillable = [
            'name',
            'description',
            'address',
            'city',
            'state',
            'postal_code',
            'country',
            'phone',
            'email',
            'website',
            'latitude',
            'longitude',
            'opening_hours',
            'amenities',
            'is_active',
            'status',
            'user_id',
            'location_id',
            'venue_type_id',
        ];

    protected $casts = [
        'opening_hours' => 'array',
        'amenities' => 'array',
        'is_active' => 'boolean',
        'status' => 'string',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Get the user (venue moderator) who owns this venue
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all users associated with this venue
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all deals for this venue
     */
    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    /**
     * Get all events for this venue
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Register media collections for venue images
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('venue_images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->singleFile();

        $this->addMediaCollection('venue_gallery')
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
     * Get the full address as a single string
     */
    public function getFullAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]));
    }

    /**
     * Scope for active venues
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

        /**
         * Scope for featured venues
         */
        public function scopeFeatured($query)
        {
            return $query->where('featured', true);
        }

        /**
         * Get the drink types offered at this venue
         */
        public function drinkTypes()
        {
            return $this->belongsToMany(DrinkType::class, 'venue_drink_types');
        }

        /**
         * Get the music genres played at this venue
         */
        public function musicGenres()
        {
            return $this->belongsToMany(MusicGenre::class, 'venue_music_genres');
        }

    /**
     * Get all reviews for this venue
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get approved reviews for this venue
     */
    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    /**
     * Get the location this venue belongs to
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the venue type this venue belongs to
     */
    public function venueType(): BelongsTo
    {
        return $this->belongsTo(VenueType::class);
    }


}

