<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = now()->format('Y-m-d');

        if ($user->isAdmin() || $user->isApoteker()) {
            $patients = Patient::select(['id', 'name', 'patient_code', 'phone', 'created_by', 'created_at'])
                ->with('creator:id,name')
                ->latest()
                ->take(5)
                ->get();

            $deliveries = Delivery::select(['id', 'patient_id', 'courier_id', 'status', 'priority', 'delivery_date', 'delivery_address', 'created_at'])
                ->with([
                    'patient:id,name',
                    'courier:id,name',
                ])
                ->latest()
                ->take(5)
                ->get();
        } else {
            $patients = collect();

            $deliveries = Delivery::select(['id', 'patient_id', 'courier_id', 'status', 'priority', 'delivery_date', 'delivery_address', 'created_at'])
                ->where('courier_id', $user->id)
                ->where('delivery_date', $today)
                ->with('patient:id,name')
                ->latest()
                ->get();
        }

        $stats = $this->getCachedStats($user, $today);

        return view('dashboard', compact('patients', 'deliveries', 'stats'));
    }

    private function getCachedStats(User $user, string $today): array
    {
        $cacheKey = "dashboard.stats.{$user->id}.{$today}";

        return Cache::remember($cacheKey, 120, function () use ($user, $today) {
            $patientQuery = Patient::query();
            if (! $user->isAdmin() && ! $user->isApoteker()) {
                $patientQuery->where('created_by', $user->id);
            }

            $deliveryQuery = Delivery::query();
            if ($user->isKurir()) {
                $deliveryQuery->where('courier_id', $user->id);
            }

            $deliveryStats = (clone $deliveryQuery)
                ->selectRaw(
                    'SUM(CASE WHEN delivery_date = ? THEN 1 ELSE 0 END) as today_deliveries,
                     SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending_deliveries,
                     SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as delivered_count',
                    [$today, 'pending', 'delivered']
                )
                ->first();

            return [
                'total_patients' => $patientQuery->count(),
                'today_deliveries' => (int) ($deliveryStats->today_deliveries ?? 0),
                'pending_deliveries' => (int) ($deliveryStats->pending_deliveries ?? 0),
                'delivered_count' => (int) ($deliveryStats->delivered_count ?? 0),
            ];
        });
    }
}
