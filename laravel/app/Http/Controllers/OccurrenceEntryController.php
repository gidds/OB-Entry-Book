<?php

namespace App\Http\Controllers;

use App\Models\ManagementInstruction;
use App\Models\OccurrenceEntry;
use App\Services\ObNumberGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OccurrenceEntryController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $entries = OccurrenceEntry::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('ob_number', 'like', '%'.$search.'%')
                        ->orWhere('customer', 'like', '%'.$search.'%')
                        ->orWhere('entry_text', 'like', '%'.$search.'%');
                });
            })
            ->latest('occurred_on')
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('entries.index', [
            'entries' => $entries,
            'search' => $search,
            'instructions' => ManagementInstruction::query()
                ->with('acknowledgements')
                ->latest('id')
                ->limit(50)
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('entries.create');
    }

    public function store(Request $request, ObNumberGenerator $numbers): RedirectResponse
    {
        $validated = $request->validate([
            'customer' => ['nullable', 'string', 'max:255'],
            'entry_text' => ['required', 'string'],
        ]);

        $entry = DB::transaction(function () use ($validated, $numbers): OccurrenceEntry {
            $today = now();

            return OccurrenceEntry::create([
                'ob_number' => $numbers->next($today),
                'occurred_on' => $today->toDateString(),
                'customer' => $validated['customer'] ?? null,
                'entry_text' => $validated['entry_text'],
            ]);
        });

        return redirect()
            ->route('entries.index')
            ->with('status', 'OB '.$entry->ob_number.' added successfully.');
    }
}
