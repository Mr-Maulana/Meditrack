@extends('layouts.app')

@section('title', 'Kelola Pengguna')
@section('page-title', 'Manajemen SDM')

@section('content')
<div class="space-y-8 animate-fade-in pb-12">
    <!-- Header & Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Personel Rumah Sakit</h2>
            <p class="text-gray-500 text-sm mt-1">Daftar dokter, apoteker, dan staff operasional Meditrack.</p>
        </div>
        
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('users.create') }}" class="inline-flex items-center px-6 py-3 bg-tni-800 text-white rounded-2xl hover:bg-black transition shadow-lg font-bold group">
                <i class="fas fa-user-plus mr-2 group-hover:scale-110 transition-transform"></i> Tambah Personel
            </a>
        </div>
    </div>

    <!-- Quick Stats Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-tni-100 text-tni-700 flex items-center justify-center text-xl">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Personel</p>
                <p class="text-xl font-bold text-gray-800">{{ $users->total() }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-green-100 text-green-700 flex items-center justify-center text-xl">
                <i class="fas fa-signal"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Daring (Online)</p>
                <p class="text-xl font-bold text-green-600">{{ $users->filter(fn($u) => $u->isOnline())->count() }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gold-100 text-gold-700 flex items-center justify-center text-xl">
                <i class="fas fa-mortar-pestle"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Apoteker</p>
                <p class="text-xl font-bold text-gray-800">{{ $users->where('role', 'apoteker')->count() }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center text-xl">
                <i class="fas fa-truck-fast"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kurir</p>
                <p class="text-xl font-bold text-gray-800">{{ $users->where('role', 'kurir')->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-8 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Personel</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">ID / Profesi</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kontak</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Akses System</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-tni-600 to-tni-900 text-white flex items-center justify-center font-bold text-lg shadow-lg shadow-tni-200">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800 group-hover:text-tni-700 transition-colors">{{ $user->name }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6">
                            <div class="text-sm">
                                <p class="font-bold text-gray-700">{{ $user->employee_id ?? '-' }}</p>
                                <p class="text-xs text-tni-600 font-medium">{{ $user->profession ?? 'Staf Umum' }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-6 text-sm text-gray-600">
                            {{ $user->phone ?? '-' }}
                        </td>
                        <td class="px-6 py-6">
                            @php
                                $roleColors = [
                                    'admin' => 'bg-blue-50 text-blue-700 border-blue-100',
                                    'apoteker' => 'bg-gold-50 text-gold-700 border-gold-100',
                                    'kurir' => 'bg-purple-50 text-purple-700 border-purple-100',
                                ];
                                $color = $roleColors[$user->role] ?? 'bg-gray-50 text-gray-700 border-gray-100';
                            @endphp
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $color }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="px-6 py-6">
                            @if($user->isOnline())
                            <span class="flex items-center text-green-600 text-[10px] font-bold uppercase tracking-wider">
                                <span class="w-2 h-2 rounded-full bg-green-500 mr-2 animate-pulse"></span> Daring (Aktif)
                            </span>
                            @else
                            <span class="flex items-center text-gray-400 text-[10px] font-bold uppercase tracking-wider">
                                <span class="w-2 h-2 rounded-full bg-gray-300 mr-2"></span> Luring (Nonaktif)
                            </span>
                            @endif
                            <p class="text-[9px] text-gray-400 mt-1">
                                {{ $user->last_seen ? $user->last_seen->diffForHumans() : 'Belum pernah masuk' }}
                            </p>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('users.show', $user) }}" class="p-2 text-gray-400 hover:text-blue-600 transition-colors" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('users.edit', $user) }}" class="p-2 text-gray-400 hover:text-gold-600 transition-colors" title="Edit Data">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                <form action="{{ route('users.toggle-status', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-orange-600 transition-colors" title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="fas fa-power-off"></i>
                                    </button>
                                </form>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus personel ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition-colors" title="Hapus Permanen">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-12 text-center text-gray-500 italic">
                            Belum ada data personel yang terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
        <div class="px-8 py-6 border-t border-gray-50 bg-gray-50/30">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection