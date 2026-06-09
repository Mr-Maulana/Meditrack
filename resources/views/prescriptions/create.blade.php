@extends('layouts.app')

@section('title', 'Tambah Resep Obat')
@section('page-title', 'Tambah Resep')

@section('content')
<div class="max-w-5xl mx-auto space-y-8 animate-fade-in pb-12">
    <!-- Header & Actions -->
    <div class="flex items-center justify-between">
        <a href="{{ route('prescriptions.index') }}" class="text-tni-600 hover:text-tni-800 flex items-center font-bold transition-colors text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
        </a>
    </div>

    <!-- Main Form Card -->
    <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
        <!-- Banner Header -->
        <div class="bg-gradient-to-r from-tni-800 to-tni-900 p-10 text-white relative">
            <div class="absolute top-0 right-0 p-10 opacity-10">
                <i class="fas fa-file-medical text-8xl"></i>
            </div>
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <h2 class="text-3xl font-black tracking-tight">Peresepan Obat Pasien</h2>
                    <p class="text-tni-100 opacity-80 mt-2 font-medium">Input daftar obat dan instruksi dosis secara mendetail.</p>
                </div>
                <button type="button" id="addMedication" class="px-6 py-3 bg-gold-500 text-tni-900 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg hover:bg-gold-400 transition-all flex items-center gap-2">
                    <i class="fas fa-plus"></i> Tambah Item Obat
                </button>
            </div>
        </div>

        <form action="{{ route('prescriptions.store') }}" method="POST" id="prescriptionForm" class="p-10">
            @csrf
            
            <div class="space-y-10">
                <!-- Patient Selection -->
                <div class="bg-gray-50/50 p-8 rounded-[2rem] border border-gray-100">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                        <span class="w-2 h-2 bg-tni-600 rounded-full"></span> Target Pasien
                    </h3>
                    <div class="max-w-md relative">
                        <!-- Hidden real input that gets submitted -->
                        <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id', $patient_id ?? '') }}">

                        <!-- ① SEARCH MODE (visible when no patient selected) -->
                        <div id="patientSearchMode">
                            <div class="relative group" id="patientSearchWrapper">
                                <span class="absolute inset-y-0 left-0 pl-5 flex items-center text-gray-400 group-focus-within:text-tni-600 transition-colors pointer-events-none z-10">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" id="patientSearchInput" autocomplete="off"
                                    placeholder="Cari nama atau No. RM pasien..."
                                    class="w-full pl-12 pr-10 py-4 bg-white border border-gray-200 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all shadow-sm outline-none">
                                <button type="button" id="patientClearBtn" onclick="clearTyping()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-300 hover:text-gray-500 transition-colors hidden">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            </div>
                            <!-- Dropdown results -->
                            <div id="patientDropdown" class="hidden absolute z-50 left-0 right-0 mt-2 bg-white border border-gray-100 rounded-2xl shadow-2xl overflow-hidden">
                                <div id="patientDropdownList" class="max-h-60 overflow-y-auto divide-y divide-gray-50"></div>
                                <div id="patientNoResult" class="hidden px-5 py-4 text-xs text-gray-400 font-bold text-center">
                                    <i class="fas fa-search-minus mr-1 opacity-50"></i> Pasien tidak ditemukan
                                </div>
                            </div>
                        </div>

                        <!-- ② LOCKED MODE (visible after patient is selected) -->
                        <div id="patientLockedMode" class="hidden">
                            <div class="flex items-center gap-4 bg-white border-2 border-tni-400 rounded-2xl px-5 py-4 shadow-sm">
                                <!-- Avatar -->
                                <div class="w-11 h-11 bg-gradient-to-br from-tni-600 to-tni-800 text-white rounded-xl flex items-center justify-center flex-shrink-0 shadow">
                                    <i class="fas fa-user-check text-sm"></i>
                                </div>
                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-black text-gray-800 truncate" id="selectedPatientName">-</p>
                                    <p class="text-[10px] text-tni-500 font-bold" id="selectedPatientCode">-</p>
                                </div>
                                <!-- Lock badge + X button -->
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <span class="hidden sm:inline-flex items-center gap-1 text-[9px] font-black text-tni-700 bg-tni-100 px-2.5 py-1 rounded-lg uppercase tracking-wider">
                                        <i class="fas fa-lock text-[8px]"></i> Terpilih
                                    </span>
                                    <button type="button" onclick="clearPatientSearch()"
                                        title="Batal pilih pasien"
                                        class="w-8 h-8 flex items-center justify-center bg-red-50 hover:bg-red-500 text-red-400 hover:text-white rounded-xl transition-all">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </div>
                            </div>
                            <p class="mt-2 text-[10px] text-gray-400 italic ml-1">
                                <i class="fas fa-info-circle mr-1"></i>Klik ✕ untuk mengganti pasien
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Medications Container -->
                <div id="medicationsContainer" class="space-y-6">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] flex items-center gap-2 px-2">
                        <span class="w-2 h-2 bg-gold-500 rounded-full"></span> Daftar Item Obat & Instruksi
                    </h3>
                    
                    <!-- Medication items will be added here -->
                    <div class="medication-item bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative group animate-fade-in" data-index="0">
                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-6">
                            <div class="md:col-span-2 lg:col-span-2">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Nama Obat</label>
                                <input type="text" name="medications[0][name]" required placeholder="Contoh: Paracetamol 500mg" 
                                    class="w-full px-5 py-3.5 bg-gray-50 border border-transparent rounded-xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Dosis</label>
                                <input type="text" name="medications[0][dosage]" required placeholder="1 Tablet" 
                                    class="w-full px-5 py-3.5 bg-gray-50 border border-transparent rounded-xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Frekuensi</label>
                                <select name="medications[0][frequency]" required 
                                    class="w-full px-5 py-3.5 bg-gray-50 border border-transparent rounded-xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all appearance-none">
                                    <option value="3x1">3 x 1 Hari</option>
                                    <option value="2x1">2 x 1 Hari</option>
                                    <option value="1x1">1 x 1 Hari</option>
                                    <option value="3x2">3 x 2 Hari</option>
                                    <option value="P.R.N">Bila Perlu (P.R.N)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Durasi</label>
                                <input type="text" name="medications[0][duration]" required placeholder="5 Hari" 
                                    class="w-full px-5 py-3.5 bg-gray-50 border border-transparent rounded-xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all">
                            </div>
                            <div class="flex items-end">
                                <button type="button" class="remove-medication w-full py-3.5 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all opacity-0 group-hover:opacity-100">
                                    <i class="fas fa-trash-can mr-2"></i> Hapus
                                </button>
                            </div>
                            <div class="md:col-span-3 lg:col-span-6 mt-2">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Instruksi Khusus (Opsional)</label>
                                <input type="text" name="medications[0][instructions]" placeholder="Contoh: Sesudah makan / Sebelum tidur" 
                                    class="w-full px-5 py-3.5 bg-gray-50 border border-transparent rounded-xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                @error('medications')
                    <div class="p-4 bg-red-50 rounded-2xl border border-red-100 flex items-center gap-3 text-red-700">
                        <i class="fas fa-circle-exclamation"></i>
                        <p class="text-xs font-bold uppercase tracking-wider">{{ $message }}</p>
                    </div>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="mt-12 pt-10 border-t border-gray-100 flex flex-col sm:flex-row justify-end gap-4">
                <a href="{{ route('prescriptions.index') }}" class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-800 transition-colors flex items-center justify-center">
                    Batal
                </a>
                <button type="submit" class="px-12 py-4 bg-gradient-to-br from-tni-800 to-black text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-2xl shadow-tni-300 hover:scale-[1.02] transition-all flex items-center justify-center gap-3">
                    <i class="fas fa-check-circle text-gold-400"></i> Simpan Resep
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
// ── Patient data from server ──────────────────────────────────
@php
    $patientsJson = $patients->map(function($p) {
        return [
            'id'   => $p->id,
            'name' => $p->name,
            'code' => $p->patient_code ?? 'RM-?',
        ];
    })->values();
