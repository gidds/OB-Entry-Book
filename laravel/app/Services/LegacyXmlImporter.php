<?php

namespace App\Services;

use App\Models\ManagementInstruction;
use App\Models\OccurrenceEntry;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use SimpleXMLElement;

class LegacyXmlImporter
{
    public function import(?string $sourceDirectory = null): array
    {
        $sourceDirectory ??= base_path('../Entry Book/data');

        $files = [
            'entries' => $sourceDirectory.'/entries.xml',
            'instructions' => $sourceDirectory.'/instructions.xml',
            'auth' => $sourceDirectory.'/auth.xml',
        ];

        foreach ($files as $file) {
            if (! is_file($file)) {
                throw new RuntimeException('Legacy XML file not found: '.$file);
            }
        }

        return DB::transaction(function () use ($files): array {
            return [
                'operators' => $this->importOperators($files['auth']),
                'entries' => $this->importEntries($files['entries']),
                'instructions' => $this->importInstructions($files['instructions']),
            ];
        });
    }

    private function xml(string $path): SimpleXMLElement
    {
        $previous = libxml_use_internal_errors(true);
        $xml = simplexml_load_file($path, SimpleXMLElement::class, LIBXML_NONET | LIBXML_NOBLANKS);
        $errors = libxml_get_errors();
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if ($xml === false) {
            $message = $errors ? trim($errors[0]->message) : 'Unknown XML parse error';
            throw new RuntimeException('Unable to parse '.$path.': '.$message);
        }

        return $xml;
    }

    private function importOperators(string $path): int
    {
        $xml = $this->xml($path);
        $count = 0;

        foreach ($xml->operator as $operator) {
            $legacyKey = 'auth.xml:operator:'.$count;
            $name = trim((string) $operator->name);
            $pin = trim((string) $operator->password);

            $user = User::firstOrNew(['legacy_key' => $legacyKey]);
            $user->name = $name;
            $user->role = 'controller';

            if ($pin !== '' && (! $user->pin_hash || ! Hash::check($pin, $user->pin_hash))) {
                $user->pin_hash = Hash::make($pin);
            }

            $user->save();
            $count++;
        }

        return $count;
    }

    private function importEntries(string $path): int
    {
        $xml = $this->xml($path);
        $count = 0;

        foreach ($xml->entry as $entry) {
            $legacyId = trim((string) $entry['id']);
            $date = trim((string) $entry->date);
            $obNumber = trim((string) $entry->ob_number);
            $customer = trim((string) $entry->customer);
            $text = trim((string) $entry->obentry);

            $legacyKey = 'entries.xml:'.hash('sha256', implode('|', [
                (string) $count,
                $legacyId,
                $date,
                $obNumber,
                $customer,
                $text,
            ]));

            OccurrenceEntry::updateOrCreate(
                ['legacy_key' => $legacyKey],
                [
                    'legacy_id' => $legacyId !== '' ? $legacyId : null,
                    'ob_number' => $obNumber,
                    'occurred_on' => $date,
                    'customer' => $customer !== '' ? $customer : null,
                    'entry_text' => $text,
                ],
            );

            $count++;
        }

        return $count;
    }

    private function importInstructions(string $path): int
    {
        $xml = $this->xml($path);
        $count = 0;

        foreach ($xml->instruction as $item) {
            $legacyId = trim((string) $item['id']);
            $manager = trim((string) $item->manager);
            $entryTime = trim((string) $item->entry_time);
            $identity = $legacyId !== '' ? $legacyId : 'index:'.$count;

            $instruction = ManagementInstruction::updateOrCreate(
                ['legacy_id' => $identity],
                [
                    'instruction_date' => trim((string) $item->date),
                    'manager_name' => $manager !== '' ? $manager : 'Legacy management',
                    'instruction_text' => trim((string) $item->instruction_text),
                    'legacy_entry_time' => $entryTime !== '' ? $entryTime : null,
                ],
            );

            $acknowledgedBy = trim((string) $item->ackop);
            if ($acknowledgedBy !== '' && strtolower($acknowledgedBy) !== 'none') {
                $operator = User::query()
                    ->where('role', 'controller')
                    ->whereRaw('LOWER(name) = ?', [strtolower($acknowledgedBy)])
                    ->first();

                if (! $instruction->acknowledgements()->where('operator_name', $acknowledgedBy)->exists()) {
                    $instruction->acknowledgements()->create([
                        'user_id' => $operator?->id,
                        'operator_name' => $acknowledgedBy,
                        'acknowledged_at' => null,
                    ]);
                }
            }

            $count++;
        }

        return $count;
    }
}
