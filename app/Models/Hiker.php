<?php

namespace App\Models;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;


class Hiker extends Model {
use HasUuid;
protected $fillable = ['name','email','phone','emergency_contact'];
public function trips(){ return $this->hasMany(Trip::class); }
}