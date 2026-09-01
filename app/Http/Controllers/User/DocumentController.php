<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));

        $availableLanguages = collect(['english', 'hindi', 'gujarati'])
            ->filter(fn ($lang) => Document::published()->where('language', $lang)->exists())
            ->values()
            ->all();

        $language = $request->query('language');

        if (! $language || ! in_array($language, $availableLanguages, true)) {
            $language = $availableLanguages[0] ?? 'english';
        }

        $documents = Document::published()
            ->when($search !== '', fn ($query) => $query->where('title', 'like', "%{$search}%"))
            ->where('language', $language)
            ->ordered()
            ->paginate(10)
            ->withQueryString();

        return view('user.documents.index', [
            'documents' => $documents,
            'language' => $language,
            'availableLanguages' => $availableLanguages,
        ]);
    }
}
