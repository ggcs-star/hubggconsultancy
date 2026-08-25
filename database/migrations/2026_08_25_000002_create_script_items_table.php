<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('script_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('script_topic_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['video', 'document']);
            $table->string('title');
            $table->text('url');
            $table->string('original_filename')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('script_items');
    }
};
