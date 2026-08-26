<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onboarding_checklist_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('onboarding_checklist_item_id');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('onboarding_checklist_item_id', 'occ_item_id_foreign')
                ->references('id')->on('onboarding_checklist_items')->cascadeOnDelete();

            $table->unique(['user_id', 'onboarding_checklist_item_id'], 'onboarding_checklist_user_item_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboarding_checklist_completions');
    }
};
