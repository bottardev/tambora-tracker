<?php

namespace App\Models;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;


class Checkpoint extends Model {
use HasUuid;
protected $fillable = ['route_id','name','order_no','location','radius_m'];
protected $casts = [ 'location' => 'string' ];
public function route(){ return $this->belongsTo(Route::class); }
}