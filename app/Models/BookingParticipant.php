<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookingParticipant extends Model
{
    protected $fillable = [
        'booking_id',
        'name',
        'gender',
        'nationality',
        'origin_country',
        'age_group',
        'occupation',
        'id_type',
        'id_number',
        'health_certificate_path',
        'is_leader',
        'meta',
    ];

    protected $casts = [
        'is_leader' => 'boolean',
        'meta' => 'array',
    ];

    protected $appends = [
        'health_certificate_url',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function getHealthCertificateUrlAttribute(): ?string
    {
        $path = $this->health_certificate_path;

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
