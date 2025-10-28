<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->uuid('id')->primary()->collation('utf8mb4_unicode_ci');
            $table->string('name');
            $table->text('description')->nullable();
            $table->float('total_distance_km')->nullable();
            $table->timestamps();
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE routes ADD COLUMN path LINESTRING NOT NULL');

            $versionRow = DB::selectOne('SELECT VERSION() AS version');
            $isMariaDb = isset($versionRow->version) && str_contains(strtolower($versionRow->version), 'mariadb');

            if (! $isMariaDb) {
                DB::statement('ALTER TABLE routes MODIFY COLUMN path LINESTRING NOT NULL SRID 4326');
            } else {
                DB::statement('ALTER TABLE routes ADD CONSTRAINT routes_path_srid CHECK (ST_SRID(path) = 4326)');
            }

            DB::statement('ALTER TABLE routes ADD SPATIAL INDEX routes_path_spatial_index (path)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
