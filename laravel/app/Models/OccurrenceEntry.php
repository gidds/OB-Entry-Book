<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OccurrenceEntry extends Model
{
    public const EDIT_WINDOW_MINUTES = 60;

    protected $fillable = [
        'ob_number',
        'occurred_on',
        'customer',
        'entry_text',
        'legacy_id',
        'legacy_key',
    ];

    protected function casts(): array
    {
        return [
            'occurred_on' => 'date',
        ];
    }

    public function isEditable(): bool
    {
        return $this->created_at !== null
            && now()->lt($this->created_at->copy()->addMinutes(self::EDIT_WINDOW_MINUTES));
    }
}
