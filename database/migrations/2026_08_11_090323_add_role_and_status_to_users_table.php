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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'user'])->default('user')->after('email');
            $table->boolean('profile_completed')->default(false)->after('role');
            $table->enum('salesperson_status', ['none', 'pending', 'approved', 'rejected'])
                ->default('none')->after('profile_completed');
            $table->string('phone')->nullable()->after('salesperson_status');
            $table->string('city')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'profile_completed', 'salesperson_status', 'phone', 'city']);
        });
    }
};
