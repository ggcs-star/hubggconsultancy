<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_quiz_answers', function (Blueprint $table) {
            $table->unsignedInteger('points_awarded')->nullable()->after('is_correct');
        });
    }

    public function down(): void
    {
        Schema::table('client_quiz_answers', function (Blueprint $table) {
            $table->dropColumn('points_awarded');
        });
    }
};
