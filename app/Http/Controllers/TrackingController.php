<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TrackingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Start delivery tracking
    public function startDelivery(Delivery $delivery)
    {
        // Authorization check
        if (Auth::id() !== $delivery->courier_id) {
            abort(403, 'Unauthorized action.');
        }

        $delivery->update([
            'departure_time' => now(),
            'delivery_status' => 'in_transit',
            'status' => 'on_delivery',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengantaran dimulai',
            'departure_time' => $delivery->departure_time->format('H:i'),
        ]);
    }

    // Update courier's current location
    public function updateLocation(Request $request, Delivery $delivery)
    {
        // Authorization check
        if (Auth::id() !== $delivery->courier_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $delivery->update([
            'current_latitude' => $request->latitude,
            'current_longitude' => $request->longitude,
        ]);

        // Check if near destination
        if ($delivery->isNearDestination()) {
            $delivery->update([
                'delivery_status' => 'arrived',
                'arrival_time' => now(),
            ]);

            return response()->json([
                'success' => true,
                'arrived' => true,
                'message' => 'Anda telah sampai di lokasi tujuan',
            ]);
        }

        return response()->json([
            'success' => true,
            'arrived' => false,
            'distance' => $delivery->calculateDistance(
                $delivery->latitude,
                $delivery->longitude,
                $request->latitude,
                $request->longitude
            ),
        ]);
    }

    // Show delivery tracking page
    public function track(Delivery $delivery)
    {
        // Authorization check
        $user = Auth::user();
        if ($user->isKurir() && $delivery->courier_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        if ($user->isApoteker()) {
            $patientCreatorId = $delivery->patient->created_by;
            if ($patientCreatorId !== $user->id) {
                abort(403, 'Unauthorized action.');
            }
        }

        $delivery->load(['patient', 'courier', 'prescription']);
        return view('tracking.show', compact('delivery'));
    }

    // Show delivery completion form
    public function showCompleteForm(Delivery $delivery)
    {
        // Authorization check
        if (Auth::id() !== $delivery->courier_id) {
            abort(403, 'Unauthorized action.');
        }

        if ($delivery->delivery_status !== 'arrived') {
            return redirect()->route('tracking.track', $delivery)
                ->with('error', 'Anda belum sampai di lokasi tujuan');
        }

        $delivery->load('patient');
        return view('tracking.complete', compact('delivery'));
    }

    // Complete delivery with documentation
    public function completeDelivery(Request $request, Delivery $delivery)
    {
        // Authorization check
        if (Auth::id() !== $delivery->courier_id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'receiver_name' => 'required|string|max:255',
            'receiver_phone' => 'required|string|max:20',
            'proof_image' => 'required|image|max:2048',
            'delivery_notes' => 'nullable|string',
            'signature' => 'nullable|string', // For signature canvas
        ]);

        // Store proof image
        $proofImagePath = $request->file('proof_image')->store('delivery-proofs', 'public');

        // Store signature if provided
        $signaturePath = null;
        if ($request->signature) {
            $signaturePath = $this->storeSignature($request->signature, $delivery->id);
        }

        $delivery->update([
            'receiver_name' => $request->receiver_name,
            'receiver_phone' => $request->receiver_phone,
            'proof_image' => $proofImagePath,
            'receiver_signature' => $signaturePath,
            'delivery_notes' => $request->delivery_notes,
            'delivery_status' => 'delivered',
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);

        return redirect()->route('deliveries.show', $delivery)
            ->with('success', 'Pengantaran berhasil diselesaikan!');
    }

    // Store signature from canvas
    private function storeSignature($signatureData, $deliveryId)
    {
        $image = str_replace('data:image/png;base64,', '', $signatureData);
        $image = str_replace(' ', '+', $image);
        $imageName = 'signature-' . $deliveryId . '-' . time() . '.png';
        
        Storage::disk('public')->put('signatures/' . $imageName, base64_decode($image));
        
        return 'signatures/' . $imageName;
    }

    // Get delivery tracking data for map
    public function getTrackingData(Delivery $delivery)
    {
        // Authorization check
        $user = Auth::user();
        if ($user->isKurir() && $delivery->courier_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'delivery' => $delivery->load('patient'),
            'route' => [
                'start' => $delivery->courier ? [
                    'name' => 'Lokasi Kurir',
                    'lat' => $delivery->current_latitude,
                    'lng' => $delivery->current_longitude,
                ] : null,
                'end' => [
                    'name' => $delivery->patient->name,
                    'address' => $delivery->delivery_address,
                    'lat' => $delivery->latitude,
                    'lng' => $delivery->longitude,
                ],
            ],
            'progress' => $delivery->delivery_progress,
            'estimated_arrival' => $delivery->estimated_arrival?->format('H:i'),
            'status' => $delivery->delivery_status,
        ]);
    }

    // Get all active deliveries for courier
    public function getActiveDeliveries()
    {
        $user = Auth::user();
        
        if (!$user->isKurir()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $deliveries = Delivery::with('patient')
            ->where('courier_id', $user->id)
            ->whereIn('delivery_status', ['in_transit', 'arrived'])
            ->whereDate('delivery_date', today())
            ->get();

        return response()->json($deliveries);
    }
}