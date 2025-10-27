<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Trip extends Model
{
    use HasUuid;

    protected $fillable = ['code', 'hiker_id', 'route_id', 'start_time', 'end_time', 'status'];

    protected $casts = ['start_time' => 'datetime', 'end_time' => 'datetime'];

    public function hiker()
    {
        return $this->belongsTo(Hiker::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Trip $trip) {
            if (blank($trip->code)) {
                $trip->code = static::generateUniqueCode();
            }
        });
    }

    public static function generateUniqueCode(): string
    {
        do {
            $code = 'TRIP-' . Str::upper(Str::random(5));
        } while (static::where('code', $code)->exists());

        return $code;
    }

    public function lastLocation(): HasOne
    {
        return $this->hasOne(Location::class)
            ->select([
                'locations.id',
                'locations.trip_id',
                'locations.recorded_at',
                'locations.accuracy_m',
            ])
            ->selectRaw('ST_X(locations.location) as lng, ST_Y(locations.location) as lat')
            ->latestOfMany('recorded_at');
    }
}
