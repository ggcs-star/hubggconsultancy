<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // New registrations now land as an approved salesperson automatically
        // instead of needing manual review on the Salesperson Applications
        // page. Only affects the default for future inserts — existing users'
        // salesperson_status values are left exactly as they are.
        DB::statement("ALTER TABLE users MODIFY salesperson_status ENUM('none', 'pending', 'approved', 'rejected') NOT NULL DEFAULT 'approved'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY salesperson_status ENUM('none', 'pending', 'approved', 'rejected') NOT NULL DEFAULT 'none'");
    }
};
