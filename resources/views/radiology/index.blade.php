@extends('layouts.app')

@section('title', 'Hasil Radiologi')
@section('page-title', 'Hasil Radiologi')
@section('page-subtitle', 'Manajemen data hasil pemindaian dan ekspertise radiologi pasien')

@section('content')
<div class="space-y-8 animate-fade-in pb-12">
    <!-- Header & Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Daftar Laporan Radiologi</h2>
            <p class="text-gray-500 text-sm mt-1">Unggah file JPG/PNG, kelola pembacaan dokter, dan kirim laporan hasil radiologi ke pasien.</p>
        </div>
        
        <div class="flex flex-wrap gap-3 items-center">
            <form action="{{ route('radiology.index') }}" method="GET" class="relative group flex items-center gap-2">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / kode pasien..." 
                    class="pl-12 pr-6 py-3 bg-white border border-gray-100 rounded-2xl text-sm focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all w-64 shadow-sm font-medium">
                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-tni-600 transition-colors"></i>
                @if(request('search') || request('status'))
                    <a href="{{ route('radiology.index') }}" class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-2xl text-sm font-bold transition">Reset</a>
                @endif
            </form>
            @if(auth()->user()->isAdmin() || auth()->user()->isApoteker() || auth()->user()->isOperator())
                <a href="{{ route('radiology.create') }}" class="inline-flex items-center px-6 py-3 bg-tni-800 text-white rounded-2xl hover:bg-black transition shadow-lg font-bold group">
                    <i class="fas fa-plus-circle mr-2 group-hover:scale-110 transition-transform"></i> Upload Scan Baru
                </a>
                <a href="{{ route('radiology.chat-center') }}" class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white rounded-2xl hover:bg-emerald-700 transition shadow-lg font-bold">
                    <i class="fas fa-comments mr-2"></i> Simulasi Chat
                </a>
            @endif
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-tni-100 text-tni-700 flex items-center justify-center text-2xl shadow-inner">
                <i class="fas fa-x-ray"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Pemeriksaan</p>
                <p class="text-2xl font-black text-gray-900">{{ $results->total() }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-2xl shadow-inner animate-pulse">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Menunggu Diagnosa</p>
                <p class="text-2xl font-black text-gray-900">{{ \App\Models\RadiologyResult::where('status', 'pending')->count() }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-green-100 text-green-700 flex items-center justify-center text-2xl shadow-inner">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Selesai Ekspertise</p>
                <p class="text-2xl font-black text-gray-900">{{ \App\Models\RadiologyResult::where('status', 'completed')->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1">
        <a href="{{ route('radiology.index', ['status' => '', 'search' => request('search')]) }}" 
           class="px-5 py-2.5 rounded-full text-xs font-bold transition-all {{ !request('status') ? 'bg-tni-800 text-white shadow-md' : 'bg-white text-gray-500 border border-gray-100 hover:bg-gray-50' }}">
            Semua Data
        </a>
        <a href="{{ route('radiology.index', ['status' => 'pending', 'search' => request('search')]) }}" 
           class="px-5 py-2.5 rounded-full text-xs font-bold transition-all {{ request('status') == 'pending' ? 'bg-amber-500 text-white shadow-md' : 'bg-white text-gray-500 border border-gray-100 hover:bg-gray-50' }}">
            Menunggu Diagnosa
        </a>
        <a href="{{ route('radiology.index', ['status' => 'completed', 'search' => request('search')]) }}" 
           class="px-5 py-2.5 rounded-full text-xs font-bold transition-all {{ request('status') == 'completed' ? 'bg-green-600 text-white shadow-md' : 'bg-white text-gray-500 border border-gray-100 hover:bg-gray-50' }}">
            Selesai
        </a>
    </div>

    <!-- Scans Table -->
    <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-8 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pasien</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Informasi Pengunggahan</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Hasil Ekspertise Dokter</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status Pengiriman</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($results as $result)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <!-- Patient Identity -->
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center font-bold text-lg shadow-lg">
                                    <i class="fas fa-file-medical-alt"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800 group-hover:text-tni-700 transition-colors">{{ $result->patient->name }}</p>
                                    <p class="text-[11px] text-tni-600 font-bold uppercase tracking-wider">{{ $result->patient->patient_code ?? 'NO-CODE' }}</p>
                                </div>
                            </div>
                        </td>

                        <!-- Upload Metadata -->
                        <td class="px-6 py-6">
                            <div class="text-sm">
                                <p class="font-bold text-gray-700">{{ $result->operator->name }}</p>
                                <p class="text-xs text-gray-400 flex items-center gap-1 mt-0.5">
                                    <i class="far fa-calendar-alt"></i> {{ $result->created_at->format('d M Y, H:i') }}
                                </p>
                            </div>
                        </td>

                        <!-- Doctor Reading -->
                        <td class="px-6 py-6">
                            @if($result->status === 'completed')
                                <div class="text-sm">
                                    <p class="font-bold text-gray-800">{{ $result->doctor->name ?? 'Dokter' }}</p>
                                    <p class="text-[11px] text-green-600 font-bold uppercase tracking-wider flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Sudah Dibaca
                                    </p>
                                </div>
                            @else
                                <span class="px-3 py-1 bg-amber-50 text-amber-700 border border-amber-100 rounded-full text-[10px] font-bold uppercase tracking-wider flex items-center w-fit gap-1 animate-pulse">
                                    <i class="fas fa-spinner fa-spin text-[8px]"></i> Menunggu Diagnosa
                                </span>
                            @endif
                        </td>

                        <!-- Shipping Channel Status -->
                        <td class="px-6 py-6">
                            @if($result->sent_via)
                                <div class="flex flex-col gap-1">
                                    @if($result->sent_via === 'whatsapp' || $result->sent_via === 'both')
                                        <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full text-[10px] font-bold uppercase tracking-wider w-fit flex items-center gap-1">
                                            <i class="fab fa-whatsapp"></i> WhatsApp
                                        </span>
                                    @endif
                                    @if($result->sent_via === 'email' || $result->sent_via === 'both')
                                        <span class="px-3 py-1 bg-blue-50 text-blue-700 border border-blue-100 rounded-full text-[10px] font-bold uppercase tracking-wider w-fit flex items-center gap-1">
                                            <i class="far fa-envelope"></i> Gmail
                                        </span>
                                    @endif
                                    <p class="text-[10px] text-gray-400 mt-0.5">Kirim: {{ $result->sent_at ? $result->sent_at->format('d M, H:i') : '' }}</p>
                                </div>
                            @else
                                <span class="px-3 py-1 bg-gray-50 text-gray-400 border border-gray-100 rounded-full text-[10px] font-bold uppercase tracking-wider w-fit">
                                    Belum Dikirim
                                </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="px-8 py-6 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('radiology.show', $result->id) }}" class="p-2.5 bg-gray-50 text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl transition-all" title="Buka Scan & Detail">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>

                                <!-- Doctor Actions -->
                                @if(auth()->user()->isDokter() || auth()->user()->isAdmin())
                                    <a href="{{ route('radiology.edit', $result->id) }}" class="px-4 py-2 bg-tni-800 text-white rounded-xl hover:bg-black transition-all text-xs font-bold flex items-center gap-1" title="Input Hasil Baca">
                                        <i class="fas fa-stethoscope"></i> {{ $result->status === 'completed' ? 'Edit Diagnosa' : 'Input Diagnosa' }}
                                    </a>
                                @endif

                                <!-- Operator / Admin Actions -->
                                @if(auth()->user()->isAdmin() || auth()->user()->isApoteker() || auth()->user()->isOperator())
                                    <a href="{{ route('radiology.edit', $result->id) }}" class="p-2.5 bg-gray-50 text-gray-400 hover:bg-gold-50 hover:text-gold-700 rounded-xl transition-all" title="Update File Scan">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>

                                    @if($result->status === 'completed')
                                        <!-- Cetak PDF -->
                                        <a href="{{ route('radiology.public-report.pdf', $result->share_token) }}" target="_blank" class="p-2.5 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-800 rounded-xl transition-all" title="Cetak Laporan PDF">
                                            <i class="fas fa-print text-sm"></i>
                                        </a>

                                        <!-- WhatsApp Trigger -->
                                        <form action="{{ route('radiology.send', [$result->id, 'whatsapp']) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2.5 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 hover:text-emerald-800 rounded-xl transition-all" title="Kirim Laporan via WhatsApp">
                                                <i class="fab fa-whatsapp text-sm"></i>
                                            </button>
                                        </form>

                                        <!-- Gmail Trigger -->
                                        <form action="{{ route('radiology.send', [$result->id, 'email']) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2.5 bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-800 rounded-xl transition-all" title="Kirim Laporan via Gmail">
                                                <i class="far fa-envelope text-sm"></i>
                                            </button>
                                        </form>
                                        
                                        <!-- Chat Room Simulator link -->
                                        <a href="{{ route('radiology.chat-center') }}?result_id={{ $result->id }}" class="p-2.5 bg-purple-50 text-purple-600 hover:bg-purple-100 rounded-xl transition-all" title="Simulasi Chat Pasien">
                                            <i class="far fa-comments text-sm"></i>
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-folder-open text-3xl text-gray-300"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-800">Belum Ada Laporan Radiologi</h3>
                                <p class="text-gray-500 text-sm max-w-xs mt-1">Silakan unggah hasil pemindaian JPG/PNG pasien baru.</p>
                                @if(auth()->user()->isAdmin() || auth()->user()->isApoteker() || auth()->user()->isOperator())
                                    <a href="{{ route('radiology.create') }}" class="mt-6 px-6 py-3 bg-tni-100 text-tni-700 rounded-2xl font-bold hover:bg-tni-200 transition">
                                        Upload Scan Sekarang
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($results->hasPages())
        <div class="px-8 py-6 border-t border-gray-50 bg-gray-50/30">
            {{ $results->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