@endphp
const PATIENTS = @json($patientsJson);

// ── DOM refs ─────────────────────────────────────────────────
const searchInput   = document.getElementById('patientSearchInput');
const hiddenInput   = document.getElementById('patient_id');
const dropdown      = document.getElementById('patientDropdown');
const dropdownList  = document.getElementById('patientDropdownList');
const noResult      = document.getElementById('patientNoResult');
const clearBtn      = document.getElementById('patientClearBtn');
const searchMode    = document.getElementById('patientSearchMode');
const lockedMode    = document.getElementById('patientLockedMode');
const badgeName     = document.getElementById('selectedPatientName');
const badgeCode     = document.getElementById('selectedPatientCode');

let selectedId = '{{ old('patient_id', $patient_id ?? '') }}';

// Pre-fill if value already set (e.g. old())
if (selectedId) {
    const pre = PATIENTS.find(p => String(p.id) === String(selectedId));
    if (pre) lockPatient(pre);
}

// ── Search logic ──────────────────────────────────────────────
searchInput.addEventListener('input', function () {
    const q = this.value.trim().toLowerCase();
    clearBtn.classList.toggle('hidden', q.length === 0);
    if (q.length === 0) { hideDropdown(); return; }
    const matches = PATIENTS.filter(p =>
        p.name.toLowerCase().includes(q) || p.code.toLowerCase().includes(q)
    );
    renderDropdown(matches, q);
});

