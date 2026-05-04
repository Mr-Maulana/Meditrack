@extends('layouts.app')

@section('title', 'Tugas Antar Obat')
@section('page-title', 'Operasional Kurir')

@section('content')
<div class="space-y-8 animate-fade-in pb-12">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-tni-700 to-tni-900 rounded-2xl flex items-center justify-center text-gold-400 shadow-xl border border-tni-600">
                <i class="fas fa-motorcycle text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-black text-gray-800 tracking-tight">Antar Obat</h2>
                <p class="text-gray-500 text-sm font-medium">Kelola dan proses pengiriman obat ke alamat pasien.</p>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <button onclick="getCurrentLocation()" class="inline-flex items-center px-6 py-3 bg-white text-tni-700 border border-tni-100 rounded-2xl hover:bg-tni-50 transition-all shadow-sm font-bold group">
                <i class="fas fa-location-crosshairs mr-2 group-hover:rotate-90 transition-transform"></i> Lokasi Saya
            </button>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center gap-5">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fas fa-box-open"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Tugas Tersedia</p>
                <p class="text-2xl font-black text-gray-800">{{ $availableDeliveries->count() }}</p>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center gap-5">
            <div class="w-12 h-12 rounded-2xl bg-gold-50 text-gold-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fas fa-person-running"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Sedang Berjalan</p>
                <p class="text-2xl font-black text-gray-800">{{ $inProgressDeliveries->count() }}</p>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center gap-5">
            <div class="w-12 h-12 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fas fa-circle-check"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Selesai Hari Ini</p>
                <p class="text-2xl font-black text-gray-800">
                    {{ Auth::user()->deliveries()->whereDate('delivered_at', today())->count() }}
                </p>
            </div>
        </div>
    </div>

    <!-- Active Delivery Banner -->
    @if($inProgressDeliveries->count() > 0)
    <div class="bg-gradient-to-br from-tni-800 to-tni-900 rounded-[2.5rem] p-8 text-white shadow-2xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 opacity-10">
            <i class="fas fa-truck-fast text-[12rem] rotate-[-15deg]"></i>
        </div>
        
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-6">
                <span class="flex h-3 w-3 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-gold-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-gold-500"></span>
                </span>
                <h3 class="text-lg font-black uppercase tracking-[0.2em] text-gold-400">Pengantaran Berjalan</h3>
            </div>
            
            <div class="space-y-6">
                @foreach($inProgressDeliveries as $delivery)
                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-3xl p-6 hover:bg-white/15 transition-all">
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h4 class="text-xl font-bold text-white">{{ $delivery->patient->name }}</h4>
                                <span class="px-3 py-0.5 bg-gold-500 text-tni-900 text-[10px] font-black rounded-full uppercase">{{ $delivery->patient->patient_code }}</span>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-gold-400 shrink-0">
                                        <i class="fas fa-location-dot text-xs"></i>
                                    </div>
                                    <p class="text-sm text-tni-100 font-medium">{{ $delivery->delivery_address }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-gold-400 shrink-0">
                                        <i class="fas fa-clock text-xs"></i>
                                    </div>
                                    <p class="text-sm text-tni-100 font-medium">Dimulai: {{ $delivery->assessment->start_time ? $delivery->assessment->start_time->format('H:i') : '-' }}</p>
                                </div>
                            </div>

                            <!-- Medications List (Daring/Daftar) -->
                            <div class="mt-6">
                                <p class="text-[10px] font-bold text-gold-400 uppercase tracking-widest mb-3">Daftar Obat Pasien:</p>
                                <div class="flex flex-wrap gap-2">
                                    @php
                                        $meds = $delivery->prescription->medications ?? [['name' => $delivery->prescription->medication_name]];
                                    @endphp
                                    @foreach($meds as $med)
                                    <div class="px-3 py-1.5 bg-white/5 border border-white/10 rounded-xl flex items-center gap-2">
                                        <i class="fas fa-pills text-[10px] text-gold-500"></i>
                                        <span class="text-xs font-bold text-white">{{ $med['name'] }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        <div class="w-full lg:w-auto shrink-0">
                            @if($delivery->assessment->arrival_time)
                            <a href="{{ route('delivery-process.assessment', $delivery->assessment->id) }}" 
                               class="flex items-center justify-center gap-2 px-8 py-4 bg-gold-500 text-tni-900 rounded-2xl font-black uppercase tracking-widest hover:bg-gold-400 transition-all shadow-xl shadow-gold-500/20 group w-full lg:w-auto">
                                <i class="fas fa-clipboard-check group-hover:scale-110 transition-transform"></i> Lanjutkan Assesmen
                            </a>
                            @else
                            <a href="{{ route('delivery-process.route', $delivery->assessment->id) }}" 
                               class="flex items-center justify-center gap-2 px-8 py-4 bg-white text-tni-900 rounded-2xl font-black uppercase tracking-widest hover:bg-tni-50 transition-all shadow-xl group w-full lg:w-auto">
                                <i class="fas fa-route group-hover:translate-x-1 transition-transform"></i> Navigasi Jalan
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Available Task List -->
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-black text-gray-800 uppercase tracking-wider flex items-center gap-3">
                <span class="w-2 h-8 bg-gold-500 rounded-full"></span>
                Tugas Pengantaran Baru
            </h3>
            <span class="text-xs font-bold text-gray-400 uppercase">{{ now()->format('d M Y') }}</span>
        </div>

        @if($availableDeliveries->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($availableDeliveries as $delivery)
            <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-xl hover:border-gold-200 transition-all duration-300 p-6 flex flex-col group overflow-hidden relative">
                @if($delivery->priority === 'urgent')
                <div class="absolute top-0 right-0">
                    <div class="bg-red-500 text-white text-[9px] font-black uppercase py-1 px-4 rounded-bl-xl tracking-tighter animate-pulse">
                        Urgent
                    </div>
                </div>
                @endif

                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center text-tni-700 font-black text-xl group-hover:bg-tni-800 group-hover:text-white transition-colors duration-300">
                        {{ substr($delivery->patient->name, 0, 1) }}
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800">{{ $delivery->patient->name }}</h4>
                        <p class="text-[10px] text-tni-600 font-bold uppercase">{{ $delivery->patient->patient_code }}</p>
                    </div>
                </div>

                <div class="space-y-3 mb-6 flex-1">
                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-2xl border border-gray-100">
                        <i class="fas fa-map-marker-alt text-tni-400 mt-1 text-xs"></i>
                        <p class="text-[11px] text-gray-600 font-medium leading-relaxed">{{ Str::limit($delivery->delivery_address, 80) }}</p>
                    </div>
                    
                    <!-- Medication Preview -->
                    @if($delivery->prescription)
                    <div class="p-3 bg-blue-50/50 rounded-2xl border border-blue-100/50">
                        <p class="text-[9px] font-bold text-blue-800 uppercase tracking-widest mb-2">Daftar Obat:</p>
                        @php
                            $meds = $delivery->prescription->medications ?? [['name' => $delivery->prescription->medication_name]];
                        @endphp
                        <div class="flex flex-wrap gap-1.5">
                            @foreach(array_slice($meds, 0, 3) as $med)
                            <span class="text-[10px] bg-white border border-blue-100 text-blue-700 px-2 py-0.5 rounded-lg font-bold">
                                {{ $med['name'] }}
                            </span>
                            @endforeach
                            @if(count($meds) > 3)
                            <span class="text-[9px] text-blue-500 font-bold px-1">+{{ count($meds) - 3 }} Lainnya</span>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                <button onclick="selectDelivery({{ $delivery->id }})" 
                        class="w-full py-4 bg-tni-800 text-white rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-black transition-all shadow-lg shadow-tni-900/10 group-hover:scale-[1.02]">
                    <i class="fas fa-truck-fast mr-2"></i> Proses Antar
                </button>
            </div>
            @endforeach
        </div>
        @else
        <div class="bg-white rounded-[2.5rem] p-16 text-center border-2 border-dashed border-gray-100">
            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-300">
                <i class="fas fa-box-open text-4xl"></i>
            </div>
            <h4 class="text-xl font-bold text-gray-800 mb-2">Belum Ada Tugas Baru</h4>
            <p class="text-gray-500 max-w-sm mx-auto">Semua pengantaran untuk hari ini telah selesai atau sedang diproses. Silakan cek kembali nanti.</p>
        </div>
        @endif
    </div>
</div>

<!-- Modal Loading Premium -->
<div id="loadingModal" class="fixed inset-0 bg-tni-900/80 backdrop-blur-md overflow-y-auto h-full w-full z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-[2.5rem] p-10 max-w-sm w-full mx-4 shadow-2xl transform transition-all text-center">
        <div class="relative w-20 h-20 mx-auto mb-6">
            <div class="absolute inset-0 rounded-full border-4 border-gray-100"></div>
            <div class="absolute inset-0 rounded-full border-4 border-gold-500 border-t-transparent animate-spin"></div>
            <div class="absolute inset-0 flex items-center justify-center text-gold-600">
                <i class="fas fa-motorcycle text-xl"></i>
            </div>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Menyiapkan Perjalanan</h3>
        <p class="text-sm text-gray-500">Sistem sedang mengaktifkan pelacakan dan menyiapkan rute navigasi...</p>
    </div>
</div>

<script>
let currentLocation = null;

function getCurrentLocation() {
    const btn = event.currentTarget;
    const originalContent = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner animate-spin mr-2"></i> Mencari...';
    
    // Locked location as requested
    setTimeout(() => {
        currentLocation = {
            lat: 5.182907239056203,
            lng: 97.14981118058444
        };
        btn.innerHTML = originalContent;
        btn.classList.add('bg-green-50', 'text-green-700', 'border-green-200');
        alert(`Lokasi Berhasil Ditemukan (Dikunci ke RS)!\nLatitude: ${currentLocation.lat.toFixed(6)}\nLongitude: ${currentLocation.lng.toFixed(6)}\n\nJl. Samudera No.53A, Kp Jawa, Kec. Banda Sakti, Kota Lhokseumawe, Aceh 24314`);
    }, 500); // Simulate network delay
}

function selectDelivery(deliveryId) {
    document.getElementById('loadingModal').classList.remove('hidden');
    
    fetch(`/delivery-process/${deliveryId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                document.getElementById('loadingModal').classList.add('hidden');
                return;
            }
            
            if (confirm(`Konfirmasi Pengantaran?\n\nPasien: ${data.patient.name}\nAlamat: ${data.address}\n\nSistem akan mulai melacak waktu perjalanan Anda.`)) {
                startDeliveryProcess(deliveryId);
            } else {
                document.getElementById('loadingModal').classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan koneksi.');
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
            alert(data.error || 'Gagal memproses tugas.');
            document.getElementById('loadingModal').classList.add('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan sistem.');
        document.getElementById('loadingModal').classList.add('hidden');
    });
}
</script>

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fade-in 0.5s ease-out forwards;
    }
</style>
@endsection