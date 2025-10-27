<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['trip_id', 'recorded_at', 'location', 'accuracy_m', 'battery_pct', 'snapped', 'on_trail'];

    protected $casts = [
        'recorded_at' => 'datetime',
        'snapped' => 'boolean',
        'on_trail' => 'boolean',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    protected function lat(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => isset($attributes['lat']) ? (float) $attributes['lat'] : null,
        );
    }

    protected function lng(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => isset($attributes['lng']) ? (float) $attributes['lng'] : null,
        );
    }
}
