@extends('layouts.app')

@section('title', 'Pelacakan Pengantaran')
@section('page-title', 'Tracking')

@section('content')
<div class="space-y-6 animate-fade-in">
    <!-- Header & Status -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-tni-800 to-tni-600 px-8 py-6 text-white flex flex-col md:flex-row justify-between items-center">
            <div class="mb-4 md:mb-0">
                <h2 class="text-2xl font-bold">Pelacakan Paket Obat</h2>
                <p class="text-tni-100">No. Resi: #{{ str_pad($delivery->id, 8, '0', STR_PAD_LEFT) }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-right hidden md:block">
                    <p class="text-xs text-tni-200 font-bold uppercase tracking-wider">Status Saat Ini</p>
                    <p class="text-lg font-extrabold text-gold-400">
                        @php
                            $labels = [
                                'pending' => 'Menunggu',
                                'on_delivery' => 'Dalam Perjalanan',
                                'delivered' => 'Sampai Tujuan',
                                'failed' => 'Gagal Terkirim'
                            ];
                            echo $labels[$delivery->status] ?? $delivery->status;
                        @endphp
                    </p>
                </div>
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/30 shadow-lg">
                    <i class="fas fa-truck-loading text-3xl text-gold-400"></i>
                </div>
            </div>
        </div>

        <!-- Progress Stepper -->
        <div class="px-8 py-12">
            <div class="relative">
                <!-- Line -->
                <div class="absolute top-1/2 left-0 w-full h-1 bg-gray-100 -translate-y-1/2"></div>
                <div class="absolute top-1/2 left-0 h-1 bg-tni-500 -translate-y-1/2 transition-all duration-1000" 
                     style="width: @if($delivery->status == 'pending') 0% @elseif($delivery->status == 'on_delivery') 50% @else 100% @endif"></div>

                <!-- Steps -->
                <div class="relative flex justify-between">
                    <!-- Step 1: Pesanan Dibuat -->
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center z-10 border-4 border-white shadow-md transition-colors duration-300 {{ in_array($delivery->status, ['pending', 'on_delivery', 'delivered', 'failed']) ? 'bg-tni-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                            <i class="fas fa-file-invoice text-sm"></i>
                        </div>
                        <p class="mt-3 text-xs font-bold text-gray-800">Dibuat</p>
                        <p class="text-[10px] text-gray-500">{{ $delivery->created_at->format('d/m/Y') }}</p>
                    </div>

                    <!-- Step 2: Dalam Pengiriman -->
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center z-10 border-4 border-white shadow-md transition-colors duration-300 {{ in_array($delivery->status, ['on_delivery', 'delivered', 'failed']) ? 'bg-tni-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                            <i class="fas fa-motorcycle text-sm"></i>
                        </div>
                        <p class="mt-3 text-xs font-bold text-gray-800">Dikirim</p>
                        <p class="text-[10px] text-gray-500">{{ $delivery->status != 'pending' ? 'Sedang Jalan' : '-' }}</p>
                    </div>

                    <!-- Step 3: Selesai -->
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center z-10 border-4 border-white shadow-md transition-colors duration-300 {{ $delivery->status == 'delivered' ? 'bg-green-600 text-white' : ($delivery->status == 'failed' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-500') }}">
                            <i class="fas {{ $delivery->status == 'failed' ? 'fa-times' : 'fa-check' }} text-sm"></i>
                        </div>
                        <p class="mt-3 text-xs font-bold text-gray-800">Selesai</p>
                        <p class="text-[10px] text-gray-500">{{ $delivery->delivered_at ? $delivery->delivered_at->format('d/m/Y H:i') : '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Delivery Details -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center border-b pb-3 border-gray-50">
                    <i class="fas fa-info-circle mr-3 text-tni-500"></i> Informasi Pengiriman
                </h3>
                
                <div class="space-y-6">
                    <div>
                        <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-1">Penerima</p>
                        <p class="text-sm font-bold text-gray-800">{{ $delivery->patient->name }}</p>
                        <p class="text-xs text-gray-500">{{ $delivery->patient->phone }}</p>
                    </div>

                    <div>
                        <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-1">Alamat Tujuan</p>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $delivery->delivery_address }}</p>
                    </div>

                    @if($delivery->courier)
                    <div class="pt-4 border-t border-gray-50 flex items-center">
                        <div class="w-10 h-10 rounded-full bg-tni-100 flex items-center justify-center text-tni-600 font-bold mr-3">
                            {{ substr($delivery->courier->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-0.5">Kurir Pengantar</p>
                            <p class="text-sm font-bold text-gray-800">{{ $delivery->courier->name }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Realtime Map -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden h-full flex flex-col">
                <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-map-marker-alt mr-3 text-tni-500"></i> Posisi Kurir Terkini
                    </h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 animate-pulse">
                        <span class="w-2 h-2 bg-red-500 rounded-full mr-1.5"></span> Live
                    </span>
                </div>
                <div class="flex-grow bg-gray-100 relative min-h-[400px]">
                    <!-- Map Placeholder -->
                    <div id="map" class="absolute inset-0"></div>
                    
                    @if($delivery->status == 'pending')
                    <div class="absolute inset-0 bg-white/80 backdrop-blur-sm flex items-center justify-center z-20 p-8 text-center">
                        <div>
                            <div class="w-16 h-16 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-800">Menunggu Kurir</h4>
                            <p class="text-sm text-gray-600 max-w-xs mx-auto mt-2">
                                Peta akan aktif setelah kurir mengambil paket dan memulai perjalanan.
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .leaflet-container { font-family: inherit; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    @if($delivery->status != 'pending')
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Map
        const lat = {{ $delivery->latitude ?? -6.175392 }};
        const lng = {{ $delivery->longitude ?? 106.827153 }};
        
        const map = L.map('map').setView([lat, lng], 13);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Destination Marker
        const destIcon = L.divIcon({
            html: '<div class="w-8 h-8 bg-red-600 text-white rounded-full flex items-center justify-center shadow-lg border-2 border-white"><i class="fas fa-home"></i></div>',
            className: '',
            iconSize: [32, 32],
            iconAnchor: [16, 32]
        });
        L.marker([lat, lng], {icon: destIcon}).addTo(map).bindPopup('Tujuan: {{ $delivery->patient->name }}');

        // Courier Marker (Simulated position if not available)
        const courierIcon = L.divIcon({
            html: '<div class="w-8 h-8 bg-tni-600 text-white rounded-full flex items-center justify-center shadow-lg border-2 border-white animate-bounce"><i class="fas fa-motorcycle"></i></div>',
            className: '',
            iconSize: [32, 32],
            iconAnchor: [16, 16]
        });
        
        // Use delivery current lat/lng as courier pos for now, or simulate offset
        const courierMarker = L.marker([lat - 0.005, lng - 0.005], {icon: courierIcon}).addTo(map).bindPopup('Kurir: {{ $delivery->courier->name ?? "Petugas" }}');

        // Fit bounds
        const group = new L.featureGroup([
            L.marker([lat, lng]),
            L.marker([lat - 0.005, lng - 0.005])
        ]);
        map.fitBounds(group.getBounds().pad(0.1));
    });
    @endif
</script>
@endpush
@endsection
