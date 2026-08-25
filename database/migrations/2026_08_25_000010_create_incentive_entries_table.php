<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incentive_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->decimal('amount', 14, 2)->default(0);
            $table->enum('type', ['points', 'bonus', 'cash', 'gift'])->default('bonus');
            $table->enum('source', ['contest', 'manual'])->default('manual');
            $table->foreignId('contest_id')->nullable()->constrained()->nullOnDelete();
            $table->date('awarded_at');
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incentive_entries');
    }
};
