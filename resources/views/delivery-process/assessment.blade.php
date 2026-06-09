@extends('layouts.app')

@section('title', 'Assesmen Serah Terima')
@section('page-title', 'Penyelesaian Tugas')

@section('styles')
<style>
    .camera-container {
        width: 100%;
        height: 350px;
        background-color: #f8fafc;
        border: 3px dashed #e2e8f0;
        border-radius: 2rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        overflow: hidden;
        position: relative;
    }
    
    .camera-container:hover {
        border-color: #F59E0B;
        background-color: #FFFBEB;
    }
    
    .camera-preview {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: none;
        border-radius: 2rem;
    }
    
    .signature-pad {
        width: 100%;
        height: 250px;
        border: 1px solid #e2e8f0;
        border-radius: 2rem;
        background-color: #f8fafc;
        cursor: crosshair;
        display: block;
        touch-action: none;
    }
    
    .step-card {
        background: white;
        border-radius: 2.5rem;
        padding: 2.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        border: 1px solid #f1f5f9;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .step-badge {
        position: absolute;
        top: 0;
        right: 0;
        background: #f1f5f9;
        color: #64748b;
        padding: 0.5rem 1.5rem;
        border-radius: 0 0 0 1.5rem;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }

    .custom-radio:checked + label {
        border-color: #254328;
        background-color: #f0fdf4;
    }

    .custom-radio:checked + label .icon-box {
        background-color: #254328;
        color: white;
    }

    .progress-stepper {
        display: flex;
        justify-content: space-between;
        margin-bottom: 3rem;
        position: relative;
    }

    .progress-stepper::before {
        content: '';
        position: absolute;
        top: 1.25rem;
        left: 0;
        right: 0;
        height: 2px;
        background: #e2e8f0;
        z-index: 1;
    }

    .step-node {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
    }

    .node-circle {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 1rem;
        background: white;
        border: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 12px;
        color: #94a3b8;
        transition: all 0.3s ease;
    }

    .step-node.active .node-circle {
        background: #254328;
        border-color: #254328;
        color: white;
        box-shadow: 0 10px 15px -3px rgba(37, 67, 40, 0.3);
    }

    .step-node.completed .node-circle {
        background: #10b981;
        border-color: #10b981;
        color: white;
    }

    .loading-spinner {
        width: 1.5rem;
        height: 1.5rem;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 0.6s linear infinite;
    }

    @keyframes spin { to { transform: rotate(360deg); } }
</style>
@endsection

