<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Uses a raw statement instead of Schema::table()->change() since that
     * requires doctrine/dbal, which isn't installed in this project.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE users MODIFY email VARCHAR(191) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE users MODIFY email VARCHAR(191) NOT NULL');
    }
};
