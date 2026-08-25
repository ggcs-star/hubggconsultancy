<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ScriptTopic;
use Illuminate\View\View;

class ScriptController extends Controller
{
    public function index(): View
    {
        $topics = ScriptTopic::published()
            ->with(['items' => fn ($query) => $query->published()->ordered()])
            ->ordered()
            ->get()
            ->filter(fn (ScriptTopic $topic) => $topic->items->isNotEmpty())
            ->values();

        return view('user.scripts.index', [
            'topics' => $topics,
        ]);
    }
}
