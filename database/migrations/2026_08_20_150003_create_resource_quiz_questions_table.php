<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resource_quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_checkpoint_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['radio', 'checkbox', 'text']);
            $table->text('question_text');
            $table->unsignedInteger('points')->default(1);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resource_quiz_questions');
    }
};
