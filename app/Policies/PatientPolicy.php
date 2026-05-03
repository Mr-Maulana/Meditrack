<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PatientPolicy
{
    public function view(User $user, Patient $patient): bool
    {
        return $user->isAdmin() || $user->isApoteker() || $patient->created_by === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isApoteker();
    }

    public function update(User $user, Patient $patient): bool
    {
        return $user->isAdmin() || $user->isApoteker() || $patient->created_by === $user->id;
    }

    public function delete(User $user, Patient $patient): bool
    {
        return $user->isAdmin() || $user->isApoteker() || $patient->created_by === $user->id;
    }

    public function print(User $user, Patient $patient): bool
    {
        return $user->isAdmin() || $user->isApoteker() || $patient->created_by === $user->id;
    }

    public function addPrescription(User $user, Patient $patient): bool
    {
        return $user->isAdmin() || $user->isApoteker() || $patient->created_by === $user->id;
    }
}