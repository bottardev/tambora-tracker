<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Checkpoint extends Model
{
    use HasUuid;

    protected $fillable = ['route_id', 'name', 'order_no', 'location', 'radius_m'];

    protected function location(): Attribute
    {
        return Attribute::make(
            get: function ($value, array $attributes): ?string {
                if (!$value || !isset($attributes['id'])) {
                    return null;
                }

                return DB::table($this->getTable())
                    ->where('id', $attributes['id'])
                    ->selectRaw('ST_AsText(location) as wkt')
                    ->value('wkt');
            },
            set: function ($value) {
                if (!$value) {
                    return null;
                }

                $result = DB::selectOne('SELECT ST_AsBinary(ST_GeomFromText(?, 4326)) AS geom', [$value]);

                return $result?->geom;
            }
        );
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
