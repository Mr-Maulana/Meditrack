@extends('layouts.app')

@section('title', 'Detail Resep Obat')
@section('page-title', 'Detail Resep')

@section('content')
<div class="max-w-5xl mx-auto space-y-8 animate-fade-in pb-12">
    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <a href="{{ route('prescriptions.index') }}" class="text-tni-600 hover:text-tni-800 flex items-center font-medium transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Resep
        </a>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('prescriptions.print', $prescription) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-tni-800 text-white rounded-xl hover:bg-black transition shadow-md font-medium">
                <i class="fas fa-print mr-2 text-gold-400"></i> Cetak Etiket Obat
            </a>
            <a href="{{ route('prescriptions.edit', $prescription) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded-xl hover:bg-yellow-600 transition shadow-md font-medium">
                <i class="fas fa-edit mr-2"></i> Edit Resep
            </a>
            <form action="{{ route('prescriptions.destroy', $prescription) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus resep ini?');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-500 text-white rounded-xl hover:bg-red-600 transition shadow-md font-medium">
                    <i class="fas fa-trash mr-2"></i> Hapus
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <!-- Banner Header -->
        <div class="bg-gradient-to-r from-tni-800 to-tni-600 p-8 text-white relative">
            <div class="absolute top-0 right-0 p-8 opacity-10">
                <i class="fas fa-file-prescription text-8xl"></i>
            </div>
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-3xl border border-white/30 text-gold-400">
                        <i class="fas fa-prescription"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold">Resep Obat Pasien</h2>
                        <p class="text-tni-100 opacity-90">Dibuat pada {{ $prescription->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
                
                @if(!$prescription->delivery)
                    <a href="{{ route('deliveries.create', ['patient_id' => $prescription->patient_id]) }}" class="px-6 py-2 bg-gold-500 text-tni-900 rounded-full font-bold shadow-lg hover:bg-gold-400 transition transform hover:scale-105">
                        <i class="fas fa-truck-medical mr-2"></i> Buat Pengantaran
                    </a>
                @else
                    <a href="{{ route('deliveries.show', $prescription->delivery) }}" class="px-6 py-2 bg-tni-900 text-gold-400 border border-gold-500/50 rounded-full font-bold shadow-lg hover:bg-black transition">
                        <i class="fas fa-box mr-2"></i> Status Pengantaran
                    </a>
                @endif
            </div>
        </div>

        <div class="p-8 grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Left: Patient Information -->
            <div class="lg:col-span-1 space-y-6">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center border-b pb-2">
                    <i class="fas fa-user-injured mr-2 text-tni-600"></i> Informasi Pasien
                </h3>
                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 space-y-4">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Nama Pasien</p>
                        <p class="text-gray-800 font-bold text-lg">{{ $prescription->patient->name }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Nomor RM / Kode</p>
                        <p class="text-tni-700 font-bold">{{ $prescription->patient->patient_code ?? $prescription->patient->medical_record_number }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Alamat</p>
                        <p class="text-gray-600 text-sm leading-relaxed">{{ $prescription->patient->address }}</p>
                    </div>
                </div>
            </div>

            <!-- Right: Medications List -->
            <div class="lg:col-span-2 space-y-6">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center border-b pb-2">
                    <i class="fas fa-pills mr-2 text-gold-600"></i> Daftar Obat & Instruksi
                </h3>
                
                <div class="space-y-4">
                    @php
                        $meds = $prescription->medications ?? [
                            [
                                'name' => $prescription->medication_name,
                                'dosage' => $prescription->dosage,
                                'frequency' => $prescription->frequency,
                                'duration' => $prescription->duration,
                                'instructions' => $prescription->instructions,
                            ]
                        ];
                    @endphp

                    @foreach($meds as $med)
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-gold-400"></div>
                        
                        <div class="flex flex-col md:flex-row justify-between gap-4">
                            <div class="flex-1">
                                <h4 class="text-xl font-bold text-gray-800 mb-4 group-hover:text-tni-700 transition-colors">
                                    {{ $med['name'] ?? 'Obat' }}
                                </h4>
                                
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    <div class="p-3 bg-gray-50 rounded-xl">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-0.5">Dosis</p>
                                        <p class="text-sm font-bold text-gray-700">{{ $med['dosage'] ?? '-' }}</p>
                                    </div>
                                    <div class="p-3 bg-gray-50 rounded-xl">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-0.5">Frekuensi</p>
                                        <p class="text-sm font-bold text-gray-700">{{ $med['frequency'] ?? '-' }}</p>
                                    </div>
                                    <div class="p-3 bg-gray-50 rounded-xl">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-0.5">Durasi</p>
                                        <p class="text-sm font-bold text-gray-700">{{ $med['duration'] ?? '-' }}</p>
                                    </div>
                                </div>

                                @if(!empty($med['instructions']))
                                <div class="mt-4 p-3 bg-blue-50/50 border border-blue-100 rounded-xl">
                                    <p class="text-[10px] font-bold text-blue-400 uppercase mb-1">Instruksi Khusus</p>
                                    <p class="text-sm text-blue-800 italic">"{{ $med['instructions'] }}"</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fade-in 0.5s ease-out forwards;
    }
</style>
@endsection
