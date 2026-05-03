@extends('layouts.app')

@section('title', 'Edit Pasien: ' . $patient->name)
@section('page-title', 'Pembaruan Data')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 animate-fade-in pb-12">
    <!-- Header & Actions -->
    <div class="flex items-center justify-between">
        <a href="{{ route('patients.show', $patient) }}" class="text-tni-600 hover:text-tni-800 flex items-center font-bold transition-colors text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Batal & Kembali
        </a>
    </div>

    <!-- Main Form Card -->
    <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
        <!-- Banner Header -->
        <div class="bg-gradient-to-r from-tni-800 to-gold-600 p-10 text-white relative">
            <div class="absolute top-0 right-0 p-10 opacity-10">
                <i class="fas fa-user-edit text-8xl"></i>
            </div>
            <div class="relative z-10">
                <h2 class="text-3xl font-black tracking-tight">Perbarui Data Pasien</h2>
                <p class="text-tni-100 opacity-80 mt-2 font-medium">Lakukan perubahan pada informasi rekam medis atau kontak pasien.</p>
            </div>
        </div>

        <form action="{{ route('patients.update', $patient) }}" method="POST" class="p-10">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <!-- Left Section: Basic Info -->
                <div class="space-y-8">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] flex items-center gap-2">
                        <span class="w-2 h-2 bg-tni-600 rounded-full"></span> Identitas Personal
                    </h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label for="name" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Nama Lengkap Pasien <span class="text-red-500">*</span></label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 pl-5 flex items-center text-gray-400 group-focus-within:text-tni-600 transition-colors">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" id="name" name="name" value="{{ old('name', $patient->name) }}" required 
                                    class="w-full pl-12 pr-6 py-4 bg-gray-50/50 border border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all shadow-inner"
                                    placeholder="Nama sesuai KTP">
                            </div>
                            @error('name') <p class="mt-2 text-[10px] text-red-600 font-bold uppercase">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="gender" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Gender <span class="text-red-500">*</span></label>
                                <select id="gender" name="gender" required 
                                    class="w-full px-5 py-4 bg-gray-50/50 border border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all shadow-inner appearance-none">
                                    <option value="male" {{ old('gender', $patient->gender) == 'male' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="female" {{ old('gender', $patient->gender) == 'female' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label for="date_of_birth" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Tgl Lahir <span class="text-red-500">*</span></label>
                                <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $patient->date_of_birth ? $patient->date_of_birth->format('Y-m-d') : '') }}" required 
                                    class="w-full px-5 py-4 bg-gray-50/50 border border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all shadow-inner">
                            </div>
                        </div>

                        <div>
                            <label for="phone" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Nomor Telepon <span class="text-red-500">*</span></label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 pl-5 flex items-center text-gray-400 group-focus-within:text-tni-600 transition-colors">
                                    <i class="fas fa-phone"></i>
                                </span>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone', $patient->phone) }}" required 
                                    class="w-full pl-12 pr-6 py-4 bg-gray-50/50 border border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all shadow-inner"
                                    placeholder="0812xxxxxxxx">
                            </div>
                            @error('phone') <p class="mt-2 text-[10px] text-red-600 font-bold uppercase">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Right Section: Medical & Address -->
                <div class="space-y-8">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] flex items-center gap-2">
                        <span class="w-2 h-2 bg-gold-500 rounded-full"></span> Data Medis & Lokasi
                    </h3>

                    <div class="space-y-6">
                        <div>
                            <label for="address" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Alamat Domisili <span class="text-red-500">*</span></label>
                            <textarea id="address" name="address" rows="3" required 
                                class="w-full px-5 py-4 bg-gray-50/50 border border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all shadow-inner"
                                placeholder="Alamat lengkap pengiriman...">{{ old('address', $patient->address) }}</textarea>
                        </div>

                        <div>
                            <label for="medical_condition" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Kondisi Medis (Opsional)</label>
                            <textarea id="medical_condition" name="medical_condition" rows="3" 
                                class="w-full px-5 py-4 bg-gray-50/50 border border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all shadow-inner"
                                placeholder="Alergi obat, penyakit kronis, dll...">{{ old('medical_condition', $patient->medical_condition) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="mt-12 pt-10 border-t border-gray-100 flex flex-col sm:flex-row justify-end gap-4">
                <a href="{{ route('patients.show', $patient) }}" class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 transition-colors flex items-center justify-center">
                    Batal
                </a>
                <button type="submit" class="px-12 py-4 bg-gradient-to-br from-gold-500 to-gold-700 text-tni-900 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-2xl shadow-gold-200 hover:scale-[1.02] transition-all flex items-center justify-center gap-3">
                    <i class="fas fa-check-circle"></i> Perbarui Data
                </button>
            </div>
        </form>
    </div>
</div>
@endsection