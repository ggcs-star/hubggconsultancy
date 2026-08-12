<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onboarding_assessment_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('onboarding_assessment_question_id');
            $table->foreign('onboarding_assessment_question_id', 'oa_options_question_fk')
                ->references('id')->on('onboarding_assessment_questions')->cascadeOnDelete();
            $table->string('option_text');
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboarding_assessment_options');
    }
};
