<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('trip_id');
            $table->dateTime('recorded_at');
            $table->unsignedInteger('accuracy_m')->nullable();
            $table->float('altitude_m')->nullable();
            $table->timestamps();

            $table->foreign('trip_id')->references('id')->on('trips')->cascadeOnDelete();
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE locations ADD COLUMN location POINT NOT NULL');

            $versionRow = DB::selectOne('SELECT VERSION() AS version');
            $isMariaDb = isset($versionRow->version) && str_contains(strtolower($versionRow->version), 'mariadb');

            if (! $isMariaDb) {
                DB::statement('ALTER TABLE locations MODIFY COLUMN location POINT NOT NULL SRID 4326');
            } else {
                DB::statement('ALTER TABLE locations ADD CONSTRAINT locations_location_srid CHECK (ST_SRID(location) = 4326)');
            }

            DB::statement('ALTER TABLE locations ADD SPATIAL INDEX locations_location_spatial_index (location)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
