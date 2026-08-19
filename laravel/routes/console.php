<?php

use App\Models\User;
use App\Services\LegacyXmlImporter;
use App\Services\UserCredentialGuard;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

Artisan::command('ob:status', function () {
    $this->info('OB Entry Book Laravel rebuild OK');
})->purpose('Check the Laravel rebuild bootstrap');

Artisan::command('ob:import-legacy {--path= : Directory containing entries.xml, instructions.xml and auth.xml} {--dry-run : Import inside a rolled-back transaction}', function (LegacyXmlImporter $importer) {
    $path = $this->option('path') ?: null;
    $dryRun = (bool) $this->option('dry-run');

    if ($dryRun) {
        DB::beginTransaction();
    }

    try {
        $result = $importer->import($path);

        $this->info(($dryRun ? 'Dry run: ' : '').'legacy XML parsed successfully.');
        $this->line('Operators: '.$result['operators']);
        $this->line('OB entries: '.$result['entries']);
        $this->line('Instructions: '.$result['instructions']);
    } finally {
        if ($dryRun && DB::transactionLevel() > 0) {
            DB::rollBack();
        }
    }
})->purpose('Import the preserved legacy XML into the Laravel database without modifying XML files');

Artisan::command('ob:create-user {name} {role=controller : controller, manager or admin} {--username=} {--password=} {--pin=}', function (UserCredentialGuard $credentialGuard) {
    $role = strtolower((string) $this->argument('role'));

    if (! in_array($role, ['controller', 'manager', 'admin'], true)) {
        $this->error('Role must be controller, manager or admin.');
        return 1;
    }

    $username = $this->option('username') ?: null;
    $password = $this->option('password') ?: null;
    $pin = $this->option('pin') ?: null;

    if (in_array($role, ['manager', 'admin'], true)) {
        $username ??= $this->ask('Username');
        $password ??= $this->secret('Password');

        if (! $username || ! $password) {
            $this->error('Management users require a username and password.');
            return 1;
        }

        if ($credentialGuard->passwordIsInUse($password)) {
            $this->error('That password is already in use by another user. Choose a different password.');
            return 1;
        }
    }

    if ($role === 'controller' && ! $pin) {
        $pin = $this->secret('Controller PIN');
    }

    if ($role === 'controller' && ! $pin) {
        $this->error('Controllers require a PIN.');
        return 1;
    }

    if ($role === 'controller' && $credentialGuard->pinIsInUse($pin)) {
        $this->error('That PIN is already assigned to another controller. Choose a different PIN.');
        return 1;
    }

    $user = User::create([
        'name' => (string) $this->argument('name'),
        'username' => $username,
        'password' => $password,
        'role' => $role,
        'pin_hash' => $pin ? Hash::make($pin) : null,
    ]);

    $this->info('Created '.$role.' user #'.$user->id.' ('.$user->name.').');

    return 0;
})->purpose('Create a controller, manager or admin without storing plaintext credentials');
