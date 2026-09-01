<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('script_items', function (Blueprint $table) {
            $table->string('language')->default('english')->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('script_items', function (Blueprint $table) {
            $table->dropColumn('language');
        });
    }
};
