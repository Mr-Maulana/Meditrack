<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'profession',
        'employee_id',
        'address',
        'gender',
        'profile_photo',
        'is_active',
        'last_seen',
    ];

    public function isOnline()
    {
        return $this->last_seen && $this->last_seen->gt(now()->subMinutes(5));
    }

    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo 
            ? asset('storage/' . $this->profile_photo) 
            : asset('images/default-avatar.png');
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_seen' => 'datetime',
    ];

    // Role Check Methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isApoteker()
    {
        return $this->role === 'apoteker';
    }

    public function isKurir()
    {
        return $this->role === 'kurir';
    }

    // Authorization Methods
    public function canManageUsers()
    {
        return $this->isAdmin();
    }

    public function canManagePatients()
    {
        return $this->isAdmin() || $this->isApoteker();
    }

    public function canCreatePrescription()
    {
        return $this->isAdmin() || $this->isApoteker();
    }

    public function canCreateDelivery()
    {
        return $this->isAdmin() || $this->isApoteker();
    }

    public function canDeliverPackage()
    {
        return $this->isAdmin() || $this->isKurir();
    }

    public function canEditProfile()
    {
        return true; // Semua role bisa edit profil
    }

    // Relationships
    public function patients()
    {
        return $this->hasMany(Patient::class, 'created_by');
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'courier_id');
    }

    public function createdDeliveries()
    {
        return $this->hasMany(Delivery::class, 'created_by');
    }

    public function assessments()
    {
        return $this->hasMany(DeliveryAssessment::class, 'courier_id');
    }

    public function getCreatePatientRoute()
    {
        if ($this->isAdmin() || $this->isApoteker()) {
            return route('patients.create');
        }
        return '#';
    }

    public function getPatientsIndexRoute()
    {
        if ($this->isAdmin() || $this->isApoteker()) {
            return route('patients.index');
        }
        return '#';
    }

    public function getCreateDeliveryRoute()
    {
        if ($this->isAdmin() || $this->isApoteker()) {
            return route('deliveries.create');
        }
        return '#';
    }

    public function getDeliveriesIndexRoute()
    {
        if ($this->isAdmin() || $this->isApoteker()) {
            return route('deliveries.index');
        } elseif ($this->isKurir()) {
            return route('delivery-process.index');
        }
        return '#';
    }

    public function getCreatePrescriptionRoute()
    {
        if ($this->isAdmin() || $this->isApoteker()) {
            return route('prescriptions.create');
        }
        return '#';
    }

    public function getPrescriptionsIndexRoute()
    {
        if ($this->isAdmin() || $this->isApoteker()) {
            return route('prescriptions.index');
        }
        return '#';
    }

    public function getReportsIndexRoute()
    {
        if ($this->isAdmin() || $this->isApoteker()) {
            return route('reports.index');
        }
        return '#';
    }

    public function getUsersIndexRoute()
    {
        if ($this->isAdmin()) {
            return route('users.index');
        }
        return '#';
    }
}