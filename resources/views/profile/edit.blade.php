@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Pengaturan Akun')

@section('content')
<div class="max-w-5xl mx-auto space-y-8 animate-fade-in pb-12">
    <!-- Profile Header Card -->
    <div class="bg-white rounded-[2.5rem] shadow-2xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-tni-900 via-tni-800 to-tni-700 p-10 text-white relative">
            <div class="absolute top-0 right-0 p-10 opacity-10">
                <i class="fas fa-user-gear text-9xl"></i>
            </div>
            <div class="relative z-10 flex flex-col md:flex-row items-center gap-8">
                <div class="relative group">
                    <div class="w-28 h-28 rounded-3xl bg-white/20 backdrop-blur-md flex items-center justify-center text-4xl font-bold border border-white/30 shadow-2xl overflow-hidden">
                        @if(auth()->user()->profile_photo)
                            <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="Profile Photo" class="w-full h-full object-cover">
                        @else
                            {{ substr(auth()->user()->name, 0, 1) }}
                        @endif
                    </div>
                </div>
                <div>
                    <h2 class="text-3xl font-bold">{{ auth()->user()->name }}</h2>
                    <p class="text-tni-100 opacity-90 text-sm mt-1">{{ auth()->user()->profession ?? 'Personel Meditrack' }} • <span class="uppercase font-bold text-gold-400">{{ auth()->user()->role }}</span></p>
                    <div class="mt-4 flex gap-4">
                        <span class="px-3 py-1 bg-white/10 rounded-full text-[10px] font-bold tracking-widest border border-white/20">
                            NIP: {{ auth()->user()->employee_id ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" class="p-10" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Data Personal -->
                <div class="space-y-8">
                    <section>
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest border-b pb-3 mb-6 flex items-center">
                            <i class="fas fa-user-circle mr-2 text-tni-600"></i> Informasi Personal
                        </h3>
                        <div class="space-y-5">
                            <div>
                                <label for="name" class="block text-xs font-bold text-gray-500 uppercase mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required 
                                    class="w-full px-5 py-4 bg-gray-50 border-gray-200 rounded-2xl text-sm focus:ring-tni-500 focus:border-tni-500 transition-all font-bold">
                                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="profile_photo" class="block text-xs font-bold text-gray-500 uppercase mb-2">Foto Profil (JPEG/PNG, Max 2MB)</label>
                                <input type="file" id="profile_photo" name="profile_photo" accept="image/*" 
                                    class="w-full px-5 py-3 bg-gray-50 border-gray-200 rounded-2xl text-sm focus:ring-tni-500 focus:border-tni-500 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-tni-100 file:text-tni-700 hover:file:bg-tni-200 cursor-pointer">
                                @error('profile_photo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="profession" class="block text-xs font-bold text-gray-500 uppercase mb-2">Profesi / Jabatan</label>
                                    <select id="profession" name="profession" class="w-full px-5 py-4 bg-gray-50 border-gray-200 rounded-2xl text-sm focus:ring-tni-500 focus:border-tni-500 transition-all font-bold">
                                        <option value="Dokter" {{ old('profession', auth()->user()->profession) == 'Dokter' ? 'selected' : '' }}>Dokter</option>
                                        <option value="Apoteker" {{ old('profession', auth()->user()->profession) == 'Apoteker' ? 'selected' : '' }}>Apoteker</option>
                                        <option value="Staff Administrasi" {{ old('profession', auth()->user()->profession) == 'Staff Administrasi' ? 'selected' : '' }}>Staff Administrasi</option>
                                        <option value="Kurir" {{ old('profession', auth()->user()->profession) == 'Kurir' ? 'selected' : '' }}>Kurir</option>
                                        <option value="IT Support" {{ old('profession', auth()->user()->profession) == 'IT Support' ? 'selected' : '' }}>IT Support</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="gender" class="block text-xs font-bold text-gray-500 uppercase mb-2">Jenis Kelamin</label>
                                    <select id="gender" name="gender" class="w-full px-5 py-4 bg-gray-50 border-gray-200 rounded-2xl text-sm focus:ring-tni-500 focus:border-tni-500 transition-all">
                                        <option value="male" {{ old('gender', auth()->user()->gender) == 'male' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="female" {{ old('gender', auth()->user()->gender) == 'female' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="address" class="block text-xs font-bold text-gray-500 uppercase mb-2">Alamat Domisili</label>
                                <textarea id="address" name="address" rows="3" class="w-full px-5 py-4 bg-gray-50 border-gray-200 rounded-2xl text-sm focus:ring-tni-500 focus:border-tni-500 transition-all">{{ old('address', auth()->user()->address) }}</textarea>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Kontak & Keamanan -->
                <div class="space-y-8">
                    <section>
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest border-b pb-3 mb-6 flex items-center">
                            <i class="fas fa-shield-halved mr-2 text-tni-600"></i> Kontak & Keamanan
                        </h3>
                        <div class="space-y-5">
                            <div>
                                <label for="email" class="block text-xs font-bold text-gray-500 uppercase mb-2">Email Dinas <span class="text-red-500">*</span></label>
                                <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required 
                                    class="w-full px-5 py-4 bg-gray-50 border-gray-200 rounded-2xl text-sm focus:ring-tni-500 focus:border-tni-500 transition-all font-medium">
                                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-xs font-bold text-gray-500 uppercase mb-2">No. HP / WhatsApp</label>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}" 
                                    class="w-full px-5 py-4 bg-gray-50 border-gray-200 rounded-2xl text-sm focus:ring-tni-500 focus:border-tni-500 transition-all">
                            </div>

                            <!-- Password Section -->
                            <div class="p-6 bg-amber-50 rounded-3xl border border-amber-100 mt-6">
                                <p class="text-[10px] font-bold text-amber-800 uppercase mb-4 flex items-center gap-2">
                                    <i class="fas fa-lock"></i> Ganti Password
                                </p>
                                <div class="space-y-4">
                                    <div>
                                        <label for="current_password" class="block text-[10px] font-bold text-amber-700 uppercase mb-1">Password Saat Ini</label>
                                        <input type="password" id="current_password" name="current_password" 
                                            class="w-full px-4 py-3 bg-white border-amber-200 rounded-xl text-sm focus:ring-amber-500 focus:border-amber-500 transition-all">
                                        @error('current_password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="password" class="block text-[10px] font-bold text-amber-700 uppercase mb-1">Password Baru</label>
                                            <input type="password" id="password" name="password" 
                                                class="w-full px-4 py-3 bg-white border-amber-200 rounded-xl text-sm focus:ring-amber-500 focus:border-amber-500 transition-all">
                                        </div>
                                        <div>
                                            <label for="password_confirmation" class="block text-[10px] font-bold text-amber-700 uppercase mb-1">Konfirmasi</label>
                                            <input type="password" id="password_confirmation" name="password_confirmation" 
                                                class="w-full px-4 py-3 bg-white border-amber-200 rounded-xl text-sm focus:ring-amber-500 focus:border-amber-500 transition-all">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-16 flex justify-end items-center gap-4 border-t pt-10">
                <a href="{{ route('dashboard') }}" class="px-8 py-4 text-sm font-bold text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest">
                    Kembali
                </a>
                <button type="submit" class="px-12 py-4 bg-tni-800 text-white rounded-2xl hover:bg-black transition-all shadow-xl shadow-tni-200 font-bold uppercase tracking-widest flex items-center gap-2">
                    <i class="fas fa-save"></i> Perbarui Profil
                </button>
            </div>
        </form>
    </div>
</div>
@endsection