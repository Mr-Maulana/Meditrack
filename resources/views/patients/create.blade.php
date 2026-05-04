@extends('layouts.app')

@section('title', 'Tambah Pasien Baru')
@section('page-title', 'Registrasi Pasien')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 animate-fade-in pb-12">
    <!-- Header & Actions -->
    <div class="flex items-center justify-between">
        <a href="{{ route('patients.index') }}" class="text-tni-600 hover:text-tni-800 flex items-center font-bold transition-colors text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
        </a>
    </div>

    <!-- Main Form Card -->
    <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
        <!-- Banner Header -->
        <div class="bg-gradient-to-r from-tni-800 to-tni-900 p-10 text-white relative">
            <div class="absolute top-0 right-0 p-10 opacity-10">
                <i class="fas fa-user-plus text-8xl"></i>
            </div>
            <div class="relative z-10">
                <h2 class="text-3xl font-black tracking-tight">Pendaftaran Pasien Baru</h2>
                <p class="text-tni-100 opacity-80 mt-2 font-medium">Lengkapi data rekam medis pasien untuk integrasi sistem farmasi.</p>
            </div>
        </div>

        <form action="{{ route('patients.store') }}" method="POST" class="p-10">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <!-- Left Section: Basic Info -->
                <div class="space-y-8">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] flex items-center gap-2">
                        <span class="w-2 h-2 bg-tni-600 rounded-full"></span> Identitas Personal
                    </h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label for="name" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Nama Lengkap Pasien <span class="text-red-500">*</span></label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 pl-5 flex items-center text-gray-400 group-focus-within:text-tni-600 transition-colors">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" required 
                                    class="w-full pl-12 pr-6 py-4 bg-gray-50/50 border border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all shadow-inner"
                                    placeholder="Nama sesuai KTP">
                            </div>
                            @error('name') <p class="mt-2 text-[10px] text-red-600 font-bold uppercase">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="gender" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Gender <span class="text-red-500">*</span></label>
                                <select id="gender" name="gender" required 
                                    class="w-full px-5 py-4 bg-gray-50/50 border border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all shadow-inner appearance-none">
                                    <option value="">Pilih...</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label for="date_of_birth" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Tgl Lahir <span class="text-red-500">*</span></label>
                                <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required 
                                    class="w-full px-5 py-4 bg-gray-50/50 border border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all shadow-inner">
                            </div>
                        </div>

                        <div>
                            <label for="phone" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Nomor Telepon <span class="text-red-500">*</span></label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 pl-5 flex items-center text-gray-400 group-focus-within:text-tni-600 transition-colors">
                                    <i class="fas fa-phone"></i>
                                </span>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required 
                                    class="w-full pl-12 pr-6 py-4 bg-gray-50/50 border border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all shadow-inner"
                                    placeholder="0812xxxxxxxx">
                            </div>
                            @error('phone') <p class="mt-2 text-[10px] text-red-600 font-bold uppercase">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Right Section: Medical & Address -->
                <div class="space-y-8">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] flex items-center gap-2">
                        <span class="w-2 h-2 bg-gold-500 rounded-full"></span> Data Medis & Lokasi
                    </h3>

                    <div class="space-y-6">
                        <div>
                            <label for="address" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Alamat Domisili <span class="text-red-500">*</span></label>
                            <div class="flex gap-2">
                                <textarea id="address" name="address" rows="3" required 
                                    class="w-full px-5 py-4 bg-gray-50/50 border border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all shadow-inner"
                                    placeholder="Alamat lengkap pengiriman...">{{ old('address') }}</textarea>
                                <button type="button" id="btnGeocode" class="px-4 bg-tni-800 text-white rounded-2xl hover:bg-black transition flex flex-col items-center justify-center gap-1 shadow-lg shrink-0">
                                    <i class="fas fa-search-location"></i>
                                    <span class="text-[8px] uppercase tracking-wider font-bold">Cari Map</span>
                                </button>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Peta Lokasi Otomatis</label>
                            <div id="patientMap" class="w-full h-48 rounded-2xl border border-gray-200 shadow-inner mb-2" style="z-index: 10;"></div>
                            <p class="text-[9px] text-gray-400 italic font-bold">*Titik peta akan otomatis diperbarui saat Anda selesai mengisi alamat (berpindah kolom) atau menekan tombol Cari Map.</p>
                            <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                            <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
                        </div>

                        <div>
                            <label for="medical_condition" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Kondisi Medis (Opsional)</label>
                            <textarea id="medical_condition" name="medical_condition" rows="3" 
                                class="w-full px-5 py-4 bg-gray-50/50 border border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all shadow-inner"
                                placeholder="Alergi obat, penyakit kronis, dll...">{{ old('medical_condition') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="mt-12 pt-10 border-t border-gray-100 flex flex-col sm:flex-row justify-end gap-4">
                <button type="reset" class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-red-600 transition-colors">
                    Reset Form
                </button>
                <button type="submit" class="px-12 py-4 bg-gradient-to-br from-tni-800 to-black text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-2xl shadow-tni-300 hover:scale-[1.02] transition-all flex items-center justify-center gap-3">
                    <i class="fas fa-save text-gold-400"></i> Simpan Registrasi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let defaultLat = 5.1812;
    let defaultLng = 97.1472;
    
    let map = L.map('patientMap').setView([defaultLat, defaultLng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    let marker = L.marker([defaultLat, defaultLng], {draggable: true}).addTo(map);

    let latInput = document.getElementById('latitude');
    let lngInput = document.getElementById('longitude');
    let addressInput = document.getElementById('address');
    let btnGeocode = document.getElementById('btnGeocode');

    // Init from old input if exists
    if(latInput.value && lngInput.value) {
        let lat = parseFloat(latInput.value);
        let lng = parseFloat(lngInput.value);
        map.setView([lat, lng], 16);
        marker.setLatLng([lat, lng]);
    }

    // Update hidden inputs when marker is dragged
    marker.on('dragend', function(e) {
        let pos = marker.getLatLng();
        latInput.value = pos.lat;
        lngInput.value = pos.lng;
    });

    function geocodeCurrentAddress() {
        let addr = addressInput.value.trim();
        if(addr.length > 5) {
            let btnOriginalHtml = btnGeocode.innerHTML;
            btnGeocode.innerHTML = '<i class="fas fa-spinner animate-spin"></i>';
            
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(addr + ', Lhokseumawe')}`)
                .then(res => res.json())
                .then(data => {
                    if (data && data.length > 0) {
                        let lat = parseFloat(data[0].lat);
                        let lng = parseFloat(data[0].lon);
                        map.setView([lat, lng], 16);
                        marker.setLatLng([lat, lng]);
                        latInput.value = lat;
                        lngInput.value = lng;
                    }
                    btnGeocode.innerHTML = btnOriginalHtml;
                })
                .catch(() => {
                    btnGeocode.innerHTML = btnOriginalHtml;
                });
        }
    }

    // Auto-geocode when leaving the address field
    addressInput.addEventListener('blur', geocodeCurrentAddress);
    
    // Also geocode when clicking the button
    btnGeocode.addEventListener('click', geocodeCurrentAddress);
});
</script>
@endsection