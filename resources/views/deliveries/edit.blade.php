@extends('layouts.app')

@section('title', 'Edit Pengantaran #' . $delivery->id)
@section('page-title', 'Pembaruan Pengantaran')

@section('content')
<div class="max-w-5xl mx-auto space-y-8 animate-fade-in pb-12">
    <!-- Header & Actions -->
    <div class="flex items-center justify-between">
        <a href="{{ route('deliveries.show', $delivery) }}" class="text-tni-600 hover:text-tni-800 flex items-center font-bold transition-colors text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Batal & Kembali
        </a>
    </div>

    <!-- Main Form Card -->
    <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
        <!-- Banner Header -->
        <div class="bg-gradient-to-r from-tni-800 to-gold-600 p-10 text-white relative">
            <div class="absolute top-0 right-0 p-10 opacity-10">
                <i class="fas fa-truck-ramp-box text-8xl"></i>
            </div>
            <div class="relative z-10">
                <h2 class="text-3xl font-black tracking-tight">Edit Detail Pengantaran</h2>
                <p class="text-tni-100 opacity-80 mt-2 font-medium">Lakukan perubahan pada jadwal, status, atau penugasan kurir.</p>
            </div>
        </div>

        <form action="{{ route('deliveries.update', $delivery) }}" method="POST" class="p-10">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                <!-- Left Section: Basic Info & Logistics -->
                <div class="space-y-8">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] flex items-center gap-2">
                        <span class="w-2 h-2 bg-tni-600 rounded-full"></span> Logistik & Alamat
                    </h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label for="delivery_date" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Tanggal Pengantaran <span class="text-red-500">*</span></label>
                            <input type="date" id="delivery_date" name="delivery_date" value="{{ old('delivery_date', $delivery->delivery_date->format('Y-m-d')) }}" required 
                                class="w-full px-5 py-4 bg-gray-50/50 border border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all shadow-inner">
                        </div>

                        <div>
                            <label for="delivery_address" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Alamat Lengkap <span class="text-red-500">*</span></label>
                            <textarea id="delivery_address" name="delivery_address" rows="4" required 
                                class="w-full px-5 py-4 bg-gray-50/50 border border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all shadow-inner">{{ old('delivery_address', $delivery->delivery_address) }}</textarea>
                        </div>

                        <div>
                            <label for="notes" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Catatan</label>
                            <textarea id="notes" name="notes" rows="2" 
                                class="w-full px-5 py-4 bg-gray-50/50 border border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all shadow-inner">{{ old('notes', $delivery->notes) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Right Section: Status & Assignments -->
                <div class="space-y-8">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] flex items-center gap-2">
                        <span class="w-2 h-2 bg-gold-500 rounded-full"></span> Status & Penugasan
                    </h3>

                    <div class="space-y-6">
                        <div>
                            <label for="status" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Status Pengiriman <span class="text-red-500">*</span></label>
                            <select id="status" name="status" required 
                                class="w-full px-5 py-4 bg-gray-50/50 border border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all shadow-inner appearance-none">
                                <option value="pending" {{ old('status', $delivery->status) == 'pending' ? 'selected' : '' }}>Menunggu (Pending)</option>
                                <option value="on_delivery" {{ old('status', $delivery->status) == 'on_delivery' ? 'selected' : '' }}>Sedang Diantar (On Delivery)</option>
                                <option value="delivered" {{ old('status', $delivery->status) == 'delivered' ? 'selected' : '' }}>Terkirim (Delivered)</option>
                                <option value="failed" {{ old('status', $delivery->status) == 'failed' ? 'selected' : '' }}>Gagal (Failed)</option>
                            </select>
                        </div>

                        <div>
                            <label for="priority" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Prioritas <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="cursor-pointer">
                                    <input type="radio" name="priority" value="normal" {{ old('priority', $delivery->priority) == 'normal' ? 'checked' : '' }} class="hidden peer">
                                    <div class="text-center py-4 rounded-2xl border-2 border-gray-50 text-gray-400 peer-checked:border-tni-600 peer-checked:bg-tni-50 peer-checked:text-tni-700 font-bold text-xs transition-all uppercase tracking-widest">
                                        Normal
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="priority" value="urgent" {{ old('priority', $delivery->priority) == 'urgent' ? 'checked' : '' }} class="hidden peer">
                                    <div class="text-center py-4 rounded-2xl border-2 border-gray-50 text-gray-400 peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-600 font-bold text-xs transition-all uppercase tracking-widest">
                                        Urgent
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label for="courier_id" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Personel Kurir</label>
                            <select id="courier_id" name="courier_id" 
                                class="w-full px-5 py-4 bg-gray-50/50 border border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all shadow-inner appearance-none">
                                <option value="">Belum Ditugaskan</option>
                                @foreach($couriers as $courier)
                                <option value="{{ $courier->id }}" {{ old('courier_id', $delivery->courier_id) == $courier->id ? 'selected' : '' }}>
                                    {{ $courier->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="mt-12 pt-10 border-t border-gray-100 flex flex-col sm:flex-row justify-end gap-4">
                <a href="{{ route('deliveries.show', $delivery) }}" class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 transition-colors flex items-center justify-center">
                    Batal
                </a>
                <button type="submit" class="px-12 py-4 bg-gradient-to-br from-gold-500 to-gold-700 text-tni-900 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-2xl shadow-gold-200 hover:scale-[1.02] transition-all flex items-center justify-center gap-3">
                    <i class="fas fa-save"></i> Perbarui Pengantaran
                </button>
            </div>
        </form>
    </div>
</div>
@endsection