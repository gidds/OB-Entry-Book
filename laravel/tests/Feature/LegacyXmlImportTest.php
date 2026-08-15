<?php

namespace Tests\Feature;

use App\Models\OccurrenceEntry;
use App\Models\User;
use App\Services\LegacyXmlImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LegacyXmlImportTest extends TestCase
{
    use RefreshDatabase;

    private function legacyPath(): string
    {
        return base_path('../Entry Book/data');
    }

    public function test_real_legacy_xml_imports_without_modifying_source_files(): void
    {
        $files = [
            $this->legacyPath().'/entries.xml',
            $this->legacyPath().'/instructions.xml',
            $this->legacyPath().'/auth.xml',
        ];

        $before = array_map('hash_file', array_fill(0, count($files), 'sha256'), $files);

        $result = app(LegacyXmlImporter::class)->import($this->legacyPath());

        $after = array_map('hash_file', array_fill(0, count($files), 'sha256'), $files);

        $this->assertSame($before, $after);
        $this->assertSame(2, $result['operators']);
        $this->assertSame(15, $result['entries']);
        $this->assertSame(15, $result['instructions']);
        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseCount('occurrence_entries', 15);
        $this->assertDatabaseCount('management_instructions', 15);
    }

    public function test_duplicate_historical_ob_numbers_are_preserved(): void
    {
        app(LegacyXmlImporter::class)->import($this->legacyPath());

        $this->assertSame(2, OccurrenceEntry::where('ob_number', '7\\9\\2024')->count());
        $this->assertSame(2, OccurrenceEntry::where('ob_number', '10\\9\\2024')->count());
    }

    public function test_import_is_idempotent(): void
    {
        $importer = app(LegacyXmlImporter::class);

        $importer->import($this->legacyPath());
        $counts = [
            'users' => User::count(),
            'entries' => OccurrenceEntry::count(),
            'instructions' => \App\Models\ManagementInstruction::count(),
        ];

        $importer->import($this->legacyPath());

        $this->assertSame($counts['users'], User::count());
        $this->assertSame($counts['entries'], OccurrenceEntry::count());
        $this->assertSame($counts['instructions'], \App\Models\ManagementInstruction::count());
    }

    public function test_legacy_operator_pin_is_hashed_and_still_verifiable(): void
    {
        app(LegacyXmlImporter::class)->import($this->legacyPath());

        $john = User::where('name', 'John Doe')->firstOrFail();

        $this->assertNotSame('1234', $john->pin_hash);
        $this->assertTrue(Hash::check('1234', $john->pin_hash));
    }

    public function test_dry_run_command_rolls_back_database_changes(): void
    {
        $this->artisan('ob:import-legacy', [
            '--path' => $this->legacyPath(),
            '--dry-run' => true,
        ])->assertSuccessful();

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('occurrence_entries', 0);
        $this->assertDatabaseCount('management_instructions', 0);
    }
}
