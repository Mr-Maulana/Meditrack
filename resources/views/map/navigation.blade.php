@extends('layouts.app')

@section('title', 'Navigasi ke ' . $delivery->patient->name)
@section('page-title', 'Navigasi Pengantaran')

@section('styles')
<style>
    #navigationMap {
        height: 500px;
        width: 100%;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .route-info {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .step-item {
        padding: 0.75rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: flex-start;
    }
    
    .step-number {
        background-color: #3b82f6;
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        margin-right: 0.75rem;
        flex-shrink: 0;
    }
    
    .navigation-controls {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        padding: 1rem;
        box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1);
        z-index: 1000;
    }
    
    .patient-info-card {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
    
    @media (max-width: 768px) {
        #navigationMap {
            height: 400px;
        }
        
        .navigation-controls {
            padding: 0.75rem;
        }
    }
</style>
@endsection

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-2 sm:space-y-0">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Navigasi Pengantaran</h2>
            <p class="text-gray-600">Menuju: {{ $delivery->patient->name }}</p>
        </div>
        <div class="flex space-x-2">
            <button onclick="getCurrentLocation()" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                <i class="fas fa-location-arrow mr-1"></i> Lokasi Saya
            </button>
            <button onclick="openMapsApp()" class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">
                <i class="fas fa-external-link-alt mr-1"></i> Buka di Maps
            </button>
            <a href="{{ route('map.index') }}" class="inline-flex items-center px-3 py-1.5 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm">
                <i class="fas fa-times mr-1"></i> Tutup
            </a>
        </div>
    </div>

    <!-- Patient Information -->
    <div class="patient-info-card">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h3 class="font-medium text-gray-900 mb-2">Informasi Pasien</h3>
                <div class="space-y-1">
                    <div class="flex items-center">
                        <i class="fas fa-user text-gray-400 mr-2 w-4"></i>
                        <span class="text-sm">{{ $delivery->patient->name }}</span>
                    </div>
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
                </div>
            </div>
            
            <div>
                <h3 class="font-medium text-gray-900 mb-2">Informasi Pengantaran</h3>
                <div class="space-y-1">
                    <div class="flex items-center">
                        <i class="fas fa-clock text-gray-400 mr-2 w-4"></i>
                        <span class="text-sm">{{ $delivery->delivery_date->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-gray-400 mr-2 w-4"></i>
                        <span class="text-sm">
                            @if($delivery->priority === 'urgent')
                            <span class="text-red-600 font-medium">URGENT</span>
                            @else
                            Normal
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-pills text-gray-400 mr-2 w-4"></i>
                        <span class="text-sm">{{ $delivery->prescription->medication_name ?? 'Obat' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Map -->
    <div id="navigationMap"></div>

    <!-- Route Information -->
    <div class="route-info">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-medium text-gray-900">Rute Perjalanan</h3>
            <div class="text-sm text-gray-500">
                <span id="route-distance">Memuat...</span> • <span id="route-duration">Memuat...</span>
            </div>
        </div>
        
        <div id="route-steps" class="space-y-1">
            <div class="step-item">
                <div class="step-number">1</div>
                <div>
                    <div class="font-medium">Memuat rute...</div>
                    <div class="text-sm text-gray-500">Menghitung rute optimal</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delivery Progress -->
    <div class="route-info">
        <h3 class="font-medium text-gray-900 mb-4">Status Pengantaran</h3>
        <div class="relative">
            <!-- Progress Line -->
            <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
            
            <!-- Steps -->
            <div class="space-y-6 relative z-10">
                <!-- Step 1: Pickup -->
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white">
                        <i class="fas fa-check text-xs"></i>
                    </div>
                    <div class="ml-4">
                        <div class="font-medium text-gray-900">Obat Diambil</div>
                        <div class="text-sm text-gray-500">Obat sudah diambil dari apotek</div>
                    </div>
                </div>
                
                <!-- Step 2: On the way -->
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white" id="step-on-way">
                        <i class="fas fa-truck text-xs"></i>
                    </div>
                    <div class="ml-4">
                        <div class="font-medium text-gray-900" id="step-on-way-text">Dalam Perjalanan</div>
                        <div class="text-sm text-gray-500" id="step-on-way-desc">Menuju lokasi pasien</div>
                        <div class="mt-1 text-xs text-blue-600" id="current-location-display"></div>
                    </div>
                </div>
                
                <!-- Step 3: Arrived -->
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-400" id="step-arrived">
                        <i class="fas fa-map-marker-alt text-xs"></i>
                    </div>
                    <div class="ml-4">
                        <div class="font-medium text-gray-900" id="step-arrived-text">Sampai di Lokasi</div>
                        <div class="text-sm text-gray-500" id="step-arrived-desc">Tiba di lokasi pasien</div>
                    </div>
                </div>
                
                <!-- Step 4: Delivered -->
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-400" id="step-delivered">
                        <i class="fas fa-check-circle text-xs"></i>
                    </div>
                    <div class="ml-4">
                        <div class="font-medium text-gray-900" id="step-delivered-text">Obat Diserahkan</div>
                        <div class="text-sm text-gray-500" id="step-delivered-desc">Serah terima dengan pasien</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Navigation Controls (Fixed Bottom) -->
<div class="navigation-controls">
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col sm:flex-row justify-between items-center space-y-3 sm:space-y-0">
            <div class="text-sm text-gray-600">
                <span id="time-remaining">Estimasi waktu: 15 menit</span>
            </div>
            
            <div class="flex space-x-3">
                <button onclick="markAsArrived()" id="arrived-btn" 
                   class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 flex items-center">
                    <i class="fas fa-map-marker-alt mr-2"></i> Sampai di Lokasi
                </button>
                
                <button onclick="openDeliveryForm()" id="deliver-btn" 
                   class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 flex items-center hidden">
                    <i class="fas fa-clipboard-check mr-2"></i> Serahkan Obat
                </button>
                
                <button onclick="cancelNavigation()" 
                   class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 flex items-center">
                    <i class="fas fa-times mr-2"></i> Batalkan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delivery Form Modal -->
<div id="deliveryFormModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-4 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Form Serah Terima Obat</h3>
            <button onclick="closeDeliveryForm()" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="deliveryCompletionForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="status" value="delivered">
            
            <div class="space-y-6 max-h-96 overflow-y-auto p-2">
                <!-- Patient Information -->
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-2">Konfirmasi Penerima</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Penerima *</label>
                            <input type="text" name="recipient_name" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Nama lengkap penerima">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hubungan dengan Pasien *</label>
                            <select name="recipient_relation" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih Hubungan</option>
                                <option value="pasien">Pasien Sendiri</option>
                                <option value="keluarga">Keluarga</option>
                                <option value="teman">Teman</option>
                                <option value="tetangga">Tetangga</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon Penerima</label>
                            <input type="tel" name="recipient_phone" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="0812-3456-7890">
                        </div>
                    </div>
                </div>
                
                <!-- Medicine Confirmation -->
                <div class="bg-green-50 p-4 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-2">Konfirmasi Obat</h4>
                    <div id="medicine-list">
                        <!-- Medicine items will be populated by JavaScript -->
                    </div>
                    <div class="mt-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="medicine_confirmed" required 
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">
                                Saya telah memeriksa dan memastikan obat sudah sesuai dengan resep
                            </span>
                        </label>
                    </div>
                </div>
                
                <!-- Documentation -->
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-2">Dokumentasi</h4>
                    
                    <!-- Photo Upload -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto Bukti Penyerahan</label>
                        <div class="mt-1 flex items-center">
                            <input type="file" name="proof_image" id="proof_image" accept="image/*" capture="environment"
                                class="hidden">
                            <label for="proof_image" class="cursor-pointer">
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition">
                                    <i class="fas fa-camera text-3xl text-gray-400 mb-2"></i>
                                    <p class="text-sm text-gray-600">Ambil foto atau unggah dari galeri</p>
                                    <p class="text-xs text-gray-500 mt-1">Maksimal 2MB</p>
                                </div>
                            </label>
                        </div>
                        <div id="preview-container" class="mt-2 hidden">
                            <img id="image-preview" class="h-32 rounded-lg shadow">
                        </div>
                    </div>
                    
                    <!-- Signature -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanda Tangan Penerima</label>
                        <div class="border border-gray-300 rounded-lg p-4 bg-white">
                            <div id="signature-pad" class="border border-gray-200 rounded h-40 w-full"></div>
                            <div class="mt-2 flex justify-between">
                                <button type="button" onclick="clearSignature()" class="text-sm text-red-600 hover:text-red-800">
                                    <i class="fas fa-eraser mr-1"></i> Hapus
                                </button>
                                <input type="hidden" name="signature" id="signature-input">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Tambahan</label>
                        <textarea name="delivery_notes" rows="3" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Catatan khusus tentang penyerahan obat..."></textarea>
                    </div>
                </div>
                
                <!-- Failure Option -->
                <div class="bg-red-50 p-4 rounded-lg hidden" id="failure-option">
                    <h4 class="font-medium text-gray-900 mb-2">Jika Pengantaran Gagal</h4>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="failure_reason" value="pasien_tidak_ada" class="mr-2">
                            <span class="text-sm text-gray-700">Pasien tidak ada di lokasi</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="failure_reason" value="alamat_salah" class="mr-2">
                            <span class="text-sm text-gray-700">Alamat tidak ditemukan</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="failure_reason" value="lainnya" class="mr-2">
                            <span class="text-sm text-gray-700">Lainnya</span>
                        </div>
                        <textarea name="failure_notes" rows="2" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 mt-2 hidden"
                            placeholder="Jelaskan alasan kegagalan..."></textarea>
                </div>
            </div>
            
            <div class="mt-6 pt-4 border-t border-gray-200 flex justify-between">
                <button type="button" onclick="markAsFailed()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    <i class="fas fa-times mr-2"></i> Tandai Gagal
                </button>
                
                <div class="flex space-x-3">
                    <button type="button" onclick="closeDeliveryForm()" 
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" 
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        <i class="fas fa-check-circle mr-2"></i> Konfirmasi Penyerahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- Signature Pad -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

<script>
let map;
let routePolyline;
let destinationMarker;
let currentLocationMarker;
let locationWatchId;
let signaturePad;
let deliveryData = @json($delivery);

document.addEventListener('DOMContentLoaded', function() {
    // Initialize map
    map = L.map('navigationMap').setView([-6.2088, 106.8456], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    // Initialize signature pad
    const canvas = document.getElementById('signature-pad');
    if (canvas) {
        signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(0, 0, 0)'
        });
    }
    
    // Load route and patient data
    loadRouteData();
    loadPatientMedicines();
    
    // Start tracking location
    startLocationTracking();
    
    // Set up form previews
    setupFormPreviews();
});

function loadRouteData() {
    const destinationLat = {{ $delivery->latitude ?? $delivery->patient->latitude ?? -6.2088 }};
    const destinationLng = {{ $delivery->longitude ?? $delivery->patient->longitude ?? 106.8456 }};
    
    // Add destination marker
    destinationMarker = L.marker([destinationLat, destinationLng], {
        icon: L.divIcon({
            className: 'destination-marker',
            html: '<div style="background-color: #dc2626; width: 40px; height: 40px; border-radius: 50%; border: 4px solid white; box-shadow: 0 2px 10px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white;"><i class="fas fa-flag"></i></div>',
            iconSize: [40, 40],
            iconAnchor: [20, 20]
        })
    })
    .addTo(map)
    .bindPopup(`<b>Tujuan: ${deliveryData.patient.name}</b><br>${deliveryData.delivery_address}`)
    .openPopup();
    
    // Fit map to include destination
    map.setView([destinationLat, destinationLng], 13);
    
    // Update route info (simulated)
    updateRouteInfo();
}

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
    
    // Update current location display
    document.getElementById('current-location-display').textContent = 
        `Lokasi saat ini: ${latitude.toFixed(6)}, ${longitude.toFixed(6)}`;
    
    // Update route line
    updateRouteLine(latitude, longitude);
    
    // Check if arrived
    checkIfArrived(latitude, longitude);
}

function updateRouteLine(currentLat, currentLng) {
    // Remove existing route line
    if (routePolyline) {
        map.removeLayer(routePolyline);
    }
    
    const destinationLat = {{ $delivery->latitude ?? $delivery->patient->latitude ?? -6.2088 }};
    const destinationLng = {{ $delivery->longitude ?? $delivery->patient->longitude ?? 106.8456 }};
    
    // Create route line (simplified)
    const routePoints = [
        [currentLat, currentLng],
        [destinationLat, destinationLng]
    ];
    
    routePolyline = L.polyline(routePoints, {
        color: '#3b82f6',
        weight: 4,
        opacity: 0.7,
        dashArray: '10, 10'
    }).addTo(map);
    
    // Fit map to show both points
    const bounds = L.latLngBounds(routePoints);
    map.fitBounds(bounds, { padding: [50, 50] });
}

function updateRouteInfo() {
    // Simulated route data
    document.getElementById('route-distance').textContent = '5.2 km';
    document.getElementById('route-duration').textContent = '15 menit';
    document.getElementById('time-remaining').textContent = 'Estimasi waktu: 15 menit';
    
    // Simulated route steps
    const steps = [
        'Belok kiri ke Jl. Sudirman (200 m)',
        'Lurus sampai perempatan (1.2 km)',
        'Belok kanan ke Jl. Gatot Subroto (800 m)',
        'Terus sampai tujuan (3 km)'
    ];
    
    const stepsContainer = document.getElementById('route-steps');
    stepsContainer.innerHTML = '';
    
    steps.forEach((step, index) => {
        const stepDiv = document.createElement('div');
        stepDiv.className = 'step-item';
        stepDiv.innerHTML = `
            <div class="step-number">${index + 1}</div>
            <div>
                <div class="font-medium">${step.split('(')[0].trim()}</div>
                <div class="text-sm text-gray-500">${step.match(/\(([^)]+)\)/)[1]}</div>
            </div>
        `;
        stepsContainer.appendChild(stepDiv);
    });
}

function checkIfArrived(currentLat, currentLng) {
    const destinationLat = {{ $delivery->latitude ?? $delivery->patient->latitude ?? -6.2088 }};
    const destinationLng = {{ $delivery->longitude ?? $delivery->patient->longitude ?? 106.8456 }};
    
    // Calculate distance in meters
    const distance = calculateDistance(currentLat, currentLng, destinationLat, destinationLng);
    
    if (distance < 100) { // Within 100 meters
        document.getElementById('step-on-way-text').textContent = 'Mendekati Lokasi';
        document.getElementById('step-on-way-desc').textContent = 'Hampir sampai di lokasi pasien';
        document.getElementById('arrived-btn').classList.remove('hidden');
        
        if (distance < 50) {
            document.getElementById('step-on-way-text').textContent = 'Sudah di Lokasi';
            document.getElementById('step-on-way-desc').textContent = 'Anda sudah berada di lokasi pasien';
        }
    }
}

function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371e3; // Earth's radius in meters
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

function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const { latitude, longitude } = position.coords;
                updateCurrentLocation(latitude, longitude);
            },
            function(error) {
                alert('Tidak dapat mendapatkan lokasi: ' + error.message);
            }
        );
    }
}

