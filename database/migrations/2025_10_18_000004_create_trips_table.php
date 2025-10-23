<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
public function up(): void {
Schema::create('trips', function (Blueprint $table) {
$table->uuid('id')->primary();
$table->uuid('hiker_id');
$table->uuid('route_id');
$table->dateTime('start_time');
$table->dateTime('end_time')->nullable();
$table->enum('status', ['draft','ongoing','paused','finished','canceled'])->default('draft');
$table->timestamps();


$table->foreign('hiker_id')->references('id')->on('hikers')->cascadeOnDelete();
$table->foreign('route_id')->references('id')->on('routes')->cascadeOnDelete();
});
}
public function down(): void { Schema::dropIfExists('trips'); }
};