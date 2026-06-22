@extends('layouts.app')

@section('title', 'Tambah Pengantaran')
@section('page-title', 'Penjadwalan Pengantaran')

@section('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #addressMap {
        height: 400px;
        width: 100%;
        border-radius: 1.5rem;
        border: 2px solid #f3f4f6;
    }
    .leaflet-container {
        font-family: inherit;
    }
</style>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-8 animate-fade-in pb-12">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('deliveries.index') }}" class="text-tni-600 hover:text-tni-800 flex items-center font-medium transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
        </a>
    </div>

    <form action="{{ route('deliveries.store') }}" method="POST" id="deliveryForm">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Primary Selection -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Patient & Prescription Section -->
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-visible">
                    <div class="bg-gradient-to-r from-tni-800 to-tni-600 p-6 text-white flex items-center">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-user-tag text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg">Pilih Pasien & Resep</h3>
                            <p class="text-tni-100 text-xs">Pilih pasien untuk melihat resep yang tersedia</p>
                        </div>
                    </div>
                    
                    <div class="p-8 space-y-8">
                        <!-- Patient Picker -->
                        <div>
                            <label for="patientSearchInput" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Data Pasien <span class="text-red-500">*</span></label>
                            <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id') }}">

                            <div id="patientSearchMode">
                                <div class="relative" id="patientSearchWrapper">
                                    <span class="absolute inset-y-0 left-0 pl-5 flex items-center text-gray-400 group-focus-within:text-tni-600 transition-colors pointer-events-none">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" id="patientSearchInput" autocomplete="off"
                                        placeholder="Cari nama atau Nomor Rekam Medis..."
                                        class="w-full pl-12 pr-10 py-4 bg-white border border-gray-200 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all shadow-sm outline-none">
                                    <button type="button" id="patientSearchClear" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-300 hover:text-gray-500 transition-colors hidden">
                                        <i class="fas fa-times-circle"></i>
                                    </button>

                                    <div id="patientDropdown" class="hidden absolute z-50 left-0 right-0 mt-2 bg-white border border-gray-100 rounded-2xl shadow-2xl overflow-hidden">
                                        <div id="patientDropdownList" class="max-h-60 overflow-y-auto divide-y divide-gray-50"></div>
                                        <div id="patientNoResult" class="hidden px-5 py-4 text-xs text-gray-400 font-bold text-center">
                                            <i class="fas fa-search-minus mr-1 opacity-50"></i> Pasien tidak ditemukan
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="patientLockedMode" class="hidden mt-4">
                                <div class="flex items-center gap-4 bg-white border border-tni-100 rounded-2xl p-4 shadow-sm">
                                    <div class="w-12 h-12 bg-tni-600 rounded-xl flex items-center justify-center text-white text-xl font-bold flex-shrink-0 shadow-lg">
                                        <i class="fas fa-user-check"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p id="lockedPatientName" class="text-sm font-black text-gray-800 truncate">-</p>
                                        <p id="lockedPatientCode" class="text-[10px] uppercase tracking-[0.2em] text-tni-600 font-bold">No. RM</p>
                                    </div>
                                    <button type="button" id="patientSearchReset" class="w-10 h-10 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Patient Quick Info (Initially Hidden) -->
                        <div id="patientInfo" class="hidden animate-fade-in bg-tni-50/50 rounded-2xl p-6 border border-tni-100 flex items-start gap-4">
                            <div class="w-12 h-12 bg-tni-600 rounded-xl flex items-center justify-center text-white text-xl font-bold flex-shrink-0 shadow-lg">
                                <i class="fas fa-id-card-clip"></i>
                            </div>
                            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-[10px] font-bold text-tni-400 uppercase mb-0.5">Nama Lengkap</p>
                                    <p id="selectedPatientName" class="text-sm font-bold text-tni-900"></p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-tni-400 uppercase mb-0.5">Kontak</p>
                                    <p id="selectedPatientPhone" class="text-sm font-medium text-tni-700"></p>
                                </div>
                                <div class="md:col-span-2">
                                    <p class="text-[10px] font-bold text-tni-400 uppercase mb-0.5">Alamat Terdaftar</p>
                                    <p id="selectedPatientAddress" class="text-xs text-tni-600 italic"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Prescription Picker (Initially Hidden) -->
                        <div id="prescriptionInfo" class="hidden animate-fade-in space-y-4 pt-6 border-t border-gray-100">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Resep Obat yang Akan Dikirim <span class="text-red-500">*</span></label>
                            <input type="hidden" name="prescription_id" id="prescription_id" value="{{ old('prescription_id') }}">

                            <div id="prescriptionSearchMode">
                                <div class="relative" id="prescriptionSearchWrapper">
                                    <span class="absolute inset-y-0 left-0 pl-5 flex items-center text-gray-400 group-focus-within:text-gold-600 transition-colors pointer-events-none">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" id="prescriptionSearchInput" autocomplete="off"
                                        placeholder="Cari resep..."
                                        class="w-full pl-12 pr-10 py-4 bg-white border border-gray-200 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-gold-500/20 focus:border-gold-500 transition-all shadow-sm outline-none">
                                    <button type="button" id="prescriptionSearchClear" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-300 hover:text-gray-500 transition-colors hidden">
                                        <i class="fas fa-times-circle"></i>
                                    </button>

                                    <div id="prescriptionDropdown" class="hidden absolute z-50 left-0 right-0 mt-2 bg-white border border-gray-100 rounded-2xl shadow-2xl overflow-hidden">
                                        <div id="prescriptionDropdownList" class="max-h-60 overflow-y-auto divide-y divide-gray-50"></div>
                                        <div id="prescriptionNoResult" class="hidden px-5 py-4 text-xs text-gray-400 font-bold text-center">
                                            <i class="fas fa-search-minus mr-1 opacity-50"></i> Resep tidak ditemukan
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="prescriptionLockedMode" class="hidden mt-4">
                                <div class="flex items-center gap-4 bg-white border border-gold-100 rounded-2xl p-4 shadow-sm">
                                    <div class="w-12 h-12 bg-gold-500 rounded-xl flex items-center justify-center text-white text-xl font-bold flex-shrink-0 shadow-lg">
                                        <i class="fas fa-file-prescription"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p id="lockedPrescriptionLabel" class="text-sm font-black text-gray-800 truncate">-</p>
                                        <p id="lockedPrescriptionMeta" class="text-[10px] uppercase tracking-[0.2em] text-gold-600 font-bold">Resep terpilih</p>
                                    </div>
                                    <button type="button" id="prescriptionSearchReset" class="w-10 h-10 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            <div id="prescriptionDetailsContainer" class="hidden mt-4 bg-white border-2 border-dashed border-gold-200 rounded-2xl p-6">
                                <div id="prescriptionList" class="space-y-3">
                                    <!-- Dynamic content -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Logistics & Map Section -->
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gray-50 px-8 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="font-bold text-gray-800">Detail Lokasi & Alamat</h3>
                        <div class="flex gap-2">
                            <button type="button" onclick="openMapModal()" class="text-xs font-bold bg-blue-100 text-blue-700 px-4 py-2 rounded-xl hover:bg-blue-600 hover:text-white transition shadow-sm flex items-center">
                                <i class="fas fa-map-location-dot mr-2"></i> Peta
                            </button>
                            <button type="button" onclick="getCurrentLocation()" class="text-xs font-bold bg-green-100 text-green-700 px-4 py-2 rounded-xl hover:bg-green-600 hover:text-white transition shadow-sm flex items-center">
                                <i class="fas fa-location-crosshairs mr-2"></i> GPS
                            </button>
                        </div>
                    </div>
                    <div class="p-8 space-y-6">
                        <div>
                            <label for="delivery_address" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Alamat Pengantaran <span class="text-red-500">*</span></label>
                            <textarea id="delivery_address" name="delivery_address" rows="3" required class="w-full bg-gray-50 border-gray-200 rounded-2xl py-4 px-6 text-sm focus:ring-tni-500 focus:border-tni-500 transition-all" placeholder="Alamat lengkap penerimaan obat..."></textarea>
                            
                            <!-- Hidden coordinates -->
                            <input type="hidden" id="latitude" name="latitude">
                            <input type="hidden" id="longitude" name="longitude">
                            
                            <div id="coordinatesDisplay" class="mt-3 flex items-center text-[10px] font-bold text-tni-500 hidden bg-tni-50 w-fit px-3 py-1 rounded-full border border-tni-100">
                                <i class="fas fa-location-dot mr-1.5"></i> Koordinat Terkunci: <span id="coordinatesText" class="ml-1"></span>
                            </div>
                        </div>

                        <div>
                            <label for="notes" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Catatan untuk Kurir (Opsional)</label>
                            <textarea id="notes" name="notes" rows="2" class="w-full bg-gray-50 border-gray-200 rounded-2xl py-4 px-6 text-sm focus:ring-tni-500 focus:border-tni-500 transition-all" placeholder="Contoh: Titipkan di satpam, rumah cat biru, dll..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Settings & Submit -->
            <div class="space-y-8">
                <!-- Configuration Card -->
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="p-8 space-y-6">
                        <div>
                            <label for="delivery_date" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Tanggal Antar <span class="text-red-500">*</span></label>
                            <input type="date" id="delivery_date" name="delivery_date" value="{{ date('Y-m-d') }}" required class="w-full bg-gray-50 border-gray-200 rounded-2xl py-3 px-4 text-sm focus:ring-tni-500 focus:border-tni-500 transition-all font-bold">
                        </div>

                        <div>
                            <label for="priority" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Tingkat Prioritas <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="cursor-pointer">
                                    <input type="radio" name="priority" value="normal" checked class="hidden peer">
                                    <div class="text-center py-3 rounded-2xl border-2 border-gray-100 text-gray-400 peer-checked:border-tni-600 peer-checked:bg-tni-50 peer-checked:text-tni-700 font-bold text-xs transition-all">
                                        Normal
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="priority" value="urgent" class="hidden peer">
                                    <div class="text-center py-3 rounded-2xl border-2 border-gray-100 text-gray-400 peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-600 font-bold text-xs transition-all">
                                        Urgent
                                    </div>
                                </label>
                            </div>
                        </div>

                        @if(auth()->user()->isAdmin() || auth()->user()->isApoteker())
                        <div>
                            <label for="courier_id" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Petugas Kurir</label>
                            <select id="courier_id" name="courier_id" class="w-full bg-gray-50 border-gray-200 rounded-2xl py-3 px-4 text-sm focus:ring-tni-500 focus:border-tni-500 transition-all font-medium">
                                <option value="">Tugaskan Nanti</option>
                                @foreach($couriers as $courier)
                                <option value="{{ $courier->id }}">
                                    {{ $courier->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Action Button -->
                <button type="submit" class="w-full py-6 bg-gradient-to-r from-tni-700 to-tni-900 text-white rounded-3xl hover:from-tni-800 hover:to-black transition shadow-2xl font-bold flex flex-col items-center justify-center gap-1 group">
                    <span class="text-lg">Simpan & Jadwalkan</span>
                    <span class="text-[10px] text-tni-300 font-normal group-hover:text-gold-400 transition-colors uppercase tracking-widest">Konfirmasi Pengantaran Baru</span>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Map Modal (Standard Style) -->
<div id="mapModal" class="fixed inset-0 bg-tni-900/80 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-4xl overflow-hidden animate-fade-in">
        <div class="p-6 border-b flex justify-between items-center">
            <h3 class="font-bold text-tni-800 flex items-center">
                <i class="fas fa-map-location mr-2"></i> Tentukan Titik Pengantaran
            </h3>
            <button onclick="closeMapModal()" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-red-500 hover:text-white transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6">
            <div id="addressMap" class="shadow-inner"></div>
            <div class="mt-4 flex flex-col md:flex-row gap-4 items-center justify-between">
                <div class="text-xs text-gray-500 italic" id="selectedAddress">Klik pada peta untuk mengunci alamat...</div>
                <button onclick="confirmLocation()" class="px-8 py-3 bg-tni-600 text-white rounded-2xl font-bold hover:bg-tni-700 shadow-lg transition">
                    Gunakan Lokasi Ini
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet & Logic -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>
    let map, marker, selectedLatLng;
    const patients = @json($patients->keyBy('id'));

    const patientSearchInput = document.getElementById('patientSearchInput');
    const patientSearchClear = document.getElementById('patientSearchClear');
    const patientDropdown = document.getElementById('patientDropdown');
    const patientDropdownList = document.getElementById('patientDropdownList');
    const patientNoResult = document.getElementById('patientNoResult');
    const hiddenPatientId = document.getElementById('patient_id');
    const patientSearchMode = document.getElementById('patientSearchMode');
    const patientLockedMode = document.getElementById('patientLockedMode');
    const lockedPatientName = document.getElementById('lockedPatientName');
    const lockedPatientCode = document.getElementById('lockedPatientCode');
    const patientSearchReset = document.getElementById('patientSearchReset');

    const prescriptionSearchInput = document.getElementById('prescriptionSearchInput');
    const prescriptionSearchClear = document.getElementById('prescriptionSearchClear');
    const prescriptionDropdown = document.getElementById('prescriptionDropdown');
    const prescriptionDropdownList = document.getElementById('prescriptionDropdownList');
    const prescriptionNoResult = document.getElementById('prescriptionNoResult');
    const hiddenPrescriptionId = document.getElementById('prescription_id');
    const prescriptionSearchMode = document.getElementById('prescriptionSearchMode');
    const prescriptionLockedMode = document.getElementById('prescriptionLockedMode');
    const lockedPrescriptionLabel = document.getElementById('lockedPrescriptionLabel');
    const lockedPrescriptionMeta = document.getElementById('lockedPrescriptionMeta');
    const prescriptionSearchReset = document.getElementById('prescriptionSearchReset');

    function formatPatientOption(patient, q) {
        const code = patient.patient_code || patient.medical_record_number || 'RM-?';
        return `
            <button type="button" class="w-full text-left px-5 py-3.5 hover:bg-tni-50 transition-colors flex items-center gap-3 group">
                <div class="w-11 h-11 bg-gray-100 group-hover:bg-tni-600 text-gray-400 group-hover:text-white rounded-xl flex items-center justify-center flex-shrink-0 transition-colors">
                    <i class="fas fa-user text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-black text-gray-800 truncate">${highlightMatch(`[${code}] ${patient.name}`, q)}</p>
                    <p class="text-[10px] text-gray-400 font-bold">${highlightMatch(code, q)}</p>
                </div>
            </button>
        `;
    }

    function formatPrescriptionOption(prescription, q) {
        const code = `Resep - ${new Date(prescription.created_at).toLocaleDateString('id-ID', {day: '2-digit', month: 'long', year: 'numeric'})}`;
        return `
            <button type="button" class="w-full text-left px-5 py-3.5 hover:bg-gold-50 transition-colors flex items-center gap-3 group">
                <div class="w-11 h-11 bg-gold-100 group-hover:bg-gold-500 text-gold-600 group-hover:text-white rounded-xl flex items-center justify-center flex-shrink-0 transition-colors">
                    <i class="fas fa-file-prescription text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-black text-gray-800 truncate">${highlightMatch(code, q)}</p>
                    <p class="text-[10px] text-gray-500">${prescription.notes ? prescription.notes : 'Tidak ada catatan'}</p>
                </div>
            </button>
        `;
    }

    function filterPatients(query) {
        const value = query.trim().toLowerCase();
        if (!value) {
            return Object.values(patients);
        }

        return Object.values(patients).filter(patient => {
            return patient.name.toLowerCase().includes(value)
                || (patient.patient_code || '').toLowerCase().includes(value)
                || (patient.medical_record_number || '').toLowerCase().includes(value);
        });
    }

    function filterPrescriptions(query, prescriptions) {
        const value = query.trim().toLowerCase();
        if (!value) {
            return prescriptions;
        }

        return prescriptions.filter(pr => {
            const label = `Resep - ${new Date(pr.created_at).toLocaleDateString('id-ID', {day: '2-digit', month: 'long', year: 'numeric'})}`.toLowerCase();
            return label.includes(value) || (pr.notes || '').toLowerCase().includes(value);
        });
    }

    function highlightMatch(text, q) {
        if (!q) return text;
        const idx = text.toLowerCase().indexOf(q);
        if (idx === -1) return text;
        return text.substring(0, idx)
            + `<mark class="bg-gold-200 text-tni-900 rounded px-0.5">${text.substring(idx, idx + q.length)}</mark>`
            + text.substring(idx + q.length);
    }

    function renderDropdown(results, q) {
        patientDropdownList.innerHTML = '';
        patientNoResult.classList.add('hidden');
        patientDropdown.classList.remove('hidden');

        if (!results.length) {
            patientNoResult.classList.remove('hidden');
            return;
        }

        results.forEach(patient => {
            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'w-full text-left px-5 py-3.5 hover:bg-tni-50 transition-colors flex items-center gap-3 group';
            item.innerHTML = `
                <div class="w-11 h-11 bg-gray-100 group-hover:bg-tni-600 text-gray-400 group-hover:text-white rounded-xl flex items-center justify-center flex-shrink-0 transition-colors">
                    <i class="fas fa-user text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-black text-gray-800 truncate">${highlightMatch(`[${patient.patient_code || patient.medical_record_number || 'RM-?'}] ${patient.name}`, q)}</p>
                    <p class="text-[10px] text-gray-400 font-bold">${highlightMatch(patient.patient_code || patient.medical_record_number || 'RM-?', q)}</p>
                </div>
            `;
            item.addEventListener('click', () => selectPatient(patient));
            patientDropdownList.appendChild(item);
        });
    }

    function showPatientDropdown(results, q) {
        patientDropdownList.innerHTML = '';
        if (!results.length) {
            patientNoResult.classList.remove('hidden');
            patientDropdown.classList.remove('hidden');
            return;
        }

        patientNoResult.classList.add('hidden');
        patientDropdown.classList.remove('hidden');
        renderDropdown(results, q);
    }

    function selectPatient(p) {
        if (!p) return;

        const previousPrescriptionId = hiddenPrescriptionId.value;
        hiddenPatientId.value = p.id;
        patientSearchClear.classList.remove('hidden');
        hideDropdown();
        lockPatient(p);
        patientSearchInput.value = '';
        prescriptionSearchInput.value = '';
        prescriptionSearchClear.classList.add('hidden');
        prescriptionSearchMode.classList.remove('hidden');
        prescriptionLockedMode.classList.add('hidden');
        hiddenPrescriptionId.value = '';

        document.getElementById('selectedPatientName').textContent = p.name;
        document.getElementById('selectedPatientPhone').textContent = p.phone;
        document.getElementById('selectedPatientAddress').textContent = p.address;
        document.getElementById('delivery_address').value = p.address;
        document.getElementById('patientInfo').classList.remove('hidden');

        const cont = document.getElementById('prescriptionInfo');
        cont.classList.remove('hidden');

        if (p.prescriptions && p.prescriptions.length === 1) {
            selectPrescription(p.prescriptions[0]);
        } else if (previousPrescriptionId) {
            const persisted = p.prescriptions && p.prescriptions.find(x => x.id == previousPrescriptionId);
            if (persisted) {
                selectPrescription(persisted);
            }
        }
    }

    function handlePrescriptionSelection() {
        const detailCont = document.getElementById('prescriptionDetailsContainer');
        const list = document.getElementById('prescriptionList');
        list.innerHTML = '';
        const patientId = hiddenPatientId.value;
        const p = patients[patientId];
        const prescriptionId = hiddenPrescriptionId.value;

        if (!p || !prescriptionId) {
            detailCont.classList.add('hidden');
            return;
        }

        const pr = p.prescriptions.find(x => x.id == prescriptionId);
        if (pr) {
            const meds = pr.medications || [
                {
                    name: pr.medication_name,
                    dosage: pr.dosage,
                    frequency: pr.frequency,
                    instructions: pr.instructions
                }
            ];
            let medsHtml = '';
            meds.forEach((med, i) => {
                medsHtml += `
                    <div class="bg-white p-4 rounded-2xl border border-gold-100 shadow-sm ${i > 0 ? 'mt-3' : ''}">
                        <div class="font-bold text-gold-700 text-sm mb-2 flex items-center">
                            <span class="w-5 h-5 bg-gold-500 text-white text-[10px] rounded-full flex items-center justify-center mr-2">${i+1}</span>
                            ${med.name}
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-[10px]">
                            <div class="bg-gold-50 p-2 rounded-lg text-gold-800"><strong>DOSIS:</strong> ${med.dosage}</div>
                            <div class="bg-gold-50 p-2 rounded-lg text-gold-800"><strong>FREKUENSI:</strong> ${med.frequency}</div>
                        </div>
                        ${med.instructions ? `<div class="mt-2 text-[10px] italic text-gray-500 bg-gray-50 p-2 rounded-lg border border-gray-100">"${med.instructions}"</div>` : ''}
                    </div>
                `;
            });
            list.innerHTML = medsHtml;
            detailCont.classList.remove('hidden');
        } else {
            detailCont.classList.add('hidden');
        }
    }

    function lockPatient(p) {
        lockedPatientName.textContent = p.name;
        lockedPatientCode.textContent = 'No. RM: ' + (p.patient_code || p.medical_record_number || 'RM-?');
        patientSearchMode.classList.add('hidden');
        patientLockedMode.classList.remove('hidden');
    }

    function clearPatientSearch() {
        hiddenPatientId.value = '';
        hiddenPrescriptionId.value = '';
        patientSearchInput.value = '';
        prescriptionSearchInput.value = '';
        patientSearchClear.classList.add('hidden');
        prescriptionSearchClear.classList.add('hidden');
        hideDropdown();
        hidePrescriptionDropdown();
        patientLockedMode.classList.add('hidden');
        prescriptionLockedMode.classList.add('hidden');
        patientSearchMode.classList.remove('hidden');
        prescriptionSearchMode.classList.remove('hidden');
        document.getElementById('patientInfo').classList.add('hidden');
        document.getElementById('prescriptionInfo').classList.add('hidden');
        document.getElementById('prescriptionDetailsContainer').classList.add('hidden');
        patientSearchInput.focus();
    }

    function clearTyping() {
        patientSearchInput.value = '';
        patientSearchClear.classList.add('hidden');
        hideDropdown();
        patientSearchInput.focus();
    }

    function hideDropdown() {
        patientDropdown.classList.add('hidden');
        patientDropdownList.innerHTML = '';
        patientNoResult.classList.add('hidden');
    }

    function showPrescriptionDropdown(results, q) {
        prescriptionDropdownList.innerHTML = '';
        prescriptionNoResult.classList.add('hidden');
        prescriptionDropdown.classList.remove('hidden');

        if (!results.length) {
            prescriptionNoResult.classList.remove('hidden');
            return;
        }

        results.forEach(pr => {
            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'w-full text-left px-5 py-3.5 hover:bg-gold-50 transition-colors flex items-center gap-3 group';
            const label = `Resep - ${new Date(pr.created_at).toLocaleDateString('id-ID', {day: '2-digit', month: 'long', year: 'numeric'})}`;
            item.innerHTML = `
                <div class="w-11 h-11 bg-gold-100 group-hover:bg-gold-500 text-gold-600 group-hover:text-white rounded-xl flex items-center justify-center flex-shrink-0 transition-colors">
                    <i class="fas fa-file-prescription text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-black text-gray-800 truncate">${highlightMatch(label, q)}</p>
                    <p class="text-[10px] text-gray-500">${pr.notes ? pr.notes : 'Tidak ada catatan'}</p>
                </div>
            `;
            item.addEventListener('click', () => selectPrescription(pr));
            prescriptionDropdownList.appendChild(item);
        });
    }

    function selectPrescription(pr) {
        if (!pr) return;
        hiddenPrescriptionId.value = pr.id;
        prescriptionSearchInput.value = `Resep - ${new Date(pr.created_at).toLocaleDateString('id-ID', {day: '2-digit', month: 'long', year: 'numeric'})}`;
        prescriptionSearchClear.classList.remove('hidden');
        lockPrescription(pr);
        handlePrescriptionSelection();
        hidePrescriptionDropdown();
    }

    function lockPrescription(pr) {
        lockedPrescriptionLabel.textContent = `Resep - ${new Date(pr.created_at).toLocaleDateString('id-ID', {day: '2-digit', month: 'long', year: 'numeric'})}`;
        lockedPrescriptionMeta.textContent = pr.notes ? pr.notes : 'Resep terpilih';
        prescriptionSearchMode.classList.add('hidden');
        prescriptionLockedMode.classList.remove('hidden');
    }

    function clearPrescriptionSearch() {
        hiddenPrescriptionId.value = '';
        prescriptionSearchInput.value = '';
        prescriptionSearchClear.classList.add('hidden');
        hidePrescriptionDropdown();
        prescriptionLockedMode.classList.add('hidden');
        prescriptionSearchMode.classList.remove('hidden');
        document.getElementById('prescriptionDetailsContainer').classList.add('hidden');
        prescriptionSearchInput.focus();
    }

    function clearPrescriptionTyping() {
        prescriptionSearchInput.value = '';
        prescriptionSearchClear.classList.add('hidden');
        hidePrescriptionDropdown();
        prescriptionSearchInput.focus();
    }

    function hidePrescriptionDropdown() {
        prescriptionDropdown.classList.add('hidden');
        prescriptionDropdownList.innerHTML = '';
        prescriptionNoResult.classList.add('hidden');
    }

    patientSearchInput.addEventListener('input', function() {
        const results = filterPatients(this.value);
        showPatientDropdown(results, this.value);
        patientSearchClear.classList.toggle('hidden', !this.value.trim());
    });

    patientSearchInput.addEventListener('focus', function() {
        const results = filterPatients(this.value);
        if (results.length) {
            showPatientDropdown(results, this.value);
        }
    });

    patientSearchClear.addEventListener('click', function() {
        clearTyping();
    });

    patientSearchReset.addEventListener('click', function() {
        clearPatientSearch();
    });

    prescriptionSearchInput.addEventListener('input', function() {
        const p = patients[hiddenPatientId.value];
        if (!p) return;
        const results = filterPrescriptions(this.value, p.prescriptions || []);
        showPrescriptionDropdown(results, this.value);
        prescriptionSearchClear.classList.toggle('hidden', !this.value.trim());
    });

    prescriptionSearchInput.addEventListener('focus', function() {
        const p = patients[hiddenPatientId.value];
        if (!p) return;
        const results = filterPrescriptions(this.value, p.prescriptions || []);
        if (results.length) {
            showPrescriptionDropdown(results, this.value);
        }
    });

    prescriptionSearchClear.addEventListener('click', function() {
        clearPrescriptionTyping();
    });

    prescriptionSearchReset.addEventListener('click', function() {
        clearPrescriptionSearch();
    });

    document.addEventListener('click', function(event) {
        if (!event.target.closest('#patientSearchWrapper') && !event.target.closest('#patientDropdown')
            && !event.target.closest('#prescriptionSearchWrapper') && !event.target.closest('#prescriptionDropdown')) {
            hideDropdown();
            hidePrescriptionDropdown();
        }
    });

    @if(old('patient_id'))
        const persistedPatient = patients['{{ old('patient_id') }}'];
        if (persistedPatient) {
            selectPatient(persistedPatient);
        }
    @endif

    function openMapModal() {
        document.getElementById('mapModal').classList.remove('hidden');
        setTimeout(() => {
            if (!map) {
                map = L.map('addressMap').setView([-6.2088, 106.8456], 13);
                L.tileLayer('https://{s}.tile.osm.org/{z}/{x}/{y}.png').addTo(map);
                map.on('click', e => setSelectedLocation(e.latlng.lat, e.latlng.lng));
            }
        }, 100);
    }

    function setSelectedLocation(lat, lng) {
        selectedLatLng = {lat, lng};
        if (marker) map.removeLayer(marker);
        marker = L.marker([lat, lng]).addTo(map);
        document.getElementById('selectedAddress').textContent = `Koordinat: ${lat.toFixed(6)}, ${lng.toFixed(6)} (Mencari alamat...)`;
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
            .then(r => r.json()).then(d => {
                document.getElementById('selectedAddress').textContent = d.display_name;
            });
    }

    function confirmLocation() {
        if (selectedLatLng) {
            document.getElementById('latitude').value = selectedLatLng.lat;
            document.getElementById('longitude').value = selectedLatLng.lng;
            document.getElementById('delivery_address').value = document.getElementById('selectedAddress').textContent;
            document.getElementById('coordinatesText').textContent = `${selectedLatLng.lat.toFixed(4)}, ${selectedLatLng.lng.toFixed(4)}`;
            document.getElementById('coordinatesDisplay').classList.remove('hidden');
            closeMapModal();
        }
    }

    function closeMapModal() { document.getElementById('mapModal').classList.add('hidden'); }

    function getCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(p => {
                const {latitude, longitude} = p.coords;
                document.getElementById('latitude').value = latitude;
                document.getElementById('longitude').value = longitude;
                document.getElementById('coordinatesText').textContent = `${latitude.toFixed(4)}, ${longitude.toFixed(4)}`;
                document.getElementById('coordinatesDisplay').classList.remove('hidden');
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`)
                    .then(r => r.json()).then(d => {
                        document.getElementById('delivery_address').value = d.display_name || d.name;
                    });
            });
        }
    }
</script>

<style>
    @keyframes fade-in { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fade-in 0.4s ease-out forwards; }
</style>
@endsection