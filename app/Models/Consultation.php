<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consultation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'medical_record_id',
        'doctor_id',
        'consultation_date',
        'reason',
        'diagnosis',
        'treatment',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'consultation_date' => 'datetime',
        ];
    }

    // --- Relationships ---

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function prescription(): HasOne
    {
        return $this->hasOne(Prescription::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }
}
