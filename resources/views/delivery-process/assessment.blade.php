@extends('layouts.app')

@section('title', 'Assesmen Serah Terima')
@section('page-title', 'Assesmen Serah Terima')

@section('styles')
<style>
    .camera-container {
        width: 100%;
        height: 300px;
        background-color: #f3f4f6;
        border: 2px dashed #d1d5db;
        border-radius: 0.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        overflow: hidden;
    }
    
    .camera-container:hover {
        border-color: #3b82f6;
        background-color: #f0f9ff;
    }
    
    .camera-preview {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: none;
    }
    
    .signature-pad {
        width: 100%;
        height: 200px;
        border: 2px solid #d1d5db;
        border-radius: 0.5rem;
        background-color: white;
        cursor: crosshair;
        display: block;
    }
    
    .instruction-step {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
        padding: 0.75rem;
        border-radius: 0.5rem;
        background-color: #f9fafb;
    }
    
    .instruction-step.active {
        background-color: #dbeafe;
        border-left: 4px solid #3b82f6;
    }
    
    .step-number {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: #3b82f6;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-right: 1rem;
        flex-shrink: 0;
    }
    
    .progress-bar {
        height: 8px;
        background-color: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
        margin: 2rem 0;
    }
    
    .progress-fill {
        height: 100%;
        background-color: #10b981;
        transition: width 0.3s ease;
    }

    .error-message {
        display: none;
        background-color: #fee2e2;
        border: 1px solid #fecaca;
        color: #dc2626;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
    }

    .error-message.show {
        display: block;
    }

    .success-overlay {
        position: fixed;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 50;
    }

    .success-box {
        background-color: white;
        border-radius: 0.5rem;
        padding: 2rem;
        max-width: 28rem;
        text-align: center;
    }

    .success-icon {
        width: 4rem;
        height: 4rem;
        background-color: #dcfce7;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
    }

    .success-icon i {
        color: #16a34a;
        font-size: 1.5rem;
    }

    .loading-spinner {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 0.6s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Error Message -->
    <div class="error-message" id="errorMessage">
        <p id="errorText"></p>
    </div>

    <!-- Progress Bar -->
    <div class="progress-bar">
        <div class="progress-fill" id="progressFill" style="width: 0%"></div>
    </div>
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Assesmen Serah Terima Obat</h2>
            <p class="text-gray-600">Pasien: {{ $assessment->delivery->patient->name }}</p>
        </div>
        <div class="text-sm text-gray-500">
            Waktu tiba: {{ $assessment->arrival_time->format('H:i') }}
        </div>
    </div>

    <!-- Instructions -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-medium text-blue-900 mb-4">Instruksi Assesmen</h3>
        <div class="space-y-2">
            <div class="instruction-step active" id="step1">
                <div class="step-number">1</div>
                <div>
                    <div class="font-medium text-gray-900">Verifikasi Penerima</div>
                    <div class="text-sm text-gray-600">Pastikan penerima adalah pasien yang benar</div>
                </div>
            </div>
            <div class="instruction-step" id="step2">
                <div class="step-number">2</div>
                <div>
                    <div class="font-medium text-gray-900">Kondisi Pasien</div>
                    <div class="text-sm text-gray-600">Catat kondisi kesehatan pasien saat ini</div>
                </div>
            </div>
            <div class="instruction-step" id="step3">
                <div class="step-number">3</div>
                <div>
                    <div class="font-medium text-gray-900">Edukasi Obat</div>
                    <div class="text-sm text-gray-600">Jelaskan cara penggunaan dan efek samping</div>
                </div>
            </div>
            <div class="instruction-step" id="step4">
                <div class="step-number">4</div>
                <div>
                    <div class="font-medium text-gray-900">Dokumentasi</div>
                    <div class="text-sm text-gray-600">Foto serah terima dan tanda tangan</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assessment Form -->
    <form id="assessmentForm" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <!-- Patient Verification -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">1. Verifikasi Penerima</h3>
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Penerima</label>
                        <input type="text" value="{{ $assessment->delivery->patient->name }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                        <input type="text" value="{{ $assessment->delivery->patient->phone }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50" readonly>
                    </div>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="patient_verified" name="patient_verified" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" required>
                    <label for="patient_verified" class="ml-2 block text-sm text-gray-700">
                        Saya telah memverifikasi bahwa penerima adalah pasien yang benar
                    </label>
                </div>
            </div>
        </div>

        <!-- Patient Condition -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">2. Kondisi Pasien</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kondisi Kesehatan Saat Ini</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="patient_condition" value="baik" 
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" required>
                            <div class="ml-3">
                                <div class="font-medium text-gray-700">Baik</div>
                                <div class="text-sm text-gray-500">Pasien dalam kondisi sehat</div>
                            </div>
                        </label>
                        <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="patient_condition" value="sedang" 
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" required>
                            <div class="ml-3">
                                <div class="font-medium text-gray-700">Sedang</div>
                                <div class="text-sm text-gray-500">Pasien kurang sehat</div>
                            </div>
                        </label>
                        <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="patient_condition" value="buruk" 
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" required>
                            <div class="ml-3">
                                <div class="font-medium text-gray-700">Buruk</div>
                                <div class="text-sm text-gray-500">Pasien sakit serius</div>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div>
                    <label for="patient_feedback" class="block text-sm font-medium text-gray-700 mb-1">
                        Keluhan atau Feedback Pasien
                    </label>
                    <textarea id="patient_feedback" name="patient_feedback" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Catatan kondisi atau keluhan pasien..."></textarea>
                </div>
            </div>
        </div>

        <!-- Medication Education -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">3. Edukasi Obat</h3>
            <div class="space-y-4">
                <!-- Prescription Info -->
                @if($assessment->delivery->prescription)
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-pills text-blue-600 mr-2"></i> Rincian Resep Obat
                    </h4>
                    
                    @php
                        $meds = $assessment->delivery->prescription->medications ?? [
                            [
                                'name' => $assessment->delivery->prescription->medication_name,
                                'dosage' => $assessment->delivery->prescription->dosage,
                                'frequency' => $assessment->delivery->prescription->frequency,
                                'duration' => $assessment->delivery->prescription->duration,
                                'instructions' => $assessment->delivery->prescription->instructions
                            ]
                        ];
                    @endphp

                    <div class="space-y-4">
                        @foreach($meds as $i => $med)
                        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                            <div class="font-bold text-gray-800 mb-2 flex items-center">
                                <span class="w-5 h-5 bg-blue-500 text-white text-[10px] rounded-full flex items-center justify-center mr-2">{{ $i + 1 }}</span>
                                {{ $med['name'] }}
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="text-[10px] font-medium text-gray-400 uppercase">Dosis</label>
                                    <p class="text-sm text-gray-700 font-medium">{{ $med['dosage'] ?? '-' }}</p>
                                </div>
                                <div>
                                    <label class="text-[10px] font-medium text-gray-400 uppercase">Freq</label>
                                    <p class="text-sm text-gray-700 font-medium">{{ $med['frequency'] ?? '-' }}</p>
                                </div>
                                <div>
                                    <label class="text-[10px] font-medium text-gray-400 uppercase">Durasi</label>
                                    <p class="text-sm text-gray-700 font-medium">{{ $med['duration'] ?? '-' }}</p>
                                </div>
                                <div>
                                    @if(!empty($med['instructions']))
                                    <label class="text-[10px] font-medium text-gray-400 uppercase">Instruksi</label>
                                    <p class="text-xs text-gray-500 italic">"{{ $med['instructions'] }}"</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Education Checklist -->
                <div class="space-y-3">
                    <div class="flex items-center">
                        <input type="checkbox" id="medication_understood" name="medication_understood" 
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" required>
                        <label for="medication_understood" class="ml-2 block text-sm text-gray-700">
                            Pasien memahami cara penggunaan obat dengan benar
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="side_effects_explained" name="side_effects_explained" 
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" required>
                        <label for="side_effects_explained" class="ml-2 block text-sm text-gray-700">
                            Efek samping dan kontraindikasi telah dijelaskan
                        </label>
                    </div>
                    <div>
                        <label for="special_notes" class="block text-sm font-medium text-gray-700 mb-1">
                            Catatan Tambahan untuk Pasien
                        </label>
                        <textarea id="special_notes" name="special_notes" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Catatan penting lainnya..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documentation -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">4. Dokumentasi Serah Terima</h3>
            <div class="space-y-6">
                <!-- Handover Photo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Foto Serah Terima Obat <span class="text-red-500">*</span>
                    </label>
                    <div class="camera-container" onclick="triggerCamera()" id="cameraContainer">
                        <div id="cameraPlaceholder">
                            <i class="fas fa-camera text-4xl text-gray-400 mb-3"></i>
                            <p class="text-gray-600">Klik untuk mengambil foto</p>
                            <p class="text-sm text-gray-500 mt-1">Pastikan wajah penerima dan obat terlihat jelas</p>
                        </div>
                        <img id="cameraPreview" class="camera-preview" alt="Preview">
                    </div>
                    <input type="file" id="handover_photo" name="handover_photo" 
                           accept="image/*" class="hidden" required>
                    <div class="mt-2 text-sm text-gray-500">
                        Foto harus menunjukkan penerima memegang obat yang dikirim
                    </div>
                </div>

                <!-- Signature -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tanda Tangan Penerima <span class="text-red-500">*</span>
                    </label>
                    <canvas id="signaturePad" class="signature-pad"></canvas>
                    <div class="flex justify-between mt-2">
                        <button type="button" onclick="clearSignature()" 
                                class="text-sm text-red-600 hover:text-red-800">
                            <i class="fas fa-eraser mr-1"></i> Hapus Tanda Tangan
                        </button>
                        <div class="text-sm text-gray-500">
                            Tanda tangan penerima sebagai bukti penerimaan
                        </div>
                    </div>
                    <input type="hidden" id="signature_image" name="signature_image">
                </div>

                <!-- Final Check -->
                <div class="border-t border-gray-200 pt-6">
                    <div class="flex items-start space-x-3">
                        <input type="checkbox" id="final_verification" name="final_verification" value="on"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-1" required>
                        <label for="final_verification" class="block text-sm text-gray-700">
                            Saya menyatakan bahwa proses serah terima obat telah dilakukan dengan benar
                            dan semua informasi yang diberikan adalah akurat.
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-between pt-6 border-t border-gray-200">
            <button type="button" onclick="confirmBack()" 
                    class="px-6 py-3 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </button>
            <button type="submit" id="submitBtn" 
                    class="px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 font-medium disabled:bg-gray-400 disabled:cursor-not-allowed transition">
                <i class="fas fa-paper-plane mr-2"></i> Kirim Assesmen
            </button>
        </div>
    </form>
</div>

<!-- Signature Pad Library -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

<script>
let signaturePad;
let isSubmitting = false;

document.addEventListener('DOMContentLoaded', function() {
    initSignaturePad();
    setupFormValidation();
    setupFormEventListeners();
});

function initSignaturePad() {
    const canvas = document.getElementById('signaturePad');
    
    // Set canvas size properly
    const container = canvas.parentElement;
    const dpr = window.devicePixelRatio || 1;
    
    canvas.width = container.offsetWidth * dpr;
    canvas.height = 200 * dpr;
    canvas.style.width = container.offsetWidth + 'px';
    canvas.style.height = '200px';
    
    // Create signature pad with proper scale
    const ctx = canvas.getContext('2d');
    ctx.scale(dpr, dpr);
    
    signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'white',
        penColor: 'rgb(0, 0, 0)',
        velocityFilterWeight: 0.7,
        minWidth: 0.5,
        maxWidth: 2.5,
        throttle: 16
    });
    
    // Handle resize
    window.addEventListener('resize', function() {
        resizeSignaturePad();
    });
}

