<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('onboarding_assessment_questions', function (Blueprint $table) {
            // Nullable: questions created before the multi-quiz assessment feature have no
            // quiz and are simply no longer shown — new questions always belong to a quiz.
            $table->foreignId('onboarding_assessment_quiz_id')
                ->nullable()
                ->after('id')
                ->constrained('onboarding_assessment_quizzes', 'id', 'oa_questions_quiz_fk')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('onboarding_assessment_questions', function (Blueprint $table) {
            $table->dropForeign('oa_questions_quiz_fk');
            $table->dropColumn('onboarding_assessment_quiz_id');
        });
    }
};
