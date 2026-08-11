<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_quiz_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quiz_checkpoint_id')->constrained('course_quiz_checkpoints')->cascadeOnDelete();
            $table->foreignId('quiz_question_id')->constrained('course_quiz_questions')->cascadeOnDelete();
            $table->text('answer_text')->nullable();
            $table->json('selected_option_ids')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'quiz_question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_quiz_answers');
    }
};
