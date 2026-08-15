<?php

namespace App\Http\Controllers;

use App\Models\ManagementInstruction;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class InstructionAcknowledgementController extends Controller
{
    public function store(Request $request, ManagementInstruction $instruction): RedirectResponse
    {
        $validated = $request->validate([
            'pin' => ['required', 'string', 'max:50'],
        ]);

        $operator = User::query()
            ->where('role', 'controller')
            ->whereNotNull('pin_hash')
            ->get()
            ->first(fn (User $user): bool => Hash::check($validated['pin'], $user->pin_hash));

        if (! $operator) {
            return back()->withErrors(['pin' => 'Invalid controller PIN.']);
        }

        $instruction->acknowledgements()->firstOrCreate(
            ['user_id' => $operator->id],
            [
                'operator_name' => $operator->name,
                'acknowledged_at' => now(),
            ],
        );

        return back()->with('status', 'Instruction acknowledged by '.$operator->name.'.');
    }
}
