<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_manuals', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();

            // manual, guide, cheat_sheet, faq, sop, script
            $table->string('type')->default('manual');

            $table->string('category')->nullable();

            $table->text('description')->nullable();

            // Admin-created content
            $table->longText('content')->nullable();

            // Optional cover image
            $table->string('cover_image')->nullable();

            // draft / published
            $table->string('status')->default('draft');

            // Show/hide
            $table->boolean('is_active')->default(true);

            // Keep important content at top
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_pinned')->default(false);

            // Manual ordering
            $table->integer('sort_order')->default(0);

            $table->timestamp('published_at')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_manuals');
    }
};