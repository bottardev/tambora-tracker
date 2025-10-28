<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->char('route_id', 36)->charset('latin1')->collation('latin1_swedish_ci')->nullable();
            $table->string('reason')->nullable();
            $table->boolean('is_closed')->default(true);
            $table->timestamps();

            $table->unique(['date', 'route_id']);
            $table->foreign('route_id')->references('id')->on('routes')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
