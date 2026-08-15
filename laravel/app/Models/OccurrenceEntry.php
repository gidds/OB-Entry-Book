<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OccurrenceEntry extends Model
{
    protected $fillable = [
        'ob_number',
        'occurred_on',
        'customer',
        'entry_text',
        'legacy_id',
    ];

    protected function casts(): array
    {
        return [
            'occurred_on' => 'date',
        ];
    }
}
