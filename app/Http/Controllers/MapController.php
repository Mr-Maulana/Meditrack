<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MapController extends Controller
{
    public function showMap()
    {
        $user = Auth::user();
        
        if ($user->isKurir()) {
            $deliveries = Delivery::where('courier_id', $user->id)
                ->whereIn('status', ['on_delivery', 'pending'])
                ->with('patient')
                ->get();
            
            $locations = $deliveries->map(function ($delivery) {
                return [
                    'id' => $delivery->id,
                    'patient_id' => $delivery->patient->id,
                    'patient_name' => $delivery->patient->name,
                    'address' => $delivery->delivery_address,
                    'latitude' => $delivery->latitude ?? $delivery->patient->latitude,
                    'longitude' => $delivery->longitude ?? $delivery->patient->longitude,
                    'status' => $delivery->status,
                    'priority' => $delivery->priority,
                    'patient_phone' => $delivery->patient->phone,
                    'delivery_id' => $delivery->id,
                ];
            });
        } else {
            $patients = Patient::when(!$user->isAdmin(), function ($query) use ($user) {
                    return $query->where('created_by', $user->id);
                })
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->get();
            
            $locations = $patients->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'patient_id' => $patient->id,
                    'patient_name' => $patient->name,
                    'address' => $patient->address,
                    'latitude' => $patient->latitude,
                    'longitude' => $patient->longitude,
                    'patient_phone' => $patient->phone,
                ];
            });
        }

        return view('map.index', compact('locations'));
    }

    public function getTodayDeliveries()
    {
        $user = Auth::user();
        $today = now()->format('Y-m-d');
        
        $deliveries = Delivery::with(['patient', 'prescription'])
            ->when($user->isKurir(), function ($query) use ($user) {
                return $query->where('courier_id', $user->id);
            })
            ->where('delivery_date', $today)
            ->whereIn('status', ['on_delivery', 'pending'])
            ->get();
        
        return response()->json($deliveries);
    }

    public function startNavigation(Delivery $delivery)
    {
        // Authorization check
        if (Auth::id() !== $delivery->courier_id) {
            abort(403, 'Unauthorized action.');
        }

        // Redirect to real-time navigation
        return redirect()->route('map.navigate.real', $delivery);
    }

    public function updateDeliveryStatus(Request $request, Delivery $delivery)
    {
        $request->validate([
            'status' => 'required|in:arrived,delivered,failed',
            'proof_image' => 'nullable|image|max:2048',
            'recipient_name' => 'required_if:status,delivered|string|max:255',
            'recipient_relation' => 'required_if:status,delivered|string|max:100',
            'recipient_phone' => 'nullable|string|max:20',
            'delivery_notes' => 'nullable|string',
            'signature' => 'nullable|string',
            'failure_reason' => 'required_if:status,failed|nullable|string',
            'failure_notes' => 'nullable|string',
        ]);

        // Authorization check
        if (Auth::id() !== $delivery->courier_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notes = $request->delivery_notes;
        if ($request->status == 'failed') {
            $reasonLabels = [
                'pasien_tidak_ada' => 'Pasien tidak ada di lokasi',
                'alamat_salah' => 'Alamat tidak ditemukan',
                'lainnya' => 'Lainnya',
            ];
            $reason = $reasonLabels[$request->failure_reason] ?? $request->failure_reason;
            $failureNotes = $request->failure_notes;
            
            $notes = "Gagal Kirim: " . $reason;
            if ($failureNotes) {
                $notes .= " (" . $failureNotes . ")";
            }
            if ($request->delivery_notes) {
                $notes .= ". Catatan: " . $request->delivery_notes;
            }
        }

        $data = [
            'status' => $request->status == 'arrived' ? 'on_delivery' : 
                       ($request->status == 'delivered' ? 'delivered' : 'failed'),
            'notes' => $notes,
        ];

        if ($request->status == 'delivered') {
            $data['delivered_at'] = now();
            $data['recipient_name'] = $request->recipient_name;
            $data['recipient_relation'] = $request->recipient_relation;
            $data['recipient_phone'] = $request->recipient_phone;
            
            if ($request->hasFile('proof_image')) {
                $data['proof_image'] = $request->file('proof_image')->store('delivery-proofs', 'public');
            }
            
            if ($request->signature) {
                $signaturePath = 'signatures/delivery-' . $delivery->id . '-' . time() . '.png';
                $signature = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->signature));
                Storage::disk('public')->put($signaturePath, $signature);
                $data['signature'] = $signaturePath;
            }
        }

        $delivery->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Status pengantaran berhasil diperbarui',
            'delivery' => $delivery->fresh()
        ]);
    }

    public function getNavigationRoute($deliveryId)
    {
        $delivery = Delivery::with('patient')->findOrFail($deliveryId);
        
        // Authorization check
        if (Auth::id() !== $delivery->courier_id) {
            abort(403, 'Unauthorized action.');
        }

        // In real app, you'd use Google Maps Directions API
        // For now, return simplified route data
        $routeData = [
            'origin' => [
                'latitude' => request('current_lat'),
                'longitude' => request('current_lng'),
            ],
            'destination' => [
                'latitude' => $delivery->latitude ?? $delivery->patient->latitude,
                'longitude' => $delivery->longitude ?? $delivery->patient->longitude,
            ],
            'distance' => '5.2 km', // Example distance
            'duration' => '15 menit', // Example duration
            'steps' => [
                ['instruction' => 'Belok kiri ke Jl. Sudirman', 'distance' => '200 m'],
                ['instruction' => 'Lurus sampai perempatan', 'distance' => '1.2 km'],
                ['instruction' => 'Belok kanan ke Jl. Gatot Subroto', 'distance' => '800 m'],
                ['instruction' => 'Sampai di tujuan', 'distance' => '3 km'],
            ]
        ];

        return response()->json($routeData);
    }

    public function completeDelivery(Delivery $delivery)
    {
        // Authorization check
        if (Auth::id() !== $delivery->courier_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('map.complete-delivery', compact('delivery'));
    }

    public function markAsArrived(Delivery $delivery)
    {
        // Authorization check
        if (Auth::id() !== $delivery->courier_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $delivery->update([
            'status' => 'on_delivery',
            'arrived_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status diperbarui: Sudah sampai di lokasi',
        ]);
    }

    public function getDeliveryDetails($deliveryId)
    {
        $delivery = Delivery::with(['patient', 'prescription'])->findOrFail($deliveryId);
        
        // Authorization check
        if (Auth::id() !== $delivery->courier_id) {
            abort(403, 'Unauthorized action.');
        }

        return response()->json($delivery);
    }

    public function getRouteToDestination(Request $request, $deliveryId)
    {
        $delivery = Delivery::with('patient')->findOrFail($deliveryId);
        
        // Authorization check
        if (Auth::id() !== $delivery->courier_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'current_lat' => 'required|numeric',
            'current_lng' => 'required|numeric',
        ]);

        // In production, use Google Maps Directions API
        // For demo, we'll simulate route data
        $origin = [
            'lat' => $request->current_lat,
            'lng' => $request->current_lng,
        ];
        
        $destination = [
            'lat' => $delivery->latitude ?? $delivery->patient->latitude ?? -6.2088,
            'lng' => $delivery->longitude ?? $delivery->patient->longitude ?? 106.8456,
        ];
        
        // Simulated route steps
        $routeData = $this->simulateRouteData($origin, $destination);
        
        return response()->json([
            'success' => true,
            'route' => $routeData,
            'delivery' => $delivery,
        ]);
    }

    private function simulateRouteData($origin, $destination)
    {
        // This is simulated data - in real app, use Google Maps API
        $distance = $this->calculateDistance(
            $origin['lat'], $origin['lng'],
            $destination['lat'], $destination['lng']
        );
        
        $duration = $distance / 833.33 * 60; // Assume average speed 50km/h
        
        return [
            'origin' => $origin,
            'destination' => $destination,
            'distance' => [
                'text' => number_format($distance / 1000, 1) . ' km',
                'value' => $distance,
            ],
            'duration' => [
                'text' => round($duration) . ' menit',
                'value' => round($duration * 60), // in seconds
            ],
            'steps' => [
                [
                    'instruction' => 'Mulai dari lokasi Anda',
                    'distance' => ['text' => '0 km', 'value' => 0],
                    'duration' => ['text' => '0 menit', 'value' => 0],
                    'start_location' => $origin,
                ],
                [
                    'instruction' => 'Belok kiri ke jalan utama',
                    'distance' => ['text' => '500 m', 'value' => 500],
                    'duration' => ['text' => '2 menit', 'value' => 120],
                    'start_location' => [
                        'lat' => $origin['lat'] + 0.002,
                        'lng' => $origin['lng'] + 0.002,
                    ],
                ],
                [
                    'instruction' => 'Terus lurus sepanjang jalan',
                    'distance' => ['text' => '2.5 km', 'value' => 2500],
                    'duration' => ['text' => '8 menit', 'value' => 480],
                    'start_location' => [
                        'lat' => $origin['lat'] + 0.005,
                        'lng' => $origin['lng'] + 0.008,
                    ],
                ],
                [
                    'instruction' => 'Belok kanan di perempatan',
                    'distance' => ['text' => '800 m', 'value' => 800],
                    'duration' => ['text' => '3 menit', 'value' => 180],
                    'start_location' => [
                        'lat' => $origin['lat'] + 0.01,
                        'lng' => $origin['lng'] + 0.015,
                    ],
                ],
                [
                    'instruction' => 'Sampai di tujuan',
                    'distance' => ['text' => '0 m', 'value' => 0],
                    'duration' => ['text' => '0 menit', 'value' => 0],
                    'start_location' => $destination,
                ],
            ],
            'polyline' => $this->generatePolyline($origin, $destination),
        ];
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $R = 6371000; // Earth's radius in meters
        $φ1 = deg2rad($lat1);
        $φ2 = deg2rad($lat2);
        $Δφ = deg2rad($lat2 - $lat1);
        $Δλ = deg2rad($lon2 - $lon1);

        $a = sin($Δφ/2) * sin($Δφ/2) +
            cos($φ1) * cos($φ2) *
            sin($Δλ/2) * sin($Δλ/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $R * $c;
    }

    private function generatePolyline($origin, $destination)
    {
        // Generate intermediate points for polyline
        $points = [];
        $steps = 10;
        
        for ($i = 0; $i <= $steps; $i++) {
            $lat = $origin['lat'] + ($destination['lat'] - $origin['lat']) * ($i / $steps);
            $lng = $origin['lng'] + ($destination['lng'] - $origin['lng']) * ($i / $steps);
            $points[] = [$lat, $lng];
        }
        
        return $points;
    }

    public function showRealTimeNavigation(Delivery $delivery)
    {
        // Authorization check
        if (Auth::id() !== $delivery->courier_id) {
            abort(403, 'Unauthorized action.');
        }

        $delivery->load(['patient', 'prescription']);
        
        return view('map.navigation-real', compact('delivery'));
    }
}