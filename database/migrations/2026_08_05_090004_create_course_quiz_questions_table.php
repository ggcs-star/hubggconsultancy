<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_checkpoint_id')->constrained('course_quiz_checkpoints')->cascadeOnDelete();
            $table->enum('type', ['radio', 'checkbox', 'text']);
            $table->text('question_text');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_quiz_questions');
    }
};
