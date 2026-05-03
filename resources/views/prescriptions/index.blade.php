@extends('layouts.app')

@section('title', 'Manajemen Resep Obat')
@section('page-title', 'Resep & Farmasi')

@section('content')
<div class="space-y-8 animate-fade-in pb-12">
    <!-- Header & Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Daftar Resep Obat</h2>
            <p class="text-gray-500 text-sm mt-1">Kelola peresepan obat dan instruksi dosis untuk pasien.</p>
        </div>
        
        <div class="flex flex-wrap gap-3 items-center">
            <form action="{{ route('prescriptions.index') }}" method="GET" class="relative group">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pasien / obat..." 
                    class="pl-12 pr-6 py-3 bg-white border border-gray-100 rounded-2xl text-sm focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all w-64 shadow-sm font-medium">
                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-tni-600 transition-colors"></i>
            </form>
            <a href="{{ route('prescriptions.create') }}" class="inline-flex items-center px-6 py-3 bg-tni-800 text-white rounded-2xl hover:bg-black transition shadow-lg font-bold group">
                <i class="fas fa-prescription-bottle-medical mr-2 group-hover:scale-110 transition-transform"></i> Buat Resep Baru
            </a>
        </div>
    </div>

    <!-- Quick Stats Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-tni-100 text-tni-700 flex items-center justify-center text-xl">
                <i class="fas fa-file-prescription"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Resep</p>
                <p class="text-xl font-bold text-gray-800">{{ $prescriptions->total() }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-xl">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Resep Hari Ini</p>
                <p class="text-xl font-bold text-gray-800">{{ $todayCount }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gold-100 text-gold-700 flex items-center justify-center text-xl">
                <i class="fas fa-pills"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Jenis Obat</p>
                <p class="text-xl font-bold text-gray-800">Multi-Items</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center text-xl">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Validasi</p>
                <p class="text-xl font-bold text-gray-800">Terverifikasi</p>
            </div>
        </div>
    </div>

    <!-- Prescriptions Table -->
    <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-8 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pasien</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Daftar Obat</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Dosis & Frekuensi</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Tanggal</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($prescriptions as $prescription)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-tni-600 to-tni-900 text-gold-400 flex items-center justify-center font-bold text-lg shadow-lg border border-tni-500">
                                    {{ substr($prescription->patient->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800 group-hover:text-tni-700 transition-colors">{{ $prescription->patient->name }}</p>
                                    <p class="text-[10px] text-tni-600 font-bold uppercase tracking-wider">{{ $prescription->patient->patient_code ?? 'REKAM-MEDIS' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6">
                            <div class="text-sm">
                                @if($prescription->medications && count($prescription->medications) > 0)
                                    <p class="font-bold text-gray-700">{{ $prescription->medications[0]['name'] }}</p>
                                    @if(count($prescription->medications) > 1)
                                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">+ {{ count($prescription->medications) - 1 }} Obat Lainnya</p>
                                    @endif
                                @else
                                    <p class="font-bold text-gray-700">{{ $prescription->medication_name }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-6">
                            <div class="text-sm">
                                @if($prescription->medications && count($prescription->medications) > 0)
                                    <p class="text-gray-600">{{ count($prescription->medications) }} Item Obat</p>
                                    <p class="text-[10px] text-tni-600 font-bold uppercase">{{ $prescription->duration_text }}</p>
                                @else
                                    <p class="text-gray-600">{{ $prescription->dosage }}</p>
                                    <p class="text-[10px] text-tni-600 font-bold uppercase">{{ $prescription->frequency_text }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-6 text-center">
                            <p class="text-sm font-bold text-gray-700">{{ $prescription->created_at->format('d/m/Y') }}</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">{{ $prescription->created_at->format('H:i') }} WIB</p>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('prescriptions.show', $prescription) }}" class="p-2.5 bg-gray-50 text-gray-400 hover:bg-tni-100 hover:text-tni-700 rounded-xl transition-all" title="Lihat Detail Resep">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('prescriptions.edit', $prescription) }}" class="p-2.5 bg-gray-50 text-gray-400 hover:bg-gold-100 hover:text-gold-700 rounded-xl transition-all" title="Edit Resep">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                                <form action="{{ route('prescriptions.destroy', $prescription) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus resep ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2.5 bg-gray-50 text-gray-400 hover:bg-red-100 hover:text-red-700 rounded-xl transition-all" title="Hapus Resep">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-file-prescription text-3xl text-gray-300"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-800">Belum Ada Resep Terdaftar</h3>
                                <p class="text-gray-500 text-sm max-w-xs mt-1">Silakan buat resep baru untuk pasien yang telah terdaftar di sistem.</p>
                                <a href="{{ route('prescriptions.create') }}" class="mt-6 px-6 py-3 bg-tni-100 text-tni-700 rounded-2xl font-bold hover:bg-tni-200 transition">
                                    Buat Resep Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($prescriptions->hasPages())
        <div class="px-8 py-6 border-t border-gray-50 bg-gray-50/30">
            {{ $prescriptions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
