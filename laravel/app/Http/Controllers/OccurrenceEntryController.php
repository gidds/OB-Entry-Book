<?php

namespace App\Http\Controllers;

use App\Models\ManagementInstruction;
use App\Models\OccurrenceEntry;
use App\Models\User;
use App\Services\ObNumberGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
            ->with('status', 'OB '.$entry->ob_number.' added successfully.')
            ->with('clear_ob_entry_draft', true);
    }

    public function edit(OccurrenceEntry $entry): View|RedirectResponse
    {
        if (! $entry->isEditable()) {
            return redirect()
                ->route('entries.index')
                ->withErrors(['entry' => 'This OB entry is locked because its one-hour editing window has expired.']);
        }

        return view('entries.edit', [
            'entry' => $entry,
        ]);
    }

    public function update(Request $request, OccurrenceEntry $entry): RedirectResponse
    {
        if (! $entry->isEditable()) {
            return redirect()
                ->route('entries.index')
                ->withErrors(['entry' => 'This OB entry is locked because its one-hour editing window has expired.']);
        }

        $validated = $request->validate([
            'customer' => ['nullable', 'string', 'max:255'],
            'entry_text' => ['required', 'string'],
            'pin' => ['required', 'digits_between:4,10'],
        ]);

        $controller = User::query()
            ->where('role', 'controller')
            ->whereNotNull('pin_hash')
            ->get()
            ->first(fn (User $user): bool => Hash::check($validated['pin'], $user->pin_hash));

        if (! $controller) {
            return back()
                ->withInput($request->except('pin'))
                ->withErrors(['pin' => 'Invalid controller PIN.']);
        }

        $entry->update([
            'customer' => $validated['customer'] ?? null,
            'entry_text' => $validated['entry_text'],
        ]);

        return redirect()
            ->route('entries.index')
            ->with('status', 'OB '.$entry->ob_number.' updated successfully.');
    }
}
