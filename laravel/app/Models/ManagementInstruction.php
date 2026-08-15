<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ManagementInstruction extends Model
{
    protected $fillable = [
        'instruction_date',
        'manager_id',
        'manager_name',
        'instruction_text',
        'legacy_id',
        'legacy_entry_time',
    ];

    protected function casts(): array
    {
        return [
            'instruction_date' => 'date',
            'legacy_entry_time' => 'datetime',
        ];
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function acknowledgements(): HasMany
    {
        return $this->hasMany(InstructionAcknowledgement::class);
    }
}
