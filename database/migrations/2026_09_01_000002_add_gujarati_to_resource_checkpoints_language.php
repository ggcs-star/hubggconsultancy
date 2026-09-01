<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE resource_checkpoints MODIFY language ENUM('hindi', 'english', 'gujarati') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE resource_checkpoints MODIFY language ENUM('hindi', 'english') NOT NULL");
    }
};
