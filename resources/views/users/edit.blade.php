@extends('layouts.app')

@section('title', 'Edit Personel')
@section('page-title', 'Pembaruan Data Personel')

@section('content')
<div class="max-w-5xl mx-auto space-y-8 animate-fade-in pb-12">
    <!-- Breadcrumbs -->
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
                    <span class="text-tni-700 font-medium">Edit: {{ $user->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Main Form Card -->
    <div class="bg-white rounded-[2.5rem] shadow-2xl border border-gray-100 overflow-hidden">
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-tni-800 to-tni-600 p-8 text-white relative">
            <div class="absolute top-0 right-0 p-8 opacity-10">
                <i class="fas fa-user-pen text-8xl"></i>
            </div>
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-3xl bg-white/20 backdrop-blur-md flex items-center justify-center text-3xl font-bold border border-white/30 shadow-inner">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold">Edit Personel: {{ $user->name }}</h2>
                        <p class="text-tni-100 opacity-90 text-sm mt-1">ID Personel: <span class="font-bold text-gold-400">{{ $user->employee_id ?? 'No ID' }}</span></p>
                    </div>
                </div>
                <div class="px-4 py-1.5 bg-white/10 rounded-full text-[10px] font-bold tracking-widest uppercase border border-white/20 backdrop-blur-sm">
                    Status: {{ $user->is_active ? 'AKTIF' : 'NONAKTIF' }}
                </div>
            </div>
        </div>

        <form action="{{ route('users.update', $user) }}" method="POST" class="p-10">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Identitas & Profesi -->
                <div class="space-y-8">
                    <section>
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest border-b pb-3 mb-6 flex items-center">
                            <i class="fas fa-id-card-clip mr-2 text-tni-600"></i> Identitas Kedinasan
                        </h3>
                        <div class="space-y-5">
                            <div>
                                <label for="employee_id" class="block text-xs font-bold text-gray-500 uppercase mb-2">NIP / ID Pegawai</label>
                                <input type="text" id="employee_id" name="employee_id" value="{{ old('employee_id', $user->employee_id) }}" 
                                    class="w-full px-5 py-4 bg-gray-50 border-gray-200 rounded-2xl text-sm focus:ring-tni-500 focus:border-tni-500 transition-all font-medium">
                                @error('employee_id') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="profession" class="block text-xs font-bold text-gray-500 uppercase mb-2">Profesi / Jabatan <span class="text-red-500">*</span></label>
                                    <select id="profession" name="profession" required class="w-full px-5 py-4 bg-gray-50 border-gray-200 rounded-2xl text-sm focus:ring-tni-500 focus:border-tni-500 transition-all font-bold">
                                        <option value="Dokter" {{ old('profession', $user->profession) == 'Dokter' ? 'selected' : '' }}>Dokter</option>
                                        <option value="Apoteker" {{ old('profession', $user->profession) == 'Apoteker' ? 'selected' : '' }}>Apoteker</option>
                                        <option value="Staff Administrasi" {{ old('profession', $user->profession) == 'Staff Administrasi' ? 'selected' : '' }}>Staff Administrasi</option>
                                        <option value="Kurir" {{ old('profession', $user->profession) == 'Kurir' ? 'selected' : '' }}>Kurir</option>
                                        <option value="IT Support" {{ old('profession', $user->profession) == 'IT Support' ? 'selected' : '' }}>IT Support</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="role" class="block text-xs font-bold text-gray-500 uppercase mb-2">Akses Sistem <span class="text-red-500">*</span></label>
                                    <select id="role" name="role" required class="w-full px-5 py-4 bg-gray-50 border-gray-200 rounded-2xl text-sm focus:ring-tni-500 focus:border-tni-500 transition-all font-bold text-tni-700">
                                        <option value="apoteker" {{ old('role', $user->role) == 'apoteker' ? 'selected' : '' }}>User (Apoteker/Staff)</option>
                                        <option value="kurir" {{ old('role', $user->role) == 'kurir' ? 'selected' : '' }}>Kurir (Mobile Access)</option>
                                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section>
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest border-b pb-3 mb-6 flex items-center">
                            <i class="fas fa-user-circle mr-2 text-tni-600"></i> Profil Personal
                        </h3>
                        <div class="space-y-5">
                            <div>
                                <label for="name" class="block text-xs font-bold text-gray-500 uppercase mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required 
                                    class="w-full px-5 py-4 bg-gray-50 border-gray-200 rounded-2xl text-sm focus:ring-tni-500 focus:border-tni-500 transition-all font-bold">
                                @error('name') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="gender" class="block text-xs font-bold text-gray-500 uppercase mb-2">Jenis Kelamin</label>
                                    <select id="gender" name="gender" class="w-full px-5 py-4 bg-gray-50 border-gray-200 rounded-2xl text-sm focus:ring-tni-500 focus:border-tni-500 transition-all">
                                        <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="phone" class="block text-xs font-bold text-gray-500 uppercase mb-2">No. HP / WhatsApp</label>
                                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" 
                                        class="w-full px-5 py-4 bg-gray-50 border-gray-200 rounded-2xl text-sm focus:ring-tni-500 focus:border-tni-500 transition-all">
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Kontak & Akun -->
                <div class="space-y-8">
                    <section>
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest border-b pb-3 mb-6 flex items-center">
                            <i class="fas fa-map-location-dot mr-2 text-tni-600"></i> Alamat & Domisili
                        </h3>
                        <div>
                            <textarea id="address" name="address" rows="4" class="w-full px-5 py-4 bg-gray-50 border-gray-200 rounded-2xl text-sm focus:ring-tni-500 focus:border-tni-500 transition-all">{{ old('address', $user->address) }}</textarea>
                        </div>
                    </section>

                    <section>
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest border-b pb-3 mb-6 flex items-center">
                            <i class="fas fa-key mr-2 text-tni-600"></i> Kredensial Login
                        </h3>
                        <div class="space-y-5">
                            <div>
                                <label for="email" class="block text-xs font-bold text-gray-500 uppercase mb-2">Email Dinas <span class="text-red-500">*</span></label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required 
                                    class="w-full px-5 py-4 bg-gray-50 border-gray-200 rounded-2xl text-sm focus:ring-tni-500 focus:border-tni-500 transition-all font-medium">
                                @error('email') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div class="p-6 bg-amber-50 rounded-2xl border border-amber-100">
                                <p class="text-xs font-bold text-amber-800 uppercase mb-2 flex items-center gap-2">
                                    <i class="fas fa-info-circle"></i> Ubah Password
                                </p>
                                <p class="text-[11px] text-amber-700 leading-relaxed mb-4">Kosongkan jika tidak ingin mengubah password saat ini.</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="password" class="block text-[10px] font-bold text-amber-900 uppercase mb-1">Password Baru</label>
                                        <input type="password" id="password" name="password" 
                                            class="w-full px-4 py-3 bg-white border-amber-200 rounded-xl text-sm focus:ring-amber-500 focus:border-amber-500 transition-all">
                                    </div>
                                    <div>
                                        <label for="password_confirmation" class="block text-[10px] font-bold text-amber-900 uppercase mb-1">Konfirmasi Password</label>
                                        <input type="password" id="password_confirmation" name="password_confirmation" 
                                            class="w-full px-4 py-3 bg-white border-amber-200 rounded-xl text-sm focus:ring-amber-500 focus:border-amber-500 transition-all">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-16 flex flex-col md:flex-row justify-end items-center gap-4 border-t pt-10">
                <a href="{{ route('users.index') }}" class="px-8 py-4 text-sm font-bold text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest">
                    Batal
                </a>
                <button type="submit" class="w-full md:w-auto px-12 py-4 bg-tni-800 text-white rounded-[1.5rem] hover:bg-black transition-all shadow-xl shadow-tni-200 font-bold uppercase tracking-widest flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection