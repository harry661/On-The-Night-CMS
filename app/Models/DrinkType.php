<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DrinkType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'description',
        'is_popular',
    ];

    protected $casts = [
        'is_popular' => 'boolean',
    ];

    /**
     * Get the venues that offer this drink type
     */
    public function venues(): BelongsToMany
    {
        return $this->belongsToMany(Venue::class, 'venue_drink_types');
    }

    /**
     * Scope for popular drink types
     */
    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }
}
