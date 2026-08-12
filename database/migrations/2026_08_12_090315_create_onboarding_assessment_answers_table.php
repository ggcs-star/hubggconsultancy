<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onboarding_assessment_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('onboarding_assessment_question_id');
            $table->foreign('onboarding_assessment_question_id', 'oa_answers_question_fk')
                ->references('id')->on('onboarding_assessment_questions')->cascadeOnDelete();
            $table->text('answer_text')->nullable();
            $table->json('selected_option_ids')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->unsignedInteger('points_awarded')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'onboarding_assessment_question_id'], 'onboarding_answer_user_question_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboarding_assessment_answers');
    }
};
