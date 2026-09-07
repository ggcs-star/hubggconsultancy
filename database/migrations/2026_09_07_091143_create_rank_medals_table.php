<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rank_medals', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('rank')->unique();
            $table->string('emoji', 10);
            $table->timestamps();
        });

        DB::table('rank_medals')->insert([
            ['rank' => 1, 'emoji' => '🥇', 'created_at' => now(), 'updated_at' => now()],
            ['rank' => 2, 'emoji' => '🥈', 'created_at' => now(), 'updated_at' => now()],
            ['rank' => 3, 'emoji' => '🥉', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rank_medals');
    }
};
