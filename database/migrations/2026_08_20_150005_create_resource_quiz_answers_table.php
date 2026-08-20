<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resource_quiz_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('resource_checkpoint_id')->constrained()->cascadeOnDelete();
            $table->foreignId('resource_quiz_question_id')->constrained()->cascadeOnDelete();
            $table->text('answer_text')->nullable();
            $table->json('selected_option_ids')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->unsignedInteger('points_awarded')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'resource_quiz_question_id'], 'resource_answer_user_question_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resource_quiz_answers');
    }
};
