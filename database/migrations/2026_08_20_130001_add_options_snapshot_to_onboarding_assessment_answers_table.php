<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('onboarding_assessment_answers', function (Blueprint $table) {
            // Snapshot of the exact option list (id, text, is_correct) at the moment the
            // user answered. Options are wholesale deleted+recreated on every question
            // edit (even a same-wording edit), so reconstructing "what they saw" from the
            // live/trashed options relation can show duplicate-looking rows — this avoids
            // that by keeping an authoritative per-answer copy.
            $table->json('options_snapshot')->nullable()->after('question_points');
        });
    }

    public function down(): void
    {
        Schema::table('onboarding_assessment_answers', function (Blueprint $table) {
            $table->dropColumn('options_snapshot');
        });
    }
};
