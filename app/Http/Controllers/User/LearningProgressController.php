<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LearningProgressController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $courses = $user->assignedCourses()
            ->where('is_published', true)
            ->orderBy('title')
            ->get()
            ->map(function (Course $course) use ($user) {
                $course->progress = $course->progressFor($user);

                return $course;
            });

        return view('user.learning-progress.index', [
            'courses' => $courses,
        ]);
    }
}
