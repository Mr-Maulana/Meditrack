<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_code',
        'name',
        'email',
        'phone',
        'address',
        'latitude',
        'longitude',
        'date_of_birth',
        'gender',
        'diagnosis',
        'medical_condition',
        'allergies',
        'created_by',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($patient) {
            // Generate patient code if not provided
            if (empty($patient->patient_code)) {
                $patient->patient_code = 'PT' . date('Ymd') . str_pad(Patient::count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });

        static::created(function ($patient) {
            // Create initial delivery if prescriptions exist
            if ($patient->prescriptions()->exists()) {
                $patient->deliveries()->create([
                    'prescription_id' => $patient->prescriptions()->first()->id,
                    'delivery_address' => $patient->address,
                    'delivery_date' => now(),
                    'status' => 'pending',
                    'priority' => 'normal',
                ]);
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }

    public function latestDelivery()
    {
        return $this->hasOne(Delivery::class)->latestOfMany();
    }

    // Helper methods
    public function hasActiveDelivery()
    {
        return $this->deliveries()
            ->whereIn('status', ['pending', 'on_delivery'])
            ->exists();
    }

    public function getDeliveryStatusAttribute()
    {
        $latestDelivery = $this->deliveries()->latest()->first();
        return $latestDelivery ? $latestDelivery->status : 'no_delivery';
    }

    public function getPriorityAttribute()
    {
        $latestDelivery = $this->deliveries()->latest()->first();
        return $latestDelivery ? $latestDelivery->priority : 'normal';
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
    }

    // Dynamic geographic accessors
    public function getProvinceAttribute()
    {
        $parts = explode(', ', $this->address);
        return isset($parts[3]) ? trim($parts[3]) : '';
    }

    public function getCityAttribute()
    {
        $parts = explode(', ', $this->address);
        return isset($parts[2]) ? trim($parts[2]) : '';
    }

    public function getSubdistrictAttribute()
    {
        $parts = explode(', ', $this->address);
        if (!isset($parts[1])) {
            return '';
        }
        $sub = trim($parts[1]);
        return preg_replace('/^kecamatan\s+/i', '', $sub);
    }

    public function getVillageAttribute()
    {
        $parts = explode(', ', $this->address);
        if (!isset($parts[0])) {
            return '';
        }
        $vil = trim($parts[0]);
        return preg_replace('/^(desa|kelurahan|gampong)\s+/i', '', $vil);
    }
}