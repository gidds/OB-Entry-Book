<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['username' => 'Invalid credentials.'])
                ->onlyInput('username');
        }

        if (! $request->user()?->isManagement()) {
            Auth::logout();

            return back()
                ->withErrors(['username' => 'Management access is required.'])
                ->onlyInput('username');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('entries.index'));
    }

    public function storeController(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pin' => ['required', 'digits_between:4,10'],
        ]);

        $controller = User::query()
            ->where('role', 'controller')
            ->whereNotNull('pin_hash')
            ->get()
            ->first(fn (User $user): bool => Hash::check($validated['pin'], $user->pin_hash));

        if (! $controller) {
            return back()->withErrors(['pin' => 'Invalid controller PIN.']);
        }

        Auth::login($controller);
        $request->session()->regenerate();

        return redirect()->intended(route('entries.index'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
