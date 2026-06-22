@extends('layouts.app')

@section('title', 'Laporan Radiologi')
@section('page-title', 'Laporan Radiologi')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 animate-fade-in pb-16 report-document">

    {{-- ── Screen Actions ──────────────────────────────────────────── --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 no-print">
        <div>
            <h2 class="text-3xl font-black text-tni-900 tracking-tight flex items-center gap-3">
                <span class="w-10 h-10 bg-purple-100 text-purple-600 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-x-ray text-lg"></i>
                </span>
                Laporan Radiologi
            </h2>
            <p class="text-gray-500 font-medium mt-1">
                Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} — {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
            </p>
        </div>
        <div class="flex gap-3">
            <button onclick="window.print()" class="px-6 py-2.5 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition shadow-lg font-bold flex items-center">
                <i class="fas fa-print mr-2"></i> Cetak Laporan
            </button>
            <a href="{{ route('reports.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition font-bold">
                Kembali
            </a>
        </div>
    </div>

    {{-- ── Letterhead (shown when printing) ───────────────────────── --}}
    @include('reports.partials.letterhead', [
        'documentLabel' => 'Dokumen Resmi',
        'documentCode'  => 'MT/RAD/' . date('Ymd'),
        'reportTitle'   => 'Laporan Pelayanan Radiologi',
    ])

    {{-- ── KPI Cards ────────────────────────────────────────────────── --}}
    <div class="report-stats grid grid-cols-2 md:grid-cols-5 gap-4 print:grid-cols-5 print:gap-3">
        {{-- Total --}}
        <div class="col-span-2 md:col-span-1 bg-white rounded-3xl p-6 shadow-xl border border-gray-100 text-center group hover:bg-purple-600 transition-all duration-300 print:shadow-none print:border-gray-300 print:p-3">
            <div class="w-10 h-10 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-white/20 group-hover:text-white transition-colors print:hidden">
                <i class="fas fa-x-ray text-lg"></i>
            </div>
            <div class="text-3xl font-black text-tni-900 mb-1 group-hover:text-white transition-colors print:text-xl">{{ $radiologyStats['total'] }}</div>
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest group-hover:text-purple-200 transition-colors">Total Pemeriksaan</div>
        </div>

        {{-- Pending --}}
        <div class="bg-white rounded-3xl p-6 shadow-xl border border-gray-100 text-center group hover:bg-amber-500 transition-all duration-300 print:shadow-none print:border-gray-300 print:p-3">
            <div class="w-10 h-10 bg-amber-50 text-amber-500 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-white/20 group-hover:text-white transition-colors print:hidden">
                <i class="fas fa-hourglass-half text-lg"></i>
            </div>
            <div class="text-3xl font-black text-gray-900 mb-1 group-hover:text-white transition-colors print:text-xl">{{ $radiologyStats['pending'] }}</div>
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest group-hover:text-amber-100 transition-colors">Menunggu</div>
        </div>

        {{-- Process --}}
        <div class="bg-white rounded-3xl p-6 shadow-xl border border-gray-100 text-center group hover:bg-blue-500 transition-all duration-300 print:shadow-none print:border-gray-300 print:p-3">
            <div class="w-10 h-10 bg-blue-50 text-blue-500 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-white/20 group-hover:text-white transition-colors print:hidden">
                <i class="fas fa-spinner text-lg"></i>
            </div>
            <div class="text-3xl font-black text-gray-900 mb-1 group-hover:text-white transition-colors print:text-xl">{{ $radiologyStats['process'] }}</div>
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest group-hover:text-blue-100 transition-colors">Diproses</div>
        </div>

        {{-- Completed --}}
        <div class="bg-white rounded-3xl p-6 shadow-xl border border-gray-100 text-center group hover:bg-green-600 transition-all duration-300 print:shadow-none print:border-gray-300 print:p-3">
            <div class="w-10 h-10 bg-green-50 text-green-600 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-white/20 group-hover:text-white transition-colors print:hidden">
                <i class="fas fa-check-circle text-lg"></i>
            </div>
            <div class="text-3xl font-black text-gray-900 mb-1 group-hover:text-white transition-colors print:text-xl">{{ $radiologyStats['completed'] }}</div>
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest group-hover:text-green-100 transition-colors">Selesai</div>
        </div>

        {{-- Completion Rate --}}
        <div class="bg-white rounded-3xl p-6 shadow-xl border border-gray-100 text-center group hover:bg-tni-700 transition-all duration-300 print:shadow-none print:border-gray-300 print:p-3">
            <div class="w-10 h-10 bg-tni-50 text-tni-700 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-white/20 group-hover:text-white transition-colors print:hidden">
                <i class="fas fa-percent text-lg"></i>
            </div>
            <div class="text-3xl font-black text-gray-900 mb-1 group-hover:text-white transition-colors print:text-xl">{{ $completionRate }}%</div>
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest group-hover:text-tni-200 transition-colors">Tingkat Selesai</div>
        </div>
    </div>

    {{-- ── Status Visual Bar + Daily Chart ─────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Status Distribution --}}
        <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 p-8 print:border-gray-300 print:rounded-xl print:shadow-none">
            <h3 class="text-xs font-black text-tni-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                <i class="fas fa-chart-pie text-purple-500"></i> Distribusi Status
            </h3>
            @php
                $total = max($radiologyStats['total'], 1);
                $statuses = [
                    ['label' => 'Menunggu',  'count' => $radiologyStats['pending'],   'color' => 'bg-amber-400',  'text' => 'text-amber-600'],
                    ['label' => 'Diproses',  'count' => $radiologyStats['process'],   'color' => 'bg-blue-400',   'text' => 'text-blue-600'],
                    ['label' => 'Selesai',   'count' => $radiologyStats['completed'], 'color' => 'bg-green-500',  'text' => 'text-green-700'],
                    ['label' => 'Terkirim',  'count' => $radiologyStats['sent'],      'color' => 'bg-purple-500', 'text' => 'text-purple-700'],
                ];
            @endphp

            {{-- Stacked Bar --}}
            <div class="flex h-5 rounded-full overflow-hidden mb-6 gap-0.5">
                @foreach($statuses as $s)
                    @if($s['count'] > 0)
                        <div class="{{ $s['color'] }} transition-all" style="width: {{ round(($s['count']/$total)*100, 1) }}%"
                             title="{{ $s['label'] }}: {{ $s['count'] }}"></div>
                    @endif
                @endforeach
            </div>

            <div class="space-y-3">
                @foreach($statuses as $s)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full {{ $s['color'] }} inline-block"></span>
                        <span class="text-sm font-bold text-gray-700">{{ $s['label'] }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-black {{ $s['text'] }}">{{ $s['count'] }}</span>
                        <span class="text-xs text-gray-400 w-10 text-right">{{ round(($s['count']/$total)*100, 1) }}%</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Daily Trend Chart --}}
        <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 p-8 print:border-gray-300 print:rounded-xl print:shadow-none">
            <h3 class="text-xs font-black text-tni-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                <i class="fas fa-chart-line text-blue-500"></i> Tren Harian Pemeriksaan
            </h3>
            @if($dailyTrend->count() > 0)
                @php
                    $maxDay = max($dailyTrend->max('total'), 1);
                @endphp
                <div class="flex items-end gap-1 h-32 overflow-x-auto pb-2">
                    @foreach($dailyTrend as $day)
                    <div class="flex flex-col items-center gap-1 min-w-[28px] group" title="{{ $day['date'] }}: {{ $day['total'] }} pemeriksaan">
                        <span class="text-[9px] text-gray-400 hidden group-hover:block absolute -mt-4 bg-gray-800 text-white px-1.5 py-0.5 rounded text-[9px] whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($day['date'])->format('d/m') }}: {{ $day['total'] }}
                        </span>
                        <div class="w-full flex flex-col justify-end" style="height: 110px;">
                            <div class="w-full bg-purple-100 rounded-t-lg relative overflow-hidden" style="height: {{ round(($day['total']/$maxDay)*100, 0) }}%;">
                                <div class="absolute bottom-0 left-0 right-0 bg-purple-500 rounded-t-lg" style="height: {{ $day['total'] > 0 ? round(($day['completed']/$day['total'])*100,0) : 0 }}%;"></div>
                            </div>
                        </div>
                        <span class="text-[8px] text-gray-400 rotate-45 origin-left mt-1">{{ \Carbon\Carbon::parse($day['date'])->format('d/m') }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="flex items-center gap-4 mt-4 text-xs text-gray-500">
                    <span><span class="inline-block w-3 h-3 rounded-sm bg-purple-100 mr-1"></span>Total</span>
                    <span><span class="inline-block w-3 h-3 rounded-sm bg-purple-500 mr-1"></span>Selesai</span>
                </div>
            @else
                <div class="h-32 flex flex-col items-center justify-center text-gray-300">
                    <i class="fas fa-chart-bar text-4xl mb-2"></i>
                    <p class="text-sm">Belum ada data pada periode ini</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Top Diagnoses Table ──────────────────────────────────────── --}}
    <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 overflow-hidden print:border-gray-300 print:rounded-xl print:shadow-none">
        <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50 print:bg-gray-100 flex items-center justify-between">
            <h3 class="font-black text-tni-900 uppercase tracking-widest text-xs flex items-center gap-2">
                <i class="fas fa-stethoscope text-purple-500"></i> Top 10 Diagnosa Radiologi
            </h3>
            <span class="text-xs text-gray-400">Periode {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="report-table w-full text-left">
                <thead>
                    <tr class="bg-gray-50 print:bg-gray-200">
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest w-8">#</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Diagnosa</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Jumlah</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Proporsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($diagnosisTrend as $i => $row)
                    <tr class="hover:bg-purple-50/40 transition-colors">
                        <td class="px-6 py-4 text-sm font-bold text-gray-400">{{ $i + 1 }}</td>
                        <td class="px-6 py-4 text-sm font-semibold text-tni-900">{{ $row['diagnosis'] }}</td>
                        <td class="px-6 py-4 text-sm font-black text-center text-purple-700">{{ $row['count'] }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-purple-500 rounded-full" style="width: {{ $row['percentage'] }}%"></div>
                                </div>
                                <span class="text-xs font-bold text-gray-500 w-10 text-right">{{ $row['percentage'] }}%</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-folder-open text-3xl mb-2 block"></i>
                            Tidak ada data diagnosa pada periode ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Operator Performance ─────────────────────────────────────── --}}
    @if($operatorPerformance->count() > 0)
    <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 overflow-hidden print:border-gray-300 print:rounded-xl print:shadow-none">
        <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50 print:bg-gray-100">
            <h3 class="font-black text-tni-900 uppercase tracking-widest text-xs flex items-center gap-2">
                <i class="fas fa-user-nurse text-blue-500"></i> Performa Operator Radiologi
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="report-table w-full text-left">
                <thead>
                    <tr class="bg-gray-50 print:bg-gray-200">
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Operator</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Total</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Selesai</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tingkat Penyelesaian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($operatorPerformance as $op)
                    @php
                        $rate = $op['total'] > 0 ? round(($op['completed'] / $op['total']) * 100, 1) : 0;
                    @endphp
                    <tr class="hover:bg-blue-50/40 transition-colors">
                        <td class="px-6 py-4 text-sm font-bold text-tni-900">{{ $op['name'] }}</td>
                        <td class="px-6 py-4 text-sm font-black text-center">{{ $op['total'] }}</td>
                        <td class="px-6 py-4 text-sm font-black text-center text-green-600">{{ $op['completed'] }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $rate >= 80 ? 'bg-green-500' : ($rate >= 50 ? 'bg-amber-400' : 'bg-red-400') }}"
                                         style="width: {{ $rate }}%"></div>
                                </div>
                                <span class="text-xs font-bold text-gray-500 w-10 text-right">{{ $rate }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ── Detailed Records Table ───────────────────────────────────── --}}
    <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 overflow-hidden print:border-gray-300 print:rounded-xl print:shadow-none">
        <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50 print:bg-gray-100 flex items-center justify-between">
            <h3 class="font-black text-tni-900 uppercase tracking-widest text-xs flex items-center gap-2">
                <i class="fas fa-list-ul text-purple-500"></i> Rincian Pemeriksaan
            </h3>
            <span class="text-xs text-gray-400 no-print">{{ $results->count() }} data</span>
        </div>
        <div class="overflow-x-auto">
            <table class="report-table w-full text-left">
                <thead>
                    <tr class="bg-gray-50 print:bg-gray-200">
                        <th class="px-5 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">#</th>
                        <th class="px-5 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tanggal</th>
                        <th class="px-5 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Pasien</th>
                        <th class="px-5 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Diagnosa</th>
                        <th class="px-5 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Operator</th>
                        <th class="px-5 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($results->sortByDesc('created_at') as $i => $r)
                    <tr class="hover:bg-purple-50/30 transition-colors">
                        <td class="px-5 py-3 text-xs text-gray-400">{{ $i + 1 }}</td>
                        <td class="px-5 py-3 text-xs text-gray-500 whitespace-nowrap">{{ \Carbon\Carbon::parse($r->created_at)->format('d/m/Y') }}</td>
                        <td class="px-5 py-3 text-sm font-bold text-tni-900">{{ $r->patient?->name ?? '-' }}</td>
                        <td class="px-5 py-3 text-xs text-gray-600 max-w-[200px] truncate">{{ $r->diagnosis ?: 'Belum terisi' }}</td>
                        <td class="px-5 py-3 text-xs text-gray-600">{{ $r->operator?->name ?? '-' }}</td>
                        <td class="px-5 py-3 text-center">
                            @php
                                $badge = match($r->status) {
                                    'completed' => 'bg-green-100 text-green-700',
                                    'process'   => 'bg-blue-100 text-blue-700',
                                    'pending'   => 'bg-amber-100 text-amber-700',
                                    default     => 'bg-gray-100 text-gray-600',
                                };
                                $label = match($r->status) {
                                    'completed' => 'Selesai',
                                    'process'   => 'Diproses',
                                    'pending'   => 'Menunggu',
                                    default     => ucfirst($r->status),
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black {{ $badge }}">{{ $label }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-folder-open text-3xl mb-2 block"></i>
                            Tidak ada data pada periode ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Print Footer / Signatures ──────────────────────────────── --}}
    <div class="hidden print:grid grid-cols-2 gap-20 mt-20">
        <div class="text-center">
            <p class="text-sm font-bold mb-20 text-gray-800">Mengetahui,<br>Kepala Instalasi Radiologi</p>
            <div class="border-b border-black w-48 mx-auto mb-1"></div>
            <p class="text-xs font-black uppercase">      /    </p>
        </div>
        <div class="text-center">
            <p class="text-sm font-bold mb-20 text-gray-800">Lhokseumawe, {{ now()->format('d F Y') }}<br>Petugas Administrasi</p>
            <div class="border-b border-black w-48 mx-auto mb-1"></div>
            <p class="text-xs font-black uppercase">{{ auth()->user()->name }}</p>
        </div>
    </div>
    <div class="hidden print:block text-center mt-6">
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
        .rounded-3xl, .rounded-\[2\.5rem\], .rounded-\[2rem\], .rounded-2xl { border-radius: 8px !important; }
        .shadow-xl, .shadow-2xl { box-shadow: none !important; }
        .animate-fade-in { animation: none !important; opacity: 1 !important; transform: none !important; }
        table { border-collapse: collapse !important; width: 100% !important; }
        th, td { border: 1px solid #ddd !important; }
    }
</style>
@endsection
