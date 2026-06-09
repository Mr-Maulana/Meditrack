<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'medications',
        'medication_name',
        'dosage',
        'frequency',
        'duration',
        'instructions',
    ];

    protected $casts = [
        'medications' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }

    // Helper methods
    public function getFormattedDosageAttribute()
    {
        return strtoupper($this->dosage);
    }

    public function getFrequencyTextAttribute()
    {
        $frequencyMap = [
            '1x1' => '1 kali sehari',
            '2x1' => '2 kali sehari',
            '3x1' => '3 kali sehari',
            '1x2' => '1 kali 2 tablet',
            '2x2' => '2 kali 2 tablet',
            '3x2' => '3 kali 2 tablet',
        ];

        return $frequencyMap[$this->frequency] ?? $this->frequency;
    }

    public function getDurationTextAttribute()
    {
        if (str_contains($this->duration, 'hari')) {
            return $this->duration;
        }
        
        if (is_numeric($this->duration)) {
            return $this->duration . ' hari';
        }
        
        return $this->duration;
    }

    public function scopeActive($query)
    {
        return $query->whereHas('patient', function ($q) {
            $q->whereHas('deliveries', function ($q2) {
                $q2->whereIn('status', ['pending', 'on_delivery']);
            });
        });
    }

    public function getMedicationList(): array
    {
        if (!empty($this->medications)) {
            return $this->medications;
        }

        if ($this->medication_name) {
            return [[
                'name' => $this->medication_name,
                'dosage' => $this->dosage,
                'frequency' => $this->frequency,
                'duration' => $this->duration,
                'instructions' => $this->instructions,
            ]];
        }

        return [];
    }

    public function getMedicationSummaryAttribute(): string
    {
        return collect($this->getMedicationList())
            ->pluck('name')
            ->filter()
            ->implode(', ') ?: '-';
    }
}