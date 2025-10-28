<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('ALTER TABLE routes CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        DB::statement("ALTER TABLE routes MODIFY id CHAR(36) NOT NULL COLLATE utf8mb4_unicode_ci");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('ALTER TABLE routes CONVERT TO CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        DB::statement("ALTER TABLE routes MODIFY id CHAR(36) NOT NULL COLLATE latin1_swedish_ci");
    }
};
