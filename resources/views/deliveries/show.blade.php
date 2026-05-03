@extends('layouts.app')

@section('title', 'Detail Pengantaran #' . $delivery->id)
@section('page-title', 'Informasi Pengantaran')

@section('content')
<div class="max-w-5xl mx-auto space-y-8 animate-fade-in pb-12">
    <!-- Breadcrumbs & Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-tni-600 transition-colors">
                        <i class="fas fa-home mr-2"></i> Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                        <a href="{{ route('deliveries.index') }}" class="text-gray-500 hover:text-tni-600 transition-colors">Pengantaran</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                        <span class="text-tni-700 font-medium">#{{ $delivery->id }}</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <div class="flex flex-wrap gap-2">
            @if(auth()->user()->isAdmin() || auth()->user()->isApoteker())
                <a href="{{ route('deliveries.edit', $delivery) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded-xl hover:bg-yellow-600 transition shadow-md font-medium">
                    <i class="fas fa-edit mr-2"></i> Edit Data
                </a>
            @endif
            
            @if(auth()->user()->isKurir() && in_array($delivery->status, ['pending', 'on_delivery']))
                <a href="{{ route('delivery-process.index') }}" class="inline-flex items-center px-4 py-2 bg-tni-600 text-white rounded-xl hover:bg-tni-700 transition shadow-md font-medium">
                    <i class="fas fa-motorcycle mr-2"></i> Lanjutkan Proses
                </a>
            @endif

            <a href="{{ route('deliveries.print', $delivery) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-tni-800 text-white rounded-xl hover:bg-black transition shadow-md font-medium">
                <i class="fas fa-print mr-2 text-gold-400"></i> Cetak Resi
            </a>

            <a href="{{ route('deliveries.track', $delivery) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition shadow-md font-medium">
                <i class="fas fa-map-location-dot mr-2"></i> Lacak Lokasi
            </a>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <!-- Status Header Banner -->
        @php
            $statusConfig = [
                'pending' => ['color' => 'yellow', 'icon' => 'clock', 'text' => 'Menunggu Kurir'],
                'on_delivery' => ['color' => 'blue', 'icon' => 'truck-fast', 'text' => 'Dalam Perjalanan'],
                'delivered' => ['color' => 'green', 'icon' => 'circle-check', 'text' => 'Sudah Diterima'],
                'failed' => ['color' => 'red', 'icon' => 'circle-xmark', 'text' => 'Gagal Terkirim'],
            ];
            $current = $statusConfig[$delivery->status] ?? $statusConfig['pending'];
        @endphp

        <div class="bg-gradient-to-r from-tni-800 to-tni-600 p-8 text-white">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-3xl border border-white/30">
                        <i class="fas fa-{{ $current['icon'] }}"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold">Status: {{ $current['text'] }}</h2>
                        <p class="text-tni-100 opacity-90">ID Pengantaran: #MT-{{ str_pad($delivery->id, 5, '0', STR_PAD_LEFT) }}</p>
                    </div>
                </div>
                
                @if($delivery->priority === 'urgent')
                <div class="px-6 py-2 bg-red-500/30 backdrop-blur-md border border-red-400/50 rounded-full text-sm font-bold tracking-wider animate-pulse flex items-center">
                    <i class="fas fa-bolt-lightning mr-2"></i> PRIORITAS URGENT
                </div>
                @endif
            </div>
        </div>

        <div class="p-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Patient & Prescription -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Patient Section -->
                    <section>
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <span class="w-8 h-8 rounded-lg bg-tni-100 text-tni-700 flex items-center justify-center mr-3 text-sm">
                                <i class="fas fa-user"></i>
                            </span>
                            Informasi Penerima
                        </h3>
                        <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Nama Pasien</p>
                                    <p class="text-gray-800 font-semibold text-lg">{{ $delivery->patient->name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Nomor Telepon</p>
                                    <p class="text-gray-800 font-medium">{{ $delivery->patient->phone }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Alamat Lengkap</p>
                                    <p class="text-gray-700 leading-relaxed">{{ $delivery->delivery_address }}</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Prescription Section -->
                    @if($delivery->prescription)
                    <section>
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <span class="w-8 h-8 rounded-lg bg-gold-100 text-gold-700 flex items-center justify-center mr-3 text-sm">
                                <i class="fas fa-file-prescription"></i>
                            </span>
                            Detail Resep Obat
                        </h3>
                        
                        <div class="space-y-4">
                            @php
                                $meds = $delivery->prescription->medications ?? [
                                    [
                                        'name' => $delivery->prescription->medication_name,
                                        'dosage' => $delivery->prescription->dosage,
                                        'frequency' => $delivery->prescription->frequency,
                                        'duration' => $delivery->prescription->duration,
                                        'instructions' => $delivery->prescription->instructions,
                                    ]
                                ];
                            @endphp

                            @foreach($meds as $i => $med)
                            <div class="bg-white rounded-2xl p-6 border-2 border-gold-100 relative overflow-hidden group hover:border-gold-300 transition-all shadow-sm hover:shadow-md">
                                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                                    <i class="fas fa-pills text-6xl text-gold-600"></i>
                                </div>
                                
                                <h4 class="text-gold-700 font-bold text-lg mb-4 flex items-center">
                                    <span class="w-6 h-6 bg-gold-500 text-white text-[10px] rounded-full flex items-center justify-center mr-2 shadow-sm">{{ $i + 1 }}</span>
                                    {{ $med['name'] ?? 'Obat' }}
                                </h4>
                                
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Dosis</p>
                                        <p class="text-xs font-bold text-gray-700">{{ $med['dosage'] ?? '-' }}</p>
                                    </div>
                                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Frekuensi</p>
                                        <p class="text-xs font-bold text-gray-700">{{ $med['frequency'] ?? '-' }}</p>
                                    </div>
                                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Durasi</p>
                                        <p class="text-xs font-bold text-gray-700">{{ $med['duration'] ?? '-' }}</p>
                                    </div>
                                </div>
                                @if(!empty($med['instructions']))
                                <div class="mt-3 text-[11px] italic text-gray-500 bg-gray-50 p-2.5 rounded-xl border border-gray-100 flex items-start gap-2">
                                    <i class="fas fa-info-circle text-gold-500 mt-0.5"></i>
                                    <span>"{{ $med['instructions'] }}"</span>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </section>
                    @endif
                </div>

                <!-- Right Column: Timeline & Courier -->
                <div class="space-y-8">
                    <!-- Courier Section -->
                    <section>
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center mr-3 text-sm">
                                <i class="fas fa-id-card"></i>
                            </span>
                            Petugas Kurir
                        </h3>
                        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
                            <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-tni-600 to-tni-800 flex items-center justify-center text-white text-xl font-bold border-2 border-gold-400 shadow-inner">
                                {{ $delivery->courier ? substr($delivery->courier->name, 0, 1) : '?' }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-800">{{ $delivery->courier->name ?? 'Belum Ditugaskan' }}</p>
                                @if($delivery->courier)
                                <p class="text-xs text-gray-500">{{ $delivery->courier->phone }}</p>
                                <span class="inline-block mt-1 px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded-md uppercase">Aktif</span>
                                @else
                                <p class="text-xs text-red-500 italic">Menunggu Penugasan</p>
                                @endif
                            </div>
                        </div>
                    </section>

                    <!-- Timeline Section -->
                    <section>
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <span class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center mr-3 text-sm">
                                <i class="fas fa-timeline"></i>
                            </span>
                            Riwayat Waktu
                        </h3>
                        <div class="space-y-4 ml-4">
                            <div class="relative pl-6 border-l-2 border-gray-100">
                                <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-gray-200 border-2 border-white"></div>
                                <p class="text-xs font-bold text-gray-400 uppercase mb-0.5">Dibuat Pada</p>
                                <p class="text-sm font-semibold text-gray-700">{{ $delivery->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            
                            @if($delivery->status !== 'pending')
                            <div class="relative pl-6 border-l-2 border-tni-200">
                                <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-tni-500 border-2 border-white"></div>
                                <p class="text-xs font-bold text-tni-400 uppercase mb-0.5">Mulai Diantar</p>
                                <p class="text-sm font-semibold text-gray-700">{{ $delivery->updated_at->format('d M Y, H:i') }}</p>
                            </div>
                            @endif

                            @if($delivery->status === 'delivered')
                            <div class="relative pl-6">
                                <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-green-500 border-2 border-white"></div>
                                <p class="text-xs font-bold text-green-500 uppercase mb-0.5">Selesai/Diterima</p>
                                <p class="text-sm font-semibold text-gray-700">{{ $delivery->delivered_at ? $delivery->delivered_at->format('d M Y, H:i') : '-' }}</p>
                            </div>
                            @endif
                        </div>
                    </section>
                </div>
            </div>

            <!-- Notes Section -->
            @if($delivery->notes)
            <div class="mt-8 p-6 bg-blue-50 border border-blue-100 rounded-2xl flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-blue-500 text-white flex items-center justify-center flex-shrink-0 shadow-md">
                    <i class="fas fa-note-sticky"></i>
                </div>
                <div>
                    <h4 class="font-bold text-blue-900 text-sm mb-1">Catatan Pengantaran:</h4>
                    <p class="text-blue-800 text-sm italic">{{ $delivery->notes }}</p>
                </div>
            </div>
            @endif

            <!-- Proof Image Section -->
            @if($delivery->status === 'delivered' && $delivery->proof_image)
            <div class="mt-12 pt-8 border-t border-gray-100">
                <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-camera-retro mr-3 text-tni-600"></i>
                    Bukti Penyerahan Obat
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="group relative">
                        <p class="text-sm font-bold text-gray-500 mb-3 uppercase tracking-widest">Foto Penerimaan</p>
                        <div class="rounded-3xl overflow-hidden border-4 border-white shadow-2xl transition transform hover:scale-[1.02] duration-300">
                            <img src="{{ Storage::url($delivery->proof_image) }}" alt="Bukti Foto" class="w-full h-auto object-cover max-h-[400px]">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <a href="{{ Storage::url($delivery->proof_image) }}" target="_blank" class="px-6 py-2 bg-white text-gray-900 rounded-full font-bold shadow-lg">
                                    <i class="fas fa-expand mr-2"></i> Perbesar
                                </a>
                            </div>
                        </div>
                    </div>

                    @if($delivery->assessment && $delivery->assessment->signature_image)
                    <div>
                        <p class="text-sm font-bold text-gray-500 mb-3 uppercase tracking-widest">Tanda Tangan Digital</p>
                        <div class="bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200 p-8 flex flex-col items-center justify-center min-h-[300px] shadow-inner">
                            <img src="{{ Storage::url($delivery->assessment->signature_image) }}" alt="Tanda Tangan" class="max-h-[200px] mix-blend-multiply opacity-80">
                            <p class="mt-4 text-xs font-medium text-gray-400">Verifikasi Tanda Tangan Elektronik Meditrack</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

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