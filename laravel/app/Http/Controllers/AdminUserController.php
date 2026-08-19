<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserCredentialGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function __construct(private readonly UserCredentialGuard $credentialGuard)
    {
    }

    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.users.index', [
            'users' => User::query()->orderBy('role')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', Rule::in(['controller', 'manager', 'admin'])],
            'username' => ['nullable', 'string', 'max:100', 'alpha_dash', 'unique:users,username'],
            'password' => ['nullable', 'string', 'min:10'],
            'pin' => ['nullable', 'digits_between:4,10'],
        ]);

        if (in_array($validated['role'], ['manager', 'admin'], true)
            && (empty($validated['username']) || empty($validated['password']))) {
            return back()->withInput($request->except(['password', 'pin']))
                ->withErrors(['user' => 'Managers and admins require a username and password.']);
        }

        if (! empty($validated['password'])
            && $this->credentialGuard->passwordIsInUse($validated['password'])) {
            return back()->withInput($request->except(['password', 'pin']))
                ->withErrors(['password' => 'That password is already in use by another user. Choose a different password.']);
        }

        if ($validated['role'] === 'controller' && empty($validated['pin'])) {
            return back()->withInput($request->except(['password', 'pin']))
                ->withErrors(['user' => 'Controllers require a PIN.']);
        }

        if ($validated['role'] === 'controller'
            && ! empty($validated['pin'])
            && $this->credentialGuard->pinIsInUse($validated['pin'])) {
            return back()->withInput($request->except(['password', 'pin']))
                ->withErrors(['pin' => 'That PIN is already assigned to another controller. Choose a different PIN.']);
        }

        User::create([
            'name' => $validated['name'],
            'role' => $validated['role'],
            'username' => $validated['username'] ?: null,
            'password' => $validated['password'] ?: null,
            'pin_hash' => ! empty($validated['pin']) ? Hash::make($validated['pin']) : null,
        ]);

        return back()->with('status', 'User created successfully.');
    }

    public function edit(Request $request, User $user): View
    {
        $this->authorizeAdmin($request);

        return view('admin.users.edit', [
            'managedUser' => $user,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', Rule::in(['controller', 'manager', 'admin'])],
            'username' => [
                'nullable',
                'string',
                'max:100',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:10'],
            'pin' => ['nullable', 'digits_between:4,10'],
        ]);

        if ($user->role === 'admin'
            && $validated['role'] !== 'admin'
            && User::query()->where('role', 'admin')->count() <= 1) {
            return back()->withInput($request->except(['password', 'pin']))
                ->withErrors(['user' => 'The last administrator cannot be changed to another role.']);
        }

        if (in_array($validated['role'], ['manager', 'admin'], true)) {
            if (empty($validated['username'])) {
                return back()->withInput($request->except(['password', 'pin']))
                    ->withErrors(['user' => 'Managers and admins require a username.']);
            }

            if (empty($user->password) && empty($validated['password'])) {
                return back()->withInput($request->except(['password', 'pin']))
                    ->withErrors(['user' => 'This management user requires a password.']);
            }
        }

        if (! empty($validated['password'])
            && $this->credentialGuard->passwordIsInUse($validated['password'], $user->id)) {
            return back()->withInput($request->except(['password', 'pin']))
                ->withErrors(['password' => 'That password is already in use by another user. Choose a different password.']);
        }

        if ($validated['role'] === 'controller'
            && empty($user->pin_hash)
            && empty($validated['pin'])) {
            return back()->withInput($request->except(['password', 'pin']))
                ->withErrors(['user' => 'This controller requires a PIN.']);
        }

        if ($validated['role'] === 'controller'
            && ! empty($validated['pin'])
            && $this->credentialGuard->pinIsInUse($validated['pin'], $user->id)) {
            return back()->withInput($request->except(['password', 'pin']))
                ->withErrors(['pin' => 'That PIN is already assigned to another controller. Choose a different PIN.']);
        }

        $user->name = $validated['name'];
        $user->role = $validated['role'];
        $user->username = $validated['username'] ?: null;

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        if (! empty($validated['pin'])) {
            $user->pin_hash = Hash::make($validated['pin']);
        }

        $user->save();

        return redirect()->route('admin.users.index')
            ->with('status', $user->name.' updated successfully.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'admin', 403);
    }
}
