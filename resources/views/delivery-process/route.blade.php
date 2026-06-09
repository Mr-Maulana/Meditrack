@extends('layouts.app')

@section('title', 'Navigasi Pengantaran')
@section('page-title', 'Navigasi Real-Time')

@section('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #routeMap {
        height: calc(100vh - 12rem);
        width: 100%;
        border-radius: 2.5rem;
        z-index: 10;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
    }
    
    .floating-stats {
        position: absolute;
        top: 1.5rem;
        left: 1.5rem;
        right: 1.5rem;
        z-index: 1000;
        display: flex;
        gap: 1rem;
        pointer-events: none;
    }
    
    .stat-pill {
        pointer-events: auto;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        padding: 0.75rem 1.5rem;
        border-radius: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
    }

    .bottom-controls {
        position: absolute;
        bottom: 2rem;
        left: 1.5rem;
        right: 1.5rem;
        z-index: 1000;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        pointer-events: none;
    }

    .action-group {
        pointer-events: auto;
        background: rgba(37, 67, 40, 0.9); /* tni-800 with opacity */
        backdrop-filter: blur(16px);
        padding: 1rem;
        border-radius: 2rem;
        display: flex;
        gap: 0.75rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .destination-marker {
        background-color: #ef4444;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        border: 4px solid white;
        box-shadow: 0 0 15px rgba(239, 68, 68, 0.5);
    }
    
    .current-marker {
        background-color: #3b82f6;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        border: 4px solid white;
        box-shadow: 0 0 15px rgba(59, 130, 246, 0.5);
    }

    .leaflet-control-zoom {
        border: none !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
    }

    .leaflet-control-zoom a {
        background: white !important;
        color: #254328 !important;
        border-radius: 12px !important;
        margin-bottom: 5px !important;
    }

    /* ─── Mobile Info FAB ─── */
    .info-fab {
        position: fixed;
        bottom: 6rem;
        left: 1.25rem;
        z-index: 2000;
        display: none;
    }
    @media (max-width: 767px) {
        .info-fab { display: flex; align-items: center; justify-content: center; }
    }
    .info-fab-btn {
        width: 3.25rem;
        height: 3.25rem;
        border-radius: 9999px;
        background: linear-gradient(135deg, #254328 0%, #3d6b42 100%);
        color: white;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        box-shadow: 0 8px 24px rgba(37,67,40,0.45);
        position: relative;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .info-fab-btn:active { transform: scale(0.93); }
    .info-fab-btn .fab-icon {
        transition: transform 0.35s cubic-bezier(0.4,0,0.2,1);
    }
    .info-fab-btn.open .fab-icon { transform: rotate(45deg); }
    .info-fab-btn::before {
        content: '';
        position: absolute;
        inset: -4px;
        border-radius: 9999px;
        background: rgba(37,67,40,0.25);
        animation: fabPulse 2.5s ease-out infinite;
    }
    @keyframes fabPulse {
        0%   { transform: scale(1); opacity: 0.7; }
        70%  { transform: scale(1.5); opacity: 0; }
        100% { transform: scale(1.5); opacity: 0; }
    }

    /* ─── Slide-up panel (mobile) ─── */
    .mobile-info-panel {
        position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1900;
        background: rgba(255,255,255,0.97);
        backdrop-filter: blur(20px);
        border-radius: 1.75rem 1.75rem 0 0;
        padding: 1.5rem 1.5rem 2.5rem;
        box-shadow: 0 -12px 40px rgba(0,0,0,0.18);
        transform: translateY(100%);
        transition: transform 0.38s cubic-bezier(0.4,0,0.2,1);
        display: none;
    }
    @media (max-width: 767px) {
        .mobile-info-panel { display: block; }
    }
    .mobile-info-panel.visible {
        transform: translateY(0);
    }
    .panel-drag-handle {
        width: 2.5rem;
        height: 4px;
        background: #d1d5db;
        border-radius: 9999px;
        margin: 0 auto 1.25rem;
    }

    /* ─── Panel backdrop overlay ─── */
    .panel-backdrop {
        position: fixed;
        inset: 0;
        z-index: 1800;
        background: rgba(0,0,0,0);
        pointer-events: none;
        transition: background 0.38s ease;
    }
    .panel-backdrop.visible {
        background: rgba(0,0,0,0.35);
        pointer-events: auto;
    }
</style>
@endsection

@section('content')
<div class="relative animate-fade-in">
    <!-- Map Container -->
    <div id="routeMap"></div>

    <!-- Floating Stats Section -->
    <div class="floating-stats flex-wrap md:flex-nowrap">
        <div class="stat-pill">
            <div class="w-10 h-10 bg-gold-100 text-gold-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">Estimasi Tiba</p>
                <p class="text-sm font-black text-gray-800" id="countdownTimer">--:--</p>
            </div>
        </div>

        <div class="stat-pill">
            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-route"></i>
            </div>
            <div>
                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">Sisa Jarak</p>
                <p class="text-sm font-black text-gray-800" id="distanceDisplay">-- km</p>
            </div>
        </div>

        <div class="stat-pill hidden md:flex">
            <div class="w-10 h-10 bg-tni-100 text-tni-700 rounded-xl flex items-center justify-center">
                <i class="fas fa-user"></i>
            </div>
            <div>
                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">Tujuan Pasien</p>
                <p class="text-sm font-black text-gray-800">{{ $assessment->delivery->patient->name }}</p>
            </div>
        </div>
    </div>

        <!-- Bottom Controls -->
        <div class="bottom-controls flex-col md:flex-row gap-4">
            <!-- Desktop Floating Info Card -->
            <div class="pointer-events-auto bg-white/90 backdrop-blur-xl p-6 rounded-[2rem] shadow-2xl border border-white/20 w-full md:w-80 hidden md:block" id="targetInfoCard">
                <h4 class="text-xs font-black text-gold-600 uppercase tracking-widest mb-4">Target Pengantaran</h4>
                <div class="space-y-4">
                    <div class="flex gap-3">
                        <i class="fas fa-map-marker-alt text-red-500 mt-1"></i>
                        <p class="text-[11px] font-bold text-gray-700 leading-relaxed">{{ $assessment->delivery->delivery_address }}</p>
                    </div>
                    <div class="flex flex-col gap-2">
                        <div class="flex gap-3 items-center">
                            <i class="fas fa-phone text-tni-500 mt-0.5"></i>
                            <p class="text-[11px] font-bold text-gray-700">{{ $assessment->delivery->patient->phone }}</p>
                        </div>
                        <div class="flex gap-3">
                            <a href="tel:{{ $assessment->delivery->patient->phone }}" class="flex items-center px-4 py-2 bg-tni-500 text-white rounded-xl hover:bg-tni-600 transition-colors text-[10px] font-bold"><i class="fas fa-phone mr-2"></i> Telepon</a>
                            <a href="https://wa.me/{{ $assessment->delivery->patient->phone }}" target="_blank" class="flex items-center px-4 py-2 bg-green-500 text-white rounded-xl hover:bg-green-600 transition-colors text-[10px] font-bold"><i class="fab fa-whatsapp mr-2"></i> WhatsApp</a>
                        </div>
                    </div>
                
                <div class="pt-4 border-t border-gray-100">
                    <a href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($assessment->delivery->delivery_address) }}" 
                       target="_blank"
                       class="flex items-center justify-center gap-3 w-full py-3 bg-white border-2 border-gold-500 text-gold-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-gold-50 transition-all">
                        <i class="fas fa-location-arrow"></i>
                        Buka di Google Maps
                    </a>
                </div>
                
                @if($assessment->delivery->prescription)
                <div class="pt-4 border-t border-gray-100">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-wider mb-2">Obat Dibawa:</p>
                    <div class="flex flex-wrap gap-1.5">
                        @php $meds = $assessment->delivery->prescription->medications ?? [['name' => $assessment->delivery->prescription->medication_name]]; @endphp
                        @foreach(array_slice($meds, 0, 2) as $med)
                        <span class="text-[9px] bg-blue-50 text-blue-700 px-2 py-0.5 rounded-lg font-black border border-blue-100">
                            {{ $med['name'] ?? '-' }}
                        </span>
                        @endforeach
                        @if(count($meds) > 2)
                        <span class="text-[9px] text-gray-400 font-bold">+{{ count($meds)-2 }} lainnya</span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Right Action Group -->
        <div class="action-group w-full md:w-auto">
            <button onclick="getCurrentLocation()" class="w-12 h-12 flex items-center justify-center bg-white/10 hover:bg-white/20 text-white rounded-xl transition-all border border-white/10" title="Update Lokasi">
                <i class="fas fa-location-arrow"></i>
            </button>
            <button onclick="toggleCancelModal()" class="w-12 h-12 flex items-center justify-center bg-red-500/20 hover:bg-red-500 text-red-100 rounded-xl transition-all border border-red-500/30" title="Batalkan">
                <i class="fas fa-times"></i>
            </button>
            <button onclick="markArrival()" id="arrivalBtn" class="flex-1 md:flex-none px-8 py-3 bg-gradient-to-r from-gold-400 to-gold-600 text-tni-900 rounded-xl font-black uppercase tracking-widest text-xs hover:from-gold-300 hover:to-gold-500 transition-all shadow-xl shadow-gold-500/20 flex items-center justify-center gap-2">
                <i class="fas fa-flag-checkered"></i> Tiba di Lokasi
            </button>
        </div>
    </div>
</div>

<!-- Panel Backdrop (mobile) -->
<div id="panelBackdrop" class="panel-backdrop" onclick="closeTargetPanel()"></div>

<!-- Mobile Slide-up Info Panel -->
<div id="mobileInfoPanel" class="mobile-info-panel">
    <div class="panel-drag-handle"></div>
    <div class="flex items-center justify-between mb-4">
        <h4 class="text-sm font-black text-gray-800 uppercase tracking-widest flex items-center gap-2">
            <i class="fas fa-map-pin text-gold-500"></i> Target Pengantaran
        </h4>
        <button onclick="closeTargetPanel()" class="w-8 h-8 flex items-center justify-center bg-gray-100 rounded-full text-gray-400 hover:bg-gray-200 transition-colors">
            <i class="fas fa-times text-xs"></i>
        </button>
    </div>
    <div class="space-y-3">
        <!-- Alamat -->
        <div class="flex gap-3 bg-red-50 p-3 rounded-2xl">
            <i class="fas fa-map-marker-alt text-red-500 mt-0.5"></i>
            <p class="text-xs font-bold text-gray-700 leading-relaxed">{{ $assessment->delivery->delivery_address }}</p>
        </div>
        <!-- Telepon -->
        <div class="bg-gray-50 p-3 rounded-2xl">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-phone text-tni-600 text-xs"></i>
                <p class="text-xs font-black text-gray-800">{{ $assessment->delivery->patient->phone }}</p>
            </div>
            <div class="flex gap-2">
                <a href="tel:{{ $assessment->delivery->patient->phone }}" class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-tni-600 text-white rounded-xl text-xs font-black hover:bg-tni-700 active:scale-95 transition-all">
                    <i class="fas fa-phone"></i> Telepon
                </a>
                <a href="https://wa.me/{{ $assessment->delivery->patient->phone }}" target="_blank" class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-green-500 text-white rounded-xl text-xs font-black hover:bg-green-600 active:scale-95 transition-all">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
            </div>
        </div>
        <!-- Google Maps -->
        <a href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($assessment->delivery->delivery_address) }}" target="_blank" class="flex items-center justify-center gap-2 w-full py-3 border-2 border-gold-500 text-gold-600 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-gold-50 active:scale-95 transition-all">
            <i class="fas fa-location-arrow"></i> Buka di Google Maps
        </a>
        @if($assessment->delivery->prescription)
        <div class="pt-2 border-t border-gray-100">
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-wider mb-2">Obat Dibawa:</p>
            <div class="flex flex-wrap gap-1.5">
                @php $medsMobile = $assessment->delivery->prescription->medications ?? [['name' => $assessment->delivery->prescription->medication_name]]; @endphp
                @foreach(array_slice($medsMobile, 0, 3) as $med)
                <span class="text-[9px] bg-blue-50 text-blue-700 px-2 py-0.5 rounded-lg font-black border border-blue-100">{{ $med['name'] ?? '-' }}</span>
                @endforeach
                @if(count($medsMobile) > 3)
                <span class="text-[9px] text-gray-400 font-bold">+{{ count($medsMobile)-3 }} lainnya</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Mobile FAB button -->
