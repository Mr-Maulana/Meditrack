@extends('layouts.app')

@section('title', 'Manajemen Pengantaran')
@section('page-title', 'Logistik & Pengantaran')

@section('content')
<div class="space-y-8 animate-fade-in pb-12">
    <!-- Header & Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Daftar Pengantaran Obat</h2>
            <p class="text-gray-500 text-sm mt-1">Pantau status pengiriman obat ke alamat pasien secara real-time.</p>
        </div>
        
        <div class="flex flex-wrap gap-3 items-center">
            <form action="{{ route('deliveries.index') }}" method="GET" class="relative group">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pasien..." 
                    class="pl-12 pr-6 py-3 bg-white border border-gray-100 rounded-2xl text-sm focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all w-64 shadow-sm font-medium">
                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-tni-600 transition-colors"></i>
            </form>
            @if(auth()->user()->isAdmin() || auth()->user()->isApoteker())
            <a href="{{ route('deliveries.create') }}" class="inline-flex items-center px-6 py-3 bg-tni-800 text-white rounded-2xl hover:bg-black transition shadow-lg font-bold group">
                <i class="fas fa-truck-medical mr-2 group-hover:scale-110 transition-transform"></i> Tambah Pengantaran
            </a>
            @endif
        </div>
    </div>

    <!-- Filters & Stats Container -->
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Stats Sidebar -->
        <div class="lg:w-1/4 space-y-4">
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">Ringkasan Status</p>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-2xl border border-yellow-100">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-yellow-100 text-yellow-700 flex items-center justify-center text-xs">
                                <i class="fas fa-clock"></i>
                            </div>
                            <span class="text-xs font-bold text-yellow-800 uppercase">Menunggu</span>
                        </div>
                        <span class="text-sm font-black text-yellow-900">{{ $pendingCount }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-2xl border border-blue-100">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-xs">
                                <i class="fas fa-truck-fast"></i>
                            </div>
                            <span class="text-xs font-bold text-blue-800 uppercase">Proses</span>
                        </div>
                        <span class="text-sm font-black text-blue-900">{{ $processCount }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-2xl border border-green-100">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-green-100 text-green-700 flex items-center justify-center text-xs">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <span class="text-xs font-bold text-green-800 uppercase">Selesai</span>
                        </div>
                        <span class="text-sm font-black text-green-900">{{ $deliveredCount }}</span>
                    </div>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="bg-tni-800 p-6 rounded-[2rem] shadow-xl text-white">
                <p class="text-[10px] font-bold text-tni-300 uppercase tracking-widest mb-4">Filter Pencarian</p>
                <form action="{{ route('deliveries.index') }}" method="GET" class="space-y-4">
                    <div>
                        <label class="text-[10px] font-bold text-tni-400 uppercase ml-1">Status</label>
                        <select name="status" class="w-full mt-1 bg-tni-700 border-none rounded-xl text-xs font-bold focus:ring-gold-500">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="on_delivery" {{ request('status') == 'on_delivery' ? 'selected' : '' }}>Proses Kirim</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Terkirim</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full py-3 bg-gold-500 hover:bg-gold-600 text-tni-900 font-black rounded-xl transition-all text-xs uppercase tracking-widest shadow-lg shadow-gold-500/20">
                        Terapkan Filter
                    </button>
                </form>
            </div>
        </div>

        <!-- Deliveries Table Area -->
        <div class="lg:w-3/4">
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="px-8 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pasien / Kode</th>
                                <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Jadwal & Prioritas</th>
                                <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status</th>
                                <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kurir</th>
                                <th class="px-8 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($deliveries as $delivery)
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-8 py-6">
                                    <div>
                                        <p class="text-sm font-bold text-gray-800 group-hover:text-tni-700 transition-colors">{{ $delivery->patient->name ?? 'N/A' }}</p>
                                        <p class="text-[10px] text-tni-600 font-bold uppercase tracking-wider">{{ $delivery->patient->patient_code ?? 'PASIEN-ID' }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="text-sm">
                                        <p class="font-bold text-gray-700">{{ $delivery->delivery_date ? $delivery->delivery_date->format('d M Y') : '-' }}</p>
                                        @if($delivery->priority === 'urgent')
                                            <span class="text-[10px] text-red-600 font-black uppercase tracking-widest flex items-center">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-600 mr-1 animate-ping"></span> Urgent
                                            </span>
                                        @else
                                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Normal</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    @php
                                        $statusConfig = [
                                            'pending' => ['bg' => 'bg-yellow-50', 'text' => 'text-yellow-700', 'border' => 'border-yellow-100', 'label' => 'Menunggu'],
                                            'on_delivery' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-100', 'label' => 'Proses'],
                                            'delivered' => ['bg' => 'bg-green-50', 'text' => 'text-green-700', 'border' => 'border-green-100', 'label' => 'Terkirim'],
                                            'failed' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-100', 'label' => 'Gagal'],
                                        ];
                                        $config = $statusConfig[$delivery->status] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-100', 'label' => 'Unknown'];
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $config['bg'] }} {{ $config['text'] }} {{ $config['border'] }}">
                                        {{ $config['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-[10px] font-bold text-gray-500">
                                            {{ $delivery->courier ? substr($delivery->courier->name, 0, 1) : '?' }}
                                        </div>
                                        <p class="text-xs font-bold text-gray-600">{{ $delivery->courier->name ?? 'Belum Ditugaskan' }}</p>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('deliveries.print', $delivery) }}" target="_blank" class="p-2.5 bg-gray-50 text-gray-400 hover:bg-tni-100 hover:text-tni-700 rounded-xl transition-all" title="Cetak Resi">
                                            <i class="fas fa-print text-sm"></i>
                                        </a>
                                        <a href="{{ route('deliveries.show', $delivery) }}" class="p-2.5 bg-gray-50 text-gray-400 hover:bg-tni-100 hover:text-tni-700 rounded-xl transition-all" title="Detail Pengantaran">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                        @if(auth()->user()->isAdmin() || auth()->user()->isApoteker())
                                        <a href="{{ route('deliveries.edit', $delivery) }}" class="p-2.5 bg-gray-50 text-gray-400 hover:bg-gold-100 hover:text-gold-700 rounded-xl transition-all" title="Edit Pengantaran">
                                            <i class="fas fa-edit text-sm"></i>
                                        </a>
                                        @endif
                                        <a href="{{ route('deliveries.track', $delivery) }}" class="p-2.5 bg-gray-50 text-gray-400 hover:bg-blue-100 hover:text-blue-700 rounded-xl transition-all" title="Lacak Posisi">
                                            <i class="fas fa-map-marker-alt text-sm"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-truck text-3xl text-gray-300"></i>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-800">Belum Ada Pengantaran</h3>
                                        <p class="text-gray-500 text-sm max-w-xs mt-1">Daftar pengiriman obat akan muncul di sini setelah dibuat.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($deliveries->hasPages())
                <div class="px-8 py-6 border-t border-gray-50 bg-gray-50/30">
                    {{ $deliveries->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection