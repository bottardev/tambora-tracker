<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Location extends Model {
protected $fillable = ['trip_id','ts','point','accuracy_m','battery_pct','snapped','on_trail'];
protected $casts = ['ts'=>'datetime','snapped'=>'boolean','on_trail'=>'boolean'];
public function trip(){ return $this->belongsTo(Trip::class); }
}