function openMapsApp() {
    const destinationLat = {{ $delivery->latitude ?? $delivery->patient->latitude ?? -6.2088 }};
    const destinationLng = {{ $delivery->longitude ?? $delivery->patient->longitude ?? 106.8456 }};
    
    // Open in Google Maps
    window.open(`https://www.google.com/maps/dir/?api=1&destination=${destinationLat},${destinationLng}&travelmode=driving`, '_blank');
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
            // Update UI
            document.getElementById('step-arrived').classList.remove('bg-gray-200', 'text-gray-400');
            document.getElementById('step-arrived').classList.add('bg-green-500', 'text-white');
            document.getElementById('step-arrived-text').textContent = 'Sampai di Lokasi ✓';
            document.getElementById('step-arrived-desc').textContent = 'Tiba di lokasi pasien - ' + new Date().toLocaleTimeString('id-ID');
            
            // Show delivery button
            document.getElementById('arrived-btn').classList.add('hidden');
            document.getElementById('deliver-btn').classList.remove('hidden');
            
            alert('Status diperbarui: Sudah sampai di lokasi');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Gagal memperbarui status');
    });
}

function openDeliveryForm() {
    document.getElementById('deliveryFormModal').classList.remove('hidden');
}

function closeDeliveryForm() {
    document.getElementById('deliveryFormModal').classList.add('hidden');
    // Reset form
    document.getElementById('deliveryCompletionForm').reset();
    if (signaturePad) {
        signaturePad.clear();
    }
    document.getElementById('preview-container').classList.add('hidden');
}

