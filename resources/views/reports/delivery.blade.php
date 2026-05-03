@extends('layouts.app')

@section('title', 'Laporan Pengantaran')
@section('page-title', 'Detail Pengantaran')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 animate-fade-in pb-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 no-print">
        <div>
            <h2 class="text-3xl font-black text-tni-900 tracking-tight">Laporan Detail Pengantaran</h2>
            <p class="text-gray-500 font-medium">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>
        <div class="flex gap-3">
            <button onclick="window.print()" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition shadow-lg font-bold flex items-center">
                <i class="fas fa-print mr-2"></i> Cetak
            </button>
            <a href="{{ route('reports.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition font-bold">
                Kembali
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Tugas</p>
            <p class="text-2xl font-black text-tni-900">{{ $deliveryStats['total'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            <p class="text-[10px] font-black text-green-400 uppercase tracking-widest mb-1">Terkirim</p>
            <p class="text-2xl font-black text-green-600">{{ $deliveryStats['delivered'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            <p class="text-[10px] font-black text-yellow-400 uppercase tracking-widest mb-1">Proses/Pending</p>
            <p class="text-2xl font-black text-yellow-600">{{ $deliveryStats['pending'] + $deliveryStats['on_delivery'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            <p class="text-[10px] font-black text-red-400 uppercase tracking-widest mb-1">Gagal</p>
            <p class="text-2xl font-black text-red-600">{{ $deliveryStats['failed'] }}</p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">ID & Tgl</th>
                        <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Pasien / Penerima</th>
                        <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Kurir Petugas</th>
                        <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Status</th>
                        <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Prioritas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($deliveries as $delivery)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-8 py-6 whitespace-nowrap">
                            <p class="text-sm font-black text-tni-800">#MT-{{ str_pad($delivery->id, 4, '0', STR_PAD_LEFT) }}</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $delivery->delivery_date->format('d/m/Y') }}</p>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-sm font-bold text-gray-800">{{ $delivery->patient->name }}</p>
                            <p class="text-[10px] text-gray-400 italic truncate max-w-[200px]">{{ $delivery->delivery_address }}</p>
                        </td>
                        <td class="px-8 py-6 whitespace-nowrap">
                            @if($delivery->courier)
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-[10px] font-black">{{ substr($delivery->courier->name, 0, 1) }}</div>
                                    <span class="text-xs font-bold text-gray-700">{{ $delivery->courier->name }}</span>
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">Belum ditugaskan</span>
                            @endif
                        </td>
                        <td class="px-8 py-6 whitespace-nowrap">
                            @php
                                $colors = [
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'on_delivery' => 'bg-blue-100 text-blue-700',
                                    'delivered' => 'bg-green-100 text-green-700',
                                    'failed' => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider {{ $colors[$delivery->status] }}">
                                {{ str_replace('_', ' ', $delivery->status) }}
                            </span>
                        </td>
                        <td class="px-8 py-6 whitespace-nowrap">
                            @if($delivery->priority === 'urgent')
                                <span class="text-red-500 font-black text-[10px] uppercase flex items-center">
                                    <i class="fas fa-bolt mr-1 animate-pulse"></i> Urgent
                                </span>
                            @else
                                <span class="text-gray-400 font-black text-[10px] uppercase">Normal</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
