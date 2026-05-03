@extends('layouts.app')

@section('title', 'Tambah Kurir')
@section('page-title', 'Tambah Kurir Baru')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('couriers.index') }}" class="text-tni-600 hover:text-tni-800 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Kurir
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-tni-800 to-tni-700 px-8 py-6 text-white">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4 backdrop-blur-sm border border-white/30">
                    <i class="fas fa-motorcycle text-2xl text-gold-400"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold">Registrasi Kurir Baru</h3>
                    <p class="text-tni-100 text-sm">Lengkapi data untuk menambahkan personel kurir baru</p>
                </div>
            </div>
        </div>

        <form action="{{ route('couriers.store') }}" method="POST" class="p-8 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama -->
                <div class="space-y-2">
                    <label for="name" class="text-sm font-bold text-gray-700 flex items-center">
                        <i class="fas fa-user mr-2 text-tni-500"></i> Nama Lengkap
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-tni-500 focus:border-tni-500 transition-all outline-none"
                        placeholder="Masukkan nama lengkap">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Email -->
                <div class="space-y-2">
                    <label for="email" class="text-sm font-bold text-gray-700 flex items-center">
                        <i class="fas fa-envelope mr-2 text-tni-500"></i> Alamat Email
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-tni-500 focus:border-tni-500 transition-all outline-none"
                        placeholder="contoh@rs-tni.go.id">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Telepon -->
                <div class="space-y-2">
                    <label for="phone" class="text-sm font-bold text-gray-700 flex items-center">
                        <i class="fas fa-phone mr-2 text-tni-500"></i> Nomor Telepon (WA)
                    </label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-tni-500 focus:border-tni-500 transition-all outline-none"
                        placeholder="081234567890">
                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Spacer for layout -->
                <div class="hidden md:block"></div>

                <!-- Password -->
                <div class="space-y-2">
                    <label for="password" class="text-sm font-bold text-gray-700 flex items-center">
                        <i class="fas fa-lock mr-2 text-tni-500"></i> Password
                    </label>
                    <input type="password" name="password" id="password" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-tni-500 focus:border-tni-500 transition-all outline-none"
                        placeholder="••••••••">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Confirm Password -->
                <div class="space-y-2">
                    <label for="password_confirmation" class="text-sm font-bold text-gray-700 flex items-center">
                        <i class="fas fa-shield-alt mr-2 text-tni-500"></i> Konfirmasi Password
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-tni-500 focus:border-tni-500 transition-all outline-none"
                        placeholder="••••••••">
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 flex justify-end">
                <button type="submit" class="bg-gradient-to-r from-tni-700 to-tni-600 hover:from-tni-800 hover:to-tni-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1 flex items-center">
                    <i class="fas fa-save mr-2"></i> Simpan Data Kurir
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
