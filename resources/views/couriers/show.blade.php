@extends('layouts.app')

@section('title', 'Detail Kurir')
@section('page-title', 'Informasi Kurir')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 animate-fade-in">
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('couriers.index') }}" class="text-tni-600 hover:text-tni-800 flex items-center font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Kurir
        </a>
        <div class="flex space-x-3">
            <a href="{{ route('couriers.edit', $courier) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded-xl hover:bg-yellow-600 transition shadow-md">
                <i class="fas fa-edit mr-2"></i> Edit Profil
            </a>
            <a href="{{ route('couriers.performance', $courier) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition shadow-md">
                <i class="fas fa-chart-line mr-2"></i> Lihat Performa
            </a>
        </div>
    </div>

    <!-- Profile Header -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-tni-800 to-tni-600 h-32 relative">
            <div class="absolute -bottom-16 left-8">
                <div class="w-32 h-32 rounded-2xl bg-white p-1.5 shadow-xl border-4 border-white">
                    <div class="w-full h-full rounded-xl bg-gradient-to-br from-tni-500 to-tni-700 flex items-center justify-center text-white text-4xl font-bold border-2 border-gold-400">
                        {{ substr($courier->name, 0, 1) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="pt-20 pb-8 px-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between">
                <div>
                    <h2 class="text-3xl font-extrabold text-gray-900">{{ $courier->name }}</h2>
                    <p class="text-tni-600 font-bold flex items-center mt-1 uppercase tracking-wider text-sm">
                        <i class="fas fa-motorcycle mr-2 text-gold-500"></i> Personel Kurir Rumkit TK III IM 07.01 Lhokseumawe
                    </p>
                </div>
                <div class="mt-4 md:mt-0 flex items-center space-x-2">
                    <span class="px-4 py-1.5 bg-green-100 text-green-800 rounded-full text-xs font-bold uppercase tracking-widest border border-green-200">
                        Aktif
                    </span>
                    <span class="px-4 py-1.5 bg-gray-100 text-gray-800 rounded-full text-xs font-bold uppercase tracking-widest border border-gray-200">
                        ID: #{{ str_pad($courier->id, 4, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Contact Info -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center border-b pb-3 border-gray-50">
                    <i class="fas fa-address-card mr-3 text-tni-500"></i> Informasi Kontak
                </h3>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 mr-4 flex-shrink-0">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase">Email</p>
                            <p class="text-sm font-medium text-gray-800">{{ $courier->email }}</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 mr-4 flex-shrink-0">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase">WhatsApp / Telepon</p>
                            <p class="text-sm font-medium text-gray-800">{{ $courier->phone ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 mr-4 flex-shrink-0">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase">Bergabung Sejak</p>
                            <p class="text-sm font-medium text-gray-800">{{ $courier->created_at->format('d F Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-history mr-3 text-tni-500"></i> Aktivitas Terakhir
                    </h3>
                </div>
                <div class="p-0">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/80">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">Pasien</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">Tanggal</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($deliveries as $delivery)
                            <tr class="hover:bg-gray-50/50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-sm font-bold text-gray-800">{{ $delivery->patient->name ?? 'Pasien' }}</p>
                                    <p class="text-xs text-gray-500">{{ $delivery->patient->patient_code ?? 'N/A' }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                                    {{ $delivery->delivery_date->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $colors = [
                                            'delivered' => 'bg-green-100 text-green-800 border-green-200',
                                            'on_delivery' => 'bg-blue-100 text-blue-800 border-blue-200',
                                            'failed' => 'bg-red-100 text-red-800 border-red-200',
                                            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200'
                                        ];
                                        $labels = [
                                            'delivered' => 'Terkirim',
                                            'on_delivery' => 'Dalam Perjalanan',
                                            'failed' => 'Gagal',
                                            'pending' => 'Menunggu'
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 text-[10px] font-extrabold uppercase rounded-full border {{ $colors[$delivery->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $labels[$delivery->status] ?? $delivery->status }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-10 text-center text-gray-500">
                                    <i class="fas fa-inbox text-3xl text-gray-200 mb-3 block"></i>
                                    <p class="text-sm">Belum ada riwayat pengantaran untuk kurir ini.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($deliveries->count() > 0)
                <div class="px-6 py-4 bg-gray-50/30 border-t border-gray-50 text-center">
                    <a href="{{ route('deliveries.index', ['courier_id' => $courier->id]) }}" class="text-sm font-bold text-tni-600 hover:text-tni-800 transition">
                        Lihat Semua Riwayat <i class="fas fa-chevron-right ml-1 text-xs"></i>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