function clearSignature() {
    if (signaturePad) {
        signaturePad.clear();
    }
}

function setupFormPreviews() {
    // Image preview
    document.getElementById('proof_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image-preview').src = e.target.result;
                document.getElementById('preview-container').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Failure reason toggle
    document.querySelectorAll('input[name="failure_reason"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const failureNotes = document.querySelector('textarea[name="failure_notes"]');
            failureNotes.parentElement.classList.toggle('hidden', this.value !== 'lainnya');
        });
    });
}

function loadPatientMedicines() {
    const medicineList = document.getElementById('medicine-list');
    // Simulated medicine data - in real app, fetch from API
    const medicines = [
        { name: 'Amoxicillin 500mg', dosage: '1 tablet', frequency: '3x sehari', duration: '7 hari' },
        { name: 'Paracetamol 500mg', dosage: '1 tablet', frequency: 'jika demam', duration: '3 hari' }
    ];
    
    medicines.forEach(med => {
        const medDiv = document.createElement('div');
        medDiv.className = 'bg-white p-3 rounded border mb-2';
        medDiv.innerHTML = `
            <div class="font-medium">${med.name}</div>
            <div class="text-sm text-gray-600">
                ${med.dosage} | ${med.frequency} | ${med.duration}
            </div>
            <div class="mt-2 flex items-center">
                <input type="checkbox" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded" checked>
                <span class="ml-2 text-sm text-gray-700">Obat sesuai</span>
            </div>
        `;
        medicineList.appendChild(medDiv);
    });
}

