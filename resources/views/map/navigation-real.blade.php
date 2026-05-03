@extends('layouts.app')

@section('title', 'Navigasi ke ' . $delivery->patient->name)
@section('page-title', 'Navigasi Real-time')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
<style>
    #navigationMap {
        height: 70vh;
        width: 100%;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .navigation-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
        height: calc(100vh - 120px);
    }
    
    @media (min-width: 1024px) {
        .navigation-container {
            grid-template-columns: 400px 1fr;
        }
    }
    
    .sidebar {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }
    
    .route-steps {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
    }
    
    .step-item {
        display: flex;
        align-items: flex-start;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        border-radius: 0.375rem;
        transition: all 0.2s;
    }
    
    .step-item.active {
        background-color: #dbeafe;
        border-left: 4px solid #3b82f6;
    }
    
    .step-item.completed {
        opacity: 0.7;
    }
    
    .step-number {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background-color: #e5e7eb;
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        font-weight: 600;
        margin-right: 0.75rem;
        flex-shrink: 0;
    }
    
    .step-item.active .step-number {
        background-color: #3b82f6;
        color: white;
    }
    
    .step-item.completed .step-number {
        background-color: #10b981;
        color: white;
    }
    
    .patient-info {
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .navigation-controls {
        padding: 1rem;
        border-top: 1px solid #e5e7eb;
        background: white;
    }
    
    .stats-bar {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    .stat-card {
        background: white;
        padding: 1rem;
        border-radius: 0.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        text-align: center;
    }
    
    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #3b82f6;
    }
    
    .stat-label {
        font-size: 0.75rem;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .live-location {
        position: absolute;
        top: 1rem;
        right: 1rem;
        z-index: 1000;
        background: white;
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        font-size: 0.875rem;
    }
    
    .live-dot {
        width: 8px;
        height: 8px;
        background-color: #ef4444;
        border-radius: 50%;
        margin-right: 0.5rem;
        animation: pulse 1.5s infinite;
    }
    
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
    
    .current-location {
        position: absolute;
        bottom: 1rem;
        left: 1rem;
        z-index: 1000;
        background: white;
        padding: 0.75rem;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        max-width: 300px;
        font-size: 0.75rem;
    }
</style>
@endsection

@section('content')
<div class="navigation-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Patient Information -->
        <div class="patient-info">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h3 class="font-bold text-gray-900">{{ $delivery->patient->name }}</h3>
                    <p class="text-sm text-gray-600">Pengantaran #{{ $delivery->id }}</p>
                </div>
                @if($delivery->priority === 'urgent')
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                    URGENT
                </span>
                @endif
            </div>
            
            <div class="space-y-2">
                <div class="flex items-center">
                    <i class="fas fa-phone text-gray-400 mr-2 w-4"></i>
                    <a href="tel:{{ $delivery->patient->phone }}" class="text-sm text-blue-600 hover:text-blue-800">
                        {{ $delivery->patient->phone }}
                    </a>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-map-marker-alt text-gray-400 mr-2 w-4 mt-0.5"></i>
                    <span class="text-sm">{{ $delivery->delivery_address }}</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-pills text-gray-400 mr-2 w-4"></i>
                    <span class="text-sm">{{ $delivery->prescription->medication_name ?? 'Obat' }}</span>
                </div>
            </div>
            
            <div class="mt-4 pt-4 border-t border-gray-200">
                <div class="stats-bar">
                    <div class="stat-card">
                        <div class="stat-value" id="distance-value">-</div>
                        <div class="stat-label">Jarak</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="duration-value">-</div>
                        <div class="stat-label">Waktu</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="eta-value">-</div>
                        <div class="stat-label">ETA</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Route Steps -->
        <div class="route-steps" id="route-steps-container">
            <h4 class="font-medium text-gray-900 mb-3">Rute Perjalanan</h4>
            <div id="route-steps">
                <!-- Steps will be populated by JavaScript -->
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-route text-3xl mb-2"></i>
                    <p>Menghitung rute...</p>
                </div>
            </div>
        </div>
        
        <!-- Navigation Controls -->
        <div class="navigation-controls">
            <div class="space-y-3">
                <button onclick="startNavigation()" id="start-btn" 
                       class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg flex items-center justify-center">
                    <i class="fas fa-play mr-2"></i> Mulai Navigasi
                </button>
                
                <div class="grid grid-cols-2 gap-2">
                    <button onclick="openExternalMaps()" 
                           class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded flex items-center justify-center text-sm">
                        <i class="fas fa-external-link-alt mr-1"></i> Google Maps
                    </button>
                    <button onclick="callPatient()" 
                           class="bg-purple-600 hover:bg-purple-700 text-white py-2 px-3 rounded flex items-center justify-center text-sm">
                        <i class="fas fa-phone mr-1"></i> Telepon
                    </button>
                </div>
                
                <button onclick="markAsArrived()" id="arrived-btn" 
                       class="w-full bg-yellow-600 hover:bg-yellow-700 text-white py-3 px-4 rounded-lg hidden">
                    <i class="fas fa-map-marker-alt mr-2"></i> Sampai di Lokasi
                </button>
                
                <button onclick="completeDelivery()" id="complete-btn" 
                       class="w-full bg-red-600 hover:bg-red-700 text-white py-3 px-4 rounded-lg hidden">
                    <i class="fas fa-check-circle mr-2"></i> Selesaikan Pengantaran
                </button>
                
                <button onclick="cancelNavigation()" 
                       class="w-full bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg">
                    <i class="fas fa-times mr-2"></i> Batalkan
                </button>
            </div>
        </div>
    </div>
    
    <!-- Map Container -->
    <div class="relative">
        <div id="navigationMap"></div>
        
        <!-- Live Location Indicator -->
        <div class="live-location">
            <div class="live-dot"></div>
            <span id="location-status">Melacak lokasi...</span>
        </div>
        
        <!-- Current Location Display -->
        <div class="current-location hidden" id="current-location-display">
            <div class="font-medium mb-1">Lokasi Anda:</div>
            <div class="text-gray-600" id="coordinates-display"></div>
            <div class="text-gray-500 text-xs mt-1" id="address-display"></div>
        </div>
    </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>

<script>
let map;
let routingControl;
let currentLocationMarker;
let destinationMarker;
let locationWatchId;
let currentLatLng = null;
let routeData = null;
let isNavigating = false;
let arrivalRadius = 50; // meters
let checkInterval;

const deliveryData = @json($delivery);
const destinationLat = {{ $delivery->latitude ?? $delivery->patient->latitude ?? -6.2088 }};
const destinationLng = {{ $delivery->longitude ?? $delivery->patient->longitude ?? 106.8456 }};

document.addEventListener('DOMContentLoaded', function() {
    initializeMap();
    setupLocationTracking();
    loadRoute();
});

function initializeMap() {
    // Initialize map
    map = L.map('navigationMap').setView([-6.2088, 106.8456], 13);
    
    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19,
    }).addTo(map);
    
    // Add destination marker
    destinationMarker = L.marker([destinationLat, destinationLng], {
        icon: L.divIcon({
            className: 'destination-marker',
            html: `<div style="background-color: #dc2626; width: 50px; height: 50px; border-radius: 50%; border: 4px solid white; box-shadow: 0 4px 12px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                    <i class="fas fa-flag"></i>
                </div>`,
            iconSize: [50, 50],
            iconAnchor: [25, 25]
        })
    }).addTo(map).bindPopup(`
        <b>Tujuan: ${deliveryData.patient.name}</b><br>
        ${deliveryData.delivery_address}<br>
        <small>Klik "Mulai Navigasi" untuk memulai perjalanan</small>
    `).openPopup();
}

