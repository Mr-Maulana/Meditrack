@extends('layouts.app')

@section('title', 'Ringkasan Eksekutif')
@section('page-title', 'Laporan Ringkasan')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 animate-fade-in pb-12">
    <!-- Header with Actions -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 no-print">
        <div>
            <h2 class="text-3xl font-black text-tni-900 tracking-tight">Ringkasan Eksekutif Meditrack</h2>
            <p class="text-gray-500 font-medium">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>
        <div class="flex gap-3">
            <button onclick="window.print()" class="px-6 py-2.5 bg-tni-600 text-white rounded-xl hover:bg-tni-700 transition shadow-lg font-bold flex items-center">
                <i class="fas fa-print mr-2"></i> Cetak Laporan
            </button>
            <a href="{{ route('reports.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition font-bold">
                Kembali
            </a>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-3xl p-8 shadow-xl border border-gray-100 text-center group hover:bg-tni-800 transition-all duration-300">
            <div class="w-12 h-12 bg-tni-50 text-tni-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-white/20 group-hover:text-white transition-colors">
                <i class="fas fa-users text-xl"></i>
            </div>
            <div class="text-3xl font-black text-tni-900 mb-1 group-hover:text-white transition-colors">{{ $patientStats['total'] }}</div>
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest group-hover:text-tni-300 transition-colors">Total Pasien</div>
        </div>

        <div class="bg-white rounded-3xl p-8 shadow-xl border border-gray-100 text-center group hover:bg-green-600 transition-all duration-300">
            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-white/20 group-hover:text-white transition-colors">
                <i class="fas fa-truck-check text-xl"></i>
            </div>
            <div class="text-3xl font-black text-gray-900 mb-1 group-hover:text-white transition-colors">{{ $deliveryStats['delivered'] }}</div>
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest group-hover:text-green-200 transition-colors">Berhasil Diantar</div>
        </div>

        <div class="bg-white rounded-3xl p-8 shadow-xl border border-gray-100 text-center group hover:bg-gold-500 transition-all duration-300">
            <div class="w-12 h-12 bg-gold-50 text-gold-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-white/20 group-hover:text-white transition-colors">
                <i class="fas fa-percent text-xl"></i>
            </div>
            <div class="text-3xl font-black text-gray-900 mb-1 group-hover:text-white transition-colors">{{ $successRate }}%</div>
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest group-hover:text-gold-100 transition-colors">Tingkat Kesuksesan</div>
        </div>

        <div class="bg-white rounded-3xl p-8 shadow-xl border border-gray-100 text-center group hover:bg-blue-600 transition-all duration-300">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-white/20 group-hover:text-white transition-colors">
                <i class="fas fa-hand-holding-dollar text-xl"></i>
            </div>
            <div class="text-2xl font-black text-gray-900 mb-1 group-hover:text-white transition-colors">Rp {{ number_format($estimatedRevenue, 0, ',', '.') }}</div>
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest group-hover:text-blue-200 transition-colors">Estimasi Efisiensi</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Delivery Performance Trend -->
        <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/50">
                <h3 class="font-black text-tni-900 uppercase tracking-widest text-xs">Tren Pengantaran Harian</h3>
            </div>
            <div class="p-8">
                <div class="space-y-4">
                    @foreach($dailyTrend as $trend)
                    <div class="flex items-center gap-4">
                        <div class="text-[10px] font-black text-gray-400 w-24">{{ \Carbon\Carbon::parse($trend['date'])->format('d M') }}</div>
                        <div class="flex-1 h-3 bg-gray-100 rounded-full overflow-hidden flex">
                            <div class="bg-tni-600 h-full" style="width: {{ ($trend['delivered'] / max($trend['count'], 1)) * 100 }}%"></div>
                        </div>
                        <div class="text-xs font-bold text-tni-700 w-12 text-right">{{ $trend['count'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Top Couriers -->
        <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/50">
                <h3 class="font-black text-tni-900 uppercase tracking-widest text-xs">Performa Kurir Terbaik</h3>
            </div>
            <div class="p-8">
                <div class="space-y-6">
                    @foreach($topCouriers as $courier)
                    <div class="flex items-center justify-between group">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-tni-50 text-tni-600 flex items-center justify-center font-black group-hover:bg-tni-600 group-hover:text-white transition-colors">
                                {{ substr($courier['courier_name'], 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-800">{{ $courier['courier_name'] }}</p>
                                <p class="text-[10px] text-gray-400 uppercase font-black">{{ $courier['delivered'] }} Berhasil / {{ $courier['total'] }} Total</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-black text-tni-700">{{ $courier['success_rate'] }}%</p>
                            <p class="text-[10px] font-bold text-green-500">Success Rate</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Common Diagnoses -->
        <div class="lg:col-span-2 bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/50">
                <h3 class="font-black text-tni-900 uppercase tracking-widest text-xs">Distribusi Diagnosis Penyakit Terbanyak</h3>
            </div>
            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach($commonDiagnoses as $diagnosis)
                <div class="bg-gray-50 p-6 rounded-3xl border border-gray-100">
                    <div class="flex justify-between items-start mb-4">
                        <h4 class="font-black text-tni-800 text-sm max-w-[70%] leading-tight">{{ $diagnosis['diagnosis'] }}</h4>
                        <span class="px-3 py-1 bg-white text-tni-600 rounded-full text-xs font-black shadow-sm">{{ $diagnosis['count'] }} Pasien</span>
                    </div>
                    <div class="w-full bg-white rounded-full h-2 shadow-inner overflow-hidden">
                        <div class="bg-gold-500 h-full rounded-full" style="width: {{ $diagnosis['percentage'] }}%"></div>
                    </div>
                    <div class="mt-2 text-right text-[10px] font-black text-gold-600">{{ $diagnosis['percentage'] }}% dari Total Pasien</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
        .bg-white { border: 1px solid #eee !important; box-shadow: none !important; }
        .rounded-\[2\.5rem\] { border-radius: 1rem !important; }
    }
</style>
@endsection
