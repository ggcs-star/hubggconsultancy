<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HallOfFameEntry;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HallOfFameController extends Controller
{
    public function __construct(private FileUploadService $fileUploadService)
    {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $pointsMin = $request->query('points_min');
        $pointsMax = $request->query('points_max');
        $periodFrom = $request->query('period_from');
        $periodTo = $request->query('period_to');

        $entries = HallOfFameEntry::query()
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->when(filled($pointsMin), fn ($query) => $query->where('points', '>=', $pointsMin))
            ->when(filled($pointsMax), fn ($query) => $query->where('points', '<=', $pointsMax))
            ->inPeriod($periodFrom, $periodTo)
            ->ordered()
            ->get();

        return view('admin.hall-of-fame.index', [
            'entries' => $entries,
            'search' => $search,
            'pointsMin' => $pointsMin,
            'pointsMax' => $pointsMax,
            'periodFrom' => $periodFrom,
            'periodTo' => $periodTo,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateEntry($request);
        $data['created_by'] = auth()->id();

        if ($request->hasFile('image')) {
            $data['image'] = $this->fileUploadService->store($request->file('image'), 'hall-of-fame');
        }

        HallOfFameEntry::create($data);

        return redirect()->route('admin.hall-of-fame.index')->with('status', 'Hall of Fame entry added.');
    }

    public function update(Request $request, HallOfFameEntry $hallOfFameEntry): RedirectResponse
    {
        $data = $this->validateEntry($request);

        if ($request->hasFile('image')) {
            $this->fileUploadService->delete($hallOfFameEntry->image);
            $data['image'] = $this->fileUploadService->store($request->file('image'), 'hall-of-fame');
        }

        $hallOfFameEntry->update($data);

        return redirect()->route('admin.hall-of-fame.index')->with('status', 'Hall of Fame entry updated.');
    }

    public function destroy(HallOfFameEntry $hallOfFameEntry): RedirectResponse
    {
        $this->fileUploadService->delete($hallOfFameEntry->image);
        $hallOfFameEntry->delete();

        return redirect()->route('admin.hall-of-fame.index')->with('status', 'Hall of Fame entry deleted.');
    }

    public function toggleActive(Request $request, HallOfFameEntry $hallOfFameEntry): RedirectResponse
    {
        $data = $request->validate(['is_active' => ['required', 'boolean']]);

        $hallOfFameEntry->update($data);

        return back()->with('status', $hallOfFameEntry->is_active ? 'Entry shown.' : 'Entry hidden.');
    }

    private function validateEntry(Request $request): array
    {
        return $request->validate([
            'rank' => ['required', 'integer', 'min:1'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'points' => ['required', 'integer', 'min:0'],
            'period_start' => ['nullable', 'date'],
            'period_end' => ['nullable', 'date', 'after_or_equal:period_start'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);
    }
}