@section('content')
<div class="max-w-4xl mx-auto animate-fade-in pb-20">
    <!-- Stepper Navigation -->
    <div class="progress-stepper px-4">
        <div class="step-node completed" id="node1">
            <div class="node-circle"><i class="fas fa-check"></i></div>
            <span class="text-[9px] font-black uppercase tracking-tighter text-gray-400">Verifikasi</span>
        </div>
        <div class="step-node active" id="node2">
            <div class="node-circle">2</div>
            <span class="text-[9px] font-black uppercase tracking-tighter text-gray-800">Kondisi</span>
        </div>
        <div class="step-node" id="node3">
            <div class="node-circle">3</div>
            <span class="text-[9px] font-black uppercase tracking-tighter text-gray-400">Edukasi</span>
        </div>
        <div class="step-node" id="node4">
            <div class="node-circle">4</div>
            <span class="text-[9px] font-black uppercase tracking-tighter text-gray-400">Selesai</span>
        </div>
    </div>

    <form id="assessmentForm" enctype="multipart/form-data" class="space-y-8">
        @csrf
        
        <!-- Section 1: Verification -->
        <div class="step-card">
            <div class="step-badge">Tahap 01</div>
            <h3 class="text-xl font-black text-gray-800 mb-6 flex items-center gap-3">
                <i class="fas fa-id-card text-gold-500"></i>
                Identitas Penerima
            </h3>
            
            <div class="bg-gray-50 rounded-3xl p-6 mb-6 grid grid-cols-1 md:grid-cols-2 gap-6 border border-gray-100">
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Nama Pasien</label>
                    <p class="text-sm font-bold text-gray-800">{{ $assessment->delivery->patient->name }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 block">No. Telepon</label>
                    <p class="text-sm font-bold text-gray-800">{{ $assessment->delivery->patient->phone }}</p>
                </div>
            </div>

            <label for="patient_verified" class="flex items-start gap-4 p-4 hover:bg-gray-50 rounded-2xl cursor-pointer transition-all group">
                <div class="relative shrink-0 mt-0.5">
                    <input type="checkbox" id="patient_verified" name="patient_verified" class="peer hidden" required>
                    <div class="w-6 h-6 rounded-lg border-2 border-gray-200 flex items-center justify-center transition-all group-hover:border-tni-300 peer-checked:bg-tni-800 peer-checked:border-tni-800">
                        <i class="fa-solid fa-check text-white text-[12px] opacity-0 peer-checked:opacity-100 scale-50 peer-checked:scale-100 transition-all duration-200"></i>
                    </div>
                </div>
                <p class="text-sm font-bold text-gray-600">Saya mengonfirmasi bahwa penerima adalah pasien yang sah sesuai data di atas.</p>
            </label>
        </div>

        <!-- Section 2: Condition -->
        <div class="step-card">
            <div class="step-badge">Tahap 02</div>
            <h3 class="text-xl font-black text-gray-800 mb-6 flex items-center gap-3">
                <i class="fas fa-heart-pulse text-red-500"></i>
                Kondisi Kesehatan Pasien
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                @foreach(['baik' => ['Hati', 'green', 'smile'], 'sedang' => ['Paru', 'gold', 'meh'], 'buruk' => ['Jantung', 'red', 'frown']] as $val => $info)
                <div class="relative">
                    <input type="radio" name="patient_condition" value="{{ $val }}" id="cond_{{ $val }}" class="hidden custom-radio" required>
                    <label for="cond_{{ $val }}" class="flex flex-col items-center p-6 border-2 border-gray-100 rounded-[2rem] cursor-pointer hover:bg-gray-50 transition-all">
                        <div class="w-12 h-12 rounded-2xl bg-{{ $info[1] }}-50 text-{{ $info[1] }}-600 flex items-center justify-center text-xl mb-4 icon-box transition-all">
                            <i class="fas fa-{{ $info[2] }}"></i>
                        </div>
                        <span class="text-xs font-black uppercase tracking-widest text-gray-800">{{ $val }}</span>
                    </label>
                </div>
                @endforeach
            </div>

            <div class="relative">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block ml-4">Keluhan / Feedback Pasien</label>
                <textarea name="patient_feedback" rows="3" class="w-full px-6 py-4 bg-gray-50 border-transparent rounded-[2rem] focus:bg-white focus:ring-2 focus:ring-tni-500/20 focus:border-tni-500 outline-none transition-all text-sm font-bold text-gray-800 shadow-inner" placeholder="Tuliskan keluhan atau kondisi fisik pasien jika ada..."></textarea>
            </div>
        </div>

        <!-- Section 3: Education -->
        <div class="step-card">
            <div class="step-badge">Tahap 03</div>
            <h3 class="text-xl font-black text-gray-800 mb-6 flex items-center gap-3">
                <i class="fas fa-book-open-reader text-tni-600"></i>
                Edukasi & Rincian Obat
            </h3>

            @if($assessment->delivery->prescription)
            <div class="space-y-3 mb-8">
                @php $meds = $assessment->delivery->prescription->medications ?? [['name' => $assessment->delivery->prescription->medication_name]]; @endphp
                @foreach($meds as $med)
                <div class="p-5 bg-blue-50/50 rounded-3xl border border-blue-100 flex justify-between items-center">
                    <div>
                        <h4 class="text-sm font-black text-blue-900">{{ $med['name'] ?? '-' }}</h4>
                        <p class="text-[10px] text-blue-600 font-bold uppercase">{{ $med['dosage'] ?? '-' }} | {{ $med['frequency'] ?? '-' }}</p>
                    </div>
                    <div class="w-8 h-8 bg-white rounded-xl flex items-center justify-center text-blue-500 shadow-sm border border-blue-100">
                        <i class="fas fa-pills text-xs"></i>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <div class="space-y-4">
                <label for="medication_understood" class="flex items-start gap-4 p-3 hover:bg-gray-50 rounded-xl cursor-pointer transition-all group">
                    <div class="relative shrink-0 mt-0.5">
                        <input type="checkbox" name="medication_understood" id="medication_understood" class="peer hidden" required>
                        <div class="w-6 h-6 rounded-lg border-2 border-gray-200 flex items-center justify-center transition-all group-hover:border-tni-300 peer-checked:bg-tni-800 peer-checked:border-tni-800">
                            <i class="fa-solid fa-check text-white text-[10px] opacity-0 peer-checked:opacity-100 scale-50 peer-checked:scale-100 transition-all duration-200"></i>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-gray-600">Pasien paham cara penggunaan obat dengan benar.</span>
                </label>
                <label for="side_effects_explained" class="flex items-start gap-4 p-3 hover:bg-gray-50 rounded-xl cursor-pointer transition-all group">
                    <div class="relative shrink-0 mt-0.5">
                        <input type="checkbox" name="side_effects_explained" id="side_effects_explained" class="peer hidden" required>
                        <div class="w-6 h-6 rounded-lg border-2 border-gray-200 flex items-center justify-center transition-all group-hover:border-tni-300 peer-checked:bg-tni-800 peer-checked:border-tni-800">
                            <i class="fa-solid fa-check text-white text-[10px] opacity-0 peer-checked:opacity-100 scale-50 peer-checked:scale-100 transition-all duration-200"></i>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-gray-600">Efek samping & larangan telah dijelaskan secara detil.</span>
                </label>
            </div>
        </div>

        <!-- Section 4: Photo & Signature -->
        <div class="step-card">
            <div class="step-badge">Tahap 04</div>
            <h3 class="text-xl font-black text-gray-800 mb-8 flex items-center gap-3">
                <i class="fas fa-camera-retro text-gold-600"></i>
                Dokumentasi & Tanda Tangan
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Photo Upload -->
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 ml-2 text-center">Foto Serah Terima</p>
                    <div class="camera-container shadow-inner" onclick="triggerCamera()">
                        <div id="cameraPlaceholder" class="text-center p-6">
                            <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-gold-500 shadow-xl mx-auto mb-4 border border-gray-50">
                                <i class="fas fa-camera text-2xl"></i>
                            </div>
                            <p class="text-sm font-black text-gray-800">Ambil Foto</p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase mt-2">Wajah Pasien & Obat</p>
                        </div>
                        <img id="cameraPreview" class="camera-preview">
                    </div>
                    <input type="file" id="handover_photo" name="handover_photo" accept="image/*" class="hidden" required>
                </div>

                <!-- Signature Pad -->
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 ml-2 text-center">Tanda Tangan Pasien</p>
                    <canvas id="signaturePad" class="signature-pad shadow-inner"></canvas>
                    <div class="flex justify-center mt-4">
                        <button type="button" onclick="clearSignature()" class="text-[10px] font-black text-red-500 uppercase tracking-widest hover:underline flex items-center gap-2">
                            <i class="fas fa-rotate-left"></i> Bersihkan Tanda Tangan
                        </button>
                    </div>
                    <input type="hidden" id="signature_image" name="signature_image">
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-gray-100 flex justify-center">
                <label for="final_verification" class="inline-flex items-center gap-4 p-4 hover:bg-green-50 rounded-2xl cursor-pointer transition-all group">
                    <div class="relative">
                        <input type="checkbox" id="final_verification" name="final_verification" class="peer hidden" required>
                        <div class="w-6 h-6 rounded-lg border-2 border-green-200 flex items-center justify-center transition-all group-hover:border-green-400 peer-checked:bg-green-600 peer-checked:border-green-600">
                            <i class="fa-solid fa-check text-white text-[12px] opacity-0 peer-checked:opacity-100 scale-50 peer-checked:scale-100 transition-all duration-200"></i>
                        </div>
                    </div>
                    <span class="text-sm font-black text-gray-800 uppercase tracking-tighter">Konfirmasi Kebenaran Data Assesmen</span>
                </label>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col md:flex-row gap-4">
            <button type="button" onclick="window.history.back()" class="flex-1 py-5 bg-white border border-gray-100 text-gray-400 rounded-3xl font-black uppercase tracking-widest hover:bg-gray-50 transition-all flex items-center justify-center gap-3">
                <i class="fas fa-arrow-left"></i> Kembali
            </button>
            <button type="submit" id="submitBtn" class="flex-[2] py-5 bg-gradient-to-r from-tni-800 to-black text-white rounded-3xl font-black uppercase tracking-widest hover:shadow-2xl transition-all flex items-center justify-center gap-3 group">
                <span id="btnText">Kirim Laporan Assesmen</span>
                <i class="fas fa-paper-plane group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
            </button>
        </div>
    </form>
</div>

<!-- Modal Sukses -->
<div id="successModal" class="fixed inset-0 z-[2000] flex items-center justify-center hidden">
    <div class="absolute inset-0 bg-tni-900/90 backdrop-blur-md"></div>
    <div class="relative bg-white rounded-[3rem] p-12 max-w-sm w-full text-center shadow-2xl mx-4">
        <div class="w-24 h-24 bg-green-50 text-green-500 rounded-[2.5rem] flex items-center justify-center text-4xl mx-auto mb-8 shadow-inner">
            <i class="fas fa-check-double"></i>
        </div>
        <h3 class="text-2xl font-black text-gray-800 mb-2">Tugas Selesai!</h3>
        <p class="text-gray-500 font-bold text-sm leading-relaxed mb-8">Laporan assesmen berhasil dikirim. Pengantaran telah diverifikasi sebagai "Terkirim".</p>
        <div class="text-xs font-black text-tni-800 uppercase tracking-widest">
            <i class="fas fa-spinner animate-spin mr-3"></i> Mengarahkan...
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
let signaturePad;
let isSubmitting = false;

document.addEventListener('DOMContentLoaded', function() {
    initSignaturePad();
    setupLogic();
});

function initSignaturePad() {
    const canvas = document.getElementById('signaturePad');
    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    canvas.getContext("2d").scale(ratio, ratio);
    
    signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgba(255, 255, 255, 0)',
        penColor: '#254328'
    });
}

