<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PatientController extends Controller
{


    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $query = Patient::when(!Auth::user()->isAdmin() && !Auth::user()->isApoteker(), function ($query) {
            return $query->where('created_by', Auth::id());
        })
        ->when($search, function($query, $search) {
            return $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('patient_code', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        });

        $totalMale = (clone $query)->where('gender', 'male')->count();
        $totalFemale = (clone $query)->where('gender', 'female')->count();
        
        $patients = $query->latest()->paginate(15)->withQueryString();

        return view('patients.index', compact('patients', 'totalMale', 'totalFemale'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:patients',
            'phone' => 'required|string|unique:patients',
            'address' => 'required|string',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'medical_condition' => 'nullable|string',
        ]);

        try {
            $patient = Patient::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'medical_condition' => $request->medical_condition,
                'created_by' => Auth::id(),
            ]);

            Log::info('Patient created', [
                'patient_id' => $patient->id,
                'created_by' => Auth::id(),
            ]);

            return redirect()->route('patients.show', $patient)
                ->with('success', 'Data pasien berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error creating patient', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Gagal menambahkan data pasien.');
        }
    }

    public function show(Patient $patient)
    {
        $this->authorize('view', $patient);
        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        $this->authorize('update', $patient);
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $this->authorize('update', $patient);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:patients,email,' . $patient->id,
            'phone' => 'required|string|unique:patients,phone,' . $patient->id,
            'address' => 'required|string',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'medical_condition' => 'nullable|string',
        ]);

        try {
            $patient->update($request->only(['name', 'email', 'phone', 'address', 'date_of_birth', 'gender', 'medical_condition']));

            Log::info('Patient updated', [
                'patient_id' => $patient->id,
                'updated_by' => Auth::id(),
            ]);

            return redirect()->route('patients.show', $patient)
                ->with('success', 'Data pasien berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating patient', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Gagal memperbarui data pasien.');
        }
    }

    public function destroy(Patient $patient)
    {
        $this->authorize('delete', $patient);

        try {
            $patient->delete();

            Log::info('Patient deleted', [
                'patient_id' => $patient->id,
                'deleted_by' => Auth::id(),
            ]);

            return redirect()->route('patients.index')
                ->with('success', 'Data pasien berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting patient', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal menghapus data pasien.');
        }
    }

    public function history(Patient $patient)
    {
        $this->authorize('view', $patient);
        
        $prescriptions = $patient->prescriptions()->latest()->paginate(10);
        $deliveries = $patient->deliveries()->latest()->paginate(10);

        return view('patients.history', compact('patient', 'prescriptions', 'deliveries'));
    }

    public function printLabel(Patient $patient)
    {
        $this->authorize('view', $patient);
        return view('patients.print', compact('patient'));
    }
}