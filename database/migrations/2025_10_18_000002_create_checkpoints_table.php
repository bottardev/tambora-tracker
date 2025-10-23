<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('checkpoints', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('route_id');
            $table->string('name'); // POS 1, POS 2, dst
            $table->unsignedInteger('order_no');
            $table->unsignedInteger('radius_m')->default(100);
            $table->timestamps();

            $table->foreign('route_id')->references('id')->on('routes')->cascadeOnDelete();
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE checkpoints ADD COLUMN location POINT NOT NULL');

            $versionRow = DB::selectOne('SELECT VERSION() AS version');
            $isMariaDb = isset($versionRow->version) && str_contains(strtolower($versionRow->version), 'mariadb');

            if (! $isMariaDb) {
                DB::statement('ALTER TABLE checkpoints MODIFY COLUMN location POINT NOT NULL SRID 4326');
            } else {
                DB::statement('ALTER TABLE checkpoints ADD CONSTRAINT checkpoints_location_srid CHECK (ST_SRID(location) = 4326)');
            }

            DB::statement('ALTER TABLE checkpoints ADD SPATIAL INDEX checkpoints_location_spatial_index (location)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('checkpoints');
    }
};