function triggerCamera() { document.getElementById('handover_photo').click(); }

document.getElementById('handover_photo').onchange = function(e) {
    if(this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('cameraPreview').src = e.target.result;
            document.getElementById('cameraPreview').style.display = 'block';
            document.getElementById('cameraPlaceholder').style.display = 'none';
        }
        reader.readAsDataURL(this.files[0]);
    }
};

function clearSignature() { signaturePad.clear(); }

function setupLogic() {
    const form = document.getElementById('assessmentForm');
    form.onsubmit = function(e) {
        e.preventDefault();
        if(isSubmitting) return;
        
        if(signaturePad.isEmpty()) { alert('Harap berikan tanda tangan penerima.'); return; }
        
        isSubmitting = true;
        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<div class="loading-spinner"></div>';
        btn.disabled = true;

        document.getElementById('signature_image').value = signaturePad.toDataURL();
        
        const formData = new FormData(form);
        fetch('{{ route("delivery-process.submit", $assessment->id) }}', {
            method: 'POST',
            body: formData,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if(data.success) {
                document.getElementById('successModal').classList.remove('hidden');
                setTimeout(() => { window.location.href = data.redirect_url; }, 2000);
            } else {
                alert(data.message || 'Terjadi kesalahan.');
                isSubmitting = false;
                btn.innerHTML = '<span>Kirim Laporan Assesmen</span>';
                btn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Submit error:', error);
            alert('Terjadi kesalahan jaringan. Periksa koneksi Anda dan coba lagi.');
            isSubmitting = false;
            btn.innerHTML = '<span>Kirim Laporan Assesmen</span>';
            btn.disabled = false;
        });
    };
}
</script>
@endsection