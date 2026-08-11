<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Module quizzes currently get their position purely from
     * after_course_lesson_id (rendered by walking that anchor in
     * CourseModule::orderedItems()). This gives them a real sort_order,
     * shared with course_lessons.sort_order in one continuous per-module
     * sequence, so the whole lesson+quiz list becomes freely draggable.
     *
     * The backfill below replicates the current anchor-walk exactly, so
     * existing modules keep the same visual order after this ships.
     */
    public function up(): void
    {
        Schema::table('course_quiz_checkpoints', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->nullable()->after('is_required');
        });

        $moduleIds = DB::table('course_modules')->pluck('id');

        foreach ($moduleIds as $moduleId) {
            $lessons = DB::table('course_lessons')
                ->where('course_module_id', $moduleId)
                ->orderBy('sort_order')
                ->get(['id']);

            $quizzes = DB::table('course_quiz_checkpoints')
                ->where('course_module_id', $moduleId)
                ->orderBy('id')
                ->get(['id', 'after_course_lesson_id']);

            $quizzesByAnchor = [];
            foreach ($quizzes as $quiz) {
                $key = $quiz->after_course_lesson_id === null ? 'start' : $quiz->after_course_lesson_id;
                $quizzesByAnchor[$key][] = $quiz->id;
            }

            $order = 1;

            foreach ($quizzesByAnchor['start'] ?? [] as $quizId) {
                DB::table('course_quiz_checkpoints')->where('id', $quizId)->update(['sort_order' => $order++]);
            }

            foreach ($lessons as $lesson) {
                DB::table('course_lessons')->where('id', $lesson->id)->update(['sort_order' => $order++]);

                foreach ($quizzesByAnchor[$lesson->id] ?? [] as $quizId) {
                    DB::table('course_quiz_checkpoints')->where('id', $quizId)->update(['sort_order' => $order++]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::table('course_quiz_checkpoints', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