function markAsFailed() {
    document.getElementById('failure-option').classList.remove('hidden');
    document.querySelector('input[name="status"]').value = 'failed';
    document.querySelector('button[type="submit"]').textContent = 'Tandai Gagal';
    document.querySelector('button[type="submit"]').classList.remove('bg-green-600');
    document.querySelector('button[type="submit"]').classList.add('bg-red-600');
}

document.getElementById('deliveryCompletionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Add signature if exists
    if (signaturePad && !signaturePad.isEmpty()) {
        formData.set('signature', signaturePad.toDataURL());
    }
    
    fetch(`/api/deliveries/{{ $delivery->id }}/status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI
            document.getElementById('step-delivered').classList.remove('bg-gray-200', 'text-gray-400');
            document.getElementById('step-delivered').classList.add('bg-green-500', 'text-white');
            document.getElementById('step-delivered-text').textContent = 'Obat Diserahkan ✓';
            document.getElementById('step-delivered-desc').textContent = 'Penyerahan selesai - ' + new Date().toLocaleTimeString('id-ID');
            
            // Hide delivery button
            document.getElementById('deliver-btn').classList.add('hidden');
            
            // Close modal
            closeDeliveryForm();
            
            alert('Pengantaran berhasil diselesaikan!');
            
            // Redirect after 3 seconds
            setTimeout(() => {
                window.location.href = '/map';
            }, 3000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Gagal menyelesaikan pengantaran');
    });
});

function cancelNavigation() {
    if (confirm('Batalkan navigasi ini?')) {
        // Stop location tracking
        if (locationWatchId && navigator.geolocation) {
            navigator.geolocation.clearWatch(locationWatchId);
        }
        
        window.location.href = '/map';
    }
}

// Clean up on page unload
window.addEventListener('beforeunload', function() {
    if (locationWatchId && navigator.geolocation) {
        navigator.geolocation.clearWatch(locationWatchId);
    }
});
</script>
@endsection