function resizeSignaturePad() {
    const canvas = document.getElementById('signaturePad');
    const container = canvas.parentElement;
    const dpr = window.devicePixelRatio || 1;
    const data = signaturePad.toData();
    
    canvas.width = container.offsetWidth * dpr;
    canvas.height = 200 * dpr;
    canvas.style.width = container.offsetWidth + 'px';
    canvas.style.height = '200px';
    
    const ctx = canvas.getContext('2d');
    ctx.scale(dpr, dpr);
    
    signaturePad.clear();
    if (data && data.length > 0) {
        signaturePad.fromData(data);
    }
}

function triggerCamera() {
    document.getElementById('handover_photo').click();
}

document.getElementById('handover_photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            showError('Ukuran file terlalu besar. Maksimal 5MB.');
            this.value = '';
            return;
        }
        
        // Validate file type
        if (!file.type.startsWith('image/')) {
            showError('File harus berupa gambar.');
            this.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('cameraPreview');
            const placeholder = document.getElementById('cameraPlaceholder');
            
            preview.src = e.target.result;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
            
            // Update step 4
            document.getElementById('step4').classList.add('active');
            updateProgress();
        };
        reader.readAsDataURL(file);
    }
});

function clearSignature() {
    if (confirm('Apakah Anda yakin ingin menghapus tanda tangan?')) {
        signaturePad.clear();
        document.getElementById('signature_image').value = '';
        updateProgress();
    }
}

