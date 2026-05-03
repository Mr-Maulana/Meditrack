<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Delivery;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = now()->format('Y-m-d');

        if ($user->isAdmin() || $user->isApoteker()) {
            $patients = Patient::with('creator')->latest()->take(5)->get();
            $deliveries = Delivery::with(['patient', 'courier'])->latest()->take(5)->get();
        } else { // Kurir
            $patients = collect();
            $deliveries = Delivery::where('courier_id', $user->id)
                ->where('delivery_date', $today)
                ->with('patient')
                ->latest()
                ->get();
        }

        // Statistics
        $stats = [
            'total_patients' => Patient::when(!$user->isAdmin() && !$user->isApoteker(), function ($query) use ($user) {
                return $query->where('created_by', $user->id);
            })->count(),
            'today_deliveries' => Delivery::where('delivery_date', $today)
                ->when($user->isKurir(), function ($query) use ($user) {
                    return $query->where('courier_id', $user->id);
                })
                ->count(),
            'pending_deliveries' => Delivery::where('status', 'pending')
                ->when($user->isKurir(), function ($query) use ($user) {
                    return $query->where('courier_id', $user->id);
                })
                ->count(),
            'delivered_count' => Delivery::where('status', 'delivered')
                ->when($user->isKurir(), function ($query) use ($user) {
                    return $query->where('courier_id', $user->id);
                })
                ->count(),
        ];

        return view('dashboard', compact('patients', 'deliveries', 'stats'));
    }
}