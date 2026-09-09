<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->string('marathi_thumbnail')->nullable()->after('gujarati_thumbnail');
            $table->string('telugu_thumbnail')->nullable()->after('marathi_thumbnail');
            $table->string('kannada_thumbnail')->nullable()->after('telugu_thumbnail');
            $table->text('marathi_youtube_url')->nullable()->after('gujarati_youtube_url');
            $table->text('telugu_youtube_url')->nullable()->after('marathi_youtube_url');
            $table->text('kannada_youtube_url')->nullable()->after('telugu_youtube_url');
        });
    }

    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropColumn(['marathi_thumbnail', 'telugu_thumbnail', 'kannada_thumbnail', 'marathi_youtube_url', 'telugu_youtube_url', 'kannada_youtube_url']);
        });
    }
};