function setupFormEventListeners() {
    // Step 1: Patient Verified
    const patientVerified = document.getElementById('patient_verified');
    if (patientVerified) {
        patientVerified.addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('step1').classList.add('active');
            } else {
                document.getElementById('step1').classList.remove('active');
            }
            updateProgress();
        });
    }
    
    // Step 2: Patient Condition (Radio Buttons)
    const patientConditionRadios = document.querySelectorAll('input[name="patient_condition"]');
    patientConditionRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('step2').classList.add('active');
            }
            updateProgress();
        });
    });
    
    // Step 3: Medication Education (Checkboxes)
    const medicationUnderstood = document.getElementById('medication_understood');
    const sideEffectsExplained = document.getElementById('side_effects_explained');
    
    if (medicationUnderstood) {
        medicationUnderstood.addEventListener('change', function() {
            checkMedicationStep();
        });
    }
    
    if (sideEffectsExplained) {
        sideEffectsExplained.addEventListener('change', function() {
            checkMedicationStep();
        });
    }
}

function checkMedicationStep() {
    const medicationUnderstood = document.getElementById('medication_understood').checked;
    const sideEffectsExplained = document.getElementById('side_effects_explained').checked;
    
    if (medicationUnderstood && sideEffectsExplained) {
        document.getElementById('step3').classList.add('active');
    } else {
        document.getElementById('step3').classList.remove('active');
    }
    
    updateProgress();
}

function updateProgress() {
    let progress = 0;
    
    // Check step 1
    if (document.getElementById('patient_verified').checked) {
        progress += 20;
    }
    
    // Check step 2
    const selectedCondition = document.querySelector('input[name="patient_condition"]:checked');
    if (selectedCondition) {
        progress += 20;
    }
    
    // Check step 3
    const medicationUnderstood = document.getElementById('medication_understood').checked;
    const sideEffectsExplained = document.getElementById('side_effects_explained').checked;
    if (medicationUnderstood && sideEffectsExplained) {
        progress += 20;
    }
    
    // Check step 4 (photo)
    if (document.getElementById('handover_photo').files.length > 0) {
        progress += 20;
    }
    
    // Check step 4 (signature)
    if (signaturePad && !signaturePad.isEmpty()) {
        progress += 20;
    }
    
    document.getElementById('progressFill').style.width = progress + '%';
}

function setupFormValidation() {
    const form = document.getElementById('assessmentForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (isSubmitting) return;
        
        // Clear previous errors
        hideError();
        
        // Validate form
        if (!validateForm()) {
            return;
        }
        
        // Convert signature to base64 if exists
        if (!signaturePad.isEmpty()) {
            const signatureData = signaturePad.toDataURL('image/png');
            document.getElementById('signature_image').value = signatureData;
        }
        
        // Submit form
        submitAssessment();
    });
}

