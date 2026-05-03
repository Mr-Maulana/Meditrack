@extends('layouts.app')

@section('title', 'Rute Pengantaran')
@section('page-title', 'Rute Pengantaran')

@section('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #routeMap {
        height: 500px;
        width: 100%;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .route-info-card {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .countdown-timer {
        font-size: 2rem;
        font-weight: bold;
        color: #3b82f6;
        text-align: center;
    }
    
    .navigation-controls {
        position: absolute;
        bottom: 1rem;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1000;
        background: white;
        padding: 1rem;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        display: flex;
        gap: 1rem;
    }
    
    .destination-marker {
        background-color: #ef4444;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        border: 3px solid white;
        box-shadow: 0 0 10px rgba(0,0,0,0.3);
    }
    
    .current-marker {
        background-color: #3b82f6;
        border-radius: 50%;
        width: 16px;
        height: 16px;
        border: 3px solid white;
        box-shadow: 0 0 10px rgba(0,0,0,0.3);
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
</style>
@endsection

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Rute Pengantaran</h2>
            <p class="text-gray-600">Menuju lokasi {{ $assessment->delivery->patient->name }}</p>
        </div>
        <div class="flex space-x-2">
            <button onclick="cancelDelivery()" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                <i class="fas fa-times mr-2"></i> Batalkan
            </button>
        </div>
    </div>

    <!-- Route Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Patient Info -->
        <div class="route-info-card">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pasien</h3>
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-500">Nama Pasien</label>
                    <p class="text-sm text-gray-900">{{ $assessment->delivery->patient->name }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">No. Telepon</label>
                    <p class="text-sm text-gray-900">{{ $assessment->delivery->patient->phone }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Alamat</label>
                    <p class="text-sm text-gray-900">{{ $assessment->delivery->delivery_address }}</p>
                </div>
                @if($assessment->delivery->notes)
                <div>
                    <label class="text-sm font-medium text-gray-500">Catatan Khusus</label>
                    <p class="text-sm text-gray-900">{{ $assessment->delivery->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Prescription Info -->
        <div class="route-info-card">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Daftar Obat Terdaftar</h3>
            @if($assessment->delivery->prescription)
            <div class="space-y-4">
                @php
                    $meds = $assessment->delivery->prescription->medications ?? [
                        [
                            'name' => $assessment->delivery->prescription->medication_name,
                            'dosage' => $assessment->delivery->prescription->dosage,
                            'frequency' => $assessment->delivery->prescription->frequency,
                            'instructions' => $assessment->delivery->prescription->instructions
                        ]
                    ];
                @endphp

                @foreach($meds as $i => $med)
                <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="font-bold text-sm text-blue-700 flex items-center mb-2">
                        <span class="w-5 h-5 bg-blue-500 text-white text-[10px] rounded-full flex items-center justify-center mr-2">{{ $i + 1 }}</span>
                        {{ $med['name'] }}
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-[10px]">
                        <div class="text-gray-600"><strong>Dosis:</strong> {{ $med['dosage'] ?? '-' }}</div>
                        <div class="text-gray-600"><strong>Freq:</strong> {{ $med['frequency'] ?? '-' }}</div>
                    </div>
                    @if(!empty($med['instructions']))
                    <p class="mt-2 text-[10px] italic text-gray-400 bg-white p-1 rounded border border-gray-50">"{{ $med['instructions'] }}"</p>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <p class="text-sm text-gray-500 italic">Tidak ada informasi resep</p>
            @endif
        </div>

        <!-- Route Stats -->
        <div class="route-info-card">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Statistik Rute</h3>
            <div class="space-y-4">
                <div class="countdown-timer" id="countdownTimer">
                    --:--
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-3 bg-blue-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600" id="distanceDisplay">0 km</div>
                        <div class="text-sm text-gray-600">Jarak</div>
                    </div>
                    <div class="text-center p-3 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600" id="timeDisplay">0 min</div>
                        <div class="text-sm text-gray-600">Estimasi</div>
                    </div>
                </div>
                <div class="pt-4 border-t border-gray-200">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Waktu Mulai:</span>
                        <span class="font-medium text-gray-900" id="startTime">
                            {{ $assessment->start_time ? $assessment->start_time->format('H:i') : 'Belum dimulai' }}
                        </span>
                    </div>
                    <div class="flex justify-between text-sm mt-2">
                        <span class="text-gray-600">Status:</span>
                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                            Dalam Perjalanan
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Container -->
    <div class="relative mb-6">
        <div id="routeMap"></div>
        <div class="navigation-controls">
            <button onclick="getCurrentLocation()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                <i class="fas fa-location-arrow mr-2"></i> Update Lokasi
            </button>
            <button onclick="calculateRoute()" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                <i class="fas fa-route mr-2"></i> Hitung Ulang Rute
            </button>
            <button onclick="markArrival()" id="arrivalBtn" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                <i class="fas fa-flag-checkered mr-2"></i> Tandai Sampai
            </button>
        </div>
    </div>

    <!-- Delivery Instructions -->
    <div class="route-info-card">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Instruksi Pengantaran</h3>
        <div class="space-y-3">
            <div class="flex items-start">
                <div class="flex-shrink-0 h-6 w-6 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                    <i class="fas fa-1 text-blue-600 text-xs"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-900">Pastikan obat dalam kondisi baik sebelum diberikan</p>
                </div>
            </div>
            <div class="flex items-start">
                <div class="flex-shrink-0 h-6 w-6 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                    <i class="fas fa-2 text-blue-600 text-xs"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-900">Verifikasi identitas penerima obat</p>
                </div>
            </div>
            <div class="flex items-start">
                <div class="flex-shrink-0 h-6 w-6 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                    <i class="fas fa-3 text-blue-600 text-xs"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-900">Jelaskan cara penggunaan dan efek samping</p>
                </div>
            </div>
            <div class="flex items-start">
                <div class="flex-shrink-0 h-6 w-6 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                    <i class="fas fa-4 text-blue-600 text-xs"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-900">Dokumentasikan serah terima dengan foto</p>
                </div>
            </div>
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
let destination = {
    lat: {{ $assessment->delivery->latitude ?? -6.2088 }},
    lng: {{ $assessment->delivery->longitude ?? 106.8456 }}
};
let routeInterval;
let countdownInterval;
let estimatedMinutes = 0;
let distanceKm = 0;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize map
    initializeMap();
    
    // Start delivery process
    startDelivery();
    
    // Start location tracking
    startLocationTracking();
});

function initializeMap() {
    // Initialize map centered on destination
    map = L.map('routeMap').setView([destination.lat, destination.lng], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    // Add destination marker
    destinationMarker = L.marker([destination.lat, destination.lng], {
        icon: L.divIcon({
            className: 'destination-marker',
            iconSize: [26, 26],
            iconAnchor: [13, 13]
        })
    }).addTo(map)
    .bindPopup(`<b>Tujuan:</b><br>${destinationAddress}`)
    .openPopup();
    
    // Get current location
    getCurrentLocation();
}

function startDelivery() {
    fetch(`/delivery-process/{{ $assessment->id }}/start`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Delivery started successfully');
        }
    })
    .catch(error => {
        console.error('Error starting delivery:', error);
    });
}

function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                currentLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                updateLocationOnMap();
                calculateRoute();
            },
            function(error) {
                console.log('Geolocation error:', error);
                // Use default location if geolocation fails
                currentLocation = { lat: -6.2088, lng: 106.8456 };
                updateLocationOnMap();
                calculateRoute();
            }
        );
    } else {
        alert('Browser tidak mendukung geolocation');
        currentLocation = { lat: -6.2088, lng: 106.8456 };
        updateLocationOnMap();
        calculateRoute();
    }
}

