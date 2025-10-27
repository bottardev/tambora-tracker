<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->string('code')->nullable()->after('id');
        });

        $trips = DB::table('trips')->select('id')->get();

        foreach ($trips as $trip) {
            do {
                $code = 'TRIP-' . Str::upper(Str::random(5));
            } while (DB::table('trips')->where('code', $code)->exists());

            DB::table('trips')->where('id', $trip->id)->update(['code' => $code]);
        }

        Schema::table('trips', function (Blueprint $table) {
            $table->unique('code');
        });

        DB::statement("ALTER TABLE trips MODIFY code VARCHAR(255) NOT NULL");
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
};
