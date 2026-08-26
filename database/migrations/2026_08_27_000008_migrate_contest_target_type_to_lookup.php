<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * The 4 values the old `target_type` enum allowed, plus the new
     * admin-requested categories — seeded here so existing contests can be
     * backfilled onto a real id before the enum column is dropped.
     */
    private array $seedNames = ['Sales', 'Revenue', 'Orders', 'New Customers', 'ACP', 'Combo', 'UPOS'];

    public function up(): void
    {
        Schema::table('contests', function (Blueprint $table) {
            $table->foreignId('target_type_id')->nullable()->after('target_type')->constrained('contest_target_types')->nullOnDelete();
        });

        $idsByName = [];

        foreach ($this->seedNames as $index => $name) {
            $id = DB::table('contest_target_types')->insertGetId([
                'name' => $name,
                'slug' => Str::slug($name),
                'sort_order' => $index + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $idsByName[$name] = $id;
        }

        $legacyMap = [
            'sales' => 'Sales',
        ];

        foreach ($legacyMap as $enumValue => $name) {
            DB::table('contests')
                ->where('target_type', $enumValue)
                ->update(['target_type_id' => $idsByName[$name]]);
        }

        Schema::table('contests', function (Blueprint $table) {
            $table->dropColumn('target_type');
        });
    }

    public function down(): void
    {
        Schema::table('contests', function (Blueprint $table) {
            $table->enum('target_type', ['sales', 'revenue', 'orders', 'new_customers'])->default('sales')->after('description');
        });

        $legacyMap = [
            'sales' => 'Sales',
            'revenue' => 'Revenue',
            'orders' => 'Orders',
            'new_customers' => 'New Customers',
        ];

        foreach ($legacyMap as $enumValue => $name) {
            $id = DB::table('contest_target_types')->where('name', $name)->value('id');

            if ($id) {
                DB::table('contests')->where('target_type_id', $id)->update(['target_type' => $enumValue]);
            }
        }

        Schema::table('contests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('target_type_id');
        });

        DB::table('contest_target_types')->whereIn('name', $this->seedNames)->delete();
    }
};
