<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Patient;
use App\Models\User; // Tambah ini
use App\Models\Prescription; // Tambah ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\DeliveryAssigned;
use App\Notifications\DeliveryStatusUpdated;

class DeliveryController extends Controller
{
    

    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        
        $query = Delivery::with(['patient', 'courier'])
            ->when($user->isKurir(), function ($query) use ($user) {
                return $query->where('courier_id', $user->id);
            })
            ->when($search, function($query, $search) {
                return $query->whereHas('patient', function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                });
            });

        // Filter cloning for stats
        $statsQuery = clone $query;
        $pendingCount = (clone $statsQuery)->where('status', 'pending')->count();
        $processCount = (clone $statsQuery)->where('status', 'on_delivery')->count();
        $deliveredCount = (clone $statsQuery)->where('status', 'delivered')->count();

        $deliveries = $query
            ->when(request('status'), function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when(request('priority'), function ($query, $priority) {
                return $query->where('priority', $priority);
            })
            ->when(request('date'), function ($query, $date) {
                return $query->whereDate('delivery_date', $date);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('deliveries.index', compact('deliveries', 'pendingCount', 'processCount', 'deliveredCount'));
    }

    public function create()
    {
        $user = Auth::user();
        
        $patients = Patient::when(!$user->isAdmin() && !$user->isApoteker(), function ($query) use ($user) {
            return $query->where('created_by', $user->id);
        })
        ->with(['prescriptions' => function ($query) {
            $query->latest();
        }])
        ->get();

        $couriers = User::where('role', 'kurir')->get();

        return view('deliveries.create', compact('patients', 'couriers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'prescription_id' => 'required|exists:prescriptions,id',
            'courier_id' => 'nullable|exists:users,id',
            'priority' => 'required|in:normal,urgent',
            'delivery_address' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'delivery_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // Ensure prescription belongs to the patient
        $prescription = Prescription::where('id', $request->prescription_id)
            ->where('patient_id', $request->patient_id)
            ->first();
            
        if (!$prescription) {
            return redirect()->back()
                ->with('error', 'Resep obat tidak valid untuk pasien ini!')
                ->withInput();
        }

        $delivery = Delivery::create([
            'patient_id' => $request->patient_id,
            'prescription_id' => $prescription->id,
            'courier_id' => $request->courier_id,
            'priority' => $request->priority,
            'delivery_address' => $request->delivery_address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'delivery_date' => $request->delivery_date,
            'notes' => $request->notes,
            'status' => $request->courier_id ? 'on_delivery' : 'pending',
        ]);

        if ($delivery->courier_id) {
            $delivery->courier->notify(new DeliveryAssigned($delivery));
        }

        return redirect()->route('deliveries.index')
            ->with('success', 'Pengantaran berhasil ditambahkan!');
    }

    public function show(Delivery $delivery)
    {
        // Authorization check
        $user = Auth::user();
        if ($user->isKurir() && $delivery->courier_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
        if ($user->isApoteker()) {
            // Apoteker can see all, but maybe you want some restriction? 
            // The user said "sinkronkan data semuanya", so I'll allow full access.
            // Previously it was: return $user->isAdmin() || $delivery->patient->created_by === $user->id;
        }

        $delivery->load(['patient', 'prescription', 'courier']);
        return view('deliveries.show', compact('delivery'));
    }

    public function edit(Delivery $delivery)
    {
        // Authorization check
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isApoteker()) {
            abort(403, 'Unauthorized action.');
        }
        
        if ($user->isApoteker()) {
            // Full access as requested
        }

        $delivery->load(['patient', 'prescription']);
        $couriers = User::where('role', 'kurir')->get();
        
        return view('deliveries.edit', compact('delivery', 'couriers'));
    }

    public function update(Request $request, Delivery $delivery)
    {
        // Authorization check
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isApoteker()) {
            abort(403, 'Unauthorized action.');
        }
        
        if ($user->isApoteker()) {
            // Full access as requested
        }

        $request->validate([
            'courier_id' => 'nullable|exists:users,id',
            'priority' => 'required|in:normal,urgent',
            'delivery_address' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'delivery_date' => 'required|date',
            'status' => 'required|in:pending,on_delivery,delivered,failed',
            'notes' => 'nullable|string',
        ]);

        $oldCourierId = $delivery->courier_id;
        $oldStatus = $delivery->status;
        $delivery->update($request->all());

        if ($delivery->courier_id && $delivery->courier_id != $oldCourierId) {
            $delivery->courier->notify(new DeliveryAssigned($delivery));
        }

        if ($delivery->status != $oldStatus) {
            $admins = User::whereIn('role', ['admin', 'apoteker'])->get();
            Notification::send($admins, new DeliveryStatusUpdated($delivery));
        }

        return redirect()->route('deliveries.index')
            ->with('success', 'Data pengantaran berhasil diperbarui!');
    }

    public function destroy(Delivery $delivery)
    {
        // Authorization check
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isApoteker()) {
            abort(403, 'Unauthorized action.');
        }
        
        if ($user->isApoteker()) {
            // Full access as requested
        }

        $delivery->delete();

        return redirect()->route('deliveries.index')
            ->with('success', 'Pengantaran berhasil dihapus!');
    }

    public function updateStatus(Request $request, Delivery $delivery)
    {
        $request->validate([
            'status' => 'required|in:pending,on_delivery,delivered,failed',
            'proof_image' => 'nullable|image|max:2048',
            'notes' => 'nullable|string',
        ]);

        // Authorization check for courier
        $user = Auth::user();
        if ($user->isKurir() && $delivery->courier_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $data = ['status' => $request->status];

        if ($request->status === 'delivered') {
            $data['delivered_at'] = now();
            
            if ($request->hasFile('proof_image')) {
                $data['proof_image'] = $request->file('proof_image')->store('delivery-proofs', 'public');
            }
        }

        if ($request->filled('notes')) {
            $data['notes'] = $request->notes;
        }

        $delivery->update($data);

        // Notify admins about courier status update
        $admins = User::whereIn('role', ['admin', 'apoteker'])->get();
        Notification::send($admins, new DeliveryStatusUpdated($delivery));

        return redirect()->route('deliveries.show', $delivery)
            ->with('success', 'Status pengantaran berhasil diperbarui!');
    }

    public function assignCourier(Request $request, Delivery $delivery)
    {
        // Authorization check
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isApoteker()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'courier_id' => 'required|exists:users,id',
        ]);

        $delivery->update([
            'courier_id' => $request->courier_id,
            'status' => 'on_delivery',
        ]);

        $delivery->courier->notify(new DeliveryAssigned($delivery));

        return redirect()->route('deliveries.show', $delivery)
            ->with('success', 'Kurir berhasil ditugaskan!');
    }

    public function track(Delivery $delivery)
    {
        $delivery->load(['patient', 'courier']);
        return view('deliveries.track', compact('delivery'));
    }

    public function printLabel(Delivery $delivery)
    {
        $delivery->load(['patient', 'prescription', 'courier']);
        return view('deliveries.print', compact('delivery'));
    }
}