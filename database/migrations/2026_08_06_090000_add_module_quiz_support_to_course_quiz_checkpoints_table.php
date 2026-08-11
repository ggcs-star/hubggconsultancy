<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Uses raw SQL for the two MODIFY COLUMN statements instead of Blueprint's
     * ->change() — this app is on Laravel 10 without doctrine/dbal installed,
     * which ->change() requires. Plain ADD COLUMN below doesn't need it.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE course_quiz_checkpoints MODIFY course_lesson_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE course_quiz_checkpoints MODIFY timestamp_seconds INT UNSIGNED NULL');

        Schema::table('course_quiz_checkpoints', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable()->after('id')
                ->constrained('courses')->cascadeOnDelete();
            $table->foreignId('course_module_id')->nullable()->after('course_lesson_id')
                ->constrained('course_modules')->cascadeOnDelete();
            $table->foreignId('after_course_lesson_id')->nullable()->after('course_module_id')
                ->constrained('course_lessons')->nullOnDelete();
            $table->boolean('is_required')->default(true)->after('after_course_lesson_id');
        });

        // Backfill course_id for existing lesson-checkpoint rows.
        DB::table('course_quiz_checkpoints')
            ->join('course_lessons', 'course_lessons.id', '=', 'course_quiz_checkpoints.course_lesson_id')
            ->join('course_modules', 'course_modules.id', '=', 'course_lessons.course_module_id')
            ->update(['course_quiz_checkpoints.course_id' => DB::raw('course_modules.course_id')]);
    }

    public function down(): void
    {
        Schema::table('course_quiz_checkpoints', function (Blueprint $table) {
            $table->dropColumn(['course_id', 'course_module_id', 'after_course_lesson_id', 'is_required']);
        });

        DB::statement('ALTER TABLE course_quiz_checkpoints MODIFY course_lesson_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE course_quiz_checkpoints MODIFY timestamp_seconds INT UNSIGNED NOT NULL');
    }
};
