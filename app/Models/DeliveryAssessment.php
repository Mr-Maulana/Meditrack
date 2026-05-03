<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_id',
        'courier_id',
        'start_time',
        'arrival_time',
        'handover_time',
        'distance_km',
        'estimated_minutes',
        'actual_minutes',
        'patient_condition',
        'medication_understood',
        'side_effects_explained',
        'patient_feedback',
        'special_notes',
        'handover_photo',
        'signature_image',
        'assessment_status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'arrival_time' => 'datetime',
        'handover_time' => 'datetime',
        'medication_understood' => 'boolean',
        'side_effects_explained' => 'boolean',
    ];

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function courier()
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    public function scopeInProgress($query)
    {
        return $query->where('assessment_status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('assessment_status', 'completed');
    }

    public function getDurationMinutesAttribute()
    {
        if ($this->start_time && $this->handover_time) {
            return $this->start_time->diffInMinutes($this->handover_time);
        }
        return null;
    }

    public function getTravelTimeMinutesAttribute()
    {
        if ($this->start_time && $this->arrival_time) {
            return $this->start_time->diffInMinutes($this->arrival_time);
        }
        return null;
    }

    public function getHandoverTimeMinutesAttribute()
    {
        if ($this->arrival_time && $this->handover_time) {
            return $this->arrival_time->diffInMinutes($this->handover_time);
        }
        return null;
    }
}