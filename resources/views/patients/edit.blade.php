@extends('layouts.app')

@section('title', 'Edit Pasien: ' . $patient->name)
@section('page-title', 'Pembaruan Data')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 animate-fade-in pb-12">
    <!-- Header & Actions -->
    <div class="flex items-center justify-between">
        <a href="{{ route('patients.show', $patient) }}" class="text-tni-600 hover:text-tni-800 flex items-center font-bold transition-colors text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Batal & Kembali
        </a>
    </div>

    <!-- Main Form Card -->
    <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
        <!-- Banner Header -->
        <div class="bg-gradient-to-r from-tni-800 to-gold-600 p-10 text-white relative">
            <div class="absolute top-0 right-0 p-10 opacity-10">
                <i class="fas fa-user-edit text-8xl"></i>
            </div>
            <div class="relative z-10">
                <h2 class="text-3xl font-black tracking-tight">Perbarui Data Pasien</h2>
                <p class="text-tni-100 opacity-80 mt-2 font-medium">Lakukan perubahan pada informasi rekam medis atau kontak pasien.</p>
            </div>
        </div>

        <form id="patientForm" action="{{ route('patients.update', $patient) }}" method="POST" class="p-10">
            @csrf
            @method('PUT')
            
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
                                <input type="text" id="name" name="name" value="{{ old('name', $patient->name) }}" required 
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
                                    <option value="male" {{ old('gender', $patient->gender) == 'male' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="female" {{ old('gender', $patient->gender) == 'female' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label for="date_of_birth" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Tgl Lahir <span class="text-red-500">*</span></label>
                                <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $patient->date_of_birth ? $patient->date_of_birth->format('Y-m-d') : '') }}" required 
                                    class="w-full px-5 py-4 bg-gray-50/50 border border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all shadow-inner">
                            </div>
                        </div>

                        <div>
                            <label for="phone" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Nomor Telepon <span class="text-red-500">*</span></label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 pl-5 flex items-center text-gray-400 group-focus-within:text-tni-600 transition-colors">
                                    <i class="fas fa-phone"></i>
                                </span>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone', $patient->phone) }}" required 
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
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <label for="province" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Provinsi</label>
                                <select id="province" name="province" required
                                    class="w-full px-5 py-4 bg-gray-50/50 border border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all shadow-inner appearance-none">
                                    <option value="">Pilih provinsi...</option>
                                </select>
                                <p id="provinceError" class="mt-2 text-[10px] text-red-600 font-bold hidden">Gagal memuat provinsi.</p>
                            </div>
                            <div>
                                <label for="city" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Kota / Kabupaten</label>
                                <select id="city" name="city" required disabled
                                    class="w-full px-5 py-4 bg-gray-50/50 border border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all shadow-inner appearance-none">
                                    <option value="">Pilih kota atau kabupaten...</option>
                                </select>
                            </div>
                            <div>
                                <label for="subdistrict" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Kecamatan</label>
                                <select id="subdistrict" name="subdistrict" required disabled
                                    class="w-full px-5 py-4 bg-gray-50/50 border border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all shadow-inner appearance-none">
                                    <option value="">Pilih kecamatan...</option>
                                </select>
                            </div>
                            <div>
                                <label for="village" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Desa / Kelurahan</label>
                                <select id="village" name="village" required disabled
                                    class="w-full px-5 py-4 bg-gray-50/50 border border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all shadow-inner appearance-none">
                                    <option value="">Pilih desa atau kelurahan...</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Peta Lokasi Otomatis</label>
                            <div id="patientMap" class="w-full h-48 rounded-2xl border border-gray-200 shadow-inner mb-2" style="z-index: 10;"></div>
                            <p class="text-[9px] text-gray-400 italic font-bold">*Pilih provinsi terlebih dahulu, lalu kota, kecamatan, dan desa untuk mempersempit lokasi secara otomatis.</p>
                            <input type="hidden" id="address" name="address" value="{{ old('address', $patient->address) }}">
                            <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $patient->latitude) }}">
                            <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $patient->longitude) }}">
                        </div>

                        <div>
                            <label for="medical_condition" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Kondisi Medis (Opsional)</label>
                            <textarea id="medical_condition" name="medical_condition" rows="3" 
                                class="w-full px-5 py-4 bg-gray-50/50 border border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all shadow-inner"
                                placeholder="Alergi obat, penyakit kronis, dll...">{{ old('medical_condition', $patient->medical_condition) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="mt-12 pt-10 border-t border-gray-100 flex flex-col sm:flex-row justify-end gap-4">
                <a href="{{ route('patients.show', $patient) }}" class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 transition-colors flex items-center justify-center">
                    Batal
                </a>
                <button type="submit" class="px-12 py-4 bg-gradient-to-br from-gold-500 to-gold-700 text-tni-900 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-2xl shadow-gold-200 hover:scale-[1.02] transition-all flex items-center justify-center gap-3">
                    <i class="fas fa-check-circle"></i> Perbarui Data
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

    let patientForm = document.getElementById('patientForm');
    let addressInput = document.getElementById('address');
    let latInput = document.getElementById('latitude');
    let lngInput = document.getElementById('longitude');
    let villageSelect = document.getElementById('village');
    let subdistrictSelect = document.getElementById('subdistrict');
    let citySelect = document.getElementById('city');
    let provinceSelect = document.getElementById('province');
    let provinceError = document.getElementById('provinceError');

    let initialValues = {
        province: '{{ old('province', $patient->province ?? '') }}',
        city: '{{ old('city', $patient->city ?? '') }}',
        subdistrict: '{{ old('subdistrict', $patient->subdistrict ?? '') }}',
        village: '{{ old('village', $patient->village ?? '') }}'
    };

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

    function normalizeAddress(addr) {
        return addr.replace(/\s+/g, ' ').trim();
    }

    function formatRegionName(name) {
        if (!name) return '';
        return name
            .toLowerCase()
            .split(' ')
            .map(word => {
                if (word === 'kota' || word === 'kabupaten' || word === 'kecamatan' || word === 'desa' || word === 'kelurahan' || word === 'gampong') {
                    return word.charAt(0).toUpperCase() + word.slice(1);
                }
                return word.charAt(0).toUpperCase() + word.slice(1);
            })
            .join(' ');
    }

    function appendOptions(select, items, selectedValue, placeholder) {
        select.innerHTML = `<option value="">${placeholder}</option>`;
        items.forEach(item => {
            let formatted = formatRegionName(item.name);
            let opt = document.createElement('option');
            opt.value = formatted;
            opt.textContent = formatted;
            opt.dataset.id = item.id;
            if (selectedValue && normalizeAddress(selectedValue).toLowerCase() === normalizeAddress(formatted).toLowerCase()) {
                opt.selected = true;
            }
            select.appendChild(opt);
        });
        select.disabled = false;
    }

    function clearSelect(select, placeholder) {
        select.innerHTML = `<option value="">${placeholder}</option>`;
        select.disabled = true;
    }

    function setLoading(select, placeholder) {
        select.innerHTML = `<option value="">${placeholder}</option>`;
        select.disabled = true;
    }

    function fetchJson(url) {
        return fetch(url).then(res => {
            if (!res.ok) {
                throw new Error(`HTTP ${res.status}`);
            }
            return res.json();
        });
    }

    function selectOptionByName(select, name) {
        if (!name) return false;
        let normalizedName = normalizeAddress(name).toLowerCase();
        let matched = Array.from(select.options).find(opt => normalizeAddress(opt.textContent).toLowerCase() === normalizedName);
        if (matched) {
            matched.selected = true;
            return true;
        }
        return false;
    }

    function loadProvinces() {
        provinceError.classList.add('hidden');
        setLoading(provinceSelect, 'Memuat provinsi...');
        setLoading(citySelect, 'Pilih kota atau kabupaten...');
        setLoading(subdistrictSelect, 'Pilih kecamatan...');
        setLoading(villageSelect, 'Pilih desa atau kelurahan...');

        fetchJson('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json')
            .then(provinces => {
                appendOptions(provinceSelect, provinces, initialValues.province, 'Pilih provinsi...');
                if (initialValues.province && selectOptionByName(provinceSelect, initialValues.province)) {
                    let provinceId = provinceSelect.selectedOptions[0]?.dataset.id;
                    if (provinceId) {
                        loadRegencies(provinceId, initialValues.city);
                    }
                }
            })
            .catch(err => {
                console.error('Gagal memuat provinsi:', err);
                clearSelect(provinceSelect, 'Gagal memuat provinsi');
                provinceError.textContent = 'Gagal memuat provinsi. Silakan refresh halaman atau coba lagi nanti.';
                provinceError.classList.remove('hidden');
            });
    }

    function loadRegencies(provinceId, selectedCityName = null) {
        setLoading(citySelect, 'Memuat kota atau kabupaten...');
        clearSelect(subdistrictSelect, 'Pilih kecamatan...');
        clearSelect(villageSelect, 'Pilih desa atau kelurahan...');

        fetchJson(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provinceId}.json`)
            .then(regencies => {
                appendOptions(citySelect, regencies, selectedCityName || initialValues.city, 'Pilih kota atau kabupaten...');
                if (selectedCityName && selectOptionByName(citySelect, selectedCityName)) {
                    let regencyId = citySelect.selectedOptions[0]?.dataset.id;
                    if (regencyId) {
                        loadDistricts(regencyId, initialValues.subdistrict);
                    }
                }
            })
            .catch(err => {
                console.error('Gagal memuat kota/kabupaten:', err);
                clearSelect(citySelect, 'Gagal memuat kota/kabupaten');
            });
    }

    function loadDistricts(regencyId, selectedSubdistrictName = null) {
        setLoading(subdistrictSelect, 'Memuat kecamatan...');
        clearSelect(villageSelect, 'Pilih desa atau kelurahan...');

        fetchJson(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${regencyId}.json`)
            .then(districts => {
                appendOptions(subdistrictSelect, districts, selectedSubdistrictName || initialValues.subdistrict, 'Pilih kecamatan...');
                if (selectedSubdistrictName && selectOptionByName(subdistrictSelect, selectedSubdistrictName)) {
                    let districtId = subdistrictSelect.selectedOptions[0]?.dataset.id;
                    if (districtId) {
                        loadVillages(districtId, initialValues.village);
                    }
                }
            })
            .catch(err => {
                console.error('Gagal memuat kecamatan:', err);
                clearSelect(subdistrictSelect, 'Gagal memuat kecamatan');
            });
    }

    function loadVillages(districtId, selectedVillageName = null) {
        setLoading(villageSelect, 'Memuat desa atau kelurahan...');

        fetchJson(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${districtId}.json`)
            .then(villages => {
                appendOptions(villageSelect, villages, selectedVillageName || initialValues.village, 'Pilih desa atau kelurahan...');
            })
            .catch(err => {
                console.error('Gagal memuat desa/kelurahan:', err);
                clearSelect(villageSelect, 'Gagal memuat desa/kelurahan');
            });
    }

    function getSelectText(select) {
        return select.value ? normalizeAddress(select.value) : '';
    }

    function formatAddressComponent(value, type) {
        if (!value) return '';
        value = normalizeAddress(value);

        if (type === 'village') {
            if (/^(desa|kelurahan|gampong)\s+/i.test(value)) {
                return value;
            }
            return `Desa ${value}`;
        }

        if (type === 'subdistrict') {
            if (/^kecamatan\s+/i.test(value)) {
                return value;
            }
            return `Kecamatan ${value}`;
        }

        return value;
    }

    function getAddressComponentRaw(value) {
        if (!value) return '';
        value = normalizeAddress(value);
        return value.replace(/^(desa|kelurahan|gampong|kecamatan)\s+/i, '').trim();
    }

    function buildAddressString() {
        let village = formatAddressComponent(getSelectText(villageSelect), 'village');
        let subdistrict = formatAddressComponent(getSelectText(subdistrictSelect), 'subdistrict');
        let city = getSelectText(citySelect);
        let province = getSelectText(provinceSelect);

        let components = [];
        if (village) components.push(village);
        if (subdistrict) components.push(subdistrict);
        if (city) components.push(city);
        if (province) components.push(province);

        return components.filter(Boolean).join(', ');
    }

    function updateHiddenAddress() {
        if (addressInput) {
            addressInput.value = buildAddressString();
        }
    }

    function buildGeocodeQueries() {
        let village = formatAddressComponent(getSelectText(villageSelect), 'village');
        let villageRaw = getAddressComponentRaw(getSelectText(villageSelect));
        let subdistrict = formatAddressComponent(getSelectText(subdistrictSelect), 'subdistrict');
        let subdistrictRaw = getAddressComponentRaw(getSelectText(subdistrictSelect));
        let city = getSelectText(citySelect);
        let province = getSelectText(provinceSelect);

        let queries = [];

        // When village is selected, Nominatim typically doesn't have village-level data
        // so we focus on subdistrict level and above
        if (village && subdistrict && city && province) {
            queries.push(`${subdistrict}, ${city}, ${province}, Indonesia`);
            queries.push(`${subdistrictRaw}, ${city}, ${province}, Indonesia`);
            queries.push(`${subdistrict}, ${province}, Indonesia`);
            queries.push(`${subdistrictRaw}, ${province}, Indonesia`);
            queries.push(`${city}, ${province}, Indonesia`);
        } else if (village && subdistrict && city) {
            queries.push(`${subdistrict}, ${city}, Indonesia`);
            queries.push(`${subdistrictRaw}, ${city}, Indonesia`);
            queries.push(`${city}, Indonesia`);
        } else if (village && subdistrict) {
            queries.push(`${subdistrict}, Indonesia`);
            queries.push(`${subdistrictRaw}, Indonesia`);
        } else if (village && city && province) {
            queries.push(`${city}, ${province}, Indonesia`);
        } else if (village && city) {
            queries.push(`${city}, Indonesia`);
        } else if (village && province) {
            queries.push(`${province}, Indonesia`);
        } else if (village) {
            queries.push(`${village}, Indonesia`);
            queries.push(`${villageRaw}, Indonesia`);
        } else if (subdistrict && city && province) {
            queries.push(`${subdistrict}, ${city}, ${province}, Indonesia`);
            queries.push(`${subdistrictRaw}, ${city}, ${province}, Indonesia`);
            queries.push(`${subdistrict}, ${province}, Indonesia`);
            queries.push(`${subdistrictRaw}, ${province}, Indonesia`);
        } else if (subdistrict && city) {
            queries.push(`${subdistrict}, ${city}, Indonesia`);
            queries.push(`${subdistrictRaw}, ${city}, Indonesia`);
        } else if (subdistrict && province) {
            queries.push(`${subdistrict}, ${province}, Indonesia`);
            queries.push(`${subdistrictRaw}, ${province}, Indonesia`);
        } else if (subdistrict) {
            queries.push(`${subdistrict}, Indonesia`);
            queries.push(`${subdistrictRaw}, Indonesia`);
        } else if (city && province) {
            queries.push(`${city}, ${province}, Indonesia`);
        } else if (city) {
            queries.push(`${city}, Indonesia`);
        } else if (province) {
            queries.push(`${province}, Indonesia`);
        }

        return Array.from(new Set(queries.filter(q => q)));
    }

    function shouldGeocode() {
        return villageSelect.value || subdistrictSelect.value || citySelect.value || provinceSelect.value;
    }

    function searchNominatim(query) {
        return fetch(`https://nominatim.openstreetmap.org/search?format=json&limit=1&addressdetails=1&q=${encodeURIComponent(query)}`)
            .then(res => res.json());
    }

    function geocodeCurrentAddress() {
        if (!shouldGeocode()) {
            return;
        }

        let queries = buildGeocodeQueries();
        if (queries.length === 0) {
            return;
        }

        let attemptIndex = 0;

        function tryNext() {
            if (attemptIndex >= queries.length) {
                console.warn('Alamat tidak ditemukan untuk semua query:', queries);
                return;
            }

            let query = queries[attemptIndex++];
            searchNominatim(query)
                .then(data => {
                    if (data && data.length > 0) {
                        let lat = parseFloat(data[0].lat);
                        let lng = parseFloat(data[0].lon);
                        map.setView([lat, lng], 16);
                        marker.setLatLng([lat, lng]);
                        latInput.value = lat;
                        lngInput.value = lng;
                    } else {
                        tryNext();
                    }
                })
                .catch(err => {
                    console.error('Geocode error for query', query, err);
                    tryNext();
                });
        }

        tryNext();
    }

    provinceSelect.addEventListener('change', function() {
        let provinceId = this.selectedOptions[0]?.dataset.id;
        if (provinceId) {
            loadRegencies(provinceId);
        }
        updateHiddenAddress();
        geocodeCurrentAddress();
    });
    citySelect.addEventListener('change', function() {
        let cityId = this.selectedOptions[0]?.dataset.id;
        if (cityId) {
            loadDistricts(cityId);
        }
        updateHiddenAddress();
        geocodeCurrentAddress();
    });
    subdistrictSelect.addEventListener('change', function() {
        let districtId = this.selectedOptions[0]?.dataset.id;
        if (districtId) {
            loadVillages(districtId);
        }
        updateHiddenAddress();
        geocodeCurrentAddress();
    });
    villageSelect.addEventListener('change', function() {
        updateHiddenAddress();
        geocodeCurrentAddress();
    });

    if (patientForm) {
        patientForm.addEventListener('submit', updateHiddenAddress);
    }

    // Load initial province list
    loadProvinces();
});
</script>
@endsection