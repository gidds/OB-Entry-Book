<?php

namespace App\Services;

use App\Models\OccurrenceEntry;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class ObNumberGenerator
{
    public function next(CarbonInterface $date): string
    {
        $year = (int) $date->year;
        $month = (int) $date->month;
        $suffix = '\\'.$month.'\\'.$year;

        $historicalMaximum = OccurrenceEntry::query()
            ->where('ob_number', 'like', '%'.$suffix)
            ->get(['ob_number'])
            ->map(function (OccurrenceEntry $entry): int {
                if (preg_match('/^(\d+)\\\\/', $entry->ob_number, $matches) !== 1) {
                    return 0;
                }

                return (int) $matches[1];
            })
            ->max() ?? 0;

        DB::table('ob_sequences')->insertOrIgnore([
            'year' => $year,
            'month' => $month,
            'last_number' => $historicalMaximum,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $sequence = DB::table('ob_sequences')
            ->where('year', $year)
            ->where('month', $month)
            ->lockForUpdate()
            ->first();

        $next = max((int) $sequence->last_number, $historicalMaximum) + 1;

        DB::table('ob_sequences')
            ->where('id', $sequence->id)
            ->update([
                'last_number' => $next,
                'updated_at' => now(),
            ]);

        return $next.$suffix;
    }
}
