@extends('layouts.app')

@section('title', 'Edit Resep Obat')
@section('page-title', 'Pembaruan Resep')

@section('content')
<div class="max-w-5xl mx-auto space-y-8 animate-fade-in pb-12">
    <!-- Header & Actions -->
    <div class="flex items-center justify-between">
        <a href="{{ route('prescriptions.show', $prescription) }}" class="text-tni-600 hover:text-tni-800 flex items-center font-bold transition-colors text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Batal & Kembali
        </a>
    </div>

    <!-- Main Form Card -->
    <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
        <!-- Banner Header -->
        <div class="bg-gradient-to-r from-tni-800 to-gold-600 p-10 text-white relative">
            <div class="absolute top-0 right-0 p-10 opacity-10">
                <i class="fas fa-file-signature text-8xl"></i>
            </div>
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <h2 class="text-3xl font-black tracking-tight">Edit Resep Pasien</h2>
                    <p class="text-tni-100 opacity-80 mt-2 font-medium">Modifikasi daftar obat atau instruksi pemakaian.</p>
                </div>
                <button type="button" id="addMedication" class="px-6 py-3 bg-white text-tni-900 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg hover:bg-tni-100 transition-all flex items-center gap-2">
                    <i class="fas fa-plus"></i> Tambah Item
                </button>
            </div>
        </div>

        <form action="{{ route('prescriptions.update', $prescription) }}" method="POST" id="prescriptionForm" class="p-10">
            @csrf
            @method('PUT')
            
            <div class="space-y-10">
                <!-- Patient Selection (Readonly) -->
                <div class="bg-gray-50/50 p-8 rounded-[2rem] border border-gray-100">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                        <span class="w-2 h-2 bg-tni-600 rounded-full"></span> Target Pasien
                    </h3>
                    <div class="max-w-md relative">
                        <div class="w-full pl-12 pr-6 py-4 bg-white border border-gray-200 rounded-2xl text-sm font-bold text-gray-400 shadow-sm flex items-center gap-3">
                            <i class="fas fa-lock text-xs"></i>
                            [{{ $prescription->patient->patient_code ?? 'RM' }}] {{ $prescription->patient->name }}
                        </div>
                        <input type="hidden" name="patient_id" value="{{ $prescription->patient_id }}">
                        <p class="mt-2 text-[10px] text-gray-400 italic ml-1">Pasien tidak dapat diubah pada mode edit resep.</p>
                    </div>
                </div>

                <!-- Medications Container -->
                <div id="medicationsContainer" class="space-y-6">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] flex items-center gap-2 px-2">
                        <span class="w-2 h-2 bg-gold-500 rounded-full"></span> Daftar Item Obat & Instruksi
                    </h3>
                    
                    @php
                        $meds = old('medications', $prescription->medications ?? [
                            [
                                'name' => $prescription->medication_name,
                                'dosage' => $prescription->dosage,
                                'frequency' => $prescription->frequency,
                                'duration' => $prescription->duration,
                                'instructions' => $prescription->instructions,
                            ]
                        ]);
                    @endphp

                    @foreach($meds as $index => $med)
                    <div class="medication-item bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative group animate-fade-in" data-index="{{ $index }}">
                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-6">
                            <div class="md:col-span-2 lg:col-span-2">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Nama Obat</label>
                                <input type="text" name="medications[{{ $index }}][name]" value="{{ $med['name'] ?? '' }}" required 
                                    class="w-full px-5 py-3.5 bg-gray-50 border border-transparent rounded-xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Dosis</label>
                                <input type="text" name="medications[{{ $index }}][dosage]" value="{{ $med['dosage'] ?? '' }}" required 
                                    class="w-full px-5 py-3.5 bg-gray-50 border border-transparent rounded-xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Frekuensi</label>
                                <select name="medications[{{ $index }}][frequency]" required 
                                    class="w-full px-5 py-3.5 bg-gray-50 border border-transparent rounded-xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all appearance-none">
                                    @foreach(['3x1', '2x1', '1x1', '3x2', 'P.R.N'] as $f)
                                        <option value="{{ $f }}" {{ ($med['frequency'] ?? '') == $f ? 'selected' : '' }}>{{ $f }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Durasi</label>
                                <input type="text" name="medications[{{ $index }}][duration]" value="{{ $med['duration'] ?? '' }}" required 
                                    class="w-full px-5 py-3.5 bg-gray-50 border border-transparent rounded-xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all">
                            </div>
                            <div class="flex items-end">
                                <button type="button" class="remove-medication w-full py-3.5 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all opacity-0 group-hover:opacity-100">
                                    <i class="fas fa-trash-can mr-2"></i> Hapus
                                </button>
                            </div>
                            <div class="md:col-span-3 lg:col-span-6 mt-2">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Instruksi Khusus (Opsional)</label>
                                <input type="text" name="medications[{{ $index }}][instructions]" value="{{ $med['instructions'] ?? '' }}" 
                                    class="w-full px-5 py-3.5 bg-gray-50 border border-transparent rounded-xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all">
                            </div>
                        </div>
                    </div>
                    @endforeach

                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-12 pt-10 border-t border-gray-100 flex flex-col sm:flex-row justify-end gap-4">
                <a href="{{ route('prescriptions.show', $prescription) }}" class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-800 transition-colors flex items-center justify-center">
                    Batal
                </a>
                <button type="submit" class="px-12 py-4 bg-gradient-to-br from-gold-500 to-gold-700 text-tni-900 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-2xl shadow-gold-200 hover:scale-[1.02] transition-all flex items-center justify-center gap-3">
                    <i class="fas fa-check-circle"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<template id="medicationTemplate">
    <div class="medication-item bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative group animate-fade-in" data-index="__INDEX__">
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-6">
            <div class="md:col-span-2 lg:col-span-2">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Nama Obat</label>
                <input type="text" name="medications[__INDEX__][name]" required placeholder="Nama Obat" 
                    class="w-full px-5 py-3.5 bg-gray-50 border border-transparent rounded-xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Dosis</label>
                <input type="text" name="medications[__INDEX__][dosage]" required placeholder="Dosis" 
                    class="w-full px-5 py-3.5 bg-gray-50 border border-transparent rounded-xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Frekuensi</label>
                <select name="medications[__INDEX__][frequency]" required 
                    class="w-full px-5 py-3.5 bg-gray-50 border border-transparent rounded-xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all appearance-none">
                    <option value="3x1">3 x 1 Hari</option>
                    <option value="2x1">2 x 1 Hari</option>
                    <option value="1x1">1 x 1 Hari</option>
                    <option value="3x2">3 x 2 Hari</option>
                    <option value="P.R.N">Bila Perlu</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Durasi</label>
                <input type="text" name="medications[__INDEX__][duration]" required placeholder="Durasi" 
                    class="w-full px-5 py-3.5 bg-gray-50 border border-transparent rounded-xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all">
            </div>
            <div class="flex items-end">
                <button type="button" class="remove-medication w-full py-3.5 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all">
                    <i class="fas fa-trash-can mr-2"></i> Hapus
                </button>
            </div>
            <div class="md:col-span-3 lg:col-span-6 mt-2">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Instruksi Khusus (Opsional)</label>
                <input type="text" name="medications[__INDEX__][instructions]" placeholder="Instruksi..." 
                    class="w-full px-5 py-3.5 bg-gray-50 border border-transparent rounded-xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all">
            </div>
        </div>
    </div>
</template>

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fade-in 0.3s ease-out forwards;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('medicationsContainer');
    const addButton = document.getElementById('addMedication');
    const template = document.getElementById('medicationTemplate').innerHTML;
    let index = {{ count($meds) }};

    addButton.addEventListener('click', function() {
        const html = template.replace(/__INDEX__/g, index);
        const div = document.createElement('div');
        div.innerHTML = html;
        container.appendChild(div.firstElementChild);
        index++;
    });

    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-medication')) {
            const item = e.target.closest('.medication-item');
            if (container.querySelectorAll('.medication-item').length > 1) {
                item.classList.add('opacity-0', 'scale-95');
                setTimeout(() => item.remove(), 200);
            } else {
                alert('Minimal harus ada satu obat dalam resep.');
            }
        }
    });
});
</script>
@endsection
