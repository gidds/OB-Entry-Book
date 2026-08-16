<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Throwable;

class InstallationState
{
    public function isInstalled(): bool
    {
        if (is_file(storage_path('app/installed'))) {
            return true;
        }

        try {
            return Schema::hasTable('users')
                && User::query()->whereIn('role', ['admin', 'manager'])->exists();
        } catch (Throwable) {
            return false;
        }
    }

    public function markInstalled(): void
    {
        $directory = storage_path('app');
        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        file_put_contents($directory.'/installed', now()->toIso8601String().PHP_EOL, LOCK_EX);
    }
}
