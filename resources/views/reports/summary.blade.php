@extends('layouts.app')

@section('title', 'Ringkasan Eksekutif')
@section('page-title', 'Ringkasan Eksekutif')
@section('content')
<div class="max-w-6xl mx-auto space-y-8 animate-fade-in pb-12 report-document">
    <!-- Header with Actions (Visible on Screen) -->
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

    @include('reports.partials.letterhead', [
        'documentLabel' => 'Dokumen Resmi',
        'documentCode' => 'MT/REP/' . date('Ymd'),
        'reportTitle' => 'Laporan Ringkasan Eksekutif',
    ])

    <!-- Stats Overview -->
    <div class="report-stats grid grid-cols-1 md:grid-cols-4 gap-6 print:grid-cols-4 print:gap-4">
        <div class="bg-white rounded-3xl p-8 shadow-xl border border-gray-100 text-center group hover:bg-tni-800 transition-all duration-300 print:shadow-none print:border-gray-300 print:p-4">
            <div class="w-12 h-12 bg-tni-50 text-tni-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-white/20 group-hover:text-white transition-colors print:hidden">
                <i class="fas fa-users text-xl"></i>
            </div>
            <div class="text-3xl font-black text-tni-900 mb-1 group-hover:text-white transition-colors print:text-xl">{{ $patientStats['total'] }}</div>
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest group-hover:text-tni-300 transition-colors">Total Pasien</div>
        </div>

        <div class="bg-white rounded-3xl p-8 shadow-xl border border-gray-100 text-center group hover:bg-green-600 transition-all duration-300 print:shadow-none print:border-gray-300 print:p-4">
            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-white/20 group-hover:text-white transition-colors print:hidden">
                <i class="fas fa-truck-check text-xl"></i>
            </div>
            <div class="text-3xl font-black text-gray-900 mb-1 group-hover:text-white transition-colors print:text-xl">{{ $deliveryStats['delivered'] }}</div>
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest group-hover:text-green-200 transition-colors">Berhasil Diantar</div>
        </div>

        <div class="bg-white rounded-3xl p-8 shadow-xl border border-gray-100 text-center group hover:bg-gold-500 transition-all duration-300 print:shadow-none print:border-gray-300 print:p-4">
            <div class="w-12 h-12 bg-gold-50 text-gold-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-white/20 group-hover:text-white transition-colors print:hidden">
                <i class="fas fa-percent text-xl"></i>
            </div>
            <div class="text-3xl font-black text-gray-900 mb-1 group-hover:text-white transition-colors print:text-xl">{{ $successRate }}%</div>
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest group-hover:text-gold-100 transition-colors">Sukses Rate</div>
        </div>

        <div class="bg-white rounded-3xl p-8 shadow-xl border border-gray-100 text-center group hover:bg-blue-600 transition-all duration-300 print:shadow-none print:border-gray-300 print:p-4">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-white/20 group-hover:text-white transition-colors print:hidden">
                <i class="fas fa-hand-holding-dollar text-xl"></i>
            </div>
            <div class="text-2xl font-black text-gray-900 mb-1 group-hover:text-white transition-colors print:text-xl">Rp {{ number_format($estimatedRevenue, 0, ',', '.') }}</div>
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest group-hover:text-blue-200 transition-colors">Efisiensi</div>
        </div>
    </div>

    <!-- Formal Table for Print -->
    <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden print:border-gray-300 print:rounded-xl">
        <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/50 print:bg-gray-100">
            <h3 class="font-black text-tni-900 uppercase tracking-widest text-xs">Rincian Performa Laporan</h3>
        </div>
        <div class="p-0 overflow-x-auto">
            <table class="report-table w-full text-left">
                <thead>
                    <tr class="bg-gray-50 print:bg-gray-200">
                        <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Kategori Data</th>
                        <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Volume</th>
                        <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Persentase</th>
                        <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr>
                        <td class="px-8 py-5 text-sm font-bold text-tni-900">Total Pasien Terdaftar</td>
                        <td class="px-8 py-5 text-sm font-black text-center">{{ $patientStats['total'] }}</td>
                        <td class="px-8 py-5 text-sm font-bold text-center">100%</td>
                        <td class="px-8 py-5 text-xs text-gray-500 text-right">Data Pasien Aktif</td>
                    </tr>
                    <tr>
                        <td class="px-8 py-5 text-sm font-bold text-tni-900">Pengantaran Selesai</td>
                        <td class="px-8 py-5 text-sm font-black text-center">{{ $deliveryStats['delivered'] }}</td>
                        <td class="px-8 py-5 text-sm font-bold text-center text-green-600">{{ round(($deliveryStats['delivered'] / max($deliveryStats['total'], 1)) * 100, 1) }}%</td>
                        <td class="px-8 py-5 text-xs text-gray-500 text-right">Konfirmasi Penerimaan</td>
                    </tr>
                    <tr>
                        <td class="px-8 py-5 text-sm font-bold text-tni-900">Pasien Urgent</td>
                        <td class="px-8 py-5 text-sm font-black text-center">{{ $patientStats['total'] }}</td>
                        <td class="px-8 py-5 text-sm font-bold text-center text-red-600">--%</td>
                        <td class="px-8 py-5 text-xs text-gray-500 text-right">Prioritas Tinggi</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Print Footer / Signatures -->
    <div class="hidden print:grid grid-cols-2 gap-20 mt-20">
        <div class="text-center">
            <p class="text-sm font-bold mb-20 text-gray-800">Mengetahui,<br>Kepala Instalasi Farmasi</p>
            <div class="border-b border-black w-48 mx-auto mb-1"></div>
            <p class="text-xs font-black uppercase">      /    </p>
        </div>
        <div class="text-center">
            <p class="text-sm font-bold mb-20 text-gray-800">Lhokseumawe, {{ now()->format('d F Y') }}<br>Petugas Administrasi</p>
            <div class="border-b border-black w-48 mx-auto mb-1"></div>
            <p class="text-xs font-black uppercase">{{ auth()->user()->name }}</p>
        </div>
    </div>
    <div class="text-center">
        <p class="text-sm font-bold mb-20 text-gray-800">Mengetahui,<br>Kepala Rumah Sakit TK III IM 07.01 Lhokseumawe</p>
        <div class="border-b border-black w-48 mx-auto mb-1"></div>
        <p class="text-xs font-black uppercase">dr. sudirman suti, S.P, FISR., M.H.<br>Letnan Kolonel Ckm NRP 11050020241075</p>
    </div>
</div>

<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; margin: 0; padding: 20px; }
        .max-w-6xl { max-width: 100% !important; margin: 0 !important; }
        .bg-white { border: 1px solid #ddd !important; box-shadow: none !important; }
        .rounded-3xl, .rounded-\[2\.5rem\], .rounded-2xl { border-radius: 8px !important; }
        .shadow-xl, .shadow-2xl { box-shadow: none !important; }
        .animate-fade-in { animation: none !important; opacity: 1 !important; transform: none !important; }
        table { border-collapse: collapse !important; width: 100% !important; }
        th, td { border: 1px solid #ddd !important; }
    }
</style>
@endsection
