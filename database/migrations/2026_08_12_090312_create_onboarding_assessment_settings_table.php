<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onboarding_assessment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('Onboarding Assessment');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('passing_score_percent')->default(70);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboarding_assessment_settings');
    }
};
