<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contests', function (Blueprint $table) {
            $table->enum('achievement_source', ['manual', 'crm'])->default('manual')->after('participant_mode');
        });

        Schema::create('contest_point_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contest_id')->constrained()->cascadeOnDelete();
            $table->string('lead_status');
            $table->unsignedInteger('points');
            $table->timestamps();

            $table->unique(['contest_id', 'lead_status']);
        });

        Schema::table('contest_achievements', function (Blueprint $table) {
            $table->foreignId('lead_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('contest_achievements', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lead_id');
        });

        Schema::dropIfExists('contest_point_rules');

        Schema::table('contests', function (Blueprint $table) {
            $table->dropColumn('achievement_source');
        });
    }
};
