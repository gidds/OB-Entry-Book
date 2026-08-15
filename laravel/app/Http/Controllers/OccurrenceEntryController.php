<?php

namespace App\Http\Controllers;

use App\Models\OccurrenceEntry;
use App\Services\ObNumberGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OccurrenceEntryController extends Controller
{
    public function index(): View
    {
        return view('entries.index', [
            'entries' => OccurrenceEntry::query()
                ->latest('occurred_on')
                ->latest('id')
                ->limit(100)
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
