<?php

namespace App\Services;

use App\Models\OccurrenceEntry;
use Carbon\CarbonInterface;

class ObNumberGenerator
{
    public function next(CarbonInterface $date): string
    {
        $suffix = '\\'.$date->month.'\\'.$date->year;

        $highest = OccurrenceEntry::query()
            ->where('ob_number', 'like', '%'.$suffix)
            ->get(['ob_number'])
            ->map(function (OccurrenceEntry $entry): int {
                if (preg_match('/^(\d+)\\\\/', $entry->ob_number, $matches) !== 1) {
                    return 0;
                }

                return (int) $matches[1];
            })
            ->max() ?? 0;

        return ($highest + 1).$suffix;
    }
}
