<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->uuid('id')->primary();
            $table->string('code', 32)->unique();
            $table->date('trip_date');
            $table->uuid('route_id')->index();
            $table->uuid('hiker_id')->index();
            $table->string('status')->default('pending-payment');
            $table->string('payment_method')->nullable();
            $table->dateTime('payment_due_at')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('currency', 3)->default('IDR');
            $table->unsignedInteger('participants_count')->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('created_via')->default('dashboard');
            $table->timestamps();
            $table->softDeletes();
            // foreignId already defines FK for created_by
            $table->foreign('route_id')->references('id')->on('routes')->cascadeOnDelete();
            $table->foreign('hiker_id')->references('id')->on('hikers')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
