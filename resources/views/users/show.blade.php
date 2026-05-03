@extends('layouts.app')

@section('title', 'Detail Personel')
@section('page-title', 'Informasi Personel')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 animate-fade-in pb-12">
    <!-- Breadcrumbs & Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-tni-600 transition-colors">
                        <i class="fas fa-home mr-2"></i> Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                        <a href="{{ route('users.index') }}" class="text-gray-500 hover:text-tni-600 transition-colors">Manajemen User</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                        <span class="text-tni-700 font-medium">{{ $user->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <div class="flex gap-2">
            <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center px-4 py-2 bg-gold-500 text-white rounded-xl hover:bg-gold-600 transition shadow-md font-bold text-xs uppercase tracking-wider">
                <i class="fas fa-user-pen mr-2"></i> Edit Data
            </a>
            <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition shadow-md font-bold text-xs uppercase tracking-wider">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Profile Detail Card -->
    <div class="bg-white rounded-[2.5rem] shadow-2xl border border-gray-100 overflow-hidden">
        <!-- Profile Header -->
        <div class="bg-gradient-to-r from-tni-900 via-tni-800 to-tni-700 p-12 text-white relative">
            <div class="absolute top-0 right-0 p-12 opacity-10">
                <i class="fas fa-hospital-user text-[10rem]"></i>
            </div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center gap-8">
                <div class="w-32 h-32 rounded-[2rem] bg-white/20 backdrop-blur-md flex items-center justify-center text-5xl font-bold border-2 border-white/30 shadow-2xl">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div class="text-center md:text-left">
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-2">
                        <h2 class="text-3xl font-bold">{{ $user->name }}</h2>
                        <span class="px-3 py-1 bg-white/20 rounded-full text-[10px] font-bold tracking-widest uppercase border border-white/30">
                            {{ $user->role }}
                        </span>
                    </div>
                    <p class="text-tni-100 text-lg opacity-90 font-medium">{{ $user->profession ?? 'Personel Meditrack' }}</p>
                    <div class="mt-4 flex flex-wrap justify-center md:justify-start gap-6">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-id-card text-gold-400"></i>
                            <span class="text-sm font-bold">NIP: {{ $user->employee_id ?? '-' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar-check text-green-400"></i>
                            <span class="text-sm font-bold">Terdaftar: {{ $user->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-12">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Info Personal -->
                <div class="space-y-8">
                    <section>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6 flex items-center">
                            <i class="fas fa-user mr-2 text-tni-600"></i> Data Personal
                        </h3>
                        <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100 space-y-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Jenis Kelamin</p>
                                    <p class="text-gray-800 font-bold">{{ $user->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Status Akun</p>
                                    <p class="{{ $user->is_active ? 'text-green-600' : 'text-red-500' }} font-bold flex items-center">
                                        <span class="w-2 h-2 rounded-full {{ $user->is_active ? 'bg-green-500' : 'bg-red-500' }} mr-2"></span>
                                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Alamat Domisili</p>
                                <p class="text-gray-700 text-sm leading-relaxed">{{ $user->address ?? 'Alamat belum diisi' }}</p>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Info Kontak & Akun -->
                <div class="space-y-8">
                    <section>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6 flex items-center">
                            <i class="fas fa-shield-halved mr-2 text-tni-600"></i> Kontak & Keamanan
                        </h3>
                        <div class="bg-white rounded-3xl p-8 border border-gray-100 space-y-6 shadow-sm">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Email Dinas</p>
                                    <p class="text-gray-800 font-bold">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-sm">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Nomor Telepon</p>
                                    <p class="text-gray-800 font-bold">{{ $user->phone ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="pt-6 border-t border-gray-100">
                                <p class="text-[11px] text-gray-400 italic">Terakhir diperbarui: {{ $user->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <!-- Activity Summary (Optional/Placeholder) -->
            <div class="mt-12 bg-tni-50 rounded-[2rem] p-8 border border-tni-100 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-tni-800 text-white flex items-center justify-center text-xl shadow-lg">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-tni-900">Performa Personel</h4>
                        <p class="text-xs text-tni-700">Ringkasan aktivitas personel di sistem Meditrack.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="text-center px-6 border-r border-tni-200">
                        <p class="text-[10px] font-bold text-tni-400 uppercase">Input Resep</p>
                        <p class="text-lg font-bold text-tni-800">{{ $user->patients()->count() }}</p>
                    </div>
                    <div class="text-center px-6">
                        <p class="text-[10px] font-bold text-tni-400 uppercase">Antar Obat</p>
                        <p class="text-lg font-bold text-tni-800">{{ $user->deliveries()->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
