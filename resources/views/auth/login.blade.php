@extends('layouts.auth')

@section('title', 'Login - MediTrack')

@section('content')
    <!-- Flash Messages -->
    @if(session('error'))
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
        {{ session('error') }}
    </div>
    @endif

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
        {{ session('success') }}
    </div>
    @endif

    <div class="space-y-6">
        <!-- System Alert -->
        <div class="p-4 bg-[#1e481e] rounded-2xl border border-[#2d722d] shadow-inner flex items-start gap-3">
            <div class="w-8 h-8 rounded-full bg-[#f9ac13]/20 flex-shrink-0 flex items-center justify-center">
                <i class="fas fa-shield-halved text-[#f9ac13] text-xs"></i>
            </div>
            <p class="text-[10px] text-[#e0f0e0] font-bold leading-relaxed uppercase tracking-wider">
                Sistem Terenkripsi. Pastikan Anda masuk menggunakan kredensial dinas yang valid.
            </p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            
            <!-- Email -->
            <div>
                <label for="email" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2 ml-1">
                    Email Dinas
                </label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-gray-400 group-focus-within:text-[#1e481e] transition-colors"></i>
                    </div>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="w-full pl-12 pr-5 py-4 bg-gray-100/50 border border-transparent rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#1e481e]/20 focus:border-[#1e481e] focus:bg-white transition-all text-sm font-bold text-gray-800 placeholder-gray-400 shadow-inner"
                        placeholder="Masukkan Email"
                    >
                </div>
                @error('email')
                    <p class="mt-2 text-[10px] text-red-600 font-bold uppercase tracking-wider">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2 ml-1">
                    Sandi Keamanan
                </label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400 group-focus-within:text-[#1e481e] transition-colors"></i>
                    </div>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="w-full pl-12 pr-5 py-4 bg-gray-100/50 border border-transparent rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#1e481e]/20 focus:border-[#1e481e] focus:bg-white transition-all text-sm font-bold text-gray-800 placeholder-gray-400 shadow-inner"
                        placeholder="••••••••"
                    >
                </div>
                @error('password')
                    <p class="mt-2 text-[10px] text-red-600 font-bold uppercase tracking-wider">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between px-1">
                <label class="flex items-center cursor-pointer group">
                    <div class="relative">
                        <input type="checkbox" id="remember" name="remember" class="sr-only">
                        <div class="w-10 h-5 bg-gray-200 rounded-full shadow-inner group-focus-within:ring-2 group-focus-within:ring-[#f9ac13]/50 transition-all"></div>
                        <div class="dot absolute left-1 top-1 bg-white w-3 h-3 rounded-full transition-transform"></div>
                    </div>
                    <span class="ml-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Ingat Sesi</span>
                </label>
                
                @if (Route::has('password.request'))
                <a href="#" class="text-[10px] font-black text-[#1e481e] hover:text-[#f9ac13] uppercase tracking-widest transition-colors">
                    Reset Sandi?
                </a>
                @endif
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full bg-gradient-to-br from-[#1e481e] to-black hover:from-black hover:to-[#2d722d] text-white font-black py-5 px-4 rounded-[1.5rem] shadow-2xl shadow-green-900/20 focus:outline-none focus:ring-2 focus:ring-[#f9ac13] focus:ring-offset-2 transition-all uppercase tracking-[0.2em] text-xs flex items-center justify-center gap-3 group"
                >
                    Login <i class="fas fa-chevron-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                </button>
            </div>
        </form>
    </div>

    <style>
        input:checked ~ .dot {
            transform: translateX(20px);
            background-color: #f9ac13;
        }
        input:checked ~ div:first-child {
            background-color: #1a2e1a;
        }
    </style>
@endsection