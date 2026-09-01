<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScriptTopic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ScriptTopicController extends Controller
{
    public function index(): View
    {
        $topics = ScriptTopic::withCount('items')->ordered()->paginate(10)->withQueryString();

        return view('admin.scripts.index', [
            'topics' => $topics,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateTopic($request);

        $topic = ScriptTopic::create($data);

        return redirect()->route('admin.scripts.show', $topic)->with('status', 'Topic added.');
    }

    public function show(ScriptTopic $script): View
    {
        $script->load('items');

        return view('admin.scripts.show', [
            'topic' => $script,
        ]);
    }

    public function update(Request $request, ScriptTopic $script): RedirectResponse
    {
        $data = $this->validateTopic($request);

        $script->update($data);

        return redirect()->route('admin.scripts.index')->with('status', 'Topic updated.');
    }

    public function destroy(ScriptTopic $script): RedirectResponse
    {
        foreach ($script->items as $item) {
            Storage::disk('public')->delete($item->url);
        }

        $script->delete();

        return redirect()->route('admin.scripts.index')->with('status', 'Topic deleted.');
    }

    public function togglePublish(Request $request, ScriptTopic $script): RedirectResponse
    {
        $data = $request->validate(['is_published' => ['required', 'boolean']]);

        $script->update($data);

        return back()->with('status', $script->is_published ? "\"{$script->title}\" published." : "\"{$script->title}\" set to draft.");
    }

    private function validateTopic(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
