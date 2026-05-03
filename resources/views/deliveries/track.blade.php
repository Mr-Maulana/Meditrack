@extends('layouts.app')

@section('title', 'Pelacakan Pengantaran')
@section('page-title', 'Live Tracking')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
<style>
    #map {
        height: 600px;
        width: 100%;
        border-radius: 2.5rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
    }
    
    .tracking-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 2rem;
    }

    .step-active {
        background: #254328;
        color: white;
        box-shadow: 0 0 20px rgba(37, 67, 40, 0.4);
    }

    .pulse-red {
        background: rgba(239, 68, 68, 1);
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 1);
        animation: pulse-red 2s infinite;
    }

    @keyframes pulse-red {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }

    .leaflet-div-icon {
        background: transparent !important;
        border: none !important;
    }

    .courier-marker-icon {
        filter: drop-shadow(0 10px 15px rgba(0,0,0,0.2));
    }
</style>
@endsection

@section('content')
<div class="space-y-8 animate-fade-in pb-12">
    <!-- Status Header Card -->
    <div class="bg-gradient-to-br from-tni-800 to-tni-900 rounded-[2.5rem] p-8 md:p-12 text-white relative overflow-hidden shadow-2xl">
        <div class="absolute top-0 right-0 p-8 opacity-10">
            <i class="fas fa-satellite-dish text-[10rem] rotate-12"></i>
        </div>
        
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-8">
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <span class="px-4 py-1.5 bg-gold-500 text-tni-900 rounded-full text-[10px] font-black uppercase tracking-widest">Live Tracking</span>
                    <span class="text-tni-300 font-bold text-xs">ID Pengiriman: #{{ str_pad($delivery->id, 8, '0', STR_PAD_LEFT) }}</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-black tracking-tight">Status Paket: 
                    <span class="text-gold-400">
                        @php
                            $labels = [
                                'pending' => 'Menunggu Penjemputan',
                                'on_delivery' => 'Dalam Perjalanan',
                                'delivered' => 'Tiba di Tujuan',
                                'failed' => 'Gagal Terkirim'
                            ];
                            echo $labels[$delivery->status] ?? $delivery->status;
                        @endphp
                    </span>
                </h2>
                <p class="text-tni-100 max-w-xl font-medium leading-relaxed opacity-80">
                    Sistem sedang memantau pergerakan kurir secara real-time. Pastikan Anda berada di lokasi tujuan untuk menerima paket obat.
                </p>
            </div>
            
            <div class="shrink-0 flex items-center gap-6 bg-white/10 backdrop-blur-md p-6 rounded-3xl border border-white/10">
                <div class="text-right">
                    <p class="text-[10px] font-black text-gold-400 uppercase tracking-widest mb-1">Waktu Sistem (Live)</p>
                    <p class="text-xl font-black" id="live-clock">{{ now()->format('H:i:s') }} <span class="text-sm font-medium text-tni-200">WIB</span></p>
                </div>
                <div class="w-14 h-14 bg-gold-500 text-tni-900 rounded-2xl flex items-center justify-center text-2xl shadow-xl shadow-gold-500/20">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>

        <!-- Mini Progress Bar -->
        <div class="mt-12 relative">
            <div class="flex justify-between text-[10px] font-black uppercase tracking-[0.2em] mb-4 text-tni-300">
                <span>Diterima Farmasi</span>
                <span>Perjalanan Kurir</span>
                <span>Diterima Pasien</span>
            </div>
            <div class="h-3 bg-white/10 rounded-full overflow-hidden p-0.5 border border-white/5">
                <div class="h-full bg-gradient-to-r from-gold-400 to-gold-600 rounded-full transition-all duration-1000 shadow-[0_0_15px_rgba(245,158,11,0.5)]" 
                     style="width: @if($delivery->status == 'pending') 20% @elseif($delivery->status == 'on_delivery') 60% @else 100% @endif">
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar Info -->
        <div class="space-y-6">
            <!-- Patient & Address Card -->
            <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100">
                <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-3">
                    <span class="w-1.5 h-4 bg-gold-500 rounded-full"></span>
                    Informasi Tujuan
                </h4>
                
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-xl shrink-0">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">Nama Pasien</p>
                            <p class="text-sm font-bold text-gray-800">{{ $delivery->patient->name }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center text-xl shrink-0">
                            <i class="fas fa-map-location-dot"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">Alamat Pengantaran</p>
                            <p class="text-xs font-bold text-gray-600 leading-relaxed">{{ $delivery->delivery_address }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Courier Card -->
            @if($delivery->courier)
            <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100 group">
                <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-3">
                    <span class="w-1.5 h-4 bg-tni-700 rounded-full"></span>
                    Petugas Kurir
                </h4>
                
                <div class="flex items-center gap-5">
                    <div class="relative">
                        <div class="w-16 h-16 bg-tni-800 text-white rounded-2xl flex items-center justify-center text-2xl font-black shadow-xl group-hover:scale-105 transition-transform">
                            {{ substr($delivery->courier->name, 0, 1) }}
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-green-500 border-4 border-white rounded-full"></div>
                    </div>
                    <div>
                        <p class="text-lg font-black text-gray-800">{{ $delivery->courier->name }}</p>
                        <div class="flex items-center gap-4 mt-2">
                            <a href="tel:{{ $delivery->courier->phone ?? '#' }}" class="text-tni-600 text-xs font-bold hover:text-gold-600 transition-colors flex items-center gap-1">
                                <i class="fas fa-phone-alt"></i> Hubungi
                            </a>
                            <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                            <span class="text-[10px] font-black text-green-600 uppercase tracking-widest">Aktif</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Log / Timeline (Mini) -->
            <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100">
                <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6">Log Perjalanan</h4>
                <div class="space-y-6">
                    <div class="flex gap-4 relative">
                        <div class="absolute left-2.5 top-8 bottom-0 w-0.5 bg-gray-100"></div>
                        <div class="w-5 h-5 rounded-full bg-green-500 border-4 border-green-100 shrink-0 z-10"></div>
                        <div>
                            <p class="text-xs font-bold text-gray-800">Paket Diterima Kurir</p>
                            <p class="text-[10px] text-gray-400 font-bold mt-0.5">{{ $delivery->created_at->format('H:i') }} WIB</p>
                        </div>
                    </div>
                    @if($delivery->status == 'on_delivery')
                    <div class="flex gap-4">
                        <div class="w-5 h-5 rounded-full bg-gold-500 border-4 border-gold-100 shrink-0 z-10 animate-pulse"></div>
                        <div>
                            <p class="text-xs font-bold text-gray-800">Sedang Menuju Lokasi</p>
                            <p class="text-[10px] text-gray-400 font-bold mt-0.5">Sekarang</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Map Section -->
        <div class="lg:col-span-2 space-y-6">
            <div class="relative group">
                <div id="map"></div>
                
                <!-- Floating Map Header -->
                <div class="absolute top-6 left-6 right-6 z-[1000] flex justify-between items-center pointer-events-none">
                    <div class="bg-white/90 backdrop-blur-md px-6 py-3 rounded-2xl shadow-xl border border-white/20 flex items-center gap-3 pointer-events-auto">
                        <span class="flex h-3 w-3 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                        </span>
                        <p class="text-xs font-black text-gray-800 uppercase tracking-widest">Pantauan Live</p>
                    </div>
                    
                    <button onclick="focusCourier()" class="w-12 h-12 bg-white/90 backdrop-blur-md rounded-2xl shadow-xl border border-white/20 flex items-center justify-center text-tni-800 pointer-events-auto hover:bg-tni-800 hover:text-white transition-all">
                        <i class="fas fa-location-crosshairs"></i>
                    </button>
                </div>

                <!-- Empty State for Map -->
                @if($delivery->status == 'pending')
                <div class="absolute inset-0 bg-tni-900/60 backdrop-blur-sm z-[1001] flex items-center justify-center rounded-[2.5rem] p-12 text-center">
                    <div class="max-w-sm">
                        <div class="w-20 h-20 bg-gold-500/20 text-gold-500 rounded-3xl flex items-center justify-center text-4xl mx-auto mb-6 shadow-2xl border border-gold-500/30">
                            <i class="fas fa-motorcycle"></i>
                        </div>
                        <h4 class="text-2xl font-black text-white mb-2">Kurir Belum Berangkat</h4>
                        <p class="text-tni-200 text-sm font-medium">Paket masih dalam tahap persiapan di bagian farmasi. Peta pelacakan akan aktif otomatis saat kurir memulai pengiriman.</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Additional Instruction Card -->
            <div class="bg-blue-50 border border-blue-100 rounded-[2rem] p-8 flex flex-col md:flex-row items-center gap-6">
                <div class="w-16 h-16 bg-blue-600 text-white rounded-2xl flex items-center justify-center text-2xl shrink-0 shadow-lg shadow-blue-600/20">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <div class="text-center md:text-left">
                    <h5 class="text-lg font-black text-blue-900 mb-1">Verifikasi Keamanan TNI AD</h5>
                    <p class="text-sm text-blue-700/80 font-bold leading-relaxed">
                        Saat kurir tiba, pastikan Anda menunjukkan identitas yang sah. Seluruh proses pengantaran ini tercatat secara resmi dalam sistem logistik Rumkit TK III IM 07.01 Lhokseumawe.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Leaflet JS & Routing Machine -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
<script>
    let map;
    let courierMarker;
    let destinationMarker;
    let routingControl;

    // Live Clock Update
    setInterval(() => {
        const now = new Date();
        const timeString = now.getHours().toString().padStart(2, '0') + ':' + 
                           now.getMinutes().toString().padStart(2, '0') + ':' + 
                           now.getSeconds().toString().padStart(2, '0');
        const clockElement = document.getElementById('live-clock');
        if (clockElement) {
            clockElement.innerHTML = `${timeString} <span class="text-sm font-medium text-tni-200">WIB</span>`;
        }
    }, 1000);

    @if($delivery->status != 'pending')
    document.addEventListener('DOMContentLoaded', function() {
        const hospitalLat = 5.1812;
        const hospitalLng = 97.1472;

        let destLat = {{ $delivery->latitude ?? 5.1812 }};
        let destLng = {{ $delivery->longitude ?? 97.1472 }};
        
        // Anti-Jakarta Fix (Demo)
        if (destLat < 0) { destLat = hospitalLat; destLng = hospitalLng; }
        
        let courierLat = {{ $delivery->current_latitude ?? ($delivery->latitude ?? 5.1812) }};
        let courierLng = {{ $delivery->current_longitude ?? ($delivery->longitude ?? 97.1472) }};
        if (courierLat < 0) { courierLat = hospitalLat; courierLng = hospitalLng; }

        map = L.map('map', { zoomControl: false }).setView([courierLat, courierLng], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        const destIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `<div class="relative w-10 h-10"><div class="absolute inset-0 bg-red-500 rounded-2xl rotate-45 shadow-xl border-4 border-white"></div><div class="absolute inset-0 flex items-center justify-center text-white text-xs"><i class="fas fa-house"></i></div></div>`,
            iconSize: [40, 40],
            iconAnchor: [20, 20]
        });

        const courierIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `<div class="relative w-12 h-12 courier-marker-icon"><div class="absolute inset-0 bg-tni-800 rounded-2xl shadow-2xl border-4 border-white flex items-center justify-center text-gold-400 text-xl"><i class="fas fa-motorcycle"></i></div><div class="absolute -top-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-white pulse-red"></div></div>`,
            iconSize: [48, 48],
            iconAnchor: [24, 24]
        });

        destinationMarker = L.marker([destLat, destLng], {icon: destIcon}).addTo(map);
        courierMarker = L.marker([courierLat, courierLng], {icon: courierIcon}).addTo(map);

        routingControl = L.Routing.control({
            waypoints: [L.latLng(courierLat, courierLng), L.latLng(destLat, destLng)],
            lineOptions: { styles: [{ color: '#254328', opacity: 0.6, weight: 6 }] },
            createMarker: function() { return null; },
            addWaypoints: false,
            routeWhileDragging: false,
            show: false
        }).addTo(map);

        const group = new L.featureGroup([destinationMarker, courierMarker]);
        map.fitBounds(group.getBounds().pad(0.3));

        setInterval(refreshCourierLocation, 10000);
    });

    function focusCourier() {
        if (courierMarker) map.flyTo(courierMarker.getLatLng(), 16);
    }

    function refreshCourierLocation() {
        fetch('{{ route("deliveries.tracking-data", $delivery->id) }}')
            .then(response => response.json())
            .then(data => {
                if (data.delivery && data.delivery.current_latitude && data.delivery.current_longitude) {
                    const newPos = L.latLng(data.delivery.current_latitude, data.delivery.current_longitude);
                    courierMarker.setLatLng(newPos);
                    routingControl.setWaypoints([
                        newPos,
                        L.latLng(destinationMarker.getLatLng().lat, destinationMarker.getLatLng().lng)
                    ]);
                }
            })
            .catch(error => console.error('Error:', error));
    }
    @endif
</script>
@endpush
@endsection
