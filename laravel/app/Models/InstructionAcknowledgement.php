<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructionAcknowledgement extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'management_instruction_id',
        'user_id',
        'operator_name',
        'acknowledged_at',
    ];

    protected function casts(): array
    {
        return [
            'acknowledged_at' => 'datetime',
        ];
    }

    public function instruction(): BelongsTo
    {
        return $this->belongsTo(ManagementInstruction::class, 'management_instruction_id');
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