function setupLocationTracking() {
    if (navigator.geolocation) {
        // First get initial position
        navigator.geolocation.getCurrentPosition(
            position => {
                currentLatLng = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                updateCurrentLocationMarker();
                updateLocationDisplay();
            },
            error => {
                console.log('Geolocation error:', error);
                alert('Tidak dapat mengakses lokasi. Pastikan GPS diaktifkan.');
            },
            { enableHighAccuracy: true }
        );
        
        // Then watch for changes
        locationWatchId = navigator.geolocation.watchPosition(
            position => {
                currentLatLng = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                
                if (isNavigating) {
                    updateCurrentLocationMarker();
                    updateLocationDisplay();
                    checkArrival();
                }
            },
            error => {
                console.log('Location watch error:', error);
                document.getElementById('location-status').textContent = 'Gagal melacak lokasi';
                document.getElementById('location-status').style.color = '#dc2626';
            },
            {
                enableHighAccuracy: true,
                maximumAge: 10000,
                timeout: 5000
            }
        );
        
        document.getElementById('location-status').textContent = 'Lokasi aktif';
        document.getElementById('location-status').style.color = '#10b981';
    } else {
        alert('Browser tidak mendukung geolocation');
    }
}

function updateCurrentLocationMarker() {
    if (!currentLatLng) return;
    
    // Remove existing marker
    if (currentLocationMarker) {
        map.removeLayer(currentLocationMarker);
    }
    
    // Create new marker
    currentLocationMarker = L.marker([currentLatLng.lat, currentLatLng.lng], {
        icon: L.divIcon({
            className: 'current-location-marker',
            html: `<div style="background-color: #3b82f6; width: 40px; height: 40px; border-radius: 50%; border: 4px solid white; box-shadow: 0 4px 12px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white; font-size: 16px;">
                    <i class="fas fa-location-arrow"></i>
                </div>`,
            iconSize: [40, 40],
            iconAnchor: [20, 20]
        }),
        zIndexOffset: 1000
    }).addTo(map);
    
    // Center map on current location if not navigating
    if (!isNavigating) {
        map.setView([currentLatLng.lat, currentLatLng.lng], 15);
    }
    
    // Show current location display
    document.getElementById('current-location-display').classList.remove('hidden');
}

