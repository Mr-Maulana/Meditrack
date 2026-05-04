@extends('layouts.app')

@section('title', $patient->name)
@section('page-title', 'Rekam Medis Pasien')

@section('content')
<div class="space-y-8 animate-fade-in pb-12">
    <!-- Header Card -->
    <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-tni-800 to-tni-900 p-8 md:p-12 text-white relative">
            <div class="absolute top-0 right-0 p-12 opacity-10">
                <i class="fas fa-file-medical text-9xl"></i>
            </div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center gap-8">
                <div class="w-32 h-32 rounded-3xl bg-white/10 backdrop-blur-md flex items-center justify-center text-5xl font-black border border-white/20 shadow-2xl text-gold-400">
                    {{ substr($patient->name, 0, 1) }}
                </div>
                <div class="text-center md:text-left flex-1">
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-2">
                        <h2 class="text-3xl md:text-4xl font-black tracking-tight">{{ $patient->name }}</h2>
                        <span class="px-3 py-1 bg-gold-500 text-tni-900 text-[10px] font-black uppercase tracking-[0.2em] rounded-full shadow-lg">
                            {{ $patient->patient_code ?? 'NO-CODE' }}
                        </span>
                    </div>
                    <p class="text-tni-100 opacity-80 font-medium flex items-center justify-center md:justify-start gap-2">
                        <i class="fas fa-id-card-clip text-gold-400"></i> Rekam Medis Terdaftar pada {{ $patient->created_at->format('d M Y') }}
                    </p>
                    
                    <div class="mt-6 flex flex-wrap justify-center md:justify-start gap-3">
                        <a href="{{ route('patients.edit', $patient) }}" class="px-6 py-2.5 bg-white text-tni-800 rounded-xl font-bold text-sm hover:bg-gold-400 hover:text-tni-900 transition-all shadow-lg flex items-center gap-2">
                            <i class="fas fa-edit"></i> Edit Data
                        </a>
                        <a href="{{ route('patients.print', $patient) }}" target="_blank" class="px-6 py-2.5 bg-tni-700 text-white rounded-xl font-bold text-sm hover:bg-tni-600 transition-all border border-tni-600 flex items-center gap-2">
                            <i class="fas fa-print text-gold-400"></i> Cetak Kartu
                        </a>
                        <a href="{{ route('patients.index') }}" class="px-6 py-2.5 bg-white/10 text-white rounded-xl font-bold text-sm hover:bg-white/20 transition-all border border-white/10 flex items-center gap-2">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-100">
            <div class="p-8">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Informasi Personal</p>
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <i class="fas fa-venus-mars"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-tighter">Jenis Kelamin</p>
                            <p class="text-sm font-bold text-gray-800">{{ $patient->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                            <i class="fas fa-cake-candles"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-tighter">Tanggal Lahir</p>
                            <p class="text-sm font-bold text-gray-800">{{ $patient->date_of_birth ? $patient->date_of_birth->format('d M Y') : '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-8">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Kontak & Alamat</p>
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-tighter">Telepon</p>
                            <p class="text-sm font-bold text-gray-800">{{ $patient->phone }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center">
                            <i class="fas fa-map-location-dot"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-tighter">Alamat Tinggal</p>
                            <p class="text-sm font-bold text-gray-800 line-clamp-2">{{ $patient->address }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-8">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Kondisi Medis</p>
                <div class="p-4 bg-red-50 rounded-2xl border border-red-100">
                    <p class="text-sm text-red-800 font-medium leading-relaxed italic">
                        "{{ $patient->medical_condition ?? 'Tidak ada riwayat kondisi medis khusus yang dicatat.' }}"
                    </p>
                </div>
            </div>
        </div>

        <!-- Maps Section -->
        <div class="p-8 border-t border-gray-100 bg-gray-50/30">
            <div class="flex items-center justify-between mb-4">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                    <i class="fas fa-map-marked-alt text-tni-600"></i> Peta Koordinat Tempat Tinggal
                </p>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold text-tni-700 bg-white border border-tni-100 shadow-sm px-3 py-1.5 rounded-lg">
                        Lat: {{ $patient->latitude ?? 'Kosong' }}
                    </span>
                    <span class="text-[10px] font-bold text-tni-700 bg-white border border-tni-100 shadow-sm px-3 py-1.5 rounded-lg">
                        Lng: {{ $patient->longitude ?? 'Kosong' }}
                    </span>
                </div>
            </div>
            <div id="patientMap" class="w-full h-64 rounded-2xl border border-gray-200 shadow-inner" style="z-index: 10;"></div>
            @if(!$patient->latitude || !$patient->longitude)
                <p class="text-[10px] text-red-500 italic mt-3 font-bold flex items-center gap-1">
                    <i class="fas fa-exclamation-triangle"></i> Titik koordinat belum diatur. Silakan Edit Data untuk menyesuaikan lokasi peta.
                </p>
            @endif
        </div>
    </div>

    <!-- Additional Info (History Placeholder) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Prescriptions History -->
        <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
            <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-tni-100 text-tni-700 flex items-center justify-center">
                    <i class="fas fa-prescription-bottle-medical text-sm"></i>
                </div>
                Riwayat Resep Terakhir
            </h3>
            <div class="space-y-4">
                @forelse($patient->prescriptions()->latest()->take(3)->get() as $presc)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <div>
                            <p class="text-sm font-bold text-gray-800">{{ $presc->medication_name }}</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $presc->created_at->format('d M Y') }}</p>
                        </div>
                        <a href="{{ route('prescriptions.show', $presc) }}" class="text-tni-600 hover:text-tni-800 font-bold text-xs">Detail</a>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 italic text-center py-4">Belum ada riwayat resep.</p>
                @endforelse
            </div>
        </div>

        <!-- Deliveries History -->
        <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
            <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-gold-100 text-gold-700 flex items-center justify-center">
                    <i class="fas fa-truck-fast text-sm"></i>
                </div>
                Pengantaran Terakhir
            </h3>
            <div class="space-y-4">
                @forelse($patient->deliveries()->latest()->take(3)->get() as $delivery)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <div>
                            <p class="text-sm font-bold text-gray-800">Status: {{ ucfirst($delivery->status) }}</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $delivery->delivery_date->format('d M Y') }}</p>
                        </div>
                        <a href="{{ route('deliveries.show', $delivery) }}" class="text-tni-600 hover:text-tni-800 font-bold text-xs">Pantau</a>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 italic text-center py-4">Belum ada riwayat pengantaran.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let lat = {{ $patient->latitude ?: 5.1812 }};
    let lng = {{ $patient->longitude ?: 97.1472 }};
    let hasCoords = {{ ($patient->latitude && $patient->longitude) ? 'true' : 'false' }};
    
    let map = L.map('patientMap').setView([lat, lng], hasCoords ? 16 : 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    let marker = L.marker([lat, lng]).addTo(map);
    
    if (hasCoords) {
        marker.bindPopup(`
            <div class="text-center">
                <strong class="text-tni-800">{{ $patient->name }}</strong><br>
                <span class="text-xs text-gray-600">{{ $patient->address }}</span>
            </div>
        `).openPopup();
    }
});
</script>
@endsection