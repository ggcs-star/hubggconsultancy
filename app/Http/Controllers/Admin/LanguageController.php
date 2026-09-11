<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LanguageController extends Controller
{
    /**
     * Called from the "+ Add Language" modal on the shared language-select
     * component — creates the language immediately so the dropdown can show
     * it selected right away, instead of waiting on the surrounding form's
     * own submit.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $language = Language::firstOrCreate(
            ['code' => Str::slug($data['name'], '_')],
            ['name' => $data['name'], 'sort_order' => (Language::max('sort_order') ?? 0) + 1]
        );

        return response()->json([
            'code' => $language->code,
            'name' => $language->name,
        ]);
    }
}
