@extends('layouts.app')

@section('title', 'Performa Kurir')
@section('page-title', 'Detail Performa')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <a href="{{ route('couriers.performance-index') }}" class="text-tni-600 hover:text-tni-800 flex items-center mb-2">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Laporan Performa
        </a>
        <h2 class="text-2xl font-bold text-tni-800">Detail Performa: {{ $courier->name }}</h2>
    </div>
</div>

<!-- Stats Dashboard -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
        <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xl mr-4">
            <i class="fas fa-box"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Total Pengantaran</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
        <div class="w-12 h-12 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xl mr-4">
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Selesai</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['completed'] }}</p>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
        <div class="w-12 h-12 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center text-xl mr-4">
            <i class="fas fa-motorcycle"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Sedang Jalan</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['on_delivery'] }}</p>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
        <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xl mr-4">
            <i class="fas fa-times-circle"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Gagal</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['failed'] }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Asesmen Kualitas -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="font-bold text-gray-800"><i class="fas fa-star text-gold-500 mr-2"></i> Rata-rata Penilaian Kualitas</h3>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">Kondisi Fisik Obat</span>
                        <span class="text-sm font-bold text-tni-600">{{ number_format($stats['avg_condition_score'], 1) }} / 5.0</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-tni-500 h-2.5 rounded-full" style="width: {{ ($stats['avg_condition_score'] / 5) * 100 }}%"></div>
                    </div>
                </div>
                
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">Edukasi Pasien (Pemahaman)</span>
                        <span class="text-sm font-bold text-blue-600">{{ number_format($stats['avg_patient_understanding'], 1) }} / 5.0</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-blue-500 h-2.5 rounded-full" style="width: {{ ($stats['avg_patient_understanding'] / 5) * 100 }}%"></div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 pt-4 border-t border-gray-100 text-sm text-gray-500">
                <i class="fas fa-info-circle mr-1"></i> Data dihitung berdasarkan {{ $assessments->count() }} asesmen serah terima.
            </div>
        </div>
    </div>

    <!-- Riwayat Pengantaran Terbaru -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="font-bold text-gray-800"><i class="fas fa-history text-gray-500 mr-2"></i> Riwayat Pengantaran</h3>
        </div>
        <div class="p-0">
            <ul class="divide-y divide-gray-100">
                @forelse($deliveries->take(5) as $delivery)
                <li class="p-4 hover:bg-gray-50 transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $delivery->patient->name ?? 'Pasien' }}</p>
                            <p class="text-xs text-gray-500">{{ $delivery->delivery_date->format('d M Y') }}</p>
                        </div>
                        <div>
                            @if($delivery->status == 'delivered')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Selesai</span>
                            @elseif($delivery->status == 'on_delivery')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Proses</span>
                            @elseif($delivery->status == 'failed')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Gagal</span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            @endif
                        </div>
                    </div>
                </li>
                @empty
                <li class="p-6 text-center text-gray-500 text-sm">Belum ada riwayat pengantaran</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