<div class="info-fab">
    <button type="button" id="infoFabBtn" class="info-fab-btn" onclick="toggleTargetPanel()" title="Info Pengantaran">
        <i class="fas fa-info fab-icon"></i>
    </button>
</div>

<!-- Modal Panduan (Hidden by default) -->
<div id="instructionModal" class="fixed inset-0 z-[2000] flex items-center justify-center hidden">
    <div class="absolute inset-0 bg-tni-900/80 backdrop-blur-md" onclick="toggleInstructions()"></div>
    <div class="relative bg-white rounded-[2.5rem] p-10 max-w-md w-full shadow-2xl mx-4">
        <h3 class="text-xl font-black text-gray-800 mb-6 flex items-center gap-3">
            <i class="fas fa-clipboard-list text-gold-500"></i>
            Instruksi Penting
        </h3>
        <div class="space-y-6">
            @foreach(['Verifikasi identitas pasien dengan benar', 'Pastikan obat tidak rusak/terbuka', 'Jelaskan dosis penggunaan', 'Lakukan dokumentasi foto penyerahan'] as $i => $inst)
            <div class="flex gap-4">
                <span class="w-8 h-8 rounded-full bg-tni-100 text-tni-700 flex items-center justify-center font-black text-xs shrink-0">{{ $i+1 }}</span>
                <p class="text-sm text-gray-600 font-bold leading-relaxed">{{ $inst }}</p>
            </div>
            @endforeach
        </div>
        <button onclick="toggleInstructions()" class="w-full mt-10 py-4 bg-tni-800 text-white rounded-2xl font-black uppercase tracking-widest hover:bg-black transition-all">
            Saya Mengerti
        </button>
    </div>
</div>

    <!-- Cancel Modal -->
    <div id="cancelModal" class="fixed inset-0 z-[3000] flex items-center justify-center hidden p-6">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-md"></div>
        <div class="relative bg-white rounded-[3rem] p-10 max-w-sm w-full text-center shadow-2xl border border-gray-100 animate-fade-in">
            <div class="w-20 h-20 bg-red-50 text-red-500 rounded-[2rem] flex items-center justify-center text-3xl mx-auto mb-6 shadow-inner">
                <i class="fas fa-ban"></i>
            </div>
            <h3 class="text-2xl font-black text-gray-800 mb-3">Batalkan Tugas?</h3>
            <p class="text-gray-500 font-bold text-sm leading-relaxed mb-10">Tugas pengantaran akan dihentikan dan status akan dikembalikan menjadi 'Menunggu'. Konfirmasi pembatalan?</p>
            
            <div class="space-y-3">
                <button onclick="confirmCancellation()" id="btnConfirmCancel" class="w-full py-4 bg-red-600 text-white rounded-2xl font-black uppercase tracking-widest hover:bg-red-700 transition-all shadow-lg shadow-red-600/20">
                    Ya, Batalkan Pengantaran
                </button>
                <button onclick="toggleCancelModal()" class="w-full py-4 bg-gray-100 text-gray-500 rounded-2xl font-black uppercase tracking-widest hover:bg-gray-200 transition-all">
                    Kembali ke Navigasi
                </button>
            </div>
        </div>
    </div>

    <!-- Success Cancel Modal -->
    <div id="successCancelModal" class="fixed inset-0 z-[4000] flex items-center justify-center hidden">
        <div class="absolute inset-0 bg-tni-900/90 backdrop-blur-xl"></div>
        <div class="relative bg-white rounded-[3rem] p-12 max-w-sm w-full text-center shadow-2xl">
            <div class="w-24 h-24 bg-red-50 text-red-500 rounded-[2.5rem] flex items-center justify-center text-4xl mx-auto mb-8 shadow-inner">
                <i class="fas fa-times-circle"></i>
            </div>
            <h3 class="text-2xl font-black text-gray-800 mb-2">Dibatalkan</h3>
            <p class="text-gray-500 font-bold text-sm leading-relaxed mb-8">Pengantaran telah berhasil dibatalkan. Mengarahkan kembali ke daftar tugas...</p>
            <div class="text-xs font-black text-tni-800 uppercase tracking-widest">
                <i class="fas fa-spinner animate-spin mr-3"></i> Memuat...
            </div>
        </div>
    </div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
