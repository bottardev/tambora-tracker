<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('booking_participants', function (Blueprint $table) {
            $table->id();
            $table->uuid('booking_id');
            $table->string('name');
            $table->string('gender', 16)->nullable();
            $table->string('nationality', 64)->nullable();
            $table->string('origin_country', 64)->nullable();
            $table->string('age_group', 32)->nullable();
            $table->string('occupation', 64)->nullable();
            $table->string('id_type', 64)->nullable();
            $table->string('id_number', 128)->nullable();
            $table->string('health_certificate_path')->nullable();
            $table->boolean('is_leader')->default(false);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('booking_id')->references('id')->on('bookings')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_participants');
    }
};
