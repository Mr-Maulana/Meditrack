<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'prescription_id',
        'courier_id',
        'priority',
        'status',
        'delivery_address',
        'latitude',
        'longitude',
        'current_latitude',
        'current_longitude',
        'distance_traveled',
        'departure_time',
        'arrival_time',
        'delivery_date',
        'notes',
        'proof_image',
        'signature', // Tambah ini
        'recipient_name', // Tambah ini
        'recipient_relation', // Tambah ini
        'recipient_phone', // Tambah ini
        'receiver_name',
        'receiver_phone',
        'receiver_signature',
        'delivery_notes',
        'delivery_status',
        'delivered_at',
        'arrived_at', // Tambah ini untuk tracking waktu sampai
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'delivered_at' => 'datetime',
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
        'distance_traveled' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'current_latitude' => 'decimal:8',
        'current_longitude' => 'decimal:8',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function courier()
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    public function getDeliveryProgressAttribute()
    {
        if (!$this->departure_time) {
            return 0;
        }

        if ($this->arrival_time) {
            return 100;
        }

        // Calculate progress based on time (simplified)
        $totalMinutes = 60; // Estimated 60 minutes delivery
        $elapsedMinutes = now()->diffInMinutes($this->departure_time);
        
        return min(90, ($elapsedMinutes / $totalMinutes) * 100);
    }

    public function getEstimatedArrivalAttribute()
    {
        if (!$this->departure_time) {
            return null;
        }

        // Add estimated 60 minutes to departure time
        return $this->departure_time->addMinutes(60);
    }

    public function isNearDestination($threshold = 0.1) // 100 meters
    {
        if (!$this->latitude || !$this->longitude || !$this->current_latitude || !$this->current_longitude) {
            return false;
        }

        $distance = $this->calculateDistance(
            $this->latitude,
            $this->longitude,
            $this->current_latitude,
            $this->current_longitude
        );

        return $distance <= $threshold;
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }

    // Tambahkan accessor untuk signature URL
    public function getSignatureUrlAttribute()
    {
        return $this->signature ? Storage::url($this->signature) : null;
    }

    public function getProofImageUrlAttribute()
    {
        return $this->proof_image ? Storage::url($this->proof_image) : null;
    }

    // Tambahkan method untuk delivery completion
    public function markAsDelivered($data)
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
            'recipient_name' => $data['recipient_name'],
            'recipient_relation' => $data['recipient_relation'],
            'recipient_phone' => $data['recipient_phone'] ?? null,
            'notes' => $data['delivery_notes'] ?? $this->notes,
            'proof_image' => $data['proof_image'] ?? $this->proof_image,
            'signature' => $data['signature'] ?? $this->signature,
        ]);
    }

    public function markAsArrived()
    {
        $this->update([
            'arrived_at' => now(),
        ]);
    }

    public function assessment()
    {
        return $this->hasOne(DeliveryAssessment::class);
    }

    public function hasAssessment()
    {
        return $this->assessment()->exists();
    }

    public function isInDeliveryProcess()
    {
        return $this->assessment()->inProgress()->exists();
    }

    public function getCurrentAssessmentAttribute()
    {
        return $this->assessment()->first();
    }
}