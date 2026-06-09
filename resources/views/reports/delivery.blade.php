@extends('layouts.app')

@section('title', 'Laporan Pengantaran')
@section('page-title', 'Detail Pengantaran')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 animate-fade-in pb-12 report-document">
    <!-- Header with Actions (Visible on Screen) -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 no-print">
        <div>
            <h2 class="text-3xl font-black text-tni-900 tracking-tight">Detail Pengantaran Meditrack</h2>
            <p class="text-gray-500 font-medium">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>
        <div class="flex gap-3">
            <button onclick="window.print()" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition shadow-lg font-bold flex items-center">
                <i class="fas fa-print mr-2"></i> Cetak Laporan
            </button>
            <a href="{{ route('reports.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition font-bold">
                Kembali
            </a>
        </div>
    </div>

    @include('reports.partials.letterhead', [
        'documentLabel' => 'Dokumen Logistik',
        'documentCode' => 'MT/DLV/' . date('Ymd'),
        'reportTitle' => 'Laporan Detail Pengantaran Obat',
    ])

    <!-- Quick Stats -->
    <div class="report-stats grid grid-cols-2 md:grid-cols-4 gap-4 print:grid-cols-4 print:gap-2">
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

    <!-- Laporan Obat Yang Telah Diantar -->
    <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-50 bg-green-50/50">
            <h3 class="font-black text-tni-900 uppercase tracking-widest text-xs flex items-center gap-2">
                <i class="fas fa-pills text-green-600"></i>
                Obat Yang Telah Diantar ({{ $deliveredDeliveries->count() }})
            </h3>
            <p class="text-xs text-gray-500 mt-1">Rincian obat yang sudah sampai ke pasien dalam periode laporan</p>
        </div>
        <div class="overflow-x-auto">
            @if($deliveredDeliveries->isEmpty())
                <div class="px-8 py-12 text-center">
                    <i class="fas fa-box-open text-4xl text-gray-200 mb-3"></i>
                    <p class="text-sm font-bold text-gray-500">Belum ada obat yang terkirim pada periode ini</p>
                    <p class="text-xs text-gray-400 mt-1">Data akan muncul setelah kurir menyelesaikan pengantaran</p>
                </div>
            @else
                <table class="report-table min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Tgl Antar</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Pasien</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Obat Diantar</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Dosis / Aturan</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Kurir</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Penerima</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($deliveredDeliveries as $delivery)
                            @php
                                $medications = $delivery->prescription?->getMedicationList() ?? [];
                                $rowCount = max(count($medications), 1);
                            @endphp
                            @if(empty($medications))
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-sm font-black text-tni-800">#MT-{{ str_pad($delivery->id, 4, '0', STR_PAD_LEFT) }}</p>
                                    <p class="text-[10px] text-gray-500 font-bold">{{ ($delivery->delivered_at ?? $delivery->delivery_date)->format('d/m/Y H:i') }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-bold text-gray-800">{{ $delivery->patient->name }}</p>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-400 italic" colspan="2">Data resep tidak tersedia</td>
                                <td class="px-6 py-4 text-xs font-bold text-gray-700">{{ $delivery->courier?->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-xs text-gray-700">{{ $delivery->recipient_name ?? $delivery->receiver_name ?? '-' }}</td>
                            </tr>
                            @else
                            @foreach($medications as $index => $med)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                @if($index === 0)
                                <td class="px-6 py-4 whitespace-nowrap align-top" rowspan="{{ $rowCount }}">
                                    <p class="text-sm font-black text-tni-800">#MT-{{ str_pad($delivery->id, 4, '0', STR_PAD_LEFT) }}</p>
                                    <p class="text-[10px] text-gray-500 font-bold">{{ ($delivery->delivered_at ?? $delivery->delivery_date)->format('d/m/Y H:i') }}</p>
                                </td>
                                <td class="px-6 py-4 align-top" rowspan="{{ $rowCount }}">
                                    <p class="text-sm font-bold text-gray-800">{{ $delivery->patient->name }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $delivery->patient->patient_code ?? '-' }}</p>
                                </td>
                                @endif
                                <td class="px-6 py-4">
                                    <p class="text-sm font-bold text-gray-800">{{ $med['name'] ?? '-' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-xs text-gray-600">{{ $med['dosage'] ?? '-' }}</p>
                                    @if(!empty($med['frequency']) || !empty($med['duration']))
                                        <p class="text-[10px] text-gray-400 mt-0.5">
                                            {{ $med['frequency'] ?? '' }}{{ !empty($med['frequency']) && !empty($med['duration']) ? ' · ' : '' }}{{ $med['duration'] ?? '' }}
                                        </p>
                                    @endif
                                </td>
                                @if($index === 0)
                                <td class="px-6 py-4 align-top" rowspan="{{ $rowCount }}">
                                    <span class="text-xs font-bold text-gray-700">{{ $delivery->courier?->name ?? 'Belum ditugaskan' }}</span>
                                </td>
                                <td class="px-6 py-4 align-top" rowspan="{{ $rowCount }}">
                                    <p class="text-xs font-bold text-gray-700">{{ $delivery->recipient_name ?? $delivery->receiver_name ?? '-' }}</p>
                                    @if($delivery->recipient_relation)
                                        <p class="text-[10px] text-gray-400">{{ $delivery->recipient_relation }}</p>
                                    @endif
                                </td>
                                @endif
                            </tr>
                            @endforeach
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <!-- Semua Pengantaran -->
    <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/50">
            <h3 class="font-black text-tni-900 uppercase tracking-widest text-xs">Semua Pengantaran ({{ $deliveries->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            @if($deliveries->isEmpty())
                <div class="px-8 py-12 text-center">
                    <p class="text-sm font-bold text-gray-500">Tidak ada data pengantaran pada periode ini</p>
                </div>
            @else
            <table class="report-table min-w-full divide-y divide-gray-100">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">ID & Tgl</th>
                        <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Pasien</th>
                        <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Obat</th>
                        <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Kurir</th>
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
                            @if($delivery->delivered_at)
                                <p class="text-[10px] text-green-600 font-bold">Selesai: {{ $delivery->delivered_at->format('d/m/Y H:i') }}</p>
                            @endif
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-sm font-bold text-gray-800">{{ $delivery->patient->name }}</p>
                            <p class="text-[10px] text-gray-400 italic truncate max-w-[200px]">{{ $delivery->delivery_address }}</p>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-xs font-bold text-gray-700">{{ $delivery->prescription?->medication_summary ?? '-' }}</p>
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
                                $statusLabels = [
                                    'pending' => 'Menunggu',
                                    'on_delivery' => 'Dalam Perjalanan',
                                    'delivered' => 'Terkirim',
                                    'failed' => 'Gagal',
                                ];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider {{ $colors[$delivery->status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $statusLabels[$delivery->status] ?? $delivery->status }}
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
            @endif
        </div>
    </div>
    <!-- Print Footer / Signatures -->
    <div class="hidden print:grid grid-cols-2 gap-20 mt-20">
        <div class="text-center">
            <p class="text-sm font-bold mb-20 text-gray-800">Mengetahui,<br>Kepala Rumah Sakit TK III IM 07.01 Lhokseumawe</p>
            <div class="border-b border-black w-48 mx-auto mb-1"></div>
            <p class="text-xs font-black uppercase">DR. SUDIRMAN SUTI, S.P, FISR., M.H.<br>LETNAN KOLONEL CKM NRP<br>11050020241075</p>
        </div>
        <div class="text-center">
            <p class="text-sm font-bold mb-20 text-gray-800">Lhokseumawe, {{ now()->format('d F Y') }}<br>Petugas Administrasi</p>
            <div class="border-b border-black w-48 mx-auto mb-1"></div>
            <p class="text-xs font-black uppercase">{{ auth()->user()->name }}</p>
        </div>
    </div>
</div>

<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; margin: 0; padding: 20px; }
        .max-w-6xl { max-width: 100% !important; margin: 0 !important; }
        .bg-white { border: 1px solid #ddd !important; box-shadow: none !important; }
        .rounded-3xl, .rounded-[2.5rem], .rounded-2xl { border-radius: 8px !important; }
        .shadow-xl, .shadow-2xl, .shadow-sm { box-shadow: none !important; }
        table { border-collapse: collapse !important; width: 100% !important; }
        th, td { border: 1px solid #ddd !important; padding: 8px !important; }
        .animate-pulse { animation: none !important; }
    }
</style>
@endsection
