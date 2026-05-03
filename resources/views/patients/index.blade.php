@extends('layouts.app')

@section('title', 'Daftar Pasien')
@section('page-title', 'Database Pasien')

@section('content')
<div class="space-y-8 animate-fade-in pb-12">
    <!-- Header & Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Data Pasien</h2>
            <p class="text-gray-500 text-sm mt-1">Kelola data rekam medis dan informasi kontak pasien Rumkit TK III IM 07.01 Lhokseumawe.</p>
        </div>
        
        <div class="flex flex-wrap gap-3 items-center">
            <form action="{{ route('patients.index') }}" method="GET" class="relative group">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / kode pasien..." 
                    class="pl-12 pr-6 py-3 bg-white border border-gray-100 rounded-2xl text-sm focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all w-64 shadow-sm font-medium">
                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-tni-600 transition-colors"></i>
            </form>
            <a href="{{ route('patients.create') }}" class="inline-flex items-center px-6 py-3 bg-tni-800 text-white rounded-2xl hover:bg-black transition shadow-lg font-bold group">
                <i class="fas fa-plus-circle mr-2 group-hover:scale-110 transition-transform"></i> Tambah Pasien Baru
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-tni-100 text-tni-700 flex items-center justify-center text-2xl shadow-inner">
                <i class="fas fa-user-injured"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Pasien</p>
                <p class="text-2xl font-black text-gray-900">{{ $patients->total() }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-2xl shadow-inner">
                <i class="fas fa-mars"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Laki-Laki</p>
                <p class="text-2xl font-black text-gray-900">{{ $totalMale }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-pink-100 text-pink-700 flex items-center justify-center text-2xl shadow-inner">
                <i class="fas fa-venus"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Perempuan</p>
                <p class="text-2xl font-black text-gray-900">{{ $totalFemale }}</p>
            </div>
        </div>
    </div>

    <!-- Patients Table -->
    <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-8 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Identitas Pasien</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kontak</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Alamat</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Gender</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($patients as $patient)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-tni-600 to-tni-800 text-gold-400 flex items-center justify-center font-bold text-lg shadow-lg border border-tni-500">
                                    {{ substr($patient->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800 group-hover:text-tni-700 transition-colors">{{ $patient->name }}</p>
                                    <p class="text-[11px] text-tni-600 font-bold uppercase tracking-wider">{{ $patient->patient_code ?? 'NO-CODE' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6">
                            <div class="text-sm">
                                <p class="font-bold text-gray-700">{{ $patient->phone }}</p>
                                <p class="text-xs text-gray-400">{{ $patient->email ?? 'No Email' }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-6">
                            <p class="text-sm text-gray-600 line-clamp-1 max-w-[200px]" title="{{ $patient->address }}">
                                {{ $patient->address }}
                            </p>
                        </td>
                        <td class="px-6 py-6 text-center">
                            @if($patient->gender === 'male')
                                <span class="px-3 py-1 bg-blue-50 text-blue-700 border border-blue-100 rounded-full text-[10px] font-bold uppercase tracking-wider">Laki-laki</span>
                            @else
                                <span class="px-3 py-1 bg-pink-50 text-pink-700 border border-pink-100 rounded-full text-[10px] font-bold uppercase tracking-wider">Perempuan</span>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('patients.show', $patient) }}" class="p-2.5 bg-gray-50 text-gray-400 hover:bg-tni-100 hover:text-tni-700 rounded-xl transition-all" title="Lihat Rekam Medis">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('patients.edit', $patient) }}" class="p-2.5 bg-gray-50 text-gray-400 hover:bg-gold-100 hover:text-gold-700 rounded-xl transition-all" title="Edit Data">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                                <a href="{{ route('patients.print', $patient) }}" target="_blank" class="p-2.5 bg-gray-50 text-gray-400 hover:bg-blue-100 hover:text-blue-700 rounded-xl transition-all" title="Cetak Kartu">
                                    <i class="fas fa-print text-sm"></i>
                                </a>
                                <form action="{{ route('patients.destroy', $patient) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pasien ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2.5 bg-gray-50 text-gray-400 hover:bg-red-100 hover:text-red-700 rounded-xl transition-all" title="Hapus Data">
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
                                    <i class="fas fa-inbox text-3xl text-gray-300"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-800">Belum Ada Data Pasien</h3>
                                <p class="text-gray-500 text-sm max-w-xs mt-1">Silakan tambahkan data pasien baru untuk memulai manajemen rekam medis.</p>
                                <a href="{{ route('patients.create') }}" class="mt-6 px-6 py-3 bg-tni-100 text-tni-700 rounded-2xl font-bold hover:bg-tni-200 transition">
                                    Tambah Pasien Sekarang
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($patients->hasPages())
        <div class="px-8 py-6 border-t border-gray-50 bg-gray-50/30">
            {{ $patients->links() }}
        </div>
        @endif
    </div>
</div>
@endsection