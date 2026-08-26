<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Personal to-do/calendar — identical behavior for admin and user accounts
 * (each person only ever sees and manages their own tasks), so this
 * controller is shared rather than split into Admin\/User\ pairs like most
 * other features in this app.
 */
class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $tasks = $request->user()->tasks()->orderBy('date')->orderBy('time')->get();

        return view('tasks.index', [
            'tasks' => $tasks,
            'todoList' => $tasks,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateTask($request);
        $data['user_id'] = $request->user()->id;

        Task::create($data);

        return back()->with('status', 'Task added.');
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        abort_unless($task->user_id === $request->user()->id, 403);

        $task->update($this->validateTask($request));

        return back()->with('status', 'Task updated.');
    }

    public function toggleComplete(Request $request, Task $task): RedirectResponse|JsonResponse
    {
        abort_unless($task->user_id === $request->user()->id, 403);

        $data = $request->validate(['is_completed' => ['required', 'boolean']]);

        $task->update($data);

        if ($request->wantsJson()) {
            return response()->json(['is_completed' => $task->is_completed]);
        }

        return back()->with('status', $task->is_completed ? 'Task completed.' : 'Task reopened.');
    }

    public function destroy(Request $request, Task $task): RedirectResponse
    {
        abort_unless($task->user_id === $request->user()->id, 403);

        $task->delete();

        return back()->with('status', 'Task deleted.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'task_ids' => ['required', 'array', 'min:1'],
            'task_ids.*' => ['integer'],
        ]);

        $count = $request->user()->tasks()->whereIn('id', $data['task_ids'])->delete();

        return back()->with('status', "{$count} task(s) deleted.");
    }

    private function validateTask(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'time' => ['nullable', 'date_format:H:i'],
        ]);
    }
}
