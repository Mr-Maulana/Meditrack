@extends('layouts.app')

@section('title', 'Laporan Operasional')
@section('page-title', 'Analisis Biaya')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 animate-fade-in pb-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 no-print">
        <div>
            <h2 class="text-3xl font-black text-tni-900 tracking-tight">Estimasi Operasional & Efisiensi</h2>
            <p class="text-gray-500 font-medium">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>
        <div class="flex gap-3">
            <button onclick="window.print()" class="px-6 py-2.5 bg-gold-600 text-white rounded-xl hover:bg-gold-700 transition shadow-lg font-bold flex items-center">
                <i class="fas fa-print mr-2"></i> Cetak
            </button>
            <a href="{{ route('reports.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition font-bold">
                Kembali
            </a>
        </div>
    </div>

    <!-- Financial Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-xl relative overflow-hidden group hover:bg-tni-900 transition-all duration-300">
            <div class="relative z-10">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 group-hover:text-tni-300">Total Nilai Efisiensi</p>
                <p class="text-3xl font-black text-tni-900 group-hover:text-white transition-colors">Rp {{ number_format($estimatedRevenue, 0, ',', '.') }}</p>
                <div class="mt-4 flex items-center text-xs text-green-500 font-bold group-hover:text-green-300">
                    <i class="fas fa-arrow-up mr-1"></i> Berdasarkan {{ $deliveryCount }} pengantaran
                </div>
            </div>
            <div class="absolute -right-4 -bottom-4 text-gray-50 text-7xl opacity-10 group-hover:text-white transition-colors">
                <i class="fas fa-money-bill-trend-up"></i>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-xl">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Satuan Biaya / Trip</p>
            <p class="text-3xl font-black text-gray-800">Rp {{ number_format($revenuePerDelivery, 0, ',', '.') }}</p>
            <div class="mt-4 text-xs text-gray-500">
                Standar operasional internal RS TNI AD
            </div>
        </div>

        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-xl">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Urgent vs Normal</p>
            <div class="space-y-3 mt-1">
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-500">Urgent</span>
                    <span class="text-sm font-black text-red-600">Rp {{ number_format($revenueByPriority['urgent'], 0, ',', '.') }}</span>
                </div>
                <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-red-500 h-full" style="width: {{ $estimatedRevenue > 0 ? ($revenueByPriority['urgent'] / $estimatedRevenue) * 100 : 0 }}%"></div>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-500">Normal</span>
                    <span class="text-sm font-black text-tni-700">Rp {{ number_format($revenueByPriority['normal'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Trend -->
    <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
        <div class="px-10 py-6 border-b border-gray-50 bg-gray-50/50">
            <h3 class="font-black text-tni-900 uppercase tracking-widest text-xs">Rincian Per Bulan</h3>
        </div>
        <div class="p-10">
            <div class="space-y-6">
                @foreach($monthlyRevenue as $item)
                <div class="flex items-center gap-6">
                    <div class="w-24 text-sm font-black text-gray-400">{{ \Carbon\Carbon::parse($item->month)->format('M Y') }}</div>
                    <div class="flex-1 h-4 bg-gray-50 rounded-full overflow-hidden shadow-inner">
                        <div class="bg-gradient-to-r from-tni-600 to-tni-800 h-full rounded-full" style="width: {{ $estimatedRevenue > 0 ? ($item->revenue / $estimatedRevenue) * 100 : 0 }}%"></div>
                    </div>
                    <div class="text-sm font-black text-tni-900 w-32 text-right">Rp {{ number_format($item->revenue, 0, ',', '.') }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
