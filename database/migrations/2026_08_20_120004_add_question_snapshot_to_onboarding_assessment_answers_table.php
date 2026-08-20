<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('onboarding_assessment_answers', function (Blueprint $table) {
            // Snapshot of the question's wording/points at the moment the user answered,
            // so a later admin edit to the live question never reshapes an already-submitted
            // result — the review and scoring stay frozen to what the user actually saw.
            $table->text('question_text')->nullable()->after('onboarding_assessment_question_id');
            $table->unsignedInteger('question_points')->nullable()->after('question_text');
        });
    }

    public function down(): void
    {
        Schema::table('onboarding_assessment_answers', function (Blueprint $table) {
            $table->dropColumn(['question_text', 'question_points']);
        });
    }
};
