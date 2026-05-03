<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Delivery;
use App\Models\DeliveryAssessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class CourierController extends Controller
{
    public function index()
    {
        $couriers = User::where('role', 'kurir')->latest()->paginate(10);
        return view('couriers.index', compact('couriers'));
    }

    public function create()
    {
        return view('couriers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => 'kurir',
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('couriers.index')
            ->with('success', 'Kurir berhasil ditambahkan!');
    }

    public function show(User $courier)
    {
        if ($courier->role !== 'kurir') {
            abort(404);
        }
        
        $deliveries = Delivery::where('courier_id', $courier->id)->latest()->take(5)->get();
        return view('couriers.show', compact('courier', 'deliveries'));
    }

    public function edit(User $courier)
    {
        if ($courier->role !== 'kurir') {
            abort(404);
        }
        return view('couriers.edit', compact('courier'));
    }

    public function update(Request $request, User $courier)
    {
        if ($courier->role !== 'kurir') {
            abort(404);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$courier->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $data = $request->only(['name', 'email', 'phone']);
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $courier->update($data);

        return redirect()->route('couriers.index')
            ->with('success', 'Data kurir berhasil diperbarui!');
    }

    public function destroy(User $courier)
    {
        if ($courier->role !== 'kurir') {
            abort(404);
        }

        $courier->delete();

        return redirect()->route('couriers.index')
            ->with('success', 'Kurir berhasil dihapus!');
    }

    public function performanceIndex()
    {
        $couriers = User::where('role', 'kurir')->get()->map(function($courier) {
            $deliveries = Delivery::where('courier_id', $courier->id)->get();
            $completed = $deliveries->where('status', 'delivered')->count();
            $total = $deliveries->count();
            
            $courier->total_deliveries = $total;
            $courier->completed_deliveries = $completed;
            $courier->success_rate = $total > 0 ? round(($completed / $total) * 100) : 0;
            
            return $courier;
        });
        
        return view('couriers.performance-index', compact('couriers'));
    }
    
    public function performance(User $courier)
    {
        if ($courier->role !== 'kurir') {
            abort(404);
        }
        
        $deliveries = Delivery::where('courier_id', $courier->id)->get();
        $assessments = DeliveryAssessment::whereHas('delivery', function($q) use ($courier) {
            $q->where('courier_id', $courier->id);
        })->get();
        
        $stats = [
            'total' => $deliveries->count(),
            'completed' => $deliveries->where('status', 'delivered')->count(),
            'failed' => $deliveries->where('status', 'failed')->count(),
            'on_delivery' => $deliveries->where('status', 'on_delivery')->count(),
            'avg_condition_score' => $assessments->avg('condition_score') ?? 0,
            'avg_patient_understanding' => $assessments->avg('patient_understanding_score') ?? 0,
        ];
        
        return view('couriers.performance', compact('courier', 'stats', 'deliveries', 'assessments'));
    }
}
