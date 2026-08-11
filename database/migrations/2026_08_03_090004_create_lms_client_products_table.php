<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_client_products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('lms_product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->boolean('status')->default(true);

            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('assigned_at')->nullable();

            $table->timestamps();

            $table->unique(['client_id', 'lms_product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_client_products');
    }
};
