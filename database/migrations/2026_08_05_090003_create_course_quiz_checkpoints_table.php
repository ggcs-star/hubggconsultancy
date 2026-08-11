<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_quiz_checkpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_lesson_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('timestamp_seconds');
            $table->string('title')->nullable();
            $table->timestamps();

            $table->unique(['course_lesson_id', 'timestamp_seconds'], 'course_checkpoints_lesson_timestamp_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_quiz_checkpoints');
    }
};
