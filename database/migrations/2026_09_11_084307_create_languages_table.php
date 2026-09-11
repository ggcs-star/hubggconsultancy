<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $now = now();

        DB::table('languages')->insert([
            ['code' => 'english', 'name' => 'English', 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'hindi', 'name' => 'Hindi', 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'gujarati', 'name' => 'Gujarati', 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'marathi', 'name' => 'Marathi', 'sort_order' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'telugu', 'name' => 'Telugu', 'sort_order' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'kannada', 'name' => 'Kannada', 'sort_order' => 6, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
