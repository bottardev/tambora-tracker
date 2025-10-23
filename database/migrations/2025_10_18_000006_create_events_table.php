<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
public function up(): void {
Schema::create('events', function (Blueprint $table) {
$table->bigIncrements('id');
$table->uuid('trip_id');
$table->enum('type', ['CHECKIN_POS','OFF_TRAIL','SOS','RETURNED','CUSTOM']);
$table->uuid('checkpoint_id')->nullable();
$table->dateTime('ts');
$table->text('note')->nullable();
$table->timestamps();


$table->foreign('trip_id')->references('id')->on('trips')->cascadeOnDelete();
$table->foreign('checkpoint_id')->references('id')->on('checkpoints')->nullOnDelete();
});
}
public function down(): void { Schema::dropIfExists('events'); }
};