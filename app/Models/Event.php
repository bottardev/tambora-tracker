<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Event extends Model {
protected $fillable = ['trip_id','type','checkpoint_id','ts','note'];
protected $casts = ['ts'=>'datetime'];
public function trip(){ return $this->belongsTo(Trip::class); }
public function checkpoint(){ return $this->belongsTo(Checkpoint::class); }
}