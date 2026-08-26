<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $removedNames = ['Revenue', 'Orders', 'New Customers'];

    public function up(): void
    {
        $salesId = DB::table('contest_target_types')->where('slug', 'sales')->value('id');
        $removedIds = DB::table('contest_target_types')->whereIn('name', $this->removedNames)->pluck('id');

        // Reassign any contest using a type we're about to remove onto Sales,
        // rather than leaving it null.
        DB::table('contests')
            ->whereIn('target_type_id', $removedIds)
            ->update(['target_type_id' => $salesId]);

        DB::table('contest_target_types')->whereIn('id', $removedIds)->delete();

        // ACP / Combo / UPOS first, Sales last.
        foreach (['acp' => 1, 'combo' => 2, 'upos' => 3, 'sales' => 4] as $slug => $sortOrder) {
            DB::table('contest_target_types')->where('slug', $slug)->update(['sort_order' => $sortOrder]);
        }
    }

    public function down(): void
    {
        foreach ($this->removedNames as $index => $name) {
            DB::table('contest_target_types')->insertOrIgnore([
                'name' => $name,
                'slug' => \Illuminate\Support\Str::slug($name),
                'sort_order' => $index + 5,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach (['sales' => 1, 'revenue' => 2, 'orders' => 3, 'new-customers' => 4, 'acp' => 5, 'combo' => 6, 'upos' => 7] as $slug => $sortOrder) {
            DB::table('contest_target_types')->where('slug', $slug)->update(['sort_order' => $sortOrder]);
        }
    }
};
