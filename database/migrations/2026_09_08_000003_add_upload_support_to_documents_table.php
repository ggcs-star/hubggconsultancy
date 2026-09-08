<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Every existing row is a link today, so default true keeps
            // them behaving exactly as before this column existed.
            $table->boolean('is_external')->default(true)->after('url');
            $table->string('original_filename')->nullable()->after('is_external');
            $table->string('mime_type')->nullable()->after('original_filename');
            $table->unsignedBigInteger('file_size')->nullable()->after('mime_type');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['is_external', 'original_filename', 'mime_type', 'file_size']);
        });
    }
};