let map;
let routePolyline;
let currentLocationMarker;
let destinationMarker;
let currentLocation = null;
let destLat = @json($assessment->delivery->latitude ?: ($assessment->delivery->patient->latitude ?: null));
let destLng = @json($assessment->delivery->longitude ?: ($assessment->delivery->patient->longitude ?: null));
let deliveryAddress = @json($assessment->delivery->delivery_address);
let destination = null;

let routeInterval;
let countdownInterval;
let estimatedMinutes = 0;
let distanceKm = 0;

document.addEventListener('DOMContentLoaded', function() {
    if (destLat !== null && destLng !== null) {
        destination = { lat: destLat, lng: destLng };
        initializeTracking();
    } else {
        // Geocode the address using Nominatim (OpenStreetMap) if coordinates are missing
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(deliveryAddress + ', Lhokseumawe')}`)
            .then(res => res.json())
            .then(data => {
                if (data && data.length > 0) {
                    destination = {
                        lat: parseFloat(data[0].lat),
                        lng: parseFloat(data[0].lon)
                    };
                } else {
                    // Fallback to Lhokseumawe center if address not found
                    destination = { lat: 5.1812, lng: 97.1472 };
                    console.log("Alamat spesifik tidak ditemukan di map, menggunakan default Lhokseumawe.");
                }
                initializeTracking();
            })
            .catch(err => {
                console.error('Geocoding error:', err);
                destination = { lat: 5.1812, lng: 97.1472 };
                initializeTracking();
            });
    }
});

function initializeTracking() {
    initializeMap();
    startDelivery();
    startLocationTracking();
    
    // Show instructions on first load
    setTimeout(toggleInstructions, 1000);
}

function toggleInstructions() {
    document.getElementById('instructionModal').classList.toggle('hidden');
}

function initializeMap() {
    map = L.map('routeMap', {
        zoomControl: true
    }).setView([destination.lat, destination.lng], 15);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Reposition zoom controls to right center
    map.zoomControl.setPosition('topright');
    
    destinationMarker = L.marker([destination.lat, destination.lng], {
        icon: L.divIcon({
            className: 'destination-marker',
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        })
    }).addTo(map);
    
    getCurrentLocation();
}

function startDelivery() {
    fetch(`/delivery-process/{{ $assessment->id }}/start`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    });
}

function getCurrentLocation() {
    // Locked location as requested
    currentLocation = {
        lat: 5.182907239056203,
        lng: 97.14981118058444
    };
    updateLocationOnMap();
    calculateRoute();
}

function updateLocationOnMap() {
    if (!currentLocation) return;
    
    if (currentLocationMarker) map.removeLayer(currentLocationMarker);
    
    currentLocationMarker = L.marker([currentLocation.lat, currentLocation.lng], {
        icon: L.divIcon({
            className: 'current-marker',
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        })
    }).addTo(map);
    
    updateLocationToServer();
}

function calculateRoute() {
    if (!currentLocation || !destination) return;
    
    const lat1 = currentLocation.lat * Math.PI / 180;
    const lon1 = currentLocation.lng * Math.PI / 180;
    const lat2 = destination.lat * Math.PI / 180;
    const lon2 = destination.lng * Math.PI / 180;
    const dlat = lat2 - lat1;
    const dlon = lon2 - lon1;
    const a = Math.sin(dlat/2) * Math.sin(dlat/2) + Math.cos(lat1) * Math.cos(lat2) * Math.sin(dlon/2) * Math.sin(dlon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    
    distanceKm = 6371 * c;
    estimatedMinutes = Math.max(1, Math.round((distanceKm / 25) * 60)); // 25km/h avg
    
    document.getElementById('distanceDisplay').textContent = distanceKm.toFixed(1) + ' km';
    
    drawRouteLine();
    startCountdownTimer();
}

function drawRouteLine() {
    if (routePolyline) map.removeLayer(routePolyline);
    
    routePolyline = L.polyline([
        [currentLocation.lat, currentLocation.lng],
        [destination.lat, destination.lng]
    ], {
        color: '#3b82f6',
        weight: 6,
        opacity: 0.5,
        dashArray: '10, 15',
        lineCap: 'round'
    }).addTo(map);
    
    map.fitBounds(routePolyline.getBounds(), { padding: [100, 100] });
}

function startCountdownTimer() {
    if (countdownInterval) clearInterval(countdownInterval);
    
    let remainingSeconds = Math.round(estimatedMinutes * 60);
    
    countdownInterval = setInterval(() => {
        if (remainingSeconds > 0) {
            remainingSeconds--;
            
            const hours = Math.floor(remainingSeconds / 3600);
            const mins = Math.floor((remainingSeconds % 3600) / 60);
            const secs = remainingSeconds % 60;
            
            let display = '';
            if (hours > 0) {
                display += hours.toString().padStart(2, '0') + ':';
            }
            display += mins.toString().padStart(2, '0') + ':' + secs.toString().padStart(2, '0');
            
            document.getElementById('countdownTimer').textContent = display;
        } else {
            clearInterval(countdownInterval);
            document.getElementById('countdownTimer').textContent = 'Tiba di Lokasi';
        }
    }, 1000);
}

function startLocationTracking() {
    routeInterval = setInterval(getCurrentLocation, 15000);
}

function updateLocationToServer() {
    fetch(`/delivery-process/{{ $assessment->id }}/location`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            latitude: currentLocation.lat,
            longitude: currentLocation.lng,
            distance_km: distanceKm,
            estimated_minutes: estimatedMinutes
        })
    });
}

function markArrival() {
    if (confirm('Konfirmasi Kedatangan di Lokasi Pasien?')) {
        const btn = document.getElementById('arrivalBtn');
        const originalContent = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner animate-spin mr-2"></i> Memproses...';

        fetch(`/delivery-process/{{ $assessment->id }}/arrival`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect_url;
            } else {
                alert(data.error || 'Gagal menandai kedatangan. Silakan coba lagi.');
                btn.disabled = false;
                btn.innerHTML = originalContent;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan jaringan. Silakan coba lagi.');
            btn.disabled = false;
            btn.innerHTML = originalContent;
        });
    }
}

function toggleCancelModal() {
    document.getElementById('cancelModal').classList.toggle('hidden');
}

function confirmCancellation() {
    const btn = document.getElementById('btnConfirmCancel');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner animate-spin mr-2"></i> Memproses...';

    fetch('{{ route("delivery-process.cancel", $assessment->id) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('cancelModal').classList.add('hidden');
            document.getElementById('successCancelModal').classList.remove('hidden');
            setTimeout(() => {
                window.location.href = '{{ route("delivery-process.index") }}';
            }, 2000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btn.disabled = false;
        btn.innerHTML = 'Ya, Batalkan Pengantaran';
        alert('Gagal membatalkan pengantaran. Silakan coba lagi.');
    });
}

function toggleTargetPanel() {
    var panel    = document.getElementById('mobileInfoPanel');
    var backdrop = document.getElementById('panelBackdrop');
    var fabBtn   = document.getElementById('infoFabBtn');
    var isOpen   = panel.classList.contains('visible');
    if (isOpen) {
        closeTargetPanel();
    } else {
        panel.classList.add('visible');
        backdrop.classList.add('visible');
        fabBtn.classList.add('open');
    }
}

function closeTargetPanel() {
    var panel    = document.getElementById('mobileInfoPanel');
    var backdrop = document.getElementById('panelBackdrop');
    var fabBtn   = document.getElementById('infoFabBtn');
    panel.classList.remove('visible');
    backdrop.classList.remove('visible');
    if (fabBtn) fabBtn.classList.remove('open');
}
</script>

<style>
    @keyframes fade-in {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .animate-fade-in {
        animation: fade-in 0.8s ease-out forwards;
    }
</style>
@endsection