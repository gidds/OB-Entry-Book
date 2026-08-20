<?php

namespace App\Http\Controllers;

use App\Models\OccurrenceEntry;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use XMLWriter;

class OccurrenceExportController extends Controller
{
    public function create(): View
    {
        abort_unless(auth()->user()?->isManagement(), 403);

        return view('entries.export');
    }

    public function store(Request $request): Response
    {
        abort_unless(auth()->user()?->isManagement(), 403);

        $validated = $request->validate([
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
        ]);

        $entries = OccurrenceEntry::query()
            ->whereBetween('occurred_on', [$validated['from_date'], $validated['to_date']])
            ->orderBy('occurred_on')
            ->orderBy('id')
            ->get();

        $xml = new XMLWriter();
        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');
        $xml->setIndent(true);
        $xml->startElement('occurrence-book');
        $xml->writeAttribute('from-date', $validated['from_date']);
        $xml->writeAttribute('to-date', $validated['to_date']);
        $xml->writeAttribute('exported-at', now()->toIso8601String());

        foreach ($entries as $entry) {
            $xml->startElement('entry');
            $xml->writeElement('ob-number', $entry->ob_number);
            $xml->writeElement('date', $entry->occurred_on->format('Y-m-d'));
            $xml->writeElement('time', $entry->created_at?->format('H:i:s') ?? '');
            $xml->writeElement('customer', $entry->customer ?? '');
            $xml->writeElement('text', $entry->entry_text);
            $xml->endElement();
        }

        $xml->endElement();
        $xml->endDocument();

        $filename = sprintf('ob-export-%s-to-%s.xml', $validated['from_date'], $validated['to_date']);

        return response($xml->outputMemory(), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
