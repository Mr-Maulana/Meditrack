@extends('layouts.app')

@section('title', 'Riwayat Pengantaran Saya')
@section('page-title', 'Riwayat Pengantaran')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 animate-fade-in pb-12">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-tni-800 to-tni-600 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute top-0 right-0 p-8 opacity-10">
            <i class="fas fa-history text-8xl"></i>
        </div>
        <div class="relative z-10">
            <h2 class="text-3xl font-bold mb-2">Riwayat Tugas</h2>
            <p class="text-tni-100 opacity-90">Pantau dan kelola riwayat seluruh pengantaran yang telah Anda selesaikan.</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Status</label>
                <select name="status" class="w-full bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-tni-500 focus:border-tni-500 transition-all">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                    <option value="on_delivery" {{ request('status') == 'on_delivery' ? 'selected' : '' }}>Dalam Pengantaran</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Terkirim</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tanggal</label>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-tni-500 focus:border-tni-500 transition-all">
            </div>
            <div class="md:col-span-2 flex gap-2">
                <button type="submit" class="flex-1 bg-tni-600 text-white px-6 py-2.5 rounded-xl hover:bg-tni-700 transition shadow-md font-bold text-sm">
                    <i class="fas fa-filter mr-2"></i> Filter Data
                </button>
                <a href="{{ route('my-deliveries') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition font-bold text-sm">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Results Table -->
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-8 py-5 text-left text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">ID</th>
                        <th class="px-8 py-5 text-left text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Pasien / Penerima</th>
                        <th class="px-8 py-5 text-left text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Tanggal Tugas</th>
                        <th class="px-8 py-5 text-left text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Status</th>
                        <th class="px-8 py-5 text-center text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($deliveries as $delivery)
                    <tr class="hover:bg-tni-50/30 transition-colors">
                        <td class="px-8 py-6 whitespace-nowrap">
                            <span class="text-xs font-bold text-tni-700 bg-tni-50 px-2 py-1 rounded-md">#MT-{{ str_pad($delivery->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-tni-600 font-bold mr-3 border border-gray-200">
                                    {{ substr($delivery->patient->name ?? '?', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">{{ $delivery->patient->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-400">{{ Str::limit($delivery->delivery_address, 30) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6 whitespace-nowrap">
                            <p class="text-sm font-semibold text-gray-600">{{ $delivery->delivery_date ? $delivery->delivery_date->format('d M Y') : '-' }}</p>
                        </td>
                        <td class="px-8 py-6 whitespace-nowrap">
                            @if($delivery->status == 'delivered')
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-lg text-[10px] font-bold uppercase tracking-wider flex items-center w-fit">
                                    <i class="fas fa-check-circle mr-1.5"></i> Terkirim
                                </span>
                            @elseif($delivery->status == 'on_delivery')
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-[10px] font-bold uppercase tracking-wider flex items-center w-fit">
                                    <i class="fas fa-truck mr-1.5 animate-pulse"></i> Di Jalan
                                </span>
                            @else
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg text-[10px] font-bold uppercase tracking-wider flex items-center w-fit">
                                    <i class="fas fa-clock mr-1.5"></i> Pending
                                </span>
                            @endif
                        </td>
                        <td class="px-8 py-6 whitespace-nowrap text-center">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('my-deliveries.detail', $delivery->id) }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-tni-50 text-tni-600 hover:bg-tni-600 hover:text-white transition shadow-sm" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($delivery->status === 'delivered')
                                <a href="{{ route('my-deliveries.print', $delivery->id) }}" target="_blank" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-green-50 text-green-600 hover:bg-green-600 hover:text-white transition shadow-sm" title="Cetak Bukti Pengantaran">
                                    <i class="fas fa-print"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-folder-open text-4xl text-gray-200 mb-4"></i>
                                <p class="text-gray-400 font-medium">Belum ada riwayat pengantaran yang ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($deliveries->hasPages())
        <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100">
            {{ $deliveries->links() }}
        </div>
        @endif
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
