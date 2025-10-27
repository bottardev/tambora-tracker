<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RouteDailyQuota extends Model
{
    protected $fillable = [
        'route_id',
        'date',
        'capacity',
        'booked',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function scopeForDate(Builder $query, string $date): Builder
    {
        return $query->whereDate('date', $date);
    }

    public function scopeForRoute(Builder $query, string $routeId): Builder
    {
        return $query->where('route_id', $routeId);
    }
}
