<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('route_daily_quotas', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->uuid('route_id')->collation('utf8mb4_unicode_ci');
            $table->date('date');
            $table->unsignedInteger('capacity');
            $table->unsignedInteger('booked')->default(0);
            $table->string('status')->default('open');
            $table->timestamps();

            $table->unique(['route_id', 'date']);
            $table->foreign('route_id')->references('id')->on('routes')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('route_daily_quotas');
    }
};