function updateLocationDisplay() {
    if (!currentLatLng) return;
    
    document.getElementById('coordinates-display').textContent = 
        `${currentLatLng.lat.toFixed(6)}, ${currentLatLng.lng.toFixed(6)}`;
    
    // Reverse geocode to get address (simulated)
    const addresses = [
        'Jl. Sudirman No. 123',
        'Jl. Gatot Subroto No. 456',
        'Jl. Thamrin No. 789',
        'Jl. MH Thamrin No. 10'
    ];
    const randomAddress = addresses[Math.floor(Math.random() * addresses.length)];
    document.getElementById('address-display').textContent = randomAddress;
}

function loadRoute() {
    if (!currentLatLng) {
        setTimeout(loadRoute, 1000);
        return;
    }
    
    fetch(`/api/deliveries/{{ $delivery->id }}/route?current_lat=${currentLatLng.lat}&current_lng=${currentLatLng.lng}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                routeData = data.route;
                updateRouteInfo();
                renderRouteSteps();
                drawRouteOnMap();
            }
        })
        .catch(error => {
            console.error('Error loading route:', error);
        });
}

function drawRouteOnMap() {
    if (!routeData || !routeData.polyline) return;
    
    // Draw polyline
    L.polyline(routeData.polyline, {
        color: '#3b82f6',
        weight: 4,
        opacity: 0.7,
        lineJoin: 'round'
    }).addTo(map);
    
    // Fit map to show route
    const bounds = L.latLngBounds([
        [currentLatLng.lat, currentLatLng.lng],
        [destinationLat, destinationLng]
    ]);
    map.fitBounds(bounds.pad(0.1));
}

function updateRouteInfo() {
    if (!routeData) return;
    
    document.getElementById('distance-value').textContent = routeData.distance.text;
    document.getElementById('duration-value').textContent = routeData.duration.text;
    
    // Calculate ETA
    const now = new Date();
    const eta = new Date(now.getTime() + (routeData.duration.value * 1000));
    const etaString = eta.toLocaleTimeString('id-ID', { 
        hour: '2-digit', 
        minute: '2-digit' 
    });
    document.getElementById('eta-value').textContent = etaString;
}

function renderRouteSteps() {
    if (!routeData || !routeData.steps) return;
    
    const container = document.getElementById('route-steps');
    container.innerHTML = '';
    
    routeData.steps.forEach((step, index) => {
        const stepDiv = document.createElement('div');
        stepDiv.className = 'step-item';
        stepDiv.id = `step-${index}`;
        stepDiv.innerHTML = `
            <div class="step-number">${index + 1}</div>
            <div class="flex-1">
                <div class="font-medium text-gray-900">${step.instruction}</div>
                <div class="text-sm text-gray-500 mt-1">
                    <span class="text-blue-600 font-medium">${step.distance.text}</span>
                    <span class="mx-2">•</span>
                    <span>${step.duration.text}</span>
                </div>
            </div>
        `;
        container.appendChild(stepDiv);
    });
}

function startNavigation() {
    if (!currentLatLng) {
        alert('Tunggu lokasi Anda terdeteksi...');
        return;
    }
    
    isNavigating = true;
    document.getElementById('start-btn').classList.add('hidden');
    document.getElementById('arrived-btn').classList.remove('hidden');
    
    // Start checking arrival
    checkInterval = setInterval(checkArrival, 5000);
    
    // Update first step as active
    document.getElementById('step-0').classList.add('active');
    
    alert('Navigasi dimulai! Aplikasi akan memandu Anda ke tujuan.');
}

function checkArrival() {
    if (!currentLatLng || !isNavigating) return;
    
    const distance = calculateDistance(
        currentLatLng.lat, currentLatLng.lng,
        destinationLat, destinationLng
    );
    
    if (distance <= arrivalRadius) {
        clearInterval(checkInterval);
        document.getElementById('arrived-btn').classList.add('hidden');
        document.getElementById('complete-btn').classList.remove('hidden');
        
        // Update all steps as completed
        routeData.steps.forEach((_, index) => {
            const stepEl = document.getElementById(`step-${index}`);
            if (stepEl) {
                stepEl.classList.add('completed');
                stepEl.classList.remove('active');
            }
        });
        
        // Show completion step
        const completionStep = document.createElement('div');
        completionStep.className = 'step-item completed';
        completionStep.innerHTML = `
            <div class="step-number"><i class="fas fa-check"></i></div>
            <div class="flex-1">
                <div class="font-medium text-gray-900">Sampai di tujuan!</div>
                <div class="text-sm text-gray-500 mt-1">
                    Anda telah sampai di lokasi pasien
                </div>
            </div>
        `;
        document.getElementById('route-steps').appendChild(completionStep);
        
        alert('Anda telah sampai di lokasi pasien! Klik "Selesaikan Pengantaran" untuk melanjutkan.');
    }
}

function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371000; // Earth's radius in meters
    const φ1 = lat1 * Math.PI/180;
    const φ2 = lat2 * Math.PI/180;
    const Δφ = (lat2-lat1) * Math.PI/180;
    const Δλ = (lon2-lon1) * Math.PI/180;

    const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
              Math.cos(φ1) * Math.cos(φ2) *
              Math.sin(Δλ/2) * Math.sin(Δλ/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

    return R * c;
}

function markAsArrived() {
    fetch(`/api/deliveries/{{ $delivery->id }}/arrived`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Status diperbarui: Sudah sampai di lokasi');
        }
    });
}

function completeDelivery() {
    window.location.href = `/map/complete-delivery/{{ $delivery->id }}`;
}

function openExternalMaps() {
    window.open(`https://www.google.com/maps/dir/?api=1&destination=${destinationLat},${destinationLng}&travelmode=driving&dir_action=navigate`, '_blank');
}

function callPatient() {
    window.location.href = `tel:{{ $delivery->patient->phone }}`;
}

function cancelNavigation() {
    if (confirm('Batalkan navigasi?')) {
        if (locationWatchId && navigator.geolocation) {
            navigator.geolocation.clearWatch(locationWatchId);
        }
        if (checkInterval) {
            clearInterval(checkInterval);
        }
        window.location.href = '/map';
    }
}

// Update step progress based on distance
function updateStepProgress() {
    if (!routeData || !currentLatLng) return;
    
    let closestStep = 0;
    let minDistance = Infinity;
    
    routeData.steps.forEach((step, index) => {
        if (step.start_location) {
            const distance = calculateDistance(
                currentLatLng.lat, currentLatLng.lng,
                step.start_location.lat, step.start_location.lng
            );
            
            if (distance < minDistance) {
                minDistance = distance;
                closestStep = index;
            }
        }
    });
    
    // Update step UI
    routeData.steps.forEach((_, index) => {
        const stepEl = document.getElementById(`step-${index}`);
        if (stepEl) {
            stepEl.classList.remove('active', 'completed');
            if (index < closestStep) {
                stepEl.classList.add('completed');
            } else if (index === closestStep) {
                stepEl.classList.add('active');
            }
        }
    });
}

// Periodically update step progress
setInterval(updateStepProgress, 10000);
</script>
@endsection