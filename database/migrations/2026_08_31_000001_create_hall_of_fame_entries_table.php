<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hall_of_fame_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('rank');
            $table->string('name');
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('points')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hall_of_fame_entries');
    }
};
