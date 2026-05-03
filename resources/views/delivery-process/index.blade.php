@extends('layouts.app')

@section('title', 'Antar Obat')
@section('page-title', 'Antar Obat')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Proses Antar Obat</h2>
            <p class="text-gray-600">Pilih dan proses pengantaran obat ke pasien</p>
        </div>
        
        <div class="flex space-x-2">
            <button onclick="getCurrentLocation()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                <i class="fas fa-location-arrow mr-2"></i> Lokasi Saya
            </button>
        </div>
    </div>

    <!-- In Progress Delivery -->
    @if($inProgressDeliveries->count() > 0)
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <div class="flex items-center mb-4">
            <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mr-3"></i>
            <h3 class="text-lg font-medium text-yellow-800">Pengantaran Sedang Berjalan</h3>
        </div>
        
        @foreach($inProgressDeliveries as $delivery)
        <div class="bg-white rounded-lg shadow p-6 mb-4">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                <div>
                    <h4 class="font-medium text-gray-900">{{ $delivery->patient->name }}</h4>
                    <div class="mt-2 space-y-1">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            <span>{{ $delivery->delivery_address }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-phone mr-2"></i>
                            <span>{{ $delivery->patient->phone }}</span>
                        </div>
                        @if($delivery->assessment->start_time)
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-clock mr-2"></i>
                            <span>Dimulai: {{ $delivery->assessment->start_time->format('H:i') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div class="flex space-x-2">
                    @if($delivery->assessment->arrival_time)
                    <a href="{{ route('delivery-process.assessment', $delivery->assessment->id) }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        <i class="fas fa-clipboard-check mr-2"></i> Lanjutkan Assesmen
                    </a>
                    @else
                    <a href="{{ route('delivery-process.route', $delivery->assessment->id) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        <i class="fas fa-route mr-2"></i> Lanjutkan Perjalanan
                    </a>
                    @endif
                </div>
            </div>
            
            <!-- Prescription Info -->
            @if($delivery->prescription)
            <div class="mt-4 pt-4 border-t border-gray-200">
                <h5 class="text-sm font-medium text-gray-700 mb-2">Daftar Obat:</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @php
                        $meds = $delivery->prescription->medications ?? [
                            [
                                'name' => $delivery->prescription->medication_name,
                                'dosage' => $delivery->prescription->dosage,
                                'frequency' => $delivery->prescription->frequency
                            ]
                        ];
                    @endphp
                    @foreach($meds as $med)
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                        <div class="font-bold text-sm text-gray-800">{{ $med['name'] }}</div>
                        <div class="text-[10px] text-gray-500 mt-1">
                            {{ $med['dosage'] ?? '-' }} | {{ $med['frequency'] ?? '-' }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    <!-- Available Deliveries -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Daftar Pengantaran Tersedia</h3>
            <p class="mt-1 text-sm text-gray-600">Pilih pasien untuk memulai pengantaran</p>
        </div>
        
        <div class="p-6">
            @if($availableDeliveries->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($availableDeliveries as $delivery)
                <div class="border border-gray-200 rounded-lg p-6 hover:border-blue-300 hover:shadow-md transition">
                    <!-- Patient Info -->
                    <div class="mb-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $delivery->patient->name }}</h4>
                                <p class="text-sm text-gray-600">{{ $delivery->patient->patient_code }}</p>
                            </div>
                            @if($delivery->priority === 'urgent')
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-exclamation-triangle mr-1"></i> Urgent
                            </span>
                            @endif
                        </div>
                        
                        <div class="mt-3 space-y-2">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>
                                <span class="truncate">{{ Str::limit($delivery->delivery_address, 40) }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-phone mr-2 text-gray-400"></i>
                                <span>{{ $delivery->patient->phone }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-calendar mr-2 text-gray-400"></i>
                                <span>Tanggal: {{ $delivery->delivery_date->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Prescription Summary -->
                    @if($delivery->prescription)
                    <div class="mb-4 p-3 bg-blue-50 rounded-xl border border-blue-100">
                        <h5 class="text-xs font-bold text-blue-800 uppercase tracking-wider mb-2">Daftar Obat:</h5>
                        @php
                            $meds = $delivery->prescription->medications ?? [
                                ['name' => $delivery->prescription->medication_name]
                            ];
                        @endphp
                        <div class="space-y-1.5">
                            @foreach($meds as $med)
                            <div class="text-[11px] text-blue-700 font-bold flex items-center">
                                <i class="fas fa-pills mr-2 text-[10px] opacity-70"></i> 
                                <span class="truncate">{{ $med['name'] }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <!-- Notes -->
                    @if($delivery->notes)
                    <div class="mb-4 p-3 bg-yellow-50 rounded">
                        <h5 class="text-sm font-medium text-yellow-800 mb-1">Catatan:</h5>
                        <p class="text-sm text-yellow-700">{{ $delivery->notes }}</p>
                    </div>
                    @endif
                    
                    <!-- Action Button -->
                    <button onclick="selectDelivery({{ $delivery->id }})" 
                            class="w-full mt-4 inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <i class="fas fa-truck mr-2"></i> Antar Obat Ini
                    </button>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <i class="fas fa-truck text-4xl text-gray-300 mb-4"></i>
                <h4 class="text-lg font-medium text-gray-700 mb-2">Tidak ada pengantaran tersedia</h4>
                <p class="text-gray-600">Semua pengantaran telah diproses atau sedang dalam perjalanan.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Total Tersedia</p>
                    <p class="text-2xl font-bold">{{ $availableDeliveries->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-spinner"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Dalam Proses</p>
                    <p class="text-2xl font-bold">{{ $inProgressDeliveries->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Selesai Hari Ini</p>
                    <p class="text-2xl font-bold">
                        {{ Auth::user()->deliveries()->whereDate('delivered_at', today())->count() }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div id="loadingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-1/3 mx-auto p-5 w-64">
        <div class="bg-white rounded-lg p-6 text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p class="text-gray-700">Memulai pengantaran...</p>
        </div>
    </div>
</div>

<script>
let currentLocation = null;

function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                currentLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                alert(`Lokasi berhasil diperoleh: ${currentLocation.lat.toFixed(6)}, ${currentLocation.lng.toFixed(6)}`);
            },
            function(error) {
                alert('Tidak dapat mendapatkan lokasi: ' + error.message);
            }
        );
    } else {
        alert('Browser tidak mendukung geolocation');
    }
}

function selectDelivery(deliveryId) {
    // Show loading modal
    document.getElementById('loadingModal').classList.remove('hidden');
    
    // Get delivery details first
    fetch(`/delivery-process/${deliveryId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                document.getElementById('loadingModal').classList.add('hidden');
                return;
            }
            
            // Confirm selection
            if (confirm(`Mulai pengantaran untuk ${data.patient.name}?\nAlamat: ${data.address}`)) {
                startDeliveryProcess(deliveryId);
            } else {
                document.getElementById('loadingModal').classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengambil data pengantaran.');
            document.getElementById('loadingModal').classList.add('hidden');
        });
}

function startDeliveryProcess(deliveryId) {
    const formData = new FormData();
    formData.append('delivery_id', deliveryId);
    formData.append('_token', '{{ csrf_token() }}');
    
    fetch('{{ route("delivery-process.select") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect_url;
        } else {
            alert(data.error || 'Terjadi kesalahan saat memulai pengantaran.');
            document.getElementById('loadingModal').classList.add('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memulai pengantaran.');
        document.getElementById('loadingModal').classList.add('hidden');
    });
}
</script>
@endsection