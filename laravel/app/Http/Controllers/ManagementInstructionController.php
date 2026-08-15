<?php

namespace App\Http\Controllers;

use App\Models\ManagementInstruction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ManagementInstructionController extends Controller
{
    public function create(Request $request): View
    {
        abort_unless($request->user()?->isManagement(), 403);

        return view('instructions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isManagement(), 403);

        $validated = $request->validate([
            'instruction_text' => ['required', 'string'],
        ]);

        ManagementInstruction::create([
            'instruction_date' => now()->toDateString(),
            'manager_id' => $request->user()->id,
            'manager_name' => $request->user()->name,
            'instruction_text' => $validated['instruction_text'],
        ]);

        return redirect()
            ->route('entries.index')
            ->with('status', 'Management instruction added.');
    }
}
