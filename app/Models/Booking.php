<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\RouteDailyQuota;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasUuid;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'trip_date',
        'route_id',
        'hiker_id',
        'status',
        'payment_method',
        'payment_due_at',
        'paid_at',
        'amount',
        'currency',
        'contact_phone',
        'duration_days',
        'proof_of_payment_path',
        'participants_count',
        'notes',
        'created_by',
        'created_via',
    ];

    protected $casts = [
        'trip_date' => 'date',
        'payment_due_at' => 'datetime',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
        'participants_count' => 'integer',
    ];

    protected $appends = [
        'proof_of_payment_url',
    ];

    protected static function booted(): void
    {
        static::creating(function (Booking $booking) {
            if (blank($booking->code)) {
                $booking->code = static::generateUniqueCode();
            }
        });

        static::saved(function (Booking $booking) {
            $booking->refreshParticipantsCount();
            $booking->syncQuota();
        });

        static::deleted(function (Booking $booking) {
            $booking->syncQuota();
        });
    }

    public static function generateUniqueCode(): string
    {
        do {
            $code = 'BOOK-' . Str::upper(Str::random(5));
        } while (static::where('code', $code)->exists());

        return $code;
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function hiker(): BelongsTo
    {
        return $this->belongsTo(Hiker::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(BookingParticipant::class);
    }

    public function trip(): HasOne
    {
        return $this->hasOne(Trip::class);
    }

    public function scopeForRoute(Builder $query, string $routeId): Builder
    {
        return $query->where('route_id', $routeId);
    }

    public function scopeOnDate(Builder $query, string $date): Builder
    {
        return $query->whereDate('trip_date', $date);
    }

    public function refreshParticipantsCount(): void
    {
        $count = $this->participants()->count();

        if ($this->participants_count !== $count) {
            $this->newQuery()->whereKey($this->getKey())->update(['participants_count' => $count]);
            $this->participants_count = $count;
        }
    }

    public function syncQuota(): void
    {
        $routeId = $this->route_id;
        $date = $this->trip_date;

        if (! $routeId || ! $date) {
            return;
        }

        $quota = RouteDailyQuota::firstOrCreate(
            ['route_id' => $routeId, 'date' => $date],
            ['capacity' => config('booking.default_daily_capacity', 120), 'status' => 'open']
        );

        $booked = static::query()
            ->where('route_id', $routeId)
            ->whereDate('trip_date', $date)
            ->whereIn('status', ['pending-payment', 'awaiting-validation', 'confirmed'])
            ->sum('participants_count');

        if ($quota->booked !== $booked) {
            $quota->booked = $booked;
            $quota->save();
        }
    }

    public static function expireOverdue(): void
    {
        static::query()
            ->whereIn('status', ['pending-payment', 'awaiting-validation'])
            ->whereNotNull('payment_due_at')
            ->where('payment_due_at', '<', Carbon::now())
            ->update(['status' => 'expired']);
    }

    public function getProofOfPaymentUrlAttribute(): ?string
    {
        $path = $this->proof_of_payment_path;

        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, ['/storage/', 'storage/'])) {
            return url($path);
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        return $path;
    }
}