function validateForm() {
    // Clear previous errors
    hideError();
    
    // Check required checkboxes dengan logging
    const requiredCheckboxes = [
        { id: 'patient_verified', label: 'Verifikasi penerima' },
        { id: 'medication_understood', label: 'Pasien memahami penggunaan obat' },
        { id: 'side_effects_explained', label: 'Efek samping telah dijelaskan' },
        { id: 'final_verification', label: 'Verifikasi akhir' }
    ];
    
    for (const checkbox of requiredCheckboxes) {
        const element = document.getElementById(checkbox.id);
        console.log(`Checking ${checkbox.id}:`, element.checked);
        
        if (!element.checked) {
            showError(`Harap centang: "${checkbox.label}"`);
            element.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }
    }
    
    // Check patient condition
    const selectedCondition = document.querySelector('input[name="patient_condition"]:checked');
    console.log('Selected condition:', selectedCondition);
    if (!selectedCondition) {
        showError('Harap pilih kondisi pasien');
        document.querySelector('input[name="patient_condition"]').scrollIntoView({ behavior: 'smooth', block: 'center' });
        return false;
    }
    
    // Check photo
    const photoInput = document.getElementById('handover_photo');
    console.log('Photo files:', photoInput.files.length);
    if (!photoInput.files || photoInput.files.length === 0) {
        showError('Harap ambil foto serah terima obat');
        photoInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return false;
    }
    
    // Check signature
    console.log('Signature empty:', signaturePad.isEmpty());
    if (signaturePad.isEmpty()) {
        showError('Harap tanda tangani di kolom tanda tangan penerima');
        document.getElementById('signaturePad').scrollIntoView({ behavior: 'smooth', block: 'center' });
        return false;
    }
    
    console.log('All validations passed');
    return true;
}

function submitAssessment() {
    if (isSubmitting) return;
    
    isSubmitting = true;
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.innerHTML = '<div class="loading-spinner"></div> Mengirim...';
    submitBtn.disabled = true;
    
    // Create FormData
    const formData = new FormData(document.getElementById('assessmentForm'));
    
    // Log FormData for debugging
    console.log('Submitting assessment...');
    
    // Send request
    fetch('{{ route("delivery-process.submit", $assessment->id) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json().then(data => ({status: response.status, data: data}));
    })
    .then(({status, data}) => {
        console.log('Response data:', data);
        
        if (status === 200 && data.success) {
            showSuccessMessage();
            setTimeout(() => {
                window.location.href = data.redirect_url || '{{ route("delivery-process.index") }}';
            }, 2000);
        } else {
            throw new Error(data.message || data.error || 'Terjadi kesalahan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Gagal mengirim assesmen: ' + error.message);
        
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        isSubmitting = false;
        
        // Scroll to top
        window.scrollTo({top: 0, behavior: 'smooth'});
    });
}

function showSuccessMessage() {
    const successDiv = document.createElement('div');
    successDiv.className = 'success-overlay';
    successDiv.innerHTML = `
        <div class="success-box">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Assesmen Berhasil Dikirim!</h3>
            <p class="text-gray-600 mb-4">Status pengantaran telah diubah menjadi selesai.</p>
            <div class="text-sm text-gray-500">
                <i class="fas fa-spinner fa-spin mr-2"></i> Mengarahkan ke halaman konfirmasi...
            </div>
        </div>
    `;
    
    document.body.appendChild(successDiv);
}

function showError(message) {
    const errorDiv = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');
    errorText.innerHTML = `<i class="fas fa-exclamation-circle mr-2"></i> ${message}`;
    errorDiv.classList.add('show');
}

function hideError() {
    const errorDiv = document.getElementById('errorMessage');
    errorDiv.classList.remove('show');
}

function confirmBack() {
    if (confirm('Apakah Anda yakin ingin kembali? Data yang sudah diisi akan hilang.')) {
        window.history.back();
    }
}
</script>
@endsection