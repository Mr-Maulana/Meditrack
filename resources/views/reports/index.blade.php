@extends('layouts.app')

@section('title', 'Laporan & Analitik')
@section('page-title', 'Pusat Laporan')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 animate-fade-in pb-12">
    <!-- Header Summary Card -->
    <div class="bg-gradient-to-r from-tni-800 to-tni-600 rounded-[2.5rem] p-10 text-white shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 right-0 p-12 opacity-10 rotate-12">
            <i class="fas fa-chart-pie text-[12rem]"></i>
        </div>
        <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div>
                <h2 class="text-4xl font-black mb-4 tracking-tight">Pusat Data & Analitik</h2>
                <p class="text-tni-100 text-lg opacity-90 leading-relaxed">
                    Hasilkan laporan komprehensif mengenai pasien, pengantaran, dan performa kurir untuk meningkatkan kualitas layanan Meditrack Rumkit TK III IM 07.01 Lhokseumawe.
                </p>
            </div>
            <div class="flex flex-wrap gap-3 justify-end">
                <a href="{{ route('reports.quick', ['type' => 'summary', 'range' => 'today']) }}" class="px-6 py-3 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl hover:bg-white/20 transition text-sm font-bold">
                    <i class="fas fa-bolt mr-2 text-gold-400"></i> Hari Ini
                </a>
                <a href="{{ route('reports.quick', ['type' => 'summary', 'range' => 'month']) }}" class="px-6 py-3 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl hover:bg-white/20 transition text-sm font-bold">
                    <i class="fas fa-calendar-check mr-2 text-gold-400"></i> Bulan Ini
                </a>
            </div>
        </div>
    </div>

    <!-- Main Report Generator -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Configuration Side -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 p-8 sticky top-8">
                <h3 class="text-lg font-black text-tni-900 mb-6 flex items-center">
                    <i class="fas fa-sliders mr-3 text-tni-600"></i> Konfigurasi Laporan
                </h3>
                
                <form action="{{ route('reports.generate') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Jenis Laporan</label>
                        <select name="report_type" required class="w-full bg-gray-50 border-gray-200 rounded-2xl py-4 px-5 text-sm focus:ring-tni-500 focus:border-tni-500 transition-all font-bold text-tni-800">
                            <option value="summary">Ringkasan Eksekutif</option>
                            <option value="deliveries">Detail Pengantaran</option>
                            <option value="patients">Data Pasien & Penyakit</option>
                            <option value="financial">Estimasi Operasional</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Rentang Tanggal</label>
                            <div class="space-y-3">
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-tni-400">
                                        <i class="fas fa-calendar-alt text-xs"></i>
                                    </span>
                                    <input type="date" name="start_date" value="{{ $startDate }}" required class="w-full pl-11 pr-4 py-3 bg-gray-50 border-gray-200 rounded-xl text-xs focus:ring-tni-500 focus:border-tni-500 transition-all font-medium">
                                </div>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-tni-400">
                                        <i class="fas fa-calendar-check text-xs"></i>
                                    </span>
                                    <input type="date" name="end_date" value="{{ $endDate }}" required class="w-full pl-11 pr-4 py-3 bg-gray-50 border-gray-200 rounded-xl text-xs focus:ring-tni-500 focus:border-tni-500 transition-all font-medium">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-4 bg-tni-600 text-white rounded-2xl hover:bg-tni-700 transition shadow-lg font-black tracking-wider flex items-center justify-center group">
                            <i class="fas fa-wand-magic-sparkles mr-3 group-hover:rotate-12 transition-transform"></i>
                            GENERATE LAPORAN
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Visual Previews / Fast Links -->
        <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Report Cards -->
            <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm hover:shadow-xl transition-all group border-l-4 border-l-blue-500">
                <div class="w-14 h-14 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:bg-blue-500 group-hover:text-white transition-colors shadow-inner">
                    <i class="fas fa-truck-ramp-box"></i>
                </div>
                <h4 class="text-xl font-black text-gray-800 mb-2">Laporan Pengantaran</h4>
                <p class="text-sm text-gray-500 leading-relaxed mb-6">Analisis efisiensi waktu, status terkirim, dan performa kurir lapangan.</p>
                <a href="{{ route('reports.deliveries') }}" class="inline-flex items-center text-blue-600 font-bold text-sm hover:underline">
                    Lihat Laporan <i class="fas fa-arrow-right ml-2 text-xs"></i>
                </a>
            </div>

            <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm hover:shadow-xl transition-all group border-l-4 border-l-green-500">
                <div class="w-14 h-14 bg-green-50 text-green-500 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:bg-green-500 group-hover:text-white transition-colors shadow-inner">
                    <i class="fas fa-hospital-user"></i>
                </div>
                <h4 class="text-xl font-black text-gray-800 mb-2">Laporan Pasien</h4>
                <p class="text-sm text-gray-500 leading-relaxed mb-6">Demografi pasien, tren penyakit, dan persebaran wilayah pengantaran.</p>
                <a href="{{ route('reports.users') }}" class="inline-flex items-center text-green-600 font-bold text-sm hover:underline">
                    Lihat Laporan <i class="fas fa-arrow-right ml-2 text-xs"></i>
                </a>
            </div>

            <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm hover:shadow-xl transition-all group border-l-4 border-l-gold-500">
                <div class="w-14 h-14 bg-gold-50 text-gold-500 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:bg-gold-500 group-hover:text-white transition-colors shadow-inner">
                    <i class="fas fa-prescription-bottle-medical"></i>
                </div>
                <h4 class="text-xl font-black text-gray-800 mb-2">Laporan Peresepan</h4>
                <p class="text-sm text-gray-500 leading-relaxed mb-6">Volume obat keluar, jenis obat paling sering dipesan, dan riwayat apotek.</p>
                <a href="{{ route('reports.prescriptions') }}" class="inline-flex items-center text-gold-600 font-bold text-sm hover:underline">
                    Lihat Laporan <i class="fas fa-arrow-right ml-2 text-xs"></i>
                </a>
            </div>

            <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm hover:shadow-xl transition-all group border-l-4 border-l-tni-800">
                <div class="w-14 h-14 bg-tni-50 text-tni-800 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:bg-tni-800 group-hover:text-white transition-colors shadow-inner">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <h4 class="text-xl font-black text-gray-800 mb-2">Estimasi Operasional</h4>
                <p class="text-sm text-gray-500 leading-relaxed mb-6">Perhitungan biaya pengantaran dan efisiensi logistik rumah sakit.</p>
                <a href="{{ route('reports.generate', ['report_type' => 'financial', 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="inline-flex items-center text-tni-800 font-bold text-sm hover:underline">
                    Lihat Laporan <i class="fas fa-arrow-right ml-2 text-xs"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes fade-in { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fade-in 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
@endsection