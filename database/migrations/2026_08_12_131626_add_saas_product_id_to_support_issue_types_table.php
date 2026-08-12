<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_issue_types', function (Blueprint $table) {

            $table->foreignId('saas_product_id')
                ->nullable()
                ->after('id')
                ->constrained('saas_products')
                ->nullOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('support_issue_types', function (Blueprint $table) {

            $table->dropForeign(['saas_product_id']);

            $table->dropColumn('saas_product_id');

        });
    }
};