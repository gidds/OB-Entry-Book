<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserCredentialGuard
{
    public function passwordIsInUse(string $password, ?int $exceptUserId = null): bool
    {
        return User::query()
            ->whereNotNull('password')
            ->when($exceptUserId !== null, fn ($query) => $query->whereKeyNot($exceptUserId))
            ->pluck('password')
            ->contains(fn (string $hash) => Hash::check($password, $hash));
    }

    public function pinIsInUse(string $pin, ?int $exceptUserId = null): bool
    {
        return User::query()
            ->where('role', 'controller')
            ->whereNotNull('pin_hash')
            ->when($exceptUserId !== null, fn ($query) => $query->whereKeyNot($exceptUserId))
            ->pluck('pin_hash')
            ->contains(fn (string $hash) => Hash::check($pin, $hash));
    }
}
