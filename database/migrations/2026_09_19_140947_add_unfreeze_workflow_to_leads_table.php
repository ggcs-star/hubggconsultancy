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
            $table->timestamp('status_changed_at')->nullable()->after('status');
            $table->enum('unfreeze_status', ['none', 'pending', 'approved', 'rejected'])->default('none')->after('status_changed_at');
            $table->text('unfreeze_request_message')->nullable()->after('unfreeze_status');
            $table->timestamp('unfreeze_requested_at')->nullable()->after('unfreeze_request_message');
            $table->timestamp('unfreeze_reviewed_at')->nullable()->after('unfreeze_requested_at');
            $table->foreignId('unfreeze_reviewed_by')->nullable()->after('unfreeze_reviewed_at')->constrained('users')->nullOnDelete();
        });

        // Backfill existing rows so the freeze clock starts from a real timestamp instead of null.
        DB::table('leads')->whereNull('status_changed_at')->update(['status_changed_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['unfreeze_reviewed_by']);
            $table->dropColumn([
                'status_changed_at',
                'unfreeze_status',
                'unfreeze_request_message',
                'unfreeze_requested_at',
                'unfreeze_reviewed_at',
                'unfreeze_reviewed_by',
            ]);
        });
    }
};
