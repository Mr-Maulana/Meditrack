@extends('layouts.app')

@section('title', 'Pengantaran Selesai')
@section('page-title', 'Pengantaran Selesai')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Success Header -->
        <div class="bg-green-50 p-8 text-center">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-green-600 text-3xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Pengantaran Berhasil!</h2>
            <p class="text-gray-600">Obat telah diserahkan kepada pasien</p>
        </div>
        
        <!-- Delivery Summary -->
        <div class="p-8 space-y-6">
            <!-- Patient Info -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pasien</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase">Nama Pasien</label>
                            <p class="text-sm text-gray-900 font-bold">{{ $assessment->delivery->patient->name }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase">No. Telepon</label>
                            <p class="text-sm text-gray-900 font-medium">{{ $assessment->delivery->patient->phone }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medication List -->
            @if($assessment->delivery->prescription)
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Obat Terkirim</h3>
                <div class="space-y-2">
                    @php
                        $meds = $assessment->delivery->prescription->medications ?? [
                            ['name' => $assessment->delivery->prescription->medication_name]
                        ];
                    @endphp
                    @foreach($meds as $med)
                    <div class="flex items-center p-3 bg-green-50 rounded-lg border border-green-100">
                        <i class="fas fa-pills text-green-600 mr-3"></i>
                        <span class="text-sm font-bold text-green-800">{{ $med['name'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            
            <!-- Delivery Timeline -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Timeline Pengantaran</h3>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                            <i class="fas fa-play text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">Dimulai</div>
                            <div class="text-sm text-gray-500">{{ $assessment->start_time ? $assessment->start_time->format('H:i') : '-' }}</div>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-4">
                            <i class="fas fa-flag text-green-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">Tiba di Lokasi</div>
                            <div class="text-sm text-gray-500">{{ $assessment->arrival_time ? $assessment->arrival_time->format('H:i') : '-' }}</div>
                            @if($assessment->travel_time_minutes)
                            <div class="text-xs text-gray-400">Perjalanan: {{ $assessment->travel_time_minutes }} menit</div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center mr-4">
                            <i class="fas fa-check-circle text-purple-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">Selesai</div>
                            <div class="text-sm text-gray-500">{{ $assessment->handover_time ? $assessment->handover_time->format('H:i') : '-' }}</div>
                            @if($assessment->handover_time_minutes)
                            <div class="text-xs text-gray-400">Serah terima: {{ $assessment->handover_time_minutes }} menit</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Assessment Summary -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Ringkasan Assesmen</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <label class="text-sm font-medium text-gray-500">Kondisi Pasien</label>
                        <p class="text-sm text-gray-900 capitalize">{{ $assessment->patient_condition }}</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4">
                        <label class="text-sm font-medium text-gray-500">Pemahaman Obat</label>
                        <p class="text-sm text-gray-900">
                            {{ $assessment->medication_understood ? 'Memahami' : 'Tidak memahami' }}
                        </p>
                    </div>
                </div>
                
                @if($assessment->patient_feedback)
                <div class="mt-4 bg-yellow-50 rounded-lg p-4">
                    <label class="text-sm font-medium text-gray-500">Feedback Pasien</label>
                    <p class="text-sm text-gray-900 mt-1">{{ $assessment->patient_feedback }}</p>
                </div>
                @endif
            </div>
            
            <!-- Documentation -->
            @if($assessment->handover_photo)
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Dokumentasi</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <label class="text-sm font-medium text-gray-500 mb-2 block">Foto Serah Terima</label>
                    <a href="{{ Storage::url($assessment->handover_photo) }}" target="_blank" 
                       class="inline-block">
                        <img src="{{ Storage::url($assessment->handover_photo) }}" 
                             alt="Foto Serah Terima" 
                             class="rounded-lg w-48 h-48 object-cover hover:opacity-90 transition">
                    </a>
                </div>
            </div>
            @endif
            
            <!-- Next Steps -->
            <div class="bg-gradient-to-r from-tni-800 to-tni-900 rounded-[2rem] p-8 md:p-10 text-center relative overflow-hidden shadow-2xl mt-4">
                <div class="absolute -top-4 -right-4 p-8 opacity-10">
                    <i class="fas fa-truck-fast text-9xl text-white"></i>
                </div>
                <div class="relative z-10">
                    <h4 class="text-xl font-black text-white mb-3 uppercase tracking-widest">Tugas Selesai</h4>
                    <p class="text-tni-100 font-medium mb-8 text-sm max-w-md mx-auto leading-relaxed">
                        Pengantaran ini telah sukses dicatat dalam sistem farmasi. Anda dapat melanjutkan untuk mengambil tugas pengantaran berikutnya.
                    </p>
                    <div class="flex justify-center">
                        <a href="{{ route('delivery-process.index') }}" 
                           class="inline-flex items-center justify-center gap-3 px-10 py-4 bg-gradient-to-r from-gold-400 to-gold-600 text-tni-900 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-gold-500/20 hover:scale-[1.05] transition-all w-full md:w-auto">
                           <i class="fas fa-motorcycle text-lg"></i> Ambil Pengantaran Baru
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection