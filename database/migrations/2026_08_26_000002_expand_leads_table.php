<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('product')->nullable()->after('source');
            $table->decimal('expected_value', 14, 2)->nullable()->after('product');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->after('expected_value');
            $table->foreignId('campaign_id')->nullable()->after('source')->constrained()->nullOnDelete();
        });

        // Widen the status enum to the full pipeline. MySQL requires a raw
        // statement to redefine an enum's allowed values.
        DB::statement("ALTER TABLE leads MODIFY status ENUM(
            'new', 'contacted', 'interested', 'qualified', 'proposal', 'negotiation',
            'won', 'lost', 'not_interested', 'invalid', 'follow_up_later'
        ) NOT NULL DEFAULT 'new'");

        // Map the old, narrower status values onto their closest new equivalent.
        DB::table('leads')->where('status', 'follow_up')->update(['status' => 'follow_up_later']);
        DB::table('leads')->where('status', 'converted')->update(['status' => 'won']);
    }

    public function down(): void
    {
        DB::table('leads')->where('status', 'follow_up_later')->update(['status' => 'follow_up']);
        DB::table('leads')->where('status', 'won')->update(['status' => 'converted']);
        DB::table('leads')->whereIn('status', ['interested', 'qualified', 'proposal', 'negotiation', 'not_interested', 'invalid'])
            ->update(['status' => 'new']);

        DB::statement("ALTER TABLE leads MODIFY status ENUM('new', 'contacted', 'follow_up', 'converted', 'lost') NOT NULL DEFAULT 'new'");

        Schema::table('leads', function (Blueprint $table) {
            $table->dropConstrainedForeignId('campaign_id');
            $table->dropColumn(['product', 'expected_value', 'priority']);
        });
    }
};
