<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Holiday extends Model
{
    protected $fillable = [
        'date',
        'route_id',
        'reason',
        'is_closed',
    ];

    protected $casts = [
        'date' => 'date',
        'is_closed' => 'boolean',
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function scopeForDate(Builder $query, string $date): Builder
    {
        return $query->whereDate('date', $date);
    }

    public function scopeForRoute(Builder $query, ?string $routeId): Builder
    {
        return $routeId
            ? $query->where(function (Builder $q) use ($routeId) {
                $q->whereNull('route_id')->orWhere('route_id', $routeId);
            })
            : $query->whereNull('route_id');
    }
}
