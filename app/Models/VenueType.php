<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VenueType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
        'color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the venues for this venue type.
     */
    public function venues(): HasMany
    {
        return $this->hasMany(Venue::class);
    }

    /**
     * Scope to get only active venue types.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
