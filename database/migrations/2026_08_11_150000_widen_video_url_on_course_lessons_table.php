<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Raw SQL instead of Blueprint's ->change() — this app is on Laravel 10
     * without doctrine/dbal installed, which ->change() requires.
     *
     * video_url started as VARCHAR(191), but the field also accepts a full
     * pasted <iframe> embed snippet (see CourseLesson::youtubeId()), which
     * routinely exceeds that and was throwing "Data too long for column".
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE course_lessons MODIFY video_url TEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE course_lessons MODIFY video_url VARCHAR(191) NULL');
    }
};
