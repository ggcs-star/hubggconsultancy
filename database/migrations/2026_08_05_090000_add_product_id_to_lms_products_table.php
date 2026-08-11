<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lms_products', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->unique()->after('id')
                ->constrained('products')->nullOnDelete();
        });

        // Link existing LMS products to their matching SaaS product by name.
        DB::table('lms_products')->orderBy('id')->get()->each(function ($lmsProduct) {
            $product = DB::table('products')->whereRaw('LOWER(name) = ?', [strtolower($lmsProduct->name)])->first();

            if ($product) {
                DB::table('lms_products')->where('id', $lmsProduct->id)->update(['product_id' => $product->id]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('lms_products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_id');
        });
    }
};
