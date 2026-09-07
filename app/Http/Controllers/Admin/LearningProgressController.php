<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LearningProgressController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $courseId = trim((string) $request->query('course_id'));

        $courses = Course::where('is_published', true)->orderBy('title')->get();

        $filteredCourses = $courseId !== ''
            ? $courses->where('id', (int) $courseId)
            : $courses;

        $users = User::where('role', 'user')
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            }))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $rows = collect();

        foreach ($users as $user) {
            foreach ($filteredCourses as $course) {
                $rows->push((object) [
                    'user' => $user,
                    'course' => $course,
                    'progress' => $course->progressFor($user),
                ]);
            }
        }

        return view('admin.learning-progress.index', [
            'rows' => $rows,
            'courses' => $courses,
            'users' => $users,
        ]);
    }
}
