<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_quiz_questions', function (Blueprint $table) {
            $table->unsignedInteger('points')->default(1)->after('question_text');
        });
    }

    public function down(): void
    {
        Schema::table('course_quiz_questions', function (Blueprint $table) {
            $table->dropColumn('points');
        });
    }
};
