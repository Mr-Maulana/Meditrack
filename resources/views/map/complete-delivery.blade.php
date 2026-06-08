@extends('layouts.app')

@section('title', 'Penyelesaian Pengantaran')
@section('page-title', 'Konfirmasi Serah Terima')

@section('styles')
<style>
    .complete-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.5);
    }
    
    .signature-container {
        position: relative;
        width: 100%;
        height: 200px;
        background-color: #f9fafb;
        border: 2px dashed #e5e7eb;
        border-radius: 1rem;
        overflow: hidden;
    }

    #signature-pad {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        cursor: crosshair;
    }
</style>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-8 animate-fade-in pb-12">
    <!-- Header Card -->
    <div class="bg-gradient-to-br from-tni-800 to-tni-900 rounded-[2.5rem] p-8 md:p-10 text-white relative overflow-hidden shadow-2xl">
        <div class="absolute top-0 right-0 p-8 opacity-10">
            <i class="fas fa-file-signature text-8xl rotate-12"></i>
        </div>
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-4">
                <span class="px-4 py-1.5 bg-gold-500 text-tni-900 rounded-full text-[10px] font-black uppercase tracking-widest">Langkah Akhir</span>
                <span class="text-tni-300 font-bold text-xs">ID Pengantaran: #{{ str_pad($delivery->id, 8, '0', STR_PAD_LEFT) }}</span>
            </div>
            <h2 class="text-3xl font-black tracking-tight">Dokumentasi Penyerahan</h2>
            <p class="text-tni-100 opacity-80 mt-2 font-medium">Lengkapi form serah terima di bawah ini untuk menyelesaikan tugas pengantaran.</p>
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="complete-card rounded-[2.5rem] shadow-xl overflow-hidden">
        <form id="deliveryCompletionForm" class="p-8 md:p-10 space-y-8">
            @csrf
            <input type="hidden" name="status" id="delivery-status-input" value="delivered">

            <!-- Section 1: Receiver Details -->
            <div class="space-y-6">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] flex items-center gap-2">
                    <span class="w-2 h-2 bg-blue-500 rounded-full"></span> Informasi Penerima Obat
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="receiver-info-fields">
                    <div>
                        <label for="recipient_name" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Nama Penerima <span class="text-red-500">*</span></label>
                        <input type="text" name="recipient_name" id="recipient_name" required
                            class="w-full px-5 py-4 bg-gray-50/50 border border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all shadow-inner"
                            placeholder="Nama penerima obat">
                    </div>

                    <div>
                        <label for="recipient_relation" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Hubungan Penerima <span class="text-red-500">*</span></label>
                        <select name="recipient_relation" id="recipient_relation" required
                            class="w-full px-5 py-4 bg-gray-50/50 border border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all shadow-inner appearance-none">
                            <option value="">Pilih Hubungan...</option>
                            <option value="pasien">Pasien Sendiri</option>
                            <option value="keluarga">Keluarga Kandung</option>
                            <option value="teman">Teman / Kerabat</option>
                            <option value="tetangga">Tetangga</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="recipient_phone" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Nomor Telepon Penerima (Opsional)</label>
                        <input type="tel" name="recipient_phone" id="recipient_phone"
                            class="w-full px-5 py-4 bg-gray-50/50 border border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all shadow-inner"
                            placeholder="0812xxxxxxxx">
                    </div>
                </div>
            </div>

            <!-- Section 2: Medication Confirmation -->
            <div class="space-y-6">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] flex items-center gap-2">
                    <span class="w-2 h-2 bg-green-500 rounded-full"></span> Daftar Obat & Verifikasi
                </h3>
                
                <div class="bg-green-50/50 rounded-2xl p-6 border border-green-100/50 space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-green-500 text-white rounded-xl flex items-center justify-center text-lg shrink-0">
                            <i class="fas fa-pills"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-green-700 uppercase tracking-wider mb-1">Informasi Resep</p>
                            <p class="text-sm font-bold text-gray-800">
                                @if($delivery->prescription)
                                    {{ $delivery->prescription->medication_name }}
                                @else
                                    Resep Obat Umum
                                @endif
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                @if($delivery->prescription)
                                    Dosis: {{ $delivery->prescription->dosage }} | Frekuensi: {{ $delivery->prescription->frequency }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <label class="flex items-center gap-3 cursor-pointer pt-2 mt-2 border-t border-green-100">
                        <input type="checkbox" name="medicine_confirmed" required
                            class="w-5 h-5 rounded-lg border-green-300 text-green-600 focus:ring-green-500/20 focus:ring-offset-0">
                        <span class="text-xs font-bold text-green-800">Saya telah menyerahkan obat sesuai dengan dosis dan instruksi resep di atas.</span>
                    </label>
                </div>
            </div>

            <!-- Section 3: Failure Options (Hidden by Default) -->
            <div class="bg-red-50/50 rounded-3xl p-6 border border-red-100/50 space-y-6 hidden" id="failure-option-section">
                <h4 class="text-sm font-black text-red-800 flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i> Detail Kegagalan Pengantaran
                </h4>
                
                <div class="space-y-4">
                    <label class="block text-[10px] font-bold text-red-700 uppercase tracking-widest mb-1">Alasan Utama Kegagalan</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="flex items-center gap-3 p-4 bg-white rounded-2xl border border-red-100 cursor-pointer hover:bg-red-50/30 transition-colors">
                            <input type="radio" name="failure_reason" value="pasien_tidak_ada" class="w-4 h-4 text-red-600 focus:ring-red-500/20">
                            <span class="text-xs font-bold text-gray-700">Pasien tidak ada</span>
                        </label>
                        <label class="flex items-center gap-3 p-4 bg-white rounded-2xl border border-red-100 cursor-pointer hover:bg-red-50/30 transition-colors">
                            <input type="radio" name="failure_reason" value="alamat_salah" class="w-4 h-4 text-red-600 focus:ring-red-500/20">
                            <span class="text-xs font-bold text-gray-700">Alamat tidak ketemu</span>
                        </label>
                        <label class="flex items-center gap-3 p-4 bg-white rounded-2xl border border-red-100 cursor-pointer hover:bg-red-50/30 transition-colors">
                            <input type="radio" name="failure_reason" value="lainnya" class="w-4 h-4 text-red-600 focus:ring-red-500/20">
                            <span class="text-xs font-bold text-gray-700">Lainnya</span>
                        </label>
                    </div>

                    <div class="hidden" id="failure-notes-wrapper">
                        <label for="failure_notes" class="block text-[10px] font-bold text-red-700 uppercase tracking-widest mb-3 ml-1">Keterangan Tambahan Kegagalan</label>
                        <textarea name="failure_notes" id="failure_notes" rows="3"
                            class="w-full px-5 py-4 bg-white border border-red-200 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all shadow-inner"
                            placeholder="Jelaskan detail alasan kegagalan pengiriman obat..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Section 4: Documentation (Photo & Signature) -->
            <div class="space-y-6" id="documentation-section">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] flex items-center gap-2">
                    <span class="w-2 h-2 bg-yellow-500 rounded-full"></span> Bukti Penyerahan & Tanda Tangan
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Photo Upload -->
                    <div class="space-y-3">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest ml-1">Foto Bukti Penyerahan <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <input type="file" name="proof_image" id="proof_image" accept="image/*" capture="environment" class="hidden">
                            <label for="proof_image" class="cursor-pointer block">
                                <div class="border-2 border-dashed border-gray-200 hover:border-tni-500 rounded-2xl p-8 text-center bg-gray-50/30 hover:bg-white transition-all shadow-inner">
                                    <i class="fas fa-camera text-4xl text-gray-400 group-hover:text-tni-600 mb-3 transition-colors"></i>
                                    <p class="text-xs font-black text-gray-700 uppercase tracking-wider">Ambil Foto Kamera</p>
                                    <p class="text-[10px] text-gray-400 font-bold mt-1">Format gambar JPG/PNG, Maks. 2MB</p>
                                </div>
                            </label>
                        </div>
                        <div id="preview-container" class="mt-3 hidden relative rounded-2xl overflow-hidden shadow-lg border border-gray-100">
                            <img id="image-preview" class="w-full h-48 object-cover">
                            <button type="button" id="remove-preview-btn" class="absolute top-3 right-3 w-8 h-8 bg-red-600 text-white rounded-full flex items-center justify-center hover:bg-red-700 transition shadow">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Signature Pad -->
                    <div class="space-y-3">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest ml-1">Tanda Tangan Penerima</label>
                        <div class="signature-container shadow-inner border border-gray-100 bg-white">
                            <canvas id="signature-pad"></canvas>
                        </div>
                        <div class="flex justify-between items-center">
                            <p class="text-[9px] text-gray-400 font-bold italic">*Goreskan tanda tangan di atas kotak abu-abu</p>
                            <button type="button" id="clear-signature-btn" class="text-[10px] font-black text-red-600 hover:text-red-800 uppercase tracking-wider transition-colors flex items-center gap-1">
                                <i class="fas fa-eraser"></i> Hapus Coretan
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            <div class="space-y-3">
                <label for="delivery_notes" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest ml-1">Catatan Pengantaran (Opsional)</label>
                <textarea name="delivery_notes" id="delivery_notes" rows="3"
                    class="w-full px-5 py-4 bg-gray-50/50 border border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 transition-all shadow-inner"
                    placeholder="Masukkan catatan khusus tentang penyerahan obat..."></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="pt-8 border-t border-gray-100 flex flex-col sm:flex-row justify-between gap-4">
                <button type="button" id="toggle-failed-btn" class="px-8 py-4 bg-red-50 hover:bg-red-100 text-red-700 rounded-2xl font-black text-[10px] uppercase tracking-wider transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-times-circle"></i> Tandai Gagal
                </button>
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('map.navigate.real', $delivery) }}" class="px-8 py-4 text-[10px] font-black text-gray-400 hover:text-gray-600 uppercase tracking-widest transition-colors flex items-center justify-center">
                        Kembali Ke Peta
                    </a>
                    <button type="submit" id="submit-btn" class="px-12 py-4 bg-gradient-to-br from-tni-800 to-black text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-2xl shadow-tni-300 hover:scale-[1.02] transition-all flex items-center justify-center gap-3">
                        <i class="fas fa-check-circle text-gold-400"></i> Konfirmasi Selesai
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Signature Pad Initialization
    const canvas = document.getElementById('signature-pad');
    let signaturePad;

    if (canvas) {
        // Handle responsive canvas sizes
        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            if (signaturePad) signaturePad.clear();
        }

        signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(249, 250, 251)',
            penColor: 'rgb(17, 24, 39)'
        });

        window.addEventListener("resize", resizeCanvas);
        resizeCanvas();
    }

    // Clear Signature Action
    const clearBtn = document.getElementById('clear-signature-btn');
    if (clearBtn && signaturePad) {
        clearBtn.addEventListener('click', function() {
            signaturePad.clear();
        });
    }

    // Image Upload Preview logic
    const fileInput = document.getElementById('proof_image');
    const previewContainer = document.getElementById('preview-container');
    const imagePreview = document.getElementById('image-preview');
    const removePreviewBtn = document.getElementById('remove-preview-btn');

    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (removePreviewBtn) {
        removePreviewBtn.addEventListener('click', function() {
            fileInput.value = '';
            imagePreview.src = '';
            previewContainer.classList.add('hidden');
        });
    }

    // Toggle Gagal Kirim
    const toggleFailedBtn = document.getElementById('toggle-failed-btn');
    const failureSection = document.getElementById('failure-option-section');
    const statusInput = document.getElementById('delivery-status-input');
    const receiverInfo = document.getElementById('receiver-info-fields');
    const documentationSection = document.getElementById('documentation-section');
    const submitBtn = document.getElementById('submit-btn');

    let isFailedState = false;

    if (toggleFailedBtn) {
        toggleFailedBtn.addEventListener('click', function() {
            isFailedState = !isFailedState;

            if (isFailedState) {
                // Change to Failed State
                statusInput.value = 'failed';
                failureSection.classList.remove('hidden');
                toggleFailedBtn.innerHTML = '<i class="fas fa-check-circle"></i> Tandai Berhasil';
                toggleFailedBtn.classList.replace('bg-red-50', 'bg-green-50');
                toggleFailedBtn.classList.replace('text-red-700', 'text-green-700');
                toggleFailedBtn.classList.replace('hover:bg-red-100', 'hover:bg-green-100');

                // Hide & disable standard recipient & documentation inputs
                receiverInfo.querySelectorAll('input, select').forEach(el => {
                    el.required = false;
                    el.disabled = true;
                });
                fileInput.required = false;
                
                // Hide recipient & documentation section UI
                receiverInfo.classList.add('opacity-50');
                documentationSection.classList.add('hidden');

                // Make failure reason radio required
                failureSection.querySelectorAll('input[name="failure_reason"]').forEach(el => {
                    el.required = true;
                });

                // Style submit button red
                submitBtn.classList.replace('from-tni-800', 'from-red-700');
                submitBtn.innerHTML = '<i class="fas fa-exclamation-circle"></i> Konfirmasi Gagal';

            } else {
                // Restore to Delivered State
                statusInput.value = 'delivered';
                failureSection.classList.add('hidden');
                toggleFailedBtn.innerHTML = '<i class="fas fa-times-circle"></i> Tandai Gagal';
                toggleFailedBtn.classList.replace('bg-green-50', 'bg-red-50');
                toggleFailedBtn.classList.replace('text-green-700', 'text-red-700');
                toggleFailedBtn.classList.replace('hover:bg-green-100', 'hover:bg-red-100');

                // Restore requirements
                receiverInfo.querySelectorAll('input, select').forEach(el => {
                    if (el.id !== 'recipient_phone') {
                        el.required = true;
                    }
                    el.disabled = false;
                });
                fileInput.required = true;
                
                // Show UI elements
                receiverInfo.classList.remove('opacity-50');
                documentationSection.classList.remove('hidden');

                // Make failure reason radio not required
                failureSection.querySelectorAll('input[name="failure_reason"]').forEach(el => {
                    el.required = false;
                    el.checked = false;
                });
                document.getElementById('failure-notes-wrapper').classList.add('hidden');
                document.getElementById('failure_notes').required = false;

                // Restore submit button to default
                submitBtn.classList.replace('from-red-700', 'from-tni-800');
                submitBtn.innerHTML = '<i class="fas fa-check-circle text-gold-400"></i> Konfirmasi Selesai';
            }
        });
    }

    // Toggle failure notes textarea if 'lainnya' is selected
    const failureRadios = document.querySelectorAll('input[name="failure_reason"]');
    const failureNotesWrapper = document.getElementById('failure-notes-wrapper');
    const failureNotesInput = document.getElementById('failure_notes');

    failureRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'lainnya') {
                failureNotesWrapper.classList.remove('hidden');
                failureNotesInput.required = true;
            } else {
                failureNotesWrapper.classList.add('hidden');
                failureNotesInput.required = false;
            }
        });
    });

    // Form Submit
    const form = document.getElementById('deliveryCompletionForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Client-side validations
            if (statusInput.value === 'delivered') {
                if (!fileInput.files || fileInput.files.length === 0) {
                    alert('Harap lampirkan foto bukti penyerahan obat!');
                    return;
                }
            }

            // Disable submit button during upload
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50');
            submitBtn.innerText = 'Mengirim data...';

            const formData = new FormData(this);

            // Append signature base64 if present & not empty
            if (signaturePad && !signaturePad.isEmpty() && statusInput.value === 'delivered') {
                formData.set('signature', signaturePad.toDataURL());
            }

            fetch('/api/deliveries/{{ $delivery->id }}/status', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(res => {
                if (!res.ok) {
                    throw new Error('HTTP status ' + res.status);
                }
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    alert('Dokumentasi pengantaran berhasil dikirim!');
                    window.location.href = '/map';
                } else {
                    alert('Gagal mengirim: ' + (data.error || 'Terjadi kesalahan sistem'));
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50');
                    submitBtn.innerHTML = statusInput.value === 'delivered' 
                        ? '<i class="fas fa-check-circle text-gold-400"></i> Konfirmasi Selesai' 
                        : '<i class="fas fa-exclamation-circle"></i> Konfirmasi Gagal';
                }
            })
            .catch(err => {
                console.error('Error submitting form:', err);
                alert('Terjadi kesalahan koneksi atau server. Silakan coba lagi.');
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50');
                submitBtn.innerHTML = statusInput.value === 'delivered' 
                    ? '<i class="fas fa-check-circle text-gold-400"></i> Konfirmasi Selesai' 
                    : '<i class="fas fa-exclamation-circle"></i> Konfirmasi Gagal';
            });
        });
    }
});
</script>
@endpush
