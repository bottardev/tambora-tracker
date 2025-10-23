<?php

namespace App\Models;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;


class Route extends Model {
use HasUuid;
protected $fillable = ['name','description','path','total_distance_km'];
protected $casts = [ 'path' => 'string' /* gunakan raw/spatial di repo */ ];
public function checkpoints(){ return $this->hasMany(Checkpoint::class); }
public function trips(){ return $this->hasMany(Trip::class); }
}