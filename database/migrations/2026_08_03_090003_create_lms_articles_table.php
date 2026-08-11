<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lms_product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lms_category_id')->constrained('lms_categories')->cascadeOnDelete();
            $table->foreignId('lms_sub_category_id')->nullable()->constrained('lms_sub_categories')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->string('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_articles');
    }
};
