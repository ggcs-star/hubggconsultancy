<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('certificate_background')->nullable()->after('thumbnail');
            $table->json('certificate_fields')->nullable()->after('certificate_background');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['certificate_background', 'certificate_fields']);
        });
    }
};
