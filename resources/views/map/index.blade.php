@extends('layouts.app')

@section('title', 'Peta Lokasi')
@section('page-title', 'Peta Lokasi')

@section('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<!-- Leaflet Routing Machine -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
<style>
    #map {
        height: 600px;
        width: 100%;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .map-container {
        position: relative;
    }
    
    .location-card {
        position: absolute;
        top: 1rem;
        right: 1rem;
        z-index: 1000;
        background: white;
        padding: 1rem;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        max-width: 300px;
    }
    
    .location-item {
        padding: 0.5rem;
        border-bottom: 1px solid #e5e7eb;
        cursor: pointer;
    }
    
    .location-item:hover {
        background-color: #f3f4f6;
    }
    
    .location-item.active {
        background-color: #dbeafe;
        border-left: 4px solid #3b82f6;
    }
</style>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Peta Lokasi Pengantaran</h2>
            <p class="text-gray-600">Lokasi pasien dan rute pengantaran</p>
        </div>
        
        <div class="flex space-x-2">
            @if(auth()->user()->isKurir())
            <button onclick="getCurrentLocation()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                <i class="fas fa-location-arrow mr-2"></i> Lokasi Saya
            </button>
            @endif
            <button onclick="printMap()" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                <i class="fas fa-print mr-2"></i> Cetak Rute
            </button>
        </div>
    </div>

    <!-- Map Container -->
    <div class="map-container">
        <div id="map"></div>
        
        <!-- Locations List -->
        <div class="location-card">
            <h4 class="font-medium text-gray-900 mb-3">Daftar Lokasi</h4>
            <div id="locations-list" class="max-h-80 overflow-y-auto">
                @forelse($locations as $location)
                <div class="location-item" data-lat="{{ $location['latitude'] }}" data-lng="{{ $location['longitude'] }}" onclick="focusOnLocation({{ $location['latitude'] }}, {{ $location['longitude'] }}, '{{ $location['patient_name'] }}')">
                    <div class="font-medium text-gray-900">{{ $location['patient_name'] }}</div>
                    <div class="text-xs text-gray-500 truncate">{{ $location['address'] }}</div>
                    @if(isset($location['status']))
                    <div class="mt-1">
                        <span class="px-2 py-1 text-xs rounded-full 
                            {{ $location['status'] == 'on_delivery' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $location['status'] == 'on_delivery' ? 'Dalam Pengantaran' : 'Menunggu' }}
                        </span>
                        @if($location['priority'] == 'urgent')
                        <span class="ml-1 px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                            Urgent
                        </span>
                        @endif
                    </div>
                    @endif
                </div>
                @empty
                <div class="text-center py-4 text-gray-500">
                    <i class="fas fa-map-marker-alt text-2xl mb-2"></i>
                    <p>Tidak ada lokasi tersedia</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Route Info -->
    <div class="bg-white rounded-lg shadow p-6">
        <h4 class="text-lg font-medium text-gray-900 mb-4">Informasi Rute</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center p-4 border border-gray-200 rounded-lg">
                <div class="text-2xl font-bold text-blue-600" id="total-distance">0 km</div>
                <div class="text-sm text-gray-600">Total Jarak</div>
            </div>
            <div class="text-center p-4 border border-gray-200 rounded-lg">
                <div class="text-2xl font-bold text-green-600" id="estimated-time">0 menit</div>
                <div class="text-sm text-gray-600">Estimasi Waktu</div>
            </div>
            <div class="text-center p-4 border border-gray-200 rounded-lg">
                <div class="text-2xl font-bold text-purple-600" id="total-stops">0</div>
                <div class="text-sm text-gray-600">Total Pemberhentian</div>
            </div>
        </div>
    </div>

    <!-- Delivery List -->
    @if(auth()->user()->isKurir())
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h4 class="text-lg font-medium text-gray-900">Daftar Pengantaran Hari Ini</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pasien</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alamat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prioritas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="deliveries-table">
                    <!-- Data akan diisi oleh JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>

<!-- ... existing code ... -->

<script>
    let map;
    let markers = [];
    let currentLocationMarker;
    let locationWatchId;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize map
        map = L.map('map').setView([-6.2088, 106.8456], 12);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        // Add patient/delivery markers
        @foreach($locations as $location)
        if ({{ $location['latitude'] }} && {{ $location['longitude'] }}) {
            const markerColor = @if(isset($location['priority']) && $location['priority'] == 'urgent') 
                'red' 
            @elseif(isset($location['status']) && $location['status'] == 'on_delivery') 
                'blue' 
            @else 
                'green' 
            @endif;
            
            const markerIcon = L.divIcon({
                className: 'custom-marker',
                html: `<div style="background-color: ${markerColor}; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 16px; border: 3px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3); cursor: pointer;">
                    <i class="fas fa-@if(isset($location['status']) && $location['status'] == 'on_delivery') truck @else home @endif"></i>
                </div>`,
                iconSize: [40, 40],
                iconAnchor: [20, 20]
            });
            
            const marker = L.marker([{{ $location['latitude'] }}, {{ $location['longitude'] }}], { 
                icon: markerIcon 
            })
            .addTo(map)
            .on('click', function() {
                @if(auth()->user()->isKurir() && isset($location['delivery_id']))
                // Start navigation immediately for couriers
                startNavigationToPatient(
                    {{ $location['delivery_id'] }}, 
                    '{{ $location['patient_name'] }}',
                    {{ $location['latitude'] }},
                    {{ $location['longitude'] }},
                    '{{ $location['patient_phone'] }}'
                );
                @else
                // Just focus for non-couriers
                focusOnLocation(
                    {{ $location['latitude'] }}, 
                    {{ $location['longitude'] }}, 
                    '{{ $location['patient_name'] }}'
                );
                @endif
            })
            .bindPopup(`
                <div class="p-2">
                    <b>{{ $location['patient_name'] }}</b><br>
                    <small>{{ $location['address'] }}</small><br>
                    @if(isset($location['status']))
                    <span class="text-xs ${getStatusColor('{{ $location['status'] }}')}">
                        {{ $location['status'] == 'on_delivery' ? 'Dalam Pengantaran' : 'Menunggu' }}
                    </span><br>
                    @endif
                    @if(auth()->user()->isKurir() && isset($location['delivery_id']))
                    <button onclick="window.location.href='/map/navigate/{{ $location['delivery_id'] }}'" 
                           class="mt-2 w-full bg-blue-600 hover:bg-blue-700 text-white text-xs py-1 px-2 rounded">
                        <i class="fas fa-route mr-1"></i> Mulai Navigasi
                    </button>
                    @endif
                </div>
            `);
            
            markers.push(marker);
            
            // Store data on marker
            marker.patientData = {
                name: '{{ $location['patient_name'] }}',
                phone: '{{ $location['patient_phone'] }}',
                @if(isset($location['delivery_id']))
                deliveryId: {{ $location['delivery_id'] }},
                @endif
                latitude: {{ $location['latitude'] }},
                longitude: {{ $location['longitude'] }}
            };
        }
        @endforeach
        
        // Start location tracking
        startLocationTracking();
        
        // Add control for current location
        L.control.locate({
            position: 'topleft',
            strings: {
                title: "Tunjukkan lokasi saya"
            },
            locateOptions: {
                enableHighAccuracy: true,
                maxZoom: 16
            }
        }).addTo(map);
    });
    
    function startLocationTracking() {
        if (navigator.geolocation) {
            locationWatchId = navigator.geolocation.watchPosition(
                function(position) {
                    const { latitude, longitude } = position.coords;
                    updateCurrentLocation(latitude, longitude);
                },
                function(error) {
                    console.log('Geolocation error:', error);
                },
                {
                    enableHighAccuracy: true,
                    maximumAge: 10000,
                    timeout: 5000
                }
            );
        }
    }
    
    function updateCurrentLocation(latitude, longitude) {
        // Remove existing current location marker
        if (currentLocationMarker) {
            map.removeLayer(currentLocationMarker);
        }
        
        // Add current location marker
        currentLocationMarker = L.marker([latitude, longitude], {
            icon: L.divIcon({
                className: 'current-location-marker',
                html: '<div style="background-color: #3b82f6; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 10px rgba(0,0,0,0.3);"></div>',
                iconSize: [30, 30],
                iconAnchor: [15, 15]
            })
        }).addTo(map);
    }
    
    function focusOnLocation(lat, lng, name) {
        map.setView([lat, lng], 16);
        
        // Find and open the marker popup
        markers.forEach(marker => {
            const markerLatLng = marker.getLatLng();
            if (Math.abs(markerLatLng.lat - lat) < 0.0001 && Math.abs(markerLatLng.lng - lng) < 0.0001) {
                marker.openPopup();
            }
        });
        
        // Highlight location in list
        document.querySelectorAll('.location-item').forEach(item => {
            item.classList.remove('active');
            if (Math.abs(parseFloat(item.dataset.lat) - lat) < 0.0001 && 
                Math.abs(parseFloat(item.dataset.lng) - lng) < 0.0001) {
                item.classList.add('active');
            }
        });
    }
    
    @if(auth()->user()->isKurir())
    function startNavigationToPatient(deliveryId, patientName, lat, lng, phone) {
        // Show confirmation dialog
        if (confirm(`Mulai navigasi ke ${patientName}?`)) {
            // Redirect to navigation page
            window.location.href = `/map/navigate/${deliveryId}`;
        }
    }
    
    function getStatusColor(status) {
        switch(status) {
            case 'on_delivery': return 'text-blue-600';
            case 'pending': return 'text-yellow-600';
            case 'delivered': return 'text-green-600';
            default: return 'text-gray-600';
        }
    }
    @endif
    
    // Clean up
    window.addEventListener('beforeunload', function() {
        if (locationWatchId && navigator.geolocation) {
            navigator.geolocation.clearWatch(locationWatchId);
        }
    });
</script>

<style>
@media print {
    .no-print, .location-card, button {
        display: none !important;
    }
    
    #map {
        height: 800px !important;
    }
}
</style>
@endsection