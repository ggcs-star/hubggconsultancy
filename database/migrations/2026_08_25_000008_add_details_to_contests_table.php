<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contests', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->enum('target_type', ['sales', 'revenue', 'orders', 'new_customers'])->default('sales')->after('description');
            $table->decimal('target_value', 14, 2)->default(0)->after('target');
            $table->enum('participation_type', ['individual', 'team'])->default('individual')->after('target_value');
            $table->enum('participant_mode', ['open', 'selected'])->default('open')->after('participation_type');
            $table->string('reward_type')->nullable()->after('reward');
            $table->string('reward_second')->nullable()->after('reward_type');
            $table->string('reward_third')->nullable()->after('reward_second');
            $table->decimal('min_achievement', 14, 2)->nullable()->after('reward_third');
            $table->string('counting_method')->nullable()->after('min_achievement');
            $table->string('tie_breaker')->nullable()->after('counting_method');
            $table->text('eligibility')->nullable()->after('tie_breaker');
            $table->timestamp('payout_processed_at')->nullable()->after('eligibility');
        });
    }

    public function down(): void
    {
        Schema::table('contests', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'target_type',
                'target_value',
                'participation_type',
                'participant_mode',
                'reward_type',
                'reward_second',
                'reward_third',
                'min_achievement',
                'counting_method',
                'tie_breaker',
                'eligibility',
                'payout_processed_at',
            ]);
        });
    }
};
