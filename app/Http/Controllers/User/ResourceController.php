<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Services\ResourcePlayerPayloadBuilder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResourceController extends Controller
{
    public function index(Request $request, ResourcePlayerPayloadBuilder $payloadBuilder): View
    {
        $resources = Resource::published()->with('checkpoints.questions.options')->ordered()->get();

        $availableLanguages = collect(['english', 'hindi', 'gujarati'])
            ->filter(fn ($lang) => $resources->contains(fn (Resource $resource) => filled($resource->{$lang . '_youtube_url'})))
            ->values()
            ->all();

        return view('user.resources.index', [
            'resources' => $resources,
            'playerData' => $payloadBuilder->build($resources, $request->user()),
            'availableLanguages' => $availableLanguages,
        ]);
    }
}