function updateLocationOnMap() {
    if (!currentLocation) return;
    
    // Remove existing current location marker
    if (currentLocationMarker) {
        map.removeLayer(currentLocationMarker);
    }
    
    // Add new current location marker
    currentLocationMarker = L.marker([currentLocation.lat, currentLocation.lng], {
        icon: L.divIcon({
            className: 'current-marker',
            iconSize: [22, 22],
            iconAnchor: [11, 11]
        })
    }).addTo(map)
    .bindPopup('<b>Lokasi Anda Saat Ini</b>');
    
    // Center map on current location
    map.setView([currentLocation.lat, currentLocation.lng], 14);
    
    // Send location to server
    updateLocationToServer();
}

function calculateRoute() {
    if (!currentLocation || !destination) return;
    
    // Calculate distance using Haversine formula
    const lat1 = currentLocation.lat * Math.PI / 180;
    const lon1 = currentLocation.lng * Math.PI / 180;
    const lat2 = destination.lat * Math.PI / 180;
    const lon2 = destination.lng * Math.PI / 180;
    
    const dlat = lat2 - lat1;
    const dlon = lon2 - lon1;
    
    const a = Math.sin(dlat/2) * Math.sin(dlat/2) +
              Math.cos(lat1) * Math.cos(lat2) *
              Math.sin(dlon/2) * Math.sin(dlon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    
    distanceKm = 6371 * c; // Earth radius in km
    estimatedMinutes = Math.round((distanceKm / 30) * 60); // Assume 30 km/h average speed
    
    // Update display
    document.getElementById('distanceDisplay').textContent = distanceKm.toFixed(1) + ' km';
    document.getElementById('timeDisplay').textContent = estimatedMinutes + ' min';
    
    // Draw route line
    drawRouteLine();
    
    // Start countdown timer
    startCountdownTimer();
    
    // Send route data to server
    sendRouteDataToServer();
}

function drawRouteLine() {
    if (!currentLocation || !destination) return;
    
    // Remove existing polyline
    if (routePolyline) {
        map.removeLayer(routePolyline);
    }
    
    // Create new polyline
    routePolyline = L.polyline([
        [currentLocation.lat, currentLocation.lng],
        [destination.lat, destination.lng]
    ], {
        color: '#3b82f6',
        weight: 4,
        opacity: 0.7,
        dashArray: '10, 10'
    }).addTo(map);
    
    // Fit map to show both points
    map.fitBounds(routePolyline.getBounds());
}

function startCountdownTimer() {
    // Clear existing interval
    if (countdownInterval) {
        clearInterval(countdownInterval);
    }
    
    let remainingSeconds = estimatedMinutes * 60;
    updateCountdownDisplay(remainingSeconds);
    
    // Update countdown every second
    countdownInterval = setInterval(() => {
        if (remainingSeconds > 0) {
            remainingSeconds--;
            updateCountdownDisplay(remainingSeconds);
        } else {
            clearInterval(countdownInterval);
            document.getElementById('countdownTimer').textContent = '00:00';
            document.getElementById('countdownTimer').classList.add('text-red-600');
        }
    }, 1000);
}

function updateCountdownDisplay(seconds) {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    const display = `${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;
    document.getElementById('countdownTimer').textContent = display;
}

function startLocationTracking() {
    // Update location every 30 seconds
    routeInterval = setInterval(() => {
        getCurrentLocation();
    }, 30000);
}

function updateLocationToServer() {
    fetch(`/delivery-process/{{ $assessment->id }}/update-location`, {
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
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error('Failed to update location:', data);
        }
    })
    .catch(error => {
        console.error('Error updating location:', error);
    });
}

function sendRouteDataToServer() {
    fetch(`/delivery-process/{{ $assessment->id }}/calculate-route`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            current_lat: currentLocation.lat,
            current_lng: currentLocation.lng
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.distance_km) {
            distanceKm = data.distance_km;
            estimatedMinutes = data.estimated_minutes;
            
            document.getElementById('distanceDisplay').textContent = distanceKm.toFixed(1) + ' km';
            document.getElementById('timeDisplay').textContent = estimatedMinutes + ' min';
            
            // Update countdown timer
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
            startCountdownTimer();
        }
    })
    .catch(error => {
        console.error('Error calculating route:', error);
    });
}

function markArrival() {
    if (confirm('Apakah Anda telah tiba di lokasi pasien?')) {
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
                // Stop location tracking
                clearInterval(routeInterval);
                clearInterval(countdownInterval);
                
                // Redirect to assessment page
                window.location.href = data.redirect_url;
            } else {
                alert('Gagal menandai kedatangan: ' + (data.message || 'Terjadi kesalahan'));
            }
        })
        .catch(error => {
            console.error('Error marking arrival:', error);
            alert('Terjadi kesalahan saat menandai kedatangan');
        });
    }
}

function cancelDelivery() {
    if (confirm('Batalkan pengantaran ini? Status akan dikembalikan ke pending.')) {
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
                window.location.href = data.redirect_url || '{{ route("delivery-process.index") }}';
            } else {
                alert('Gagal membatalkan: ' + (data.message || 'Terjadi kesalahan'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat membatalkan pengantaran');
        });
    }
}

// Clean up intervals when page unloads
window.addEventListener('beforeunload', function() {
    if (routeInterval) clearInterval(routeInterval);
    if (countdownInterval) clearInterval(countdownInterval);
});
</script>
@endsection