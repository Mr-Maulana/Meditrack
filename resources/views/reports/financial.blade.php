@extends('layouts.app')

@section('title', 'Laporan Operasional')
@section('page-title', 'Analisis Biaya')
@section('content')
<div class="max-w-6xl mx-auto space-y-8 animate-fade-in pb-12 report-document">
    <!-- Header with Actions (Visible on Screen) -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 no-print">
        <div>
            <h2 class="text-3xl font-black text-tni-900 tracking-tight">Estimasi Operasional & Efisiensi</h2>
            <p class="text-gray-500 font-medium">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>
        <div class="flex gap-3">
            <button onclick="window.print()" class="px-6 py-2.5 bg-gold-600 text-white rounded-xl hover:bg-gold-700 transition shadow-lg font-bold flex items-center">
                <i class="fas fa-print mr-2"></i> Cetak Laporan
            </button>
            <a href="{{ route('reports.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition font-bold">
                Kembali
            </a>
        </div>
    </div>

    @include('reports.partials.letterhead', [
        'documentLabel' => 'Dokumen Keuangan',
        'documentCode' => 'MT/FIN/' . date('Ymd'),
        'reportTitle' => 'Laporan Estimasi Operasional & Efisiensi',
    ])

    <!-- Stats Overview -->
    <div class="report-stats grid grid-cols-1 md:grid-cols-3 gap-8 print:grid-cols-3 print:gap-4">
        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-xl print:shadow-none print:border-gray-300 print:p-4">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Total Efisiensi</p>
            <p class="text-3xl font-black text-tni-900 print:text-xl">Rp {{ number_format($estimatedRevenue, 0, ',', '.') }}</p>
        </div>

        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-xl print:shadow-none print:border-gray-300 print:p-4">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Biaya / Trip</p>
            <p class="text-3xl font-black text-gray-800 print:text-xl">Rp {{ number_format($revenuePerDelivery, 0, ',', '.') }}</p>
        </div>

        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-xl print:shadow-none print:border-gray-300 print:p-4">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Volume Tugas</p>
            <p class="text-3xl font-black text-tni-700 print:text-xl">{{ $deliveryCount }} Trip</p>
        </div>
    </div>

    <!-- Formal Table for Print -->
    <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden print:border-gray-300 print:rounded-xl">
        <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/50 print:bg-gray-100">
            <h3 class="font-black text-tni-900 uppercase tracking-widest text-xs">Rincian Keuangan Bulanan</h3>
        </div>
        <table class="report-table w-full text-left">
            <thead>
                <tr class="bg-gray-50 print:bg-gray-200">
                    <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Bulan / Periode</th>
                    <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Jumlah Trip</th>
                    <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Nilai Efisiensi (IDR)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($monthlyRevenue as $item)
                <tr>
                    <td class="px-8 py-5 text-sm font-bold text-tni-900">{{ \Carbon\Carbon::parse($item->month)->format('F Y') }}</td>
                    <td class="px-8 py-5 text-sm font-black text-center">--</td>
                    <td class="px-8 py-5 text-sm font-black text-right">Rp {{ number_format($item->revenue, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-tni-50 print:bg-gray-100 font-black">
                    <td class="px-8 py-4 text-sm uppercase">Total Keseluruhan</td>
                    <td class="px-8 py-4 text-center">{{ $deliveryCount }}</td>
                    <td class="px-8 py-4 text-right">Rp {{ number_format($estimatedRevenue, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Print Footer / Signatures -->
    <div class="hidden print:grid grid-cols-2 gap-20 mt-20">
        <div class="text-center">
            <p class="text-sm font-bold mb-20 text-gray-800">Mengetahui,<br>Bendahara Rumkit</p>
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
        table { border-collapse: collapse !important; width: 100% !important; }
        th, td { border: 1px solid #ddd !important; }
    }
</style>
@endsection
