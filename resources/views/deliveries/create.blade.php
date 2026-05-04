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
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
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
                            <label for="patient_id" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Data Pasien <span class="text-red-500">*</span></label>
                            <select id="patient_id" name="patient_id" required class="w-full bg-gray-50 border-gray-200 rounded-2xl py-4 px-6 text-sm focus:ring-tni-500 focus:border-tni-500 transition-all font-medium">
                                <option value="">Cari Nama atau Nomor Rekam Medis...</option>
                                @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" data-address="{{ $patient->address }}">
                                    [{{ $patient->patient_code ?? $patient->medical_record_number }}] {{ $patient->name }}
                                </option>
                                @endforeach
                            </select>
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
                            <label for="prescription_id" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Resep Obat yang Akan Dikirim <span class="text-red-500">*</span></label>
                            <select id="prescription_id" name="prescription_id" required class="w-full bg-gold-50 border-gold-200 rounded-2xl py-4 px-6 text-sm focus:ring-gold-500 focus:border-gold-500 transition-all font-bold text-gold-900">
                                <option value="">Pilih Resep...</option>
                            </select>
                            
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

    document.getElementById('patient_id').addEventListener('change', function() {
        const p = patients[this.value];
        if (p) {
            document.getElementById('selectedPatientName').textContent = p.name;
            document.getElementById('selectedPatientPhone').textContent = p.phone;
            document.getElementById('selectedPatientAddress').textContent = p.address;
            document.getElementById('delivery_address').value = p.address;
            document.getElementById('patientInfo').classList.remove('hidden');

            // Handle Prescriptions
            const sel = document.getElementById('prescription_id');
            const cont = document.getElementById('prescriptionInfo');
            sel.innerHTML = '<option value="">Pilih Resep...</option>';
            
            if (p.prescriptions && p.prescriptions.length > 0) {
                p.prescriptions.forEach(pr => {
                    const opt = document.createElement('option');
                    opt.value = pr.id;
                    const dateObj = new Date(pr.created_at);
                    const formattedDate = dateObj.toLocaleDateString('id-ID', {day: '2-digit', month: 'long', year: 'numeric'});
                    opt.textContent = `Resep - ${formattedDate}`;
                    sel.appendChild(opt);
                });
                cont.classList.remove('hidden');
                
                sel.onchange = function() {
                    const detailCont = document.getElementById('prescriptionDetailsContainer');
                    const list = document.getElementById('prescriptionList');
                    list.innerHTML = '';
                    const pr = p.prescriptions.find(x => x.id == this.value);
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
                };

                if (p.prescriptions.length === 1) {
                    sel.value = p.prescriptions[0].id;
                    sel.dispatchEvent(new Event('change'));
                }
            } else {
                sel.innerHTML = '<option value="">Tidak ada resep aktif</option>';
                cont.classList.remove('hidden');
            }
        } else {
            document.getElementById('patientInfo').classList.add('hidden');
            document.getElementById('prescriptionInfo').classList.add('hidden');
        }
    });

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