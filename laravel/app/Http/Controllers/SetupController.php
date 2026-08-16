<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\EnvironmentFile;
use App\Services\InstallationState;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use PDO;
use Throwable;

class SetupController extends Controller
{
    public function __construct(
        private readonly InstallationState $state,
        private readonly EnvironmentFile $environment,
    ) {
    }

    public function index(): View|RedirectResponse
    {
        if ($this->state->isInstalled()) {
            return redirect()->route('entries.index');
        }

        return view('setup.index', [
            'phpOk' => version_compare(PHP_VERSION, '8.2.0', '>='),
            'extensions' => collect(['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'ctype', 'tokenizer', 'xml'])
                ->mapWithKeys(fn (string $extension) => [$extension => extension_loaded($extension)]),
            'envWritable' => is_writable(app()->environmentFilePath()),
            'storageWritable' => is_writable(storage_path()),
            'databaseReady' => $this->databaseReady(),
        ]);
    }

    public function database(Request $request): RedirectResponse
    {
        if ($this->state->isInstalled()) {
            return redirect()->route('entries.index');
        }

        $validated = $request->validate([
            'db_host' => ['required', 'string', 'max:255'],
            'db_port' => ['required', 'integer', 'between:1,65535'],
            'db_database' => ['required', 'string', 'max:255'],
            'db_username' => ['required', 'string', 'max:255'],
            'db_password' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            new PDO(
                'mysql:host='.$validated['db_host'].';port='.$validated['db_port'].';dbname='.$validated['db_database'].';charset=utf8mb4',
                $validated['db_username'],
                $validated['db_password'] ?? '',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
            );

            $this->environment->set([
                'APP_ENV' => 'production',
                'APP_DEBUG' => 'false',
                'APP_URL' => $request->getSchemeAndHttpHost(),
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => $validated['db_host'],
                'DB_PORT' => (string) $validated['db_port'],
                'DB_DATABASE' => $validated['db_database'],
                'DB_USERNAME' => $validated['db_username'],
                'DB_PASSWORD' => $validated['db_password'] ?? '',
            ]);

            config([
                'database.default' => 'mysql',
                'database.connections.mysql.host' => $validated['db_host'],
                'database.connections.mysql.port' => (string) $validated['db_port'],
                'database.connections.mysql.database' => $validated['db_database'],
                'database.connections.mysql.username' => $validated['db_username'],
                'database.connections.mysql.password' => $validated['db_password'] ?? '',
            ]);

            DB::purge('mysql');
            DB::connection('mysql')->getPdo();
            Artisan::call('migrate', ['--force' => true]);
        } catch (Throwable $exception) {
            report($exception);

            return back()->withInput($request->except('db_password'))
                ->withErrors(['database' => 'Database setup failed: '.$exception->getMessage()]);
        }

        return redirect()->route('setup.index')->with('status', 'Database connected and tables created. Create the first administrator below.');
    }

    public function admin(Request $request): RedirectResponse
    {
        if ($this->state->isInstalled()) {
            return redirect()->route('entries.index');
        }

        if (! $this->databaseReady()) {
            return redirect()->route('setup.index')->withErrors(['database' => 'Connect the database before creating the administrator.']);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:100', 'alpha_dash', 'unique:users,username'],
            'password' => ['required', 'string', 'min:10', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'password' => $validated['password'],
            'role' => 'admin',
        ]);

        $this->state->markInstalled();
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('entries.index')->with('status', 'Installation complete. '.$user->name.' is the first administrator.');
    }

    private function databaseReady(): bool
    {
        try {
            return Schema::hasTable('users')
                && Schema::hasTable('occurrence_entries')
                && Schema::hasTable('management_instructions');
        } catch (Throwable) {
            return false;
        }
    }
}
