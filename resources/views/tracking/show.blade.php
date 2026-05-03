@extends('layouts.app')

@section('title', 'Tracking Pengantaran')
@section('page-title', 'Tracking Pengantaran')

@section('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<!-- Leaflet Routing Machine -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
<style>
    #trackingMap {
        height: 500px;
        width: 100%;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .tracking-info {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
    }
    
    .progress-container {
        margin-top: 1rem;
    }
    
    .progress-bar {
        height: 10px;
        background-color: #e5e7eb;
        border-radius: 5px;
        overflow: hidden;
    }
    
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #3b82f6, #10b981);
        border-radius: 5px;
        transition: width 0.3s ease;
    }
    
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .status-pending { background-color: #fef3c7; color: #92400e; }
    .status-in_transit { background-color: #dbeafe; color: #1e40af; }
    .status-arrived { background-color: #f0f9ff; color: #0369a1; }
    .status-delivered { background-color: #d1fae5; color: #065f46; }
    
    .info-card {
        background: #f8fafc;
        border-left: 4px solid #3b82f6;
        padding: 1rem;
        border-radius: 0.25rem;
        margin-bottom: 1rem;
    }
</style>
@endsection

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Tracking Pengantaran</h2>
                <p class="text-gray-600">ID: #{{ $delivery->id }} - {{ $delivery->patient->name }}</p>
            </div>
            
            <div class="flex space-x-2">
                @if(auth()->user()->isKurir() && $delivery->delivery_status === 'pending')
                <button onclick="startDelivery()" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    <i class="fas fa-play mr-2"></i> Mulai Pengantaran
                </button>
                @endif
                
                @if(auth()->user()->isKurir() && $delivery->delivery_status === 'arrived')
                <a href="{{ route('tracking.complete', $delivery) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-check-circle mr-2"></i> Selesaikan Pengantaran
                </a>
                @endif
                
                <a href="{{ route('deliveries.show', $delivery) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Map -->
            <div class="lg:col-span-2">
                <div class="tracking-info">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Peta Tracking</h3>
                    <div id="trackingMap"></div>
                    
                    <div class="mt-4 grid grid-cols-2 gap-4">
                        <div class="text-center p-4 border border-gray-200 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600" id="distance">0 km</div>
                            <div class="text-sm text-gray-600">Jarak ke Tujuan</div>
                        </div>
                        <div class="text-center p-4 border border-gray-200 rounded-lg">
                            <div class="text-2xl font-bold text-green-600" id="eta">-</div>
                            <div class="text-sm text-gray-600">Estimasi Waktu</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Information -->
            <div>
                <div class="tracking-info">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pengantaran</h3>
                    
                    <!-- Status -->
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Status</span>
                            <span id="statusBadge" class="status-badge status-{{ $delivery->delivery_status }}">
                                {{ ucfirst(str_replace('_', ' ', $delivery->delivery_status)) }}
                            </span>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="progress-container">
                            <div class="flex justify-between text-sm text-gray-600 mb-1">
                                <span>Progres</span>
                                <span id="progressPercentage">0%</span>
                            </div>
                            <div class="progress-bar">
                                <div id="progressFill" class="progress-fill" style="width: {{ $delivery->delivery_progress }}%"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Delivery Info -->
                    <div class="space-y-4">
                        <div class="info-card">
                            <h4 class="font-medium text-gray-900 mb-2">Informasi Pasien</h4>
                            <div class="space-y-1">
                                <p class="text-sm"><span class="font-medium">Nama:</span> {{ $delivery->patient->name }}</p>
                                <p class="text-sm"><span class="font-medium">Telepon:</span> {{ $delivery->patient->phone }}</p>
                                <p class="text-sm"><span class="font-medium">Alamat:</span> {{ $delivery->delivery_address }}</p>
                            </div>
                        </div>
                        
                        <div class="info-card">
                            <h4 class="font-medium text-gray-900 mb-2">Informasi Kurir</h4>
                            <div class="space-y-1">
                                <p class="text-sm"><span class="font-medium">Nama:</span> {{ $delivery->courier->name ?? '-' }}</p>
                                <p class="text-sm"><span class="font-medium">Telepon:</span> {{ $delivery->courier->phone ?? '-' }}</p>
                            </div>
                        </div>
                        
                        <div class="info-card">
                            <h4 class="font-medium text-gray-900 mb-2">Detail Pengantaran</h4>
                            <div class="space-y-1">
                                <p class="text-sm"><span class="font-medium">Tanggal:</span> {{ $delivery->delivery_date->format('d/m/Y') }}</p>
                                <p class="text-sm"><span class="font-medium">Prioritas:</span> 
                                    @if($delivery->priority === 'urgent')
                                    <span class="text-red-600 font-medium">Urgent</span>
                                    @else
                                    <span class="text-gray-600">Normal</span>
                                    @endif
                                </p>
                                <p class="text-sm"><span class="font-medium">Waktu Berangkat:</span> 
                                    <span id="departureTime">{{ $delivery->departure_time ? $delivery->departure_time->format('H:i') : '-' }}</span>
                                </p>
                                <p class="text-sm"><span class="font-medium">Estimasi Sampai:</span> 
                                    <span id="estimatedArrival">{{ $delivery->estimated_arrival?->format('H:i') ?? '-' }}</span>
                                </p>
                            </div>
                        </div>
                        
                        <!-- Prescription Info -->
                        @if($delivery->prescription)
                        <div class="info-card">
                            <h4 class="font-medium text-gray-900 mb-2">Resep Obat</h4>
                            <div class="space-y-1">
                                <p class="text-sm"><span class="font-medium">Obat:</span> {{ $delivery->prescription->medication_name }}</p>
                                <p class="text-sm"><span class="font-medium">Dosis:</span> {{ $delivery->prescription->dosage }}</p>
                                <p class="text-sm"><span class="font-medium">Frekuensi:</span> {{ $delivery->prescription->frequency }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>

<script>
let map;
let routingControl;
let courierMarker;
let destinationMarker;
let trackingInterval;

// Status colors mapping
const statusColors = {
    pending: 'status-pending',
    in_transit: 'status-in_transit',
    arrived: 'status-arrived',
    delivered: 'status-delivered'
};

// Status text mapping
const statusText = {
    pending: 'Menunggu',
    in_transit: 'Dalam Perjalanan',
    arrived: 'Tiba di Lokasi',
    delivered: 'Terkirim'
};

document.addEventListener('DOMContentLoaded', function() {
    initializeMap();
    loadTrackingData();
    
    // Start tracking interval if delivery is in transit
    @if($delivery->delivery_status === 'in_transit' || $delivery->delivery_status === 'arrived')
    startTracking();
    @endif
});

function initializeMap() {
    // Initialize map with default center (Jakarta)
    map = L.map('trackingMap').setView([-6.2088, 106.8456], 12);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    // Add destination marker
    @if($delivery->latitude && $delivery->longitude)
    destinationMarker = L.marker([{{ $delivery->latitude }}, {{ $delivery->longitude }}])
        .addTo(map)
        .bindPopup(`
            <b>Tujuan: {{ $delivery->patient->name }}</b><br>
            {{ $delivery->delivery_address }}
        `)
        .openPopup();
    
    map.setView([{{ $delivery->latitude }}, {{ $delivery->longitude }}], 14);
    @endif
}

function loadTrackingData() {
    fetch(`/tracking/{{ $delivery->id }}/data`)
        .then(response => response.json())
        .then(data => {
            updateUI(data);
            updateMap(data);
        })
        .catch(error => {
            console.error('Error loading tracking data:', error);
        });
}

function updateUI(data) {
    // Update status
    document.getElementById('statusBadge').textContent = statusText[data.status];
    document.getElementById('statusBadge').className = `status-badge ${statusColors[data.status]}`;
    
    // Update progress
    document.getElementById('progressPercentage').textContent = `${Math.round(data.progress)}%`;
    document.getElementById('progressFill').style.width = `${data.progress}%`;
    
    // Update times
    if (data.delivery.departure_time) {
        const departureTime = new Date(data.delivery.departure_time).toLocaleTimeString('id-ID', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        document.getElementById('departureTime').textContent = departureTime;
    }
    
    if (data.estimated_arrival) {
        document.getElementById('estimatedArrival').textContent = data.estimated_arrival;
        document.getElementById('eta').textContent = data.estimated_arrival;
    }
    
    // Update distance
    if (data.delivery.current_latitude && data.delivery.current_longitude && 
        data.delivery.latitude && data.delivery.longitude) {
        const distance = calculateDistance(
            data.delivery.latitude,
            data.delivery.longitude,
            data.delivery.current_latitude,
            data.delivery.current_longitude
        );
        document.getElementById('distance').textContent = distance.toFixed(1) + ' km';
    }
}

function updateMap(data) {
    // Clear existing routing control
    if (routingControl) {
        map.removeControl(routingControl);
    }
    
    // Clear existing courier marker
    if (courierMarker) {
        map.removeLayer(courierMarker);
    }
    
    // Add courier marker if location exists
    if (data.delivery.current_latitude && data.delivery.current_longitude) {
        courierMarker = L.marker([data.delivery.current_latitude, data.delivery.current_longitude], {
            icon: L.divIcon({
                className: 'courier-marker',
                html: '<div style="background-color: #3b82f6; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 10px rgba(0,0,0,0.3);"></div>',
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            })
        })
        .addTo(map)
        .bindPopup('<b>Posisi Kurir</b>');
        
        // Create route if both locations exist
        if (data.delivery.latitude && data.delivery.longitude) {
            routingControl = L.Routing.control({
                waypoints: [
                    L.latLng(data.delivery.current_latitude, data.delivery.current_longitude),
                    L.latLng(data.delivery.latitude, data.delivery.longitude)
                ],
                routeWhileDragging: false,
                showAlternatives: false,
                lineOptions: {
                    styles: [{ color: '#3b82f6', weight: 4, opacity: 0.7 }]
                },
                createMarker: function(i, waypoint, n) {
                    if (i === 0) {
                        return L.marker(waypoint.latLng, {
                            icon: L.divIcon({
                                className: 'start-marker',
                                html: '<div style="background-color: #3b82f6; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">K</div>',
                                iconSize: [32, 32],
                                iconAnchor: [16, 16]
                            })
                        }).bindPopup('Posisi Kurir');
                    } else {
                        return L.marker(waypoint.latLng, {
                            icon: L.divIcon({
                                className: 'destination-marker',
                                html: '<div style="background-color: #10b981; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">T</div>',
                                iconSize: [32, 32],
                                iconAnchor: [16, 16]
                            })
                        }).bindPopup('Tujuan: {{ $delivery->patient->name }}');
                    }
                }
            }).addTo(map);
            
            // Fit map to show both markers
            const bounds = L.latLngBounds([
                [data.delivery.current_latitude, data.delivery.current_longitude],
                [data.delivery.latitude, data.delivery.longitude]
            ]);
            map.fitBounds(bounds, { padding: [50, 50] });
        }
    }
}

function startDelivery() {
    if (confirm('Mulai pengantaran sekarang?')) {
        fetch(`/tracking/{{ $delivery->id }}/start`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Pengantaran dimulai!');
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error starting delivery:', error);
            alert('Gagal memulai pengantaran');
        });
    }
}

function startTracking() {
    // Update location periodically
    trackingInterval = setInterval(() => {
        updateCourierLocation();
    }, 30000); // Update every 30 seconds
    
    // Initial update
    updateCourierLocation();
}

function updateCourierLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const { latitude, longitude } = position.coords;
                
                fetch(`/tracking/{{ $delivery->id }}/location`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        latitude: latitude,
                        longitude: longitude
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Refresh tracking data
                        loadTrackingData();
                        
                        // If arrived, show notification
                        if (data.arrived) {
                            clearInterval(trackingInterval);
                            alert('Anda telah sampai di lokasi tujuan!');
                            location.reload();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error updating location:', error);
                });
            },
            function(error) {
                console.log('Geolocation error:', error);
            }
        );
    }
}

function calculateDistance(lat1, lon1, lat2, lon2) {
    const earthRadius = 6371; // km
    
    const latFrom = deg2rad(lat1);
    const lonFrom = deg2rad(lon1);
    const latTo = deg2rad(lat2);
    const lonTo = deg2rad(lon2);
    
    const latDelta = latTo - latFrom;
    const lonDelta = lonTo - lonFrom;
    
    const a = Math.sin(latDelta / 2) * Math.sin(latDelta / 2) +
              Math.cos(latFrom) * Math.cos(latTo) *
              Math.sin(lonDelta / 2) * Math.sin(lonDelta / 2);
    
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    
    return earthRadius * c;
}

function deg2rad(deg) {
    return deg * (Math.PI / 180);
}

// Clean up on page unload
window.addEventListener('beforeunload', function() {
    if (trackingInterval) {
        clearInterval(trackingInterval);
    }
});
</script>
@endsection