<?php

namespace App\Models;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;


class Trip extends Model {
use HasUuid;
protected $fillable = ['hiker_id','route_id','start_time','end_time','status'];
protected $casts = ['start_time'=>'datetime','end_time'=>'datetime'];
public function hiker(){ return $this->belongsTo(Hiker::class); }
public function route(){ return $this->belongsTo(Route::class); }
public function locations(){ return $this->hasMany(Location::class); }
public function events(){ return $this->hasMany(Event::class); }
}