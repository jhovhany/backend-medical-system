<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'consultation_id',
        'issued_by',
        'medications',
        'instructions',
        'valid_until',
    ];

    protected function casts(): array
    {
        return [
            'medications' => 'array',
            'valid_until' => 'date',
        ];
    }

    // --- Relationships ---

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}
