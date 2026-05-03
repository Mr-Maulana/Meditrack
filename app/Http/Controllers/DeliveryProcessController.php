<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\DeliveryAssessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class DeliveryProcessController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if (!$user->isKurir() && !$user->isAdmin()) {
            abort(403, 'Hanya kurir yang dapat mengakses halaman ini.');
        }

        $availableDeliveries = Delivery::with(['patient', 'assessment'])
            ->where('courier_id', $user->id)
            ->whereIn('status', ['pending', 'on_delivery'])
            ->whereDoesntHave('assessment', function ($query) {
                $query->where('assessment_status', 'in_progress');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $inProgressDeliveries = Delivery::with(['patient', 'assessment'])
            ->where('courier_id', $user->id)
            ->whereHas('assessment', function ($query) {
                $query->where('assessment_status', 'in_progress');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('delivery-process.index', compact('availableDeliveries', 'inProgressDeliveries'));
    }

    public function selectDelivery(Request $request)
    {
        $request->validate([
            'delivery_id' => 'required|exists:deliveries,id',
        ]);

        try {
            $delivery = Delivery::findOrFail($request->delivery_id);
            
            if ($delivery->courier_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Anda tidak ditugaskan untuk pengantaran ini.'
                ], 403);
            }

            if (!in_array($delivery->status, ['pending', 'on_delivery'])) {
                return response()->json([
                    'success' => false,
                    'error' => 'Pengantaran ini tidak dapat diproses.'
                ], 400);
            }

            if ($delivery->assessment && $delivery->assessment->assessment_status === 'in_progress') {
                return response()->json([
                    'success' => false,
                    'error' => 'Pengantaran ini sedang dalam proses.'
                ], 400);
            }

            $assessment = DeliveryAssessment::updateOrCreate(
                ['delivery_id' => $delivery->id],
                [
                    'courier_id' => Auth::id(),
                    'start_time' => now(),
                    'assessment_status' => 'in_progress',
                ]
            );

            $delivery->update(['status' => 'on_delivery']);

            Log::info('Delivery selected', [
                'user_id' => Auth::id(),
                'delivery_id' => $delivery->id,
                'assessment_id' => $assessment->id
            ]);

            return response()->json([
                'success' => true,
                'assessment_id' => $assessment->id,
                'redirect_url' => route('delivery-process.route', $assessment->id),
            ]);

        } catch (\Exception $e) {
            Log::error('Error selecting delivery', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan saat memilih pengantaran.'
            ], 500);
        }
    }

    public function showRoute($assessmentId)
    {
        try {
            $assessment = DeliveryAssessment::with(['delivery.patient'])
                ->findOrFail($assessmentId);
            
            if ($assessment->courier_id !== Auth::id()) {
                abort(403, 'Anda tidak memiliki akses ke pengantaran ini.');
            }

            if ($assessment->assessment_status !== 'in_progress') {
                return redirect()
                    ->route('delivery-process.index')
                    ->with('error', 'Pengantaran tidak dalam status proses.');
            }

            return view('delivery-process.route', compact('assessment'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()
                ->route('delivery-process.index')
                ->with('error', 'Data pengantaran tidak ditemukan.');
        }
    }

    public function startDelivery($assessmentId)
    {
        try {
            $assessment = DeliveryAssessment::findOrFail($assessmentId);
            
            if ($assessment->courier_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized'
                ], 403);
            }

            if (!$assessment->start_time) {
                $assessment->update(['start_time' => now()]);
                
                Log::info('Delivery started', [
                    'assessment_id' => $assessmentId,
                    'user_id' => Auth::id()
                ]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Error starting delivery', [
                'assessment_id' => $assessmentId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan saat memulai pengantaran.'
            ], 500);
        }
    }

    public function updateLocation(Request $request, $assessmentId)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'distance_km' => 'nullable|numeric|min:0',
            'estimated_minutes' => 'nullable|integer|min:0',
        ]);

        try {
            $assessment = DeliveryAssessment::with('delivery')->findOrFail($assessmentId);
            
            if ($assessment->courier_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized'
                ], 403);
            }

            // DO NOT overwrite delivery destination coordinates with courier's current location
            // If live tracking is needed, it should be saved in a separate table or column (e.g. courier_latitude)
            
            $assessment->update([
                'distance_km' => $request->distance_km,
                'estimated_minutes' => $request->estimated_minutes,
            ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Error updating location', [
                'assessment_id' => $assessmentId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan saat memperbarui lokasi.'
            ], 500);
        }
    }

    public function markArrival($assessmentId)
    {
        try {
            $assessment = DeliveryAssessment::findOrFail($assessmentId);
            
            if ($assessment->courier_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized'
                ], 403);
            }

            $assessment->update([
                'arrival_time' => now(),
            ]);

            Log::info('Delivery arrival marked', [
                'assessment_id' => $assessmentId,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Anda telah tiba di lokasi.',
                'redirect_url' => route('delivery-process.assessment', $assessmentId),
            ]);

        } catch (\Exception $e) {
            Log::error('Error marking arrival', [
                'assessment_id' => $assessmentId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan saat menandai kedatangan.'
            ], 500);
        }
    }

    public function showAssessment($assessmentId)
    {
        try {
            $assessment = DeliveryAssessment::with(['delivery.patient', 'delivery.prescription'])
                ->findOrFail($assessmentId);
            
            if ($assessment->courier_id !== Auth::id()) {
                abort(403, 'Anda tidak memiliki akses ke pengantaran ini.');
            }

            if (!$assessment->arrival_time) {
                return redirect()
                    ->route('delivery-process.route', $assessmentId)
                    ->with('error', 'Anda belum menandai kedatangan di lokasi.');
            }

            return view('delivery-process.assessment', compact('assessment'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()
                ->route('delivery-process.index')
                ->with('error', 'Data pengantaran tidak ditemukan.');
        }
    }

    public function submitAssessment(Request $request, $assessmentId)
    {
        $request->validate([
            'patient_verified' => 'required',
            'patient_condition' => 'required|in:baik,sedang,buruk',
            'medication_understood' => 'required',
            'side_effects_explained' => 'required',
            'patient_feedback' => 'nullable|string|max:1000',
            'special_notes' => 'nullable|string|max:1000',
            'handover_photo' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'signature_image' => 'nullable|string',
            'final_verification' => 'required',
        ]);

        try {
            $assessment = DeliveryAssessment::with('delivery')->findOrFail($assessmentId);
            
            // Authorization check
            if ($assessment->delivery->courier_id !== Auth::id()) {
                Log::warning('Unauthorized assessment submission attempt', [
                    'assessment_id' => $assessmentId,
                    'user_id' => Auth::id(),
                    'courier_id' => $assessment->delivery->courier_id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak'
                ], 403);
            }

            // Upload handover photo
            $photoPath = null;
            if ($request->hasFile('handover_photo')) {
                try {
                    $photoPath = $this->uploadHandoverPhoto($request->file('handover_photo'), $assessmentId);
                } catch (\Exception $e) {
                    Log::error('Failed to upload photo', ['error' => $e->getMessage()]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal mengunggah foto: ' . $e->getMessage()
                    ], 422);
                }
            }
            
            // Save signature if provided
            $signaturePath = null;
            if ($request->filled('signature_image')) {
                try {
                    $signaturePath = $this->saveSignatureImage($request->signature_image, $assessmentId);
                } catch (\Exception $e) {
                    Log::warning('Failed to save signature', ['error' => $e->getMessage()]);
                    // Continue without signature
                }
            }

            // Prepare data for update
            $updateData = [
                'patient_condition' => $request->patient_condition,
                'medication_understood' => true,
                'side_effects_explained' => true,
                'patient_feedback' => $request->patient_feedback,
                'special_notes' => $request->special_notes,
                'handover_photo' => $photoPath,
                'signature_image' => $signaturePath,
                'assessment_status' => 'completed',
                'handover_time' => now(),
            ];

            // Update assessment
            $assessment->update($updateData);

            // Update delivery
            $assessment->delivery->update([
                'status' => 'delivered',
                'delivered_at' => now(),
                'proof_image' => $photoPath,
            ]);

            Log::info('Assessment submitted successfully', [
                'assessment_id' => $assessmentId,
                'courier_id' => Auth::id(),
                'has_signature' => !is_null($signaturePath),
                'photo_path' => $photoPath
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Assesmen berhasil dikirim',
                'redirect_url' => route('delivery-process.complete', $assessmentId),
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Assessment not found', ['assessment_id' => $assessmentId]);
            return response()->json([
                'success' => false,
                'message' => 'Assesmen tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error submitting assessment', [
                'assessment_id' => $assessmentId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim assesmen: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showComplete($assessmentId)
    {
        try {
            $assessment = DeliveryAssessment::with(['delivery.patient'])->findOrFail($assessmentId);
            
            if ($assessment->courier_id !== Auth::id()) {
                abort(403, 'Anda tidak memiliki akses ke pengantaran ini.');
            }

            if ($assessment->assessment_status !== 'completed') {
                return redirect()
                    ->route('delivery-process.index')
                    ->with('error', 'Assesmen belum diselesaikan.');
            }

            return view('delivery-process.complete', compact('assessment'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()
                ->route('delivery-process.index')
                ->with('error', 'Data pengantaran tidak ditemukan.');
        }
    }

    public function cancelDelivery($assessmentId)
    {
        try {
            $assessment = DeliveryAssessment::with('delivery')->findOrFail($assessmentId);
            
            if ($assessment->courier_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized'
                ], 403);
            }

            $assessment->delivery->update(['status' => 'pending']);
            $assessment->delete();

            Log::info('Delivery cancelled', [
                'assessment_id' => $assessmentId,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengantaran berhasil dibatalkan.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error cancelling delivery', [
                'assessment_id' => $assessmentId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan saat membatalkan pengantaran.'
            ], 500);
        }
    }

    public function getDeliveryDetails($deliveryId)
    {
        try {
            $delivery = Delivery::with(['patient', 'prescription'])->findOrFail($deliveryId);
            
            if ($delivery->courier_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'delivery' => $delivery,
                'patient' => $delivery->patient,
                'prescription' => $delivery->prescription,
                'address' => $delivery->delivery_address,
                'coordinates' => [
                    'latitude' => $delivery->latitude,
                    'longitude' => $delivery->longitude,
                ],
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Data pengantaran tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error getting delivery details', [
                'delivery_id' => $deliveryId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan saat mengambil data pengantaran.'
            ], 500);
        }
    }

    public function calculateRoute(Request $request, $assessmentId)
    {
        $request->validate([
            'current_lat' => 'required|numeric|between:-90,90',
            'current_lng' => 'required|numeric|between:-180,180',
        ]);

        try {
            $assessment = DeliveryAssessment::with(['delivery'])->findOrFail($assessmentId);
            
            if ($assessment->courier_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized'
                ], 403);
            }

            $distanceKm = $this->calculateDistance(
                $request->current_lat,
                $request->current_lng,
                $assessment->delivery->latitude,
                $assessment->delivery->longitude
            );
            
            $estimatedMinutes = round(($distanceKm / 30) * 60);

            return response()->json([
                'success' => true,
                'distance_km' => round($distanceKm, 2),
                'estimated_minutes' => $estimatedMinutes,
                'destination' => [
                    'latitude' => $assessment->delivery->latitude,
                    'longitude' => $assessment->delivery->longitude,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error calculating route', [
                'assessment_id' => $assessmentId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan saat menghitung rute.'
            ], 500);
        }
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;

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

    private function uploadHandoverPhoto($photo, $assessmentId)
    {
        try {
            $filename = 'handover_' . time() . '_' . $assessmentId . '.' . $photo->getClientOriginalExtension();
            $path = 'delivery-proofs/' . $filename;
            
            // Store file langsung tanpa kompresi
            Storage::disk('public')->putFileAs('delivery-proofs', $photo, $filename);
            
            Log::info('Handover photo uploaded', ['path' => $path, 'size' => $photo->getSize()]);
            
            return $path;
        } catch (\Exception $e) {
            Log::error('Error uploading handover photo', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    private function saveSignatureImage($base64Image, $assessmentId)
    {
        try {
            if (empty($base64Image)) {
                throw new \Exception('Signature image kosong');
            }

            if (strpos($base64Image, 'data:image/png;base64,') === 0) {
                $base64Image = substr($base64Image, strlen('data:image/png;base64,'));
            } elseif (strpos($base64Image, 'data:image/jpeg;base64,') === 0) {
                $base64Image = substr($base64Image, strlen('data:image/jpeg;base64,'));
            } elseif (strpos($base64Image, 'data:image/') === 0) {
                $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
            }
            
            $base64Image = str_replace([' ', '\n', '\r'], ['+', '', ''], $base64Image);
            $imageData = base64_decode($base64Image, true);
            
            if ($imageData === false || strlen($imageData) < 100) {
                throw new \Exception('Base64 signature tidak valid');
            }
            
            $filename = 'signature_' . time() . '_' . $assessmentId . '.png';
            $path = 'signatures/' . $filename;
            
            Storage::disk('public')->put($path, $imageData);
            
            Log::info('Signature saved', ['path' => $path, 'size' => strlen($imageData)]);
            
            return $path;
        } catch (\Exception $e) {
            Log::error('Error saving signature', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function myDeliveries(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isKurir()) {
            abort(403, 'Hanya kurir yang dapat mengakses halaman ini.');
        }

        $query = Delivery::with(['patient', 'assessment'])
            ->where('courier_id', $user->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('delivery_date', $request->date);
        }

        $deliveries = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('delivery-process.my-deliveries', compact('deliveries'));
    }

    public function myDeliveryDetail($deliveryId)
    {
        $user = Auth::user();
        
        if (!$user->isKurir()) {
            abort(403, 'Hanya kurir yang dapat mengakses halaman ini.');
        }

        $delivery = Delivery::with(['patient', 'prescription', 'assessment'])
            ->where('id', $deliveryId)
            ->where('courier_id', $user->id)
            ->firstOrFail();

        return view('delivery-process.my-delivery-detail', compact('delivery'));
    }
}