<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_toolkit_items', function (Blueprint $table) {
            // When true, `url` holds a Google Drive share link (opened in an
            // embedded preview, same as Documents) instead of a path on the
            // "public" disk.
            $table->boolean('is_drive_link')->default(false)->after('url');
        });
    }

    public function down(): void
    {
        Schema::table('sales_toolkit_items', function (Blueprint $table) {
            $table->dropColumn('is_drive_link');
        });
    }
};
