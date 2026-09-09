<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE resource_checkpoints MODIFY language ENUM('hindi', 'english', 'gujarati', 'marathi', 'telugu', 'kannada') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE resource_checkpoints MODIFY language ENUM('hindi', 'english', 'gujarati') NOT NULL");
    }
};
