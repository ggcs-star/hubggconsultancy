<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_toolkit_items', function (Blueprint $table) {
            // `url` now stores the uploaded file's path on the "public" disk instead of
            // an external link — these columns hold the metadata needed to display it.
            $table->string('original_filename')->nullable()->after('url');
            $table->string('mime_type')->nullable()->after('original_filename');
            $table->unsignedBigInteger('file_size')->nullable()->after('mime_type');
        });
    }

    public function down(): void
    {
        Schema::table('sales_toolkit_items', function (Blueprint $table) {
            $table->dropColumn(['original_filename', 'mime_type', 'file_size']);
        });
    }
};
