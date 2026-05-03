@extends('layouts.app')

@section('title', 'Detail Riwayat #' . $delivery->id)
@section('page-title', 'Detail Riwayat Pengantaran')

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
                        <a href="{{ route('my-deliveries') }}" class="text-gray-500 hover:text-tni-600 transition-colors">Riwayat Saya</a>
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
        
        <div class="flex gap-2">
            <a href="{{ route('my-deliveries') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition shadow-md font-medium">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <!-- Status Header Banner -->
        @php
            $statusConfig = [
                'pending' => ['color' => 'yellow', 'icon' => 'clock', 'text' => 'Menunggu'],
                'on_delivery' => ['color' => 'blue', 'icon' => 'truck-fast', 'text' => 'Dalam Perjalanan'],
                'delivered' => ['color' => 'green', 'icon' => 'circle-check', 'text' => 'Selesai Terkirim'],
                'failed' => ['color' => 'red', 'icon' => 'circle-xmark', 'text' => 'Gagal'],
            ];
            $current = $statusConfig[$delivery->status] ?? $statusConfig['pending'];
        @endphp

        <div class="bg-gradient-to-r from-tni-700 to-tni-900 p-8 text-white">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-2xl border border-white/30">
                        <i class="fas fa-{{ $current['icon'] }}"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">Status: {{ $current['text'] }}</h2>
                        <p class="text-tni-100 text-sm">ID: #MT-{{ str_pad($delivery->id, 5, '0', STR_PAD_LEFT) }}</p>
                    </div>
                </div>
                
                @if($delivery->priority === 'urgent')
                <div class="px-4 py-1 bg-red-500 rounded-full text-[10px] font-bold tracking-widest uppercase flex items-center shadow-lg">
                    <i class="fas fa-bolt mr-1"></i> Urgent
                </div>
                @endif
            </div>
        </div>

        <div class="p-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Left: Patient Info -->
                <div class="space-y-8">
                    <section>
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center">
                            <i class="fas fa-user-circle mr-2 text-tni-600"></i> Informasi Pasien
                        </h3>
                        <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                            <div class="space-y-4">
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase mb-1">Nama Penerima</p>
                                    <p class="text-gray-800 font-bold text-lg">{{ $delivery->patient->name ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase mb-1">Kontak Telepon</p>
                                    <p class="text-gray-700 font-medium">{{ $delivery->patient->phone ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase mb-1">Alamat Pengantaran</p>
                                    <p class="text-gray-700 text-sm leading-relaxed">{{ $delivery->delivery_address }}</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    @if($delivery->notes)
                    <section>
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center">
                            <i class="fas fa-comment-dots mr-2 text-blue-600"></i> Catatan Khusus
                        </h3>
                        <div class="bg-blue-50/50 rounded-2xl p-6 border border-blue-100 italic text-blue-800 text-sm">
                            "{{ $delivery->notes }}"
                        </div>
                    </section>
                    @endif
                    @if($delivery->prescription)
                    <section>
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center">
                            <i class="fas fa-prescription-bottle-medical mr-2 text-gold-600"></i> Daftar Obat Terdaftar
                        </h3>
                        <div class="space-y-4">
                            @php
                                $meds = $delivery->prescription->medications ?? [
                                    [
                                        'name' => $delivery->prescription->medication_name,
                                        'dosage' => $delivery->prescription->dosage,
                                        'frequency' => $delivery->prescription->frequency,
                                        'instructions' => $delivery->prescription->instructions,
                                    ]
                                ];
                            @endphp

                            @foreach($meds as $i => $med)
                            <div class="bg-white rounded-2xl p-5 border-2 border-gold-100 relative shadow-sm">
                                <h4 class="text-gold-700 font-bold text-sm mb-3 flex items-center">
                                    <span class="w-5 h-5 bg-gold-500 text-white text-[10px] rounded-full flex items-center justify-center mr-2">{{ $i + 1 }}</span>
                                    {{ $med['name'] ?? 'Obat' }}
                                </h4>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="p-2 bg-gray-50 rounded-lg text-center border border-gray-100">
                                        <p class="text-[9px] font-bold text-gray-400 uppercase">Dosis</p>
                                        <p class="text-[11px] font-bold text-gray-700">{{ $med['dosage'] ?? '-' }}</p>
                                    </div>
                                    <div class="p-2 bg-gray-50 rounded-lg text-center border border-gray-100">
                                        <p class="text-[9px] font-bold text-gray-400 uppercase">Frekuensi</p>
                                        <p class="text-[11px] font-bold text-gray-700">{{ $med['frequency'] ?? '-' }}</p>
                                    </div>
                                </div>
                                @if(!empty($med['instructions']))
                                <p class="mt-2 text-[10px] italic text-gray-500 px-2 py-1 bg-gray-50 rounded border border-gray-100">"{{ $med['instructions'] }}"</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </section>
                    @endif
                </div>

                <!-- Right: Timeline & Evidence -->
                <div class="space-y-8">
                    <section>
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center">
                            <i class="fas fa-clock-rotate-left mr-2 text-purple-600"></i> Waktu Pengantaran
                        </h3>
                        <div class="bg-white rounded-2xl p-6 border border-gray-100 space-y-4">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 font-medium">Tanggal Tugas:</span>
                                <span class="text-gray-800 font-bold">{{ $delivery->delivery_date->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 font-medium">Waktu Selesai:</span>
                                <span class="text-green-600 font-bold">{{ $delivery->delivered_at ? $delivery->delivered_at->format('d/m/Y H:i') : '-' }}</span>
                            </div>
                        </div>
                    </section>

                    @if($delivery->status == 'delivered')
                    <section>
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center">
                            <i class="fas fa-check-double mr-2 text-green-600"></i> Bukti Selesai
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            @if($delivery->proof_image)
                            <div class="space-y-2">
                                <p class="text-[10px] font-bold text-gray-400 uppercase text-center">Foto Bukti</p>
                                <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                                    <img src="{{ Storage::url($delivery->proof_image) }}" alt="Bukti Foto" class="w-full h-32 object-cover">
                                </div>
                            </div>
                            @endif
                            
                            @if($delivery->assessment && $delivery->assessment->signature_image)
                            <div class="space-y-2">
                                <p class="text-[10px] font-bold text-gray-400 uppercase text-center">Tanda Tangan</p>
                                <div class="rounded-xl overflow-hidden border border-gray-200 bg-gray-50 h-32 flex items-center justify-center p-4">
                                    <img src="{{ Storage::url($delivery->assessment->signature_image) }}" alt="Tanda Tangan" class="max-h-full mix-blend-multiply opacity-70">
                                </div>
                            </div>
                            @endif
                        </div>
                    </section>
                    @endif
                </div>
            </div>
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
