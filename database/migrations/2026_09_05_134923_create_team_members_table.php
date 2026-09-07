<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('gg_user_id');
            $table->string('parent_gg_user_id')->nullable();
            $table->unsignedInteger('level')->nullable();
            $table->string('name')->nullable();
            $table->string('username')->nullable();
            $table->string('purchase_code')->nullable();
            $table->timestamp('joined_at')->nullable();
            $table->boolean('kyc_verified')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            $table->unique(['owner_user_id', 'gg_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