searchInput.addEventListener('focus', function () {
    const q = this.value.trim().toLowerCase();
    if (q.length > 0) {
        const matches = PATIENTS.filter(p =>
            p.name.toLowerCase().includes(q) || p.code.toLowerCase().includes(q)
        );
        renderDropdown(matches, q);
    }
});

// Close dropdown when clicking outside
document.addEventListener('click', function (e) {
    if (!e.target.closest('#patientSearchWrapper') && !e.target.closest('#patientDropdown')) {
        hideDropdown();
    }
});

// ── Render helpers ────────────────────────────────────────────
function renderDropdown(matches, q) {
    dropdownList.innerHTML = '';
    noResult.classList.add('hidden');
    dropdown.classList.remove('hidden');

    if (matches.length === 0) {
        noResult.classList.remove('hidden');
        return;
    }

    matches.forEach(p => {
        const item = document.createElement('button');
        item.type = 'button';
        item.className = 'w-full text-left px-5 py-3.5 hover:bg-tni-50 transition-colors flex items-center gap-3 group';
        item.innerHTML = `
            <div class="w-9 h-9 bg-gray-100 group-hover:bg-tni-600 text-gray-400 group-hover:text-white rounded-xl flex items-center justify-center flex-shrink-0 transition-colors">
                <i class="fas fa-user text-xs"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-black text-gray-800 truncate">${highlightMatch(p.name, q)}</p>
                <p class="text-[10px] text-gray-400 font-bold">${highlightMatch(p.code, q)}</p>
            </div>
        `;
        item.addEventListener('click', () => selectPatient(p));
        dropdownList.appendChild(item);
    });
}

function highlightMatch(text, q) {
    if (!q) return text;
    const idx = text.toLowerCase().indexOf(q);
    if (idx === -1) return text;
    return text.substring(0, idx) +
        `<mark class="bg-gold-200 text-tni-900 rounded px-0.5">${text.substring(idx, idx + q.length)}</mark>` +
        text.substring(idx + q.length);
}

function selectPatient(p) {
    hiddenInput.value = p.id;
    selectedId = p.id;
    searchInput.value = '';
    clearBtn.classList.add('hidden');
    hideDropdown();
    lockPatient(p);
}

// Switch to LOCKED mode — show card, hide search
function lockPatient(p) {
    badgeName.textContent = p.name;
    badgeCode.textContent  = 'No. RM: ' + p.code;
    searchMode.classList.add('hidden');
    lockedMode.classList.remove('hidden');
}

// Switch back to SEARCH mode — clear everything
function clearPatientSearch() {
    hiddenInput.value = '';
    selectedId = '';
    searchInput.value = '';
    clearBtn.classList.add('hidden');
    hideDropdown();
    lockedMode.classList.add('hidden');
    searchMode.classList.remove('hidden');
    searchInput.focus();
}

// Clear only the typed text (✕ inside search box)
function clearTyping() {
    searchInput.value = '';
    clearBtn.classList.add('hidden');
    hideDropdown();
    searchInput.focus();
}

function hideDropdown() {
    dropdown.classList.add('hidden');
    dropdownList.innerHTML = '';
    noResult.classList.add('hidden');
}

// ── Medication items ──────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('medicationsContainer');
    const addButton = document.getElementById('addMedication');
    const template  = document.getElementById('medicationTemplate').innerHTML;
    let index = 1;

    addButton.addEventListener('click', function() {
        const html = template.replace(/__INDEX__/g, index);
        const div  = document.createElement('div');
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

    // Form validation – ensure patient is selected
    document.getElementById('prescriptionForm').addEventListener('submit', function(e) {
        if (!hiddenInput.value) {
            e.preventDefault();
            lockedMode.classList.add('hidden');
            searchMode.classList.remove('hidden');
            searchInput.classList.add('border-red-400', 'ring-2', 'ring-red-200');
            searchInput.focus();
            searchInput.placeholder = '⚠ Pilih pasien terlebih dahulu!';
        }
    });
});
</script>
@endsection

