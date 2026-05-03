@extends('layouts.app')

@section('title', 'Laporan Performa Kurir')
@section('page-title', 'Performa Kurir')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <a href="{{ route('couriers.index') }}" class="text-tni-600 hover:text-tni-800 flex items-center mb-2">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Kurir
        </a>
        <h2 class="text-2xl font-bold text-tni-800">Laporan Performa Kurir</h2>
        <p class="text-sm text-gray-600 mt-1">Ringkasan performa pengantaran obat oleh semua kurir</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($couriers as $courier)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-tni-500 to-tni-700 rounded-full flex items-center justify-center text-white font-bold text-xl shadow-md border-2 border-gold-400">
                        {{ substr($courier->name, 0, 1) }}
                    </div>
                    <div class="ml-4">
                        <div class="text-lg font-bold text-gray-900">{{ $courier->name }}</div>
                        <div class="text-sm text-gray-500">{{ $courier->phone ?? '-' }}</div>
                    </div>
                </div>
            </div>
            
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-500">Tingkat Keberhasilan</span>
                        <span class="font-bold text-tni-700">{{ $courier->success_rate }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-tni-500 h-2 rounded-full" style="width: {{ $courier->success_rate }}%"></div>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Total</p>
                        <p class="text-xl font-bold text-gray-800">{{ $courier->total_deliveries }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Selesai</p>
                        <p class="text-xl font-bold text-green-600">{{ $courier->completed_deliveries }}</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 pt-4 border-t border-gray-100">
                <a href="{{ route('couriers.performance', $courier) }}" class="w-full block text-center bg-blue-50 hover:bg-blue-100 text-blue-700 font-medium py-2 rounded-lg transition">
                    Lihat Detail Performa
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full bg-white p-8 rounded-xl shadow-sm border border-gray-100 text-center">
        <i class="fas fa-motorcycle text-4xl text-gray-300 mb-3"></i>
        <p class="text-gray-500">Belum ada data kurir yang tersedia.</p>
    </div>
    @endforelse
</div>
@endsection
