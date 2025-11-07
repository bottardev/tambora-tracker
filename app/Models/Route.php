<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Route extends Model
{
    use HasUuid;

    protected $fillable = ['name', 'description', 'image', 'path', 'total_distance_km'];

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $attributes['image'] 
                ? asset('storage/' . $attributes['image']) 
                : 'https://images.unsplash.com/photo-1464822759844-d150baec93c5?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&h=300&q=80'
        );
    }

    protected function path(): Attribute
    {
        return Attribute::make(
            get: function ($value, array $attributes): ?string {
                if (!$value || !isset($attributes['id'])) {
                    return null;
                }

                return DB::table($this->getTable())
                    ->where('id', $attributes['id'])
                    ->selectRaw('ST_AsText(path) as wkt')
                    ->value('wkt');
            },
            set: function ($value) {
                if (!$value) {
                    return null;
                }

                $escaped = str_replace("'", "''", $value);

                return DB::raw("ST_GeomFromText('{$escaped}', 4326)");
            }
        );
    }

    public function checkpoints()
    {
        return $this->hasMany(Checkpoint::class);
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }
}
