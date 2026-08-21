<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->string('hindi_thumbnail')->nullable()->after('thumbnail');
            $table->string('english_thumbnail')->nullable()->after('hindi_thumbnail');
        });
    }

    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropColumn(['hindi_thumbnail', 'english_thumbnail']);
        });
    }
};
