<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Prescription;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $prescriptions = Prescription::with('patient')
            ->when($search, function($query, $search) {
                return $query->whereHas('patient', function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                })->orWhere('medication_name', 'LIKE', "%{$search}%")
                  ->orWhere('medications', 'LIKE', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();
            
        $todayCount = Prescription::whereDate('created_at', today())->count();
        return view('prescriptions.index', compact('prescriptions', 'todayCount'));
    }

    public function create(Request $request)
    {
        $patient_id = $request->get('patient_id');
        $patients = Patient::all();
        return view('prescriptions.create', compact('patients', 'patient_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'medications' => 'required|array|min:1',
            'medications.*.name' => 'required|string|max:255',
            'medications.*.dosage' => 'required|string|max:100',
            'medications.*.frequency' => 'required|string|max:50',
            'medications.*.duration' => 'required|string|max:50',
            'medications.*.instructions' => 'nullable|string',
        ]);

        Prescription::create([
            'patient_id' => $request->patient_id,
            'medications' => $request->medications,
            // For backward compatibility, save the first medication in the old columns
            'medication_name' => $request->medications[0]['name'],
            'dosage' => $request->medications[0]['dosage'],
            'frequency' => $request->medications[0]['frequency'],
            'duration' => $request->medications[0]['duration'],
            'instructions' => $request->medications[0]['instructions'],
        ]);

        return redirect()->route('prescriptions.index')->with('success', 'Resep berhasil ditambahkan!');
    }

    public function show(Prescription $prescription)
    {
        $prescription->load('patient');
        return view('prescriptions.show', compact('prescription'));
    }

    public function edit(Prescription $prescription)
    {
        $patients = Patient::all();
        return view('prescriptions.edit', compact('prescription', 'patients'));
    }

    public function update(Request $request, Prescription $prescription)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'medications' => 'required|array|min:1',
            'medications.*.name' => 'required|string|max:255',
            'medications.*.dosage' => 'required|string|max:100',
            'medications.*.frequency' => 'required|string|max:50',
            'medications.*.duration' => 'required|string|max:50',
            'medications.*.instructions' => 'nullable|string',
        ]);

        $prescription->update([
            'patient_id' => $request->patient_id,
            'medications' => $request->medications,
            'medication_name' => $request->medications[0]['name'],
            'dosage' => $request->medications[0]['dosage'],
            'frequency' => $request->medications[0]['frequency'],
            'duration' => $request->medications[0]['duration'],
            'instructions' => $request->medications[0]['instructions'],
        ]);

        return redirect()->route('prescriptions.index')->with('success', 'Resep berhasil diperbarui!');
    }

    public function destroy(Prescription $prescription)
    {
        $prescription->delete();
        return redirect()->route('prescriptions.index')->with('success', 'Resep berhasil dihapus!');
    }

    public function verify(Prescription $prescription)
    {
        // Fitur khusus apoteker (opsional)
        return back()->with('success', 'Resep terverifikasi!');
    }

    public function approve(Prescription $prescription)
    {
        // Fitur khusus apoteker (opsional)
        return back()->with('success', 'Resep disetujui!');
    }

    public function reject(Prescription $prescription)
    {
        // Fitur khusus apoteker (opsional)
        return back()->with('success', 'Resep ditolak!');
    }

    public function updateStatus(Request $request, Prescription $prescription)
    {
        // Fitur khusus apoteker (opsional)
        return back()->with('success', 'Status resep diperbarui!');
    }

    public function printLabels(Prescription $prescription)
    {
        $prescription->load('patient');
        return view('prescriptions.print', compact('prescription'));
    }
}