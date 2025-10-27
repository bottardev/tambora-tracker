<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
