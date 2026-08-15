<?php

use App\Services\LegacyXmlImporter;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

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
