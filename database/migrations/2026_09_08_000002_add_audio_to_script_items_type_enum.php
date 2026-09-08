<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE script_items MODIFY type ENUM('video', 'document', 'audio') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE script_items MODIFY type ENUM('video', 'document') NOT NULL");
    }
};
