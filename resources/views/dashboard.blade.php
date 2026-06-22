@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-8 animate-fade-in">
    <!-- Header Greeting -->
    <div class="bg-gradient-to-r from-tni-800 to-tni-600 rounded-2xl shadow-xl p-8 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute bottom-0 right-20 w-32 h-32 bg-gold-400/20 rounded-full blur-xl"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold mb-2">Selamat Datang, {{ auth()->user()->name }}!</h2>
                <p class="text-tni-100">Sistem Manajemen Pengantaran Obat Rumkit TK III IM 07.01 Lhokseumawe</p>
            </div>
            <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/30 shadow-lg overflow-hidden">
                @if(auth()->user()->profile_photo)
                    <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="Profile" class="w-full h-full object-cover">
                @else
                    <i class="fas fa-hospital-user text-4xl text-gold-400"></i>
                @endif
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1 -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1 group-hover:text-tni-600 transition-colors">Total Pasien</p>
                    <p class="text-3xl font-extrabold text-gray-900">{{ $stats['total_patients'] }}</p>
                </div>
                <div class="w-14 h-14 rounded-full bg-tni-50 text-tni-600 flex items-center justify-center group-hover:bg-tni-600 group-hover:text-white transition-colors duration-300">
                    <i class="fas fa-users text-2xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Card 2 -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1 group-hover:text-tni-600 transition-colors">Pengantaran Hari Ini</p>
                    <p class="text-3xl font-extrabold text-gray-900">{{ $stats['today_deliveries'] }}</p>
                </div>
                <div class="w-14 h-14 rounded-full bg-tni-50 text-tni-500 flex items-center justify-center group-hover:bg-tni-500 group-hover:text-white transition-colors duration-300">
                    <i class="fas fa-truck text-2xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Card 3 -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1 group-hover:text-gold-600 transition-colors">Menunggu</p>
                    <p class="text-3xl font-extrabold text-gray-900">{{ $stats['pending_deliveries'] }}</p>
                </div>
                <div class="w-14 h-14 rounded-full bg-gold-50 text-gold-500 flex items-center justify-center group-hover:bg-gold-500 group-hover:text-white transition-colors duration-300 relative">
                    <i class="fas fa-clock text-2xl"></i>
                    @if($stats['pending_deliveries'] > 0)
                        <span class="absolute top-0 right-0 flex h-3 w-3">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500 border-2 border-white"></span>
                        </span>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Card 4 -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1 group-hover:text-tni-800 transition-colors">Selesai (Bulan Ini)</p>
                    <p class="text-3xl font-extrabold text-gray-900">{{ $stats['delivered_count'] }}</p>
                </div>
                <div class="w-14 h-14 rounded-full bg-tni-50 text-tni-800 flex items-center justify-center group-hover:bg-tni-800 group-hover:text-gold-400 transition-colors duration-300">
                    <i class="fas fa-check-double text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->isAdmin() || auth()->user()->isApoteker() || auth()->user()->isDokter() || auth()->user()->isOperator())
    <!-- Radiology Stats Cards -->
    <div class="space-y-4 animate-fade-in">
        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest flex items-center">
            <i class="fas fa-x-ray mr-2 text-tni-600"></i> Ringkasan Radiologi
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Total Radiology Scans -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1 group-hover:text-tni-600 transition-colors">Total Berkas Scan</p>
                        <p class="text-3xl font-extrabold text-gray-900">{{ $stats['total_radiology'] }}</p>
                    </div>
                    <div class="w-14 h-14 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                        <i class="fas fa-folder-open text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Pending Diagnosis -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1 group-hover:text-amber-600 transition-colors">Menunggu Ekspertise Dokter</p>
                        <p class="text-3xl font-extrabold text-gray-900">{{ $stats['pending_radiology'] }}</p>
                    </div>
                    <div class="w-14 h-14 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center group-hover:bg-amber-500 group-hover:text-white transition-colors duration-300 relative">
                        <i class="fas fa-stethoscope text-2xl"></i>
                        @if($stats['pending_radiology'] > 0)
                            <span class="absolute top-0 right-0 flex h-3 w-3">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500 border-2 border-white"></span>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Completed Scan Reports -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1 group-hover:text-emerald-600 transition-colors">Ekspertise Selesai</p>
                        <p class="text-3xl font-extrabold text-gray-900">{{ $stats['completed_radiology'] }}</p>
                    </div>
                    <div class="w-14 h-14 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300">
                        <i class="fas fa-file-alt text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                <div class="w-8 h-8 rounded-lg bg-gold-100 text-gold-600 flex items-center justify-center mr-3">
                    <i class="fas fa-bolt"></i>
                </div>
                Aksi Cepat
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Admin Actions -->
                @if(auth()->user()->isAdmin())
                    <!-- Tambah Pasien -->
                    <a href="{{ route('patients.create') }}" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 hover:shadow-md transition">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-3">
                                <i class="fas fa-user-plus text-lg"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Tambah Pasien</p>
                                <p class="text-xs text-gray-500">Input data pasien baru</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                    
                    <!-- Tambah Pengantaran -->
                    <a href="{{ route('deliveries.create') }}" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-green-50 hover:border-green-300 hover:shadow-md transition">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-3">
                                <i class="fas fa-plus-circle text-lg"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Buat Pengantaran</p>
                                <p class="text-xs text-gray-500">Jadwalkan pengiriman obat</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                    
                    <!-- Kelola User -->
                    <a href="{{ route('users.index') }}" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-red-50 hover:border-red-300 hover:shadow-md transition">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-100 text-red-600 mr-3">
                                <i class="fas fa-users-cog text-lg"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Kelola User</p>
                                <p class="text-xs text-gray-500">Atur role dan permission</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                    
                    <!-- Laporan Admin -->
                    <a href="{{ route('reports.index') }}" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-purple-50 hover:border-purple-300 hover:shadow-md transition">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-3">
                                <i class="fas fa-chart-bar text-lg"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Laporan</p>
                                <p class="text-xs text-gray-500">Lihat laporan lengkap</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>

                    <!-- Upload Scan Baru -->
                    <a href="{{ route('radiology.create') }}" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-sky-50 hover:border-sky-300 hover:shadow-md transition">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-sky-100 text-sky-600 mr-3">
                                <i class="fas fa-x-ray text-lg"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Upload Scan Baru</p>
                                <p class="text-xs text-gray-500">Unggah hasil rontgen/USG baru</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>

                    <!-- Hasil Radiologi -->
                    <a href="{{ route('radiology.index') }}" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-indigo-50 hover:border-indigo-300 hover:shadow-md transition">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-indigo-100 text-indigo-600 mr-3">
                                <i class="fas fa-stethoscope text-lg"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Hasil Radiologi</p>
                                <p class="text-xs text-gray-500">Diagnosa & kelola hasil scan</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                @endif

                <!-- Apoteker Actions -->
                @if(auth()->user()->isApoteker())
                    <!-- Tambah Pasien -->
                    <a href="{{ route('patients.create') }}" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 hover:shadow-md transition">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-3">
                                <i class="fas fa-user-plus text-lg"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Tambah Pasien</p>
                                <p class="text-xs text-gray-500">Input data pasien baru</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                    
                    <!-- Buat Resep -->
                    <a href="{{ route('prescriptions.create') }}" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-purple-50 hover:border-purple-300 hover:shadow-md transition">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-3">
                                <i class="fas fa-prescription-bottle text-lg"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Buat Resep</p>
                                <p class="text-xs text-gray-500">Input resep obat baru</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                    
                    <!-- Buat Pengantaran -->
                    <a href="{{ route('deliveries.create') }}" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-green-50 hover:border-green-300 hover:shadow-md transition">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-3">
                                <i class="fas fa-truck text-lg"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Buat Pengantaran</p>
                                <p class="text-xs text-gray-500">Jadwalkan pengiriman obat</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>

                    <!-- Laporan Apoteker -->
                    <a href="{{ route('reports.index') }}" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-indigo-50 hover:border-indigo-300 hover:shadow-md transition">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-indigo-100 text-indigo-600 mr-3">
                                <i class="fas fa-chart-line text-lg"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Laporan</p>
                                <p class="text-xs text-gray-500">Laporan pengantaran & resep</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>

                    <!-- Upload Scan Baru -->
                    <a href="{{ route('radiology.create') }}" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-sky-50 hover:border-sky-300 hover:shadow-md transition">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-sky-100 text-sky-600 mr-3">
                                <i class="fas fa-x-ray text-lg"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Upload Scan Baru</p>
                                <p class="text-xs text-gray-500">Unggah hasil rontgen/USG baru</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>

                    <!-- Hasil Radiologi -->
                    <a href="{{ route('radiology.index') }}" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-indigo-50 hover:border-indigo-300 hover:shadow-md transition">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-indigo-100 text-indigo-600 mr-3">
                                <i class="fas fa-list text-lg"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Daftar Hasil Radiologi</p>
                                <p class="text-xs text-gray-500">Daftar pemeriksaan scan</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                @endif

                <!-- Kurir Actions -->
                @if(auth()->user()->isKurir())
                    <!-- Mulai Pengantaran -->
                    <a href="{{ route('delivery-process.index') }}" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-green-50 hover:border-green-300 hover:shadow-md transition">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-3">
                                <i class="fas fa-map-marked-alt text-lg"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Mulai Pengantaran</p>
                                <p class="text-xs text-gray-500">Ambil pengantaran & kirim</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                    
                    <!-- Riwayat Pengantaran -->
                    <a href="{{ route('my-deliveries') }}" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 hover:shadow-md transition">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-3">
                                <i class="fas fa-history text-lg"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Riwayat Pengantaran</p>
                                <p class="text-xs text-gray-500">Lihat pengantaran saya</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                @endif

                <!-- Dokter Actions -->
                @if(auth()->user()->isDokter())
                    <!-- Diagnosa & Ekspertise -->
                    <a href="{{ route('radiology.index') }}" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-indigo-50 hover:border-indigo-300 hover:shadow-md transition animate-fade-in">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-indigo-100 text-indigo-600 mr-3">
                                <i class="fas fa-stethoscope text-lg"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Ekspertise Radiologi</p>
                                <p class="text-xs text-gray-500">Tulis diagnosa & hasil baca scan</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                    
                    <!-- Chat Pasien -->
                    <a href="{{ route('radiology.chat-center') }}" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-emerald-50 hover:border-emerald-300 hover:shadow-md transition animate-fade-in">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-emerald-100 text-emerald-600 mr-3">
                                <i class="fas fa-comments text-lg"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Simulasi Chat Pasien</p>
                                <p class="text-xs text-gray-500">Komunikasi hasil scan dengan pasien</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>

                    <!-- Edit Profile -->
                    <a href="{{ route('profile.edit') }}" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 hover:shadow-md transition animate-fade-in">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-3">
                                <i class="fas fa-user-edit text-lg"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Edit Profil</p>
                                <p class="text-xs text-gray-500">Kelola info akun Anda</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                @endif

                <!-- Operator Actions -->
                @if(auth()->user()->isOperator())
                    <!-- Upload Scan Baru -->
                    <a href="{{ route('radiology.create') }}" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-sky-50 hover:border-sky-300 hover:shadow-md transition animate-fade-in">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-sky-100 text-sky-600 mr-3">
                                <i class="fas fa-x-ray text-lg"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Upload Scan Baru</p>
                                <p class="text-xs text-gray-500">Unggah hasil rontgen/USG baru</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                    
                    <!-- Hasil Radiologi -->
                    <a href="{{ route('radiology.index') }}" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-indigo-50 hover:border-indigo-300 hover:shadow-md transition animate-fade-in">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-indigo-100 text-indigo-600 mr-3">
                                <i class="fas fa-list text-lg"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Daftar Hasil Radiologi</p>
                                <p class="text-xs text-gray-500">Kelola dan lihat status berkas scan</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>

                    <!-- Chat Pasien -->
                    <a href="{{ route('radiology.chat-center') }}" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-emerald-50 hover:border-emerald-300 hover:shadow-md transition animate-fade-in">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-emerald-100 text-emerald-600 mr-3">
                                <i class="fas fa-comments text-lg"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Simulasi Chat Pasien</p>
                                <p class="text-xs text-gray-500">Tanya jawab hasil dengan pasien</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Patients -->
    @if($patients->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-lg bg-tni-100 text-tni-600 flex items-center justify-center mr-3">
                    <i class="fas fa-user-injured"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Pasien Terbaru</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Daftar 5 pasien terbaru</p>
                </div>
            </div>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('patients.index') }}" class="px-4 py-2 bg-tni-50 text-tni-600 border border-tni-200 rounded-lg hover:bg-tni-600 hover:text-white transition-colors duration-200 text-sm font-semibold shadow-sm">
                    Lihat Semua
                </a>
            @elseif(auth()->user()->isApoteker())
                <a href="{{ route('patients.index') }}" class="px-4 py-2 bg-tni-50 text-tni-600 border border-tni-200 rounded-lg hover:bg-tni-600 hover:text-white transition-colors duration-200 text-sm font-semibold shadow-sm">
                    Lihat Semua
                </a>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Pasien</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telepon</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Kelamin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Daftar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($patients as $patient)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-tni-700">
                            {{ $patient->patient_code ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-9 w-9 rounded-full bg-gradient-to-br from-tni-500 to-tni-700 shadow-md flex items-center justify-center text-white text-sm font-bold border border-tni-400">
                                    {{ substr($patient->name, 0, 1) }}
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-bold text-gray-800">{{ $patient->name }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">{{ $patient->phone }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($patient->gender === 'male')
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">Laki-laki</span>
                            @else
                                <span class="px-2 py-1 bg-pink-100 text-pink-800 rounded text-xs">Perempuan</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $patient->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('patients.show', $patient) }}" class="inline-flex items-center justify-center w-8 h-8 rounded bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 transition" title="Lihat Detail">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('patients.edit', $patient) }}" class="inline-flex items-center justify-center w-8 h-8 rounded bg-yellow-50 text-yellow-600 hover:bg-yellow-100 hover:text-yellow-700 transition" title="Edit">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <a href="{{ route('patients.print', $patient) }}" target="_blank" class="inline-flex items-center justify-center w-8 h-8 rounded bg-gray-50 text-gray-600 hover:bg-gray-200 hover:text-gray-800 transition" title="Cetak">
                                    <i class="fas fa-print text-xs"></i>
                                </a>
                            @elseif(auth()->user()->isApoteker())
                                <a href="{{ route('patients.show', $patient) }}" class="inline-flex items-center justify-center w-8 h-8 rounded bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 transition" title="Lihat Detail">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('patients.edit', $patient) }}" class="inline-flex items-center justify-center w-8 h-8 rounded bg-yellow-50 text-yellow-600 hover:bg-yellow-100 hover:text-yellow-700 transition" title="Edit">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <a href="{{ route('patients.print', $patient) }}" target="_blank" class="inline-flex items-center justify-center w-8 h-8 rounded bg-gray-50 text-gray-600 hover:bg-gray-200 hover:text-gray-800 transition" title="Cetak">
                                    <i class="fas fa-print text-xs"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-3xl text-gray-300 mb-3"></i>
                            <p class="text-sm">Belum ada data pasien</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Recent Deliveries -->
    @if($deliveries->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-lg bg-tni-100 text-tni-500 flex items-center justify-center mr-3">
                    <i class="fas fa-truck-medical"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Pengantaran Terbaru</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Daftar 5 pengantaran terbaru</p>
                </div>
            </div>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('deliveries.index') }}" class="px-4 py-2 bg-tni-50 text-tni-600 border border-tni-200 rounded-lg hover:bg-tni-600 hover:text-white transition-colors duration-200 text-sm font-semibold shadow-sm">
                    Lihat Semua
                </a>
            @elseif(auth()->user()->isApoteker())
                <a href="{{ route('deliveries.index') }}" class="px-4 py-2 bg-tni-50 text-tni-600 border border-tni-200 rounded-lg hover:bg-tni-600 hover:text-white transition-colors duration-200 text-sm font-semibold shadow-sm">
                    Lihat Semua
                </a>
            @elseif(auth()->user()->isKurir())
                <a href="{{ route('delivery-process.index') }}" class="px-4 py-2 bg-tni-50 text-tni-600 border border-tni-200 rounded-lg hover:bg-tni-600 hover:text-white transition-colors duration-200 text-sm font-semibold shadow-sm">
                    Lihat Semua
                </a>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pasien</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telepon</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pengantaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioritas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($deliveries as $delivery)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $delivery->patient->name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $delivery->patient->patient_code ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $delivery->patient->phone ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $delivery->delivery_date ? $delivery->delivery_date->format('d M Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusConfig = [
                                    'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'label' => 'Menunggu'],
                                    'on_delivery' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'Dalam Pengantaran'],
                                    'delivered' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Terkirim'],
                                    'failed' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Gagal'],
                                ];
                                $config = $statusConfig[$delivery->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => ucfirst($delivery->status)];
                            @endphp
                            <span class="px-3 py-1 text-xs leading-5 font-semibold rounded-full {{ $config['bg'] }} {{ $config['text'] }}">
                                {{ $config['label'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($delivery->priority === 'urgent')
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-exclamation-circle mr-1"></i> Urgent
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Normal
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('deliveries.show', $delivery) }}" class="text-blue-600 hover:text-blue-900 transition" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('deliveries.edit', $delivery) }}" class="text-yellow-600 hover:text-yellow-900 transition" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('deliveries.track', $delivery) }}" class="text-purple-600 hover:text-purple-900 transition" title="Tracking">
                                    <i class="fas fa-map-marker-alt"></i>
                                </a>
                            @elseif(auth()->user()->isApoteker())
                                <a href="{{ route('deliveries.show', $delivery) }}" class="text-blue-600 hover:text-blue-900 transition" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('deliveries.edit', $delivery) }}" class="text-yellow-600 hover:text-yellow-900 transition" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('deliveries.track', $delivery) }}" class="text-purple-600 hover:text-purple-900 transition" title="Tracking">
                                    <i class="fas fa-map-marker-alt"></i>
                                </a>
                            @elseif(auth()->user()->isKurir())
                                <a href="{{ route('my-deliveries.detail', $delivery) }}" class="text-blue-600 hover:text-blue-900 transition" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-3xl text-gray-300 mb-3"></i>
                            <p class="text-sm">Belum ada data pengantaran</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Welcome Section for Empty Dashboard -->
    @if($patients->count() === 0 && $deliveries->count() === 0)
    <div class="bg-gradient-to-br from-tni-800 to-tni-900 rounded-[1.5rem] shadow-lg p-8 md:p-10 text-center relative overflow-hidden border border-tni-700">
        <div class="absolute top-0 left-0 p-6 opacity-5">
            <i class="fas fa-hospital-user text-6xl text-white"></i>
        </div>
        <div class="absolute bottom-0 right-0 p-6 opacity-10">
            <i class="fas fa-rocket text-6xl text-gold-400"></i>
        </div>
        
        <div class="relative z-10 max-w-xl mx-auto">
            <div class="w-16 h-16 bg-gradient-to-br from-gold-400 to-gold-600 text-tni-900 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-5 shadow-lg shadow-gold-500/20 rotate-12 hover:rotate-0 transition-transform duration-500 border-2 border-white">
                <i class="fas fa-rocket"></i>
            </div>
            <h3 class="text-2xl font-black text-white mb-3 tracking-tight">Selamat Datang di <span class="text-gold-400">MediTrack!</span></h3>
            <p class="text-tni-100 text-sm mb-8 leading-relaxed font-medium">Sistem manajemen pengantaran obat terpadu milik Rumkit TK III IM 07.01 Lhokseumawe. Mari mulai dengan mendaftarkan pasien pertama Anda ke dalam sistem.</p>
            
            @if(auth()->user()->isAdmin() || auth()->user()->isApoteker())
                <a href="{{ route('patients.create') }}" class="inline-flex items-center justify-center px-8 py-3.5 bg-gradient-to-r from-gold-400 to-gold-600 text-tni-900 rounded-xl font-black text-xs uppercase tracking-[0.15em] shadow-xl shadow-gold-500/20 hover:scale-[1.03] transition-all">
                    <i class="fas fa-user-plus mr-2 text-sm"></i> Tambah Pasien Pertama
                </a>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection