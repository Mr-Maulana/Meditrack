@extends('layouts.app')

@section('title', 'Laporan Pasien')
@section('page-title', 'Analisis Data Pasien')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 animate-fade-in pb-12">
    <!-- Header with Actions (Visible on Screen) -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 no-print">
        <div>
            <h2 class="text-3xl font-black text-tni-900 tracking-tight">Data Pasien Meditrack</h2>
            <p class="text-gray-500 font-medium">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>
        <div class="flex gap-3">
            <button onclick="window.print()" class="px-6 py-2.5 bg-green-600 text-white rounded-xl hover:bg-green-700 transition shadow-lg font-bold flex items-center">
                <i class="fas fa-print mr-2"></i> Cetak Laporan
            </button>
            <a href="{{ route('reports.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition font-bold">
                Kembali
            </a>
        </div>
    </div>

    <!-- Formal Letterhead (Visible ONLY on print) -->
    <div class="hidden print:block mb-12 border-b-4 border-double border-black pb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-8">
                <div class="w-24 h-24 bg-tni-800 rounded-2xl flex items-center justify-center text-gold-400 text-5xl shadow-lg">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black uppercase tracking-tight leading-none mb-1">Tentara Nasional Indonesia Angkatan Darat</h1>
                    <h2 class="text-xl font-bold uppercase tracking-tight text-gray-800 leading-none mb-1">Kesehatan Daerah Militer Iskandar Muda</h2>
                    <h3 class="text-lg font-bold uppercase text-tni-900 leading-none">Rumkit TK III IM 07.01 Lhokseumawe</h3>
                    <p class="text-sm text-gray-500 mt-3 font-medium">Jl. Sultan Iskandar Muda No. 1, Kec. Banda Sakti, Kota Lhokseumawe, Aceh 24311</p>
                </div>
            </div>
            <div class="text-right">
                <div class="px-5 py-3 bg-gray-100 rounded-xl mb-3 border border-gray-200">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Dokumen Rekam Medis</p>
                    <p class="text-base font-black text-tni-900">MT/PAT/{{ date('Ymd') }}</p>
                </div>
                <p class="text-xs font-bold text-gray-400">Dicetak: {{ now()->format('d/m/Y H:i') }} WIB</p>
            </div>
        </div>
        <div class="text-center mt-12">
            <h2 class="text-2xl font-black uppercase border-b-2 border-black inline-block pb-1 mb-2">Laporan Analisis Data Pasien</h2>
            <p class="text-sm font-bold text-gray-600">Periode Data: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 print:grid-cols-3 print:gap-4">
        <div class="bg-white rounded-3xl p-8 shadow-xl border border-gray-100 text-center print:shadow-none print:border-gray-300 print:p-4">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Total Pasien Baru</p>
            <p class="text-4xl font-black text-tni-900 print:text-xl">{{ $patientStats['total'] }}</p>
        </div>
        <div class="bg-white rounded-3xl p-8 shadow-xl border border-gray-100 text-center print:shadow-none print:border-gray-300 print:p-4">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Pasien Ber-Resep</p>
            <p class="text-4xl font-black text-gold-500 print:text-xl">{{ $patientStats['with_prescriptions'] }}</p>
        </div>
        <div class="bg-white rounded-3xl p-8 shadow-xl border border-gray-100 text-center print:shadow-none print:border-gray-300 print:p-4">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Pasien Dilayani Antar</p>
            <p class="text-4xl font-black text-blue-500 print:text-xl">{{ $patientStats['with_deliveries'] }}</p>
        </div>
    </div>

    <!-- Diagnosis & List -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Diagnosis Distribution -->
        <div class="lg:col-span-1 bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden print:border-gray-300 print:rounded-xl">
            <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/50 print:bg-gray-100">
                <h3 class="font-black text-tni-900 uppercase tracking-widest text-xs">Top 10 Diagnosis</h3>
            </div>
            <div class="p-8 space-y-6">
                @forelse($diagnosisStats as $diagnosis)
                <div>
                    <div class="flex justify-between text-xs font-bold mb-1.5">
                        <span class="text-gray-700 truncate mr-2">{{ $diagnosis['diagnosis'] }}</span>
                        <span class="text-tni-600">{{ $diagnosis['count'] }}</span>
                    </div>
                    <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-tni-600 h-full" style="width: {{ $diagnosis['percentage'] }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-400 text-sm italic py-8">Tidak ada data diagnosis</p>
                @endforelse
            </div>
        </div>

        <!-- Patient Table -->
        <div class="lg:col-span-2 bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden print:border-gray-300 print:rounded-xl">
            <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/50 print:bg-gray-100">
                <h3 class="font-black text-tni-900 uppercase tracking-widest text-xs">Daftar Pasien Terdaftar</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50/50 print:bg-gray-200">
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama & Kode</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Diagnosis Utama</th>
                            <th class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Resep</th>
                            <th class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Tgl Daftar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($patients as $patient)
                        <tr>
                            <td class="px-6 py-5">
                                <p class="text-sm font-bold text-gray-800">{{ $patient->name }}</p>
                                <p class="text-[10px] font-black text-tni-500 uppercase">{{ $patient->patient_code ?? $patient->medical_record_number }}</p>
                            </td>
                            <td class="px-6 py-5">
                                <span class="text-xs text-gray-600">{{ $patient->diagnosis ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <span class="px-2 py-0.5 bg-gold-100 text-gold-700 rounded text-[10px] font-black">{{ $patient->prescriptions->count() }}</span>
                            </td>
                            <td class="px-6 py-5 text-right whitespace-nowrap">
                                <span class="text-xs font-medium text-gray-500">{{ $patient->created_at->format('d/m/Y') }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic">Tidak ada data pasien</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Print Footer / Signatures -->
    <div class="hidden print:grid grid-cols-2 gap-20 mt-20">
        <div class="text-center">
            <p class="text-sm font-bold mb-20 text-gray-800">Mengetahui,<br>Kepala Rekam Medis</p>
            <div class="border-b border-black w-48 mx-auto mb-1"></div>
            <p class="text-xs font-black uppercase">Pangkat / NRP</p>
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
    }
</style>
@endsection