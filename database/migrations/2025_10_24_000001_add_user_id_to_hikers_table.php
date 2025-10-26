<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('hikers', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->cascadeOnDelete()
                ->unique();
        });
    }

    public function down(): void
    {
        Schema::table('hikers', function (Blueprint $table) {
            $table->dropUnique('hikers_user_id_unique');
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
