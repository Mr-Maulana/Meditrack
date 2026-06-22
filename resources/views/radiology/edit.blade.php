@extends('layouts.app')

@section('title', 'Ekspertise Scan Radiologi')
@section('page-title', 'Hasil Radiologi')
@section('page-subtitle', 'Lembar kerja pemeriksaan hasil scan dan rekam diagnosis dokter')

@section('content')
<div class="max-w-7xl mx-auto space-y-8 animate-fade-in pb-12">
    <!-- Breadcrumbs -->
    <nav class="flex no-print" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-tni-600 transition-colors">
                    <i class="fas fa-home mr-2"></i> Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                    <a href="{{ route('radiology.index') }}" class="text-gray-500 hover:text-tni-600 transition-colors">Hasil Radiologi</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                    <span class="text-tni-700 font-medium font-bold">Ekspertise Pasien: {{ $result->patient->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Form wrapper that covers both columns so we can use file inputs/hidden fields inside image viewer -->
    <form action="{{ route('radiology.update', $result->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- Hidden input for file uploads and deletions -->
        <input type="file" id="form-image-files" name="image_files[]" multiple class="hidden">
        <div id="deleted-images-container" class="hidden"></div>
        <input type="file" id="image-file-selector" multiple accept="image/*" onchange="handleNewImagesSelected(this)" class="hidden">

        <!-- Main Workspace Split Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- LEFT PANEL (7 cols): Image Viewer & Gallery CRUD -->
            <div class="lg:col-span-7 bg-slate-950 rounded-[2.5rem] p-6 shadow-2xl border border-slate-800 flex flex-col justify-between relative min-h-[680px]">
                <!-- Header & Controls -->
                <div class="relative z-10 flex items-center justify-between border-b border-slate-800 pb-4 mb-4">
                    <div class="text-slate-200">
                        <h3 class="text-sm font-bold uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse"></span> Workstation Rontgen
                        </h3>
                    </div>
                    <button type="button" onclick="resetViewer()" class="w-8 h-8 rounded-lg bg-slate-850 hover:bg-red-900/40 text-slate-300 flex items-center justify-center transition border border-slate-800" title="Reset View">
                        <i class="fas fa-undo-alt text-xs"></i>
                    </button>
                </div>

                <!-- Image Display with controls -->
                <div class="flex justify-center items-center min-h-[540px] bg-slate-900/30 rounded-2xl relative overflow-hidden p-2 border border-slate-900">
                    <div id="carousel-container" class="relative w-full max-w-2xl mx-auto flex items-center justify-center">
                        <button type="button" id="prevBtn" onclick="prevImage()" class="absolute left-4 top-1/2 -translate-y-1/2 z-20 bg-slate-800/80 hover:bg-slate-700 text-white rounded-full w-10 h-10 flex items-center justify-center shadow transition" title="Previous">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        
                        <!-- Main Image view with absolute X button -->
                        <div class="relative w-full flex items-center justify-center">
                            <img id="carousel-image" src="" alt="Preview" class="max-h-[510px] w-auto object-contain rounded-xl shadow-2xl viewer-img transition-all duration-150" />
                            <button type="button" id="deleteBtn" onclick="deleteActiveImage()" class="absolute top-4 right-4 bg-red-655 hover:bg-red-700 text-white rounded-full w-9 h-9 flex items-center justify-center shadow-lg transition-all duration-200 hover:scale-105 active:scale-95 z-30" title="Hapus gambar rontgen ini">
                                <i class="fas fa-trash-alt text-sm"></i>
                            </button>
                        </div>

                        <button type="button" id="nextBtn" onclick="nextImage()" class="absolute right-4 top-1/2 -translate-y-1/2 z-20 bg-slate-800/80 hover:bg-slate-700 text-white rounded-full w-10 h-10 flex items-center justify-center shadow transition" title="Next">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <div id="carousel-indicator" class="absolute bottom-4 left-1/2 -translate-x-1/2 text-xs font-bold text-slate-350 bg-slate-950/80 backdrop-blur-sm px-3 py-1.5 rounded-full border border-slate-850 shadow-inner">0 / 0</div>
                    </div>
                    
                    <!-- Placeholder for empty scan -->
                    <div id="empty-scan-placeholder" class="hidden flex flex-col items-center justify-center text-center p-8">
                        <div class="w-16 h-16 bg-slate-900 rounded-full flex items-center justify-center border border-slate-800 text-slate-600 text-xl mb-3">
                            <i class="fas fa-image"></i>
                        </div>
                        <p class="text-xs font-semibold text-slate-500">Belum ada gambar rontgen.</p>
                        <p class="text-[10px] text-slate-600 mt-1">Klik tombol "+" di galeri bawah untuk menambah gambar scan.</p>
                    </div>
                </div>

                <!-- Thumbnail Gallery Strip -->
                <div class="mt-4 border-t border-slate-900 pt-4">
                    <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">GALERI SCAN RONTGEN</div>
                    <div class="flex items-center gap-3 overflow-x-auto pb-2 custom-scrollbar" id="thumbnail-strip">
                        <!-- Thumbnails rendered dynamically -->
                    </div>
                </div>

                <!-- Brightness / Contrast Controls -->
                <div class="mt-5 p-3.5 bg-slate-900/35 rounded-2xl border border-slate-900/70 grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <div class="flex justify-between items-center text-slate-400 text-[10px] font-bold tracking-wider">
                            <span>BRIGHTNESS</span>
                            <span id="label-brightness" class="text-blue-400 font-bold">100%</span>
                        </div>
                        <input type="range" id="slider-brightness" min="50" max="200" value="100" class="w-full h-1 bg-slate-800 rounded-lg appearance-none cursor-pointer accent-blue-500">
                    </div>
                    <div class="space-y-1.5">
                        <div class="flex justify-between items-center text-slate-400 text-[10px] font-bold tracking-wider">
                            <span>CONTRAST</span>
                            <span id="label-contrast" class="text-blue-400 font-bold">100%</span>
                        </div>
                        <input type="range" id="slider-contrast" min="50" max="200" value="100" class="w-full h-1 bg-slate-800 rounded-lg appearance-none cursor-pointer accent-blue-500">
                    </div>
                </div>
            </div>

            <!-- RIGHT PANEL (5 cols): Patient Info & Diagnosis Form -->
            <div class="lg:col-span-5 flex flex-col gap-6">
                <!-- Patient Mini Card -->
                <div class="bg-white rounded-[2rem] p-6 shadow-md border border-gray-100 space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-lg font-bold">
                            <i class="fas fa-address-card"></i>
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-gray-800">{{ $result->patient->name }}</h4>
                            <p class="text-xs text-indigo-600 font-bold uppercase tracking-wider">{{ $result->patient->patient_code }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-xs border-t pt-4 font-bold text-gray-500">
                        <div>
                            <span class="text-gray-400 uppercase tracking-wider block text-[10px]">Tgl Lahir</span>
                            <span class="text-gray-800 font-medium">{{ $result->patient->date_of_birth->format('d M Y') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 uppercase tracking-wider block text-[10px]">Jenis Kelamin</span>
                            <span class="text-gray-800 font-medium">{{ $result->patient->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</span>
                        </div>
                    </div>
                </div>

                @if(auth()->user()->isDokter() || auth()->user()->isAdmin() || auth()->user()->isOperator())
                <!-- Diagnosis Form -->
                <div class="bg-white rounded-[2.5rem] p-8 shadow-xl border border-gray-100 space-y-6">
                    <div class="border-b pb-4">
                        <h3 class="text-lg font-bold text-gray-850 flex items-center gap-2">
                            <i class="fas fa-stethoscope text-tni-700"></i> Diagnosis & Ekspertise Dokter
                        </h3>
                        <p class="text-xs text-gray-400 mt-1">Diagnosis klinis dan pembacaan berkas rontgen radiologi.</p>
                    </div>

                    @if(auth()->user()->isDokter() || auth()->user()->isAdmin())
                    <div class="space-y-5">
                        <div class="relative">
                            <label for="diagnosis_search" class="block text-xs font-bold text-gray-500 uppercase mb-2">Diagnosa Klinis (Cari Kode ICD-10) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="text" id="diagnosis_search" name="diagnosis" value="{{ old('diagnosis', $result->diagnosis) }}" required autocomplete="off"
                                    class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm focus:ring-tni-500 focus:border-tni-500 transition-all font-bold pr-10" 
                                    placeholder="Ketik kode ICD-10 (misal: J06.9) atau nama penyakit...">
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                            </div>
                            
                            <!-- ICD-10 Search Dropdown -->
                            <div id="icd-dropdown" class="hidden absolute left-0 right-0 mt-2 max-h-60 overflow-y-auto bg-white border border-gray-200 rounded-2xl shadow-2xl z-50 py-2 custom-scrollbar">
                                <!-- Items rendered dynamically -->
                            </div>
                            @error('diagnosis') <p class="mt-1 text-xs text-red-650 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="reading_result" class="block text-xs font-bold text-gray-500 uppercase mb-2">Hasil Baca Radiologi (Ekspertise) <span class="text-red-500">*</span></label>
                            <textarea id="reading_result" name="reading_result" rows="8" required class="w-full px-5 py-4 bg-gray-50 border-gray-200 rounded-2xl text-sm focus:ring-tni-500 focus:border-tni-500 transition-all font-medium animate-fade-in" placeholder="Tuliskan detail pembacaan medis secara lengkap...">{{ old('reading_result', $result->reading_result) }}</textarea>
                            @error('reading_result') <p class="mt-1 text-xs text-red-650 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    @else
                    <!-- If operator, just display existing reading (disabled/informational) -->
                    <div class="space-y-5 opacity-70">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Diagnosa Klinis (Hanya Dokter)</label>
                            <input type="text" disabled value="{{ $result->diagnosis }}" class="w-full px-5 py-4 bg-gray-100 border-gray-200 rounded-2xl text-sm font-bold text-gray-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Hasil Baca Radiologi (Hanya Dokter)</label>
                            <textarea disabled rows="6" class="w-full px-5 py-4 bg-gray-100 border-gray-200 rounded-2xl text-sm font-medium text-gray-500">{{ $result->reading_result }}</textarea>
                        </div>
                    </div>
                    @endif

                    @error('image_files')
                    <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-xs font-semibold">
                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                    </div>
                    @enderror

                    <div class="pt-4 flex justify-end gap-3">
                        <a href="{{ route('radiology.index') }}" class="px-6 py-4 rounded-2xl border text-sm font-bold text-gray-500 hover:bg-gray-50 transition">Batal</a>
                        <button type="submit" class="px-8 py-4 bg-tni-800 hover:bg-black text-white rounded-[1.5rem] font-bold text-sm uppercase tracking-wider flex items-center justify-center gap-2 shadow-lg shadow-tni-100">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
let scale = 1;
let imagesList = [];
let deletedExistingPaths = [];
let currentIdx = 0;

// Common ICD-10 Codes list
const icdCodes = [
    {"code": "I10", "name": "Essential (primary) hypertension / Hipertensi Primer"},
    {"code": "J06.9", "name": "Acute upper respiratory infection, unspecified / ISPA"},
    {"code": "K30", "name": "Dyspepsia / Dispepsia (Maag)"},
    {"code": "K21.9", "name": "Gastro-esophageal reflux disease without esophagitis / GERD"},
    {"code": "E11.9", "name": "Type 2 diabetes mellitus without complications / Diabetes Melitus Tipe 2"},
    {"code": "A09.9", "name": "Gastroenteritis and colitis of unspecified origin / Diare & Gastroenteritis"},
    {"code": "R50.9", "name": "Fever, unspecified / Demam (Febris)"},
    {"code": "R51", "name": "Headache / Sakit Kepala (Cephalgia)"},
    {"code": "M54.5", "name": "Low back pain / Nyeri Punggung Bawah"},
    {"code": "N39.0", "name": "Urinary tract infection, site not specified / ISK (Infeksi Saluran Kemih)"},
    {"code": "J45.909", "name": "Asthma, unspecified / Asma Bronkial"},
    {"code": "J18.9", "name": "Pneumonia, unspecified / Pneumonia (Radang Paru)"},
    {"code": "A90", "name": "Dengue fever [classical dengue] / Demam Berdarah Dengue (DBD)"},
    {"code": "B34.9", "name": "Viral infection, unspecified / Infeksi Virus"},
    {"code": "L23.9", "name": "Allergic contact dermatitis, unspecified / Dermatitis Alergi"},
    {"code": "H10.9", "name": "Unspecified conjunctivitis / Konjungtivitas (Sakit Mata)"},
    {"code": "K29.7", "name": "Gastritis, unspecified / Gastritis (Radang Lambung)"},
    {"code": "G44.2", "name": "Tension-type headache / Sakit Kepala Tegang"},
    {"code": "J02.9", "name": "Acute pharyngitis, unspecified / Faringitis Akut (Radang Tenggorokan)"},
    {"code": "J03.9", "name": "Acute tonsillitis, unspecified / Tonsilitis Akut (Amdal)"},
    {"code": "M79.1", "name": "Myalgia / Nyeri Otot (Mialgia)"},
    {"code": "R05", "name": "Cough / Batuk"},
    {"code": "R07.4", "name": "Chest pain, unspecified / Nyeri Dada"},
    {"code": "I25.1", "name": "Atherosclerotic heart disease / Penyakit Jantung Koroner (PJK)"},
    {"code": "N20.9", "name": "Urinary calculus, unspecified / Batu Ginjal (Kalkulus Uriner)"},
    {"code": "E78.5", "name": "Hypercholesterolemia / Kolesterol Tinggi (Hiperkolesterolemia)"},
    {"code": "I64", "name": "Stroke, not specified as hemorrhage or infarction / Stroke"},
    {"code": "Z00.0", "name": "General medical examination / MCU (Medical Check Up)"},
    {"code": "J30.9", "name": "Allergic rhinitis, unspecified / Rinitis Alergi"},
    {"code": "L30.9", "name": "Dermatitis, unspecified / Eksim (Dermatitis)"},
    {"code": "E03.9", "name": "Hypothyroidism, unspecified / Hipotiroidisme"},
    {"code": "E05.9", "name": "Thyrotoxicosis, unspecified / Hipertiroidisme"},
    {"code": "A01.0", "name": "Typhoid fever / Demam Tifoid (Tipes)"},
    {"code": "B19", "name": "Unspecified viral hepatitis / Hepatitis Viral"},
    {"code": "B24", "name": "Unspecified human immunodeficiency virus [HIV] disease / HIV"},
    {"code": "A15.0", "name": "Tuberculosis of lung / Tuberkulosis Paru (TB Paru)"},
    {"code": "C34.9", "name": "Malignant neoplasm of unspecified part of bronchus or lung / Kanker Paru"},
    {"code": "I50.9", "name": "Heart failure, unspecified / Gagal Jantung"},
    {"code": "K74.6", "name": "Other and unspecified cirrhosis of liver / Sirosis Hati"},
    {"code": "N18.9", "name": "Chronic kidney disease, unspecified / Gagal Ginjal Kronis (GGK)"},
    {"code": "D64.9", "name": "Anemia, unspecified / Anemia (Kurang Darah)"},
    {"code": "E66.9", "name": "Obesity, unspecified / Obesitas"},
    {"code": "F41.9", "name": "Anxiety disorder, unspecified / Gangguan Kecemasan"},
    {"code": "F32.9", "name": "Depressive episode, unspecified / Depresi"},
    {"code": "G40.9", "name": "Epilepsy, unspecified / Epilepsi (Ayan)"},
    {"code": "K58.9", "name": "Irritable bowel syndrome without diarrhea / IBS (Sindrom Iritasi Usus)"},
    {"code": "M17.9", "name": "Osteoarthritis of knee, unspecified / Radang Sendi Lutut (Osteoartritis)"},
    {"code": "R10.9", "name": "Abdominal pain, unspecified / Nyeri Perut (Kolik Abdomen)"},
    {"code": "R11", "name": "Nausea and vomiting / Mual dan Muntah"},
    
    // Orthopedic & Traumatology / Muskuloskeletal (Radiologi)
    {"code": "S42.00", "name": "Fracture of clavicle / Fraktur Klavikula (Patah Tulang Selangka)"},
    {"code": "S42.30", "name": "Fracture of shaft of humerus / Fraktur Humerus (Patah Lengan Atas)"},
    {"code": "S52.50", "name": "Fracture of lower end of radius / Fraktur Radius (Patah Pergelangan Tangan / Colles)"},
    {"code": "S52.60", "name": "Fracture of lower end of ulna / Fraktur Ulna (Patah Lengan Bawah)"},
    {"code": "S72.00", "name": "Fracture of neck of femur / Fraktur Collum Femur (Patah Leher Paha)"},
    {"code": "S72.30", "name": "Fracture of shaft of femur / Fraktur Femur (Patah Tulang Paha)"},
    {"code": "S82.20", "name": "Fracture of shaft of tibia / Fraktur Tibia (Patah Tulang Kering)"},
    {"code": "S82.40", "name": "Fracture of shaft of fibula / Fraktur Fibula (Patah Tulang Betis)"},
    {"code": "S92.9", "name": "Fracture of foot, unspecified / Fraktur Pedis (Patah Tulang Kaki)"},
    {"code": "S32.00", "name": "Fracture of lumbar vertebra / Fraktur Kompresi Lumbal (Patah Tulang Belakang)"},
    {"code": "S22.4", "name": "Multiple fractures of ribs / Fraktur Costa (Patah Banyak Tulang Rusuk)"},
    {"code": "S43.0", "name": "Dislocation of shoulder joint / Dislokasi Sendi Bahu"},
    {"code": "S83.2", "name": "Tear of meniscus of knee / Robekan Meniskus Lutut (Cedera Bantalan Sendi)"},
    {"code": "S83.5", "name": "Sprain of cruciate ligament of knee / Ruptur ACL/PCL (Cedera Ligamen Lutut)"},
    {"code": "S93.4", "name": "Sprain of ankle / Ankle Sprain (Terkilir/Keseleo Pergelangan Kaki)"},
    {"code": "M19.9", "name": "Arthrosis, unspecified / Osteoartritis Umum (Pengapuran Sendi)"},
    {"code": "M50.9", "name": "Cervical disc disorder / HNP Servikal (Saraf Terjepit Leher)"},
    {"code": "M51.2", "name": "Other intervertebral disc displacement / HNP Lumbal (Saraf Terjepit Pinggang)"},
    {"code": "M81.9", "name": "Osteoporosis, unspecified / Osteoporosis (Pengeroposan Tulang)"},
    {"code": "M41.9", "name": "Scoliosis, unspecified / Skoliosis (Kelainan Tulang Belakang Bengkok)"},
    {"code": "M47.819", "name": "Spondylosis, site unspecified / Spondilosis (Pengapuran Tulang Belakang)"},
    {"code": "M25.56", "name": "Pain in knee / Nyeri Sendi Lutut"},
    {"code": "M25.51", "name": "Pain in shoulder / Nyeri Sendi Bahu"},
    {"code": "M86.9", "name": "Osteomyelitis, unspecified / Osteomielitis (Infeksi Tulang)"},

    // Echocardiography (Echo Jantung Anak & Dewasa)
    {"code": "Q21.0", "name": "Ventricular septal defect / VSD (Kebocoran Sekat Bilik Jantung Anak)"},
    {"code": "Q21.1", "name": "Atrial septal defect / ASD (Kebocoran Sekat Serambi Jantung Anak)"},
    {"code": "Q25.0", "name": "Patent ductus arteriosus / PDA (Kelainan Pembuluh Darah Jantung Anak)"},
    {"code": "Q24.9", "name": "Congenital malformation of heart / Penyakit Jantung Bawaan (PJB Anak)"},
    {"code": "I35.0", "name": "Nonrheumatic aortic stenosis / Stenosis Katup Aorta (Penyempitan Katup Jantung)"},
    {"code": "I34.0", "name": "Mitral valve insufficiency / Regurgitasi/Kebocoran Katup Mitral"},
    {"code": "I42.9", "name": "Cardiomyopathy, unspecified / Kardiomiopati (Pelemahan/Penebalan Otot Jantung)"},
    {"code": "I31.9", "name": "Pericardial effusion / Efusi Perikardial (Cairan di Selaput Jantung)"},
    {"code": "I09.9", "name": "Rheumatic heart disease / Penyakit Jantung Rematik (PJR)"},

    // Ultrasonography (USG Obgyn 4D & USG Kandungan)
    {"code": "Z34.9", "name": "Supervision of normal pregnancy / Pemeriksaan Kehamilan Normal (USG Obgyn 4D / ANC)"},
    {"code": "O26.9", "name": "Pregnancy-related condition / Pemantauan Ibu & Janin (USG Obgyn)"},
    {"code": "O60.0", "name": "Preterm labor without delivery / Ancaman Persalinan Prematur"},
    {"code": "O42.9", "name": "Premature rupture of membranes / Ketuban Pecah Dini (KPD)"},
    {"code": "O43.9", "name": "Placental disorder / Kelainan Letak/Struktur Plasenta"},
    {"code": "O44.1", "name": "Placenta previa with hemorrhage / Plasenta Previa dengan Perdarahan"},
    {"code": "N80.9", "name": "Endometriosis, unspecified / Endometriosis (Kista Cokelat / Penebalan Dinding Rahim)"},
    {"code": "D25.9", "name": "Leiomyoma of uterus / Mioma Uteri (Miom Rahim)"},
    {"code": "N83.20", "name": "Ovarian cysts, unspecified / Kista Ovarium (Kista Indung Telur)"},

    // Ultrasonography (USG Ginjal & Saluran Kemih)
    {"code": "N20.0", "name": "Calculus of kidney / Nefrolitiasis (Batu Ginjal)"},
    {"code": "N20.1", "name": "Calculus of ureter / Ureterolitiasis (Batu Saluran Kemih / Ureter)"},
    {"code": "N13.3", "name": "Hydronephrosis, unspecified / Hidronefrosis (Pelebaran/Pembengkakan Ginjal)"},
    {"code": "N30.9", "name": "Cystitis, unspecified / Sistitis (Infeksi / Peradangan Kandung Kemih)"},
    {"code": "N28.9", "name": "Disorder of kidney and ureter / Kelainan Ginjal dan Ureter"},
    
    // Other Radiology Actions (USG Abdomen / CT Scan / MRI)
    {"code": "J90", "name": "Pleural effusion / Efusi Pleura (Cairan di Rongga Dada / Paru-Paru)"},
    {"code": "R91", "name": "Abnormal findings on diagnostic imaging of lung / Lesi/Nodul/Infiltrat Paru (Foto Thorax)"},
    {"code": "R93.0", "name": "Abnormal findings on diagnostic imaging of skull and head / Kelainan Pencitraan Kepala/Otak (CT Scan/MRI Kepala)"},
    {"code": "I63.9", "name": "Cerebral infarction / Stroke Iskemik (Penyumbatan Pembuluh Darah Otak)"},
    {"code": "I61.9", "name": "Intracerebral hemorrhage / Stroke Hemoragik (Perdarahan Otak)"},
    {"code": "K80.20", "name": "Calculus of gallbladder / Kolelitiasis (Batu Empedu / USG Abdomen)"},
    {"code": "K35.80", "name": "Acute appendicitis / Apendiksitis Akut (Usus Buntu / USG Abdomen)"},
    {"code": "R16.0", "name": "Hepatomegaly / Hepatomegali (Pembesaran Hati / Hati Bengkak)"},
    {"code": "R16.1", "name": "Splenomegaly / Splenomegali (Pembesaran Limpa / Kelenjar Limpa)"}
];

// Initialize images list from php
const existingImages = {!! json_encode(collect($result->image_paths)->map(fn($path) => ['path' => $path, 'url' => asset('storage/' . $path)])->values()) !!};

existingImages.forEach(img => {
    imagesList.push({
        type: 'existing',
        path: img.path,
        url: img.url
    });
});

function updateFilters() {
    const images = document.querySelectorAll('.viewer-img');
    const brightnessInput = document.getElementById('slider-brightness');
    const contrastInput = document.getElementById('slider-contrast');
    const brightness = brightnessInput ? brightnessInput.value : 100;
    const contrast = contrastInput ? contrastInput.value : 100;

    images.forEach(img => {
        img.style.filter = `brightness(${brightness}%) contrast(${contrast}%)`;
    });

    if (brightnessInput) {
        document.getElementById('label-brightness').textContent = `${brightness}%`;
    }
    if (contrastInput) {
        document.getElementById('label-contrast').textContent = `${contrast}%`;
    }
}

function renderGallery() {
    const container = document.getElementById('carousel-container');
    const placeholder = document.getElementById('empty-scan-placeholder');
    const imageEl = document.getElementById('carousel-image');
    const deleteBtn = document.getElementById('deleteBtn');
    const indicator = document.getElementById('carousel-indicator');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    // Handle empty state
    if (imagesList.length === 0) {
        if (container) container.classList.add('hidden');
        if (placeholder) placeholder.classList.remove('hidden');
        if (deleteBtn) deleteBtn.classList.add('hidden');
        renderThumbnailsOnly();
        updateHiddenInputs();
        return;
    }

    if (container) container.classList.remove('hidden');
    if (placeholder) placeholder.classList.add('hidden');
    if (deleteBtn) deleteBtn.classList.remove('hidden');

    // Index boundary check
    if (currentIdx < 0) currentIdx = 0;
    if (currentIdx >= imagesList.length) currentIdx = imagesList.length - 1;

    // Set active image source
    const activeItem = imagesList[currentIdx];
    if (imageEl) imageEl.src = activeItem.url;

    // Update indicator
    if (indicator) {
        indicator.textContent = `${currentIdx + 1} / ${imagesList.length}`;
    }

    // Toggle navigation arrows visibility
    if (prevBtn && nextBtn) {
        if (imagesList.length > 1) {
            prevBtn.classList.remove('hidden');
            nextBtn.classList.remove('hidden');
        } else {
            prevBtn.classList.add('hidden');
            nextBtn.classList.add('hidden');
        }
    }

    renderThumbnailsOnly();
    updateHiddenInputs();
    updateFilters();
}

function renderThumbnailsOnly() {
    const thumbnailStrip = document.getElementById('thumbnail-strip');
    if (!thumbnailStrip) return;

    thumbnailStrip.innerHTML = '';

    // Render each image thumbnail
    imagesList.forEach((item, index) => {
        const isActive = index === currentIdx;
        const borderClass = isActive 
            ? 'border-2 border-blue-500 scale-95 ring-2 ring-blue-500/20' 
            : 'border border-slate-800 hover:border-slate-600 hover:scale-95';
        
        const thumbnailHtml = `
            <div onclick="selectImage(${index})" class="w-16 h-16 shrink-0 rounded-xl overflow-hidden cursor-pointer relative bg-slate-900 transition-all duration-200 ${borderClass}">
                <img src="${item.url}" class="w-full h-full object-cover" />
                ${item.type === 'new' ? '<span class="absolute bottom-1 right-1 w-2.5 h-2.5 bg-green-500 border border-slate-950 rounded-full" title="Upload Baru"></span>' : ''}
            </div>
        `;
        thumbnailStrip.insertAdjacentHTML('beforeend', thumbnailHtml);
    });

    // Append "+" upload button
    const plusBtnHtml = `
        <button type="button" onclick="triggerFileInput()" class="w-16 h-16 shrink-0 border-2 border-dashed border-slate-700 hover:border-blue-500 rounded-xl flex flex-col items-center justify-center text-slate-500 hover:text-blue-500 transition-all duration-250 bg-slate-900/30 hover:bg-slate-900/60 cursor-pointer active:scale-95">
            <i class="fas fa-plus text-lg"></i>
            <span class="text-[8px] font-bold uppercase mt-1">Upload</span>
        </button>
    `;
    thumbnailStrip.insertAdjacentHTML('beforeend', plusBtnHtml);
}

function selectImage(index) {
    currentIdx = index;
    renderGallery();
}

function prevImage() {
    if (imagesList.length === 0) return;
    currentIdx = (currentIdx - 1 + imagesList.length) % imagesList.length;
    renderGallery();
}

// Next slide logic
function nextImage() {
    if (imagesList.length === 0) return;
    currentIdx = (currentIdx + 1) % imagesList.length;
    renderGallery();
}

function triggerFileInput() {
    document.getElementById('image-file-selector').click();
}

function handleNewImagesSelected(input) {
    if (!input.files || input.files.length === 0) return;

    for (let i = 0; i < input.files.length; i++) {
        const file = input.files[i];
        const url = URL.createObjectURL(file);
        
        imagesList.push({
            type: 'new',
            file: file,
            url: url
        });
    }

    input.value = ''; // Reset trigger element

    // Select the last uploaded image
    currentIdx = imagesList.length - 1;
    renderGallery();
}

function deleteActiveImage() {
    if (imagesList.length === 0) return;

    if (imagesList.length <= 1) {
        alert('Laporan radiologi harus memiliki minimal 1 gambar scan.');
        return;
    }

    const itemToDelete = imagesList[currentIdx];

    if (itemToDelete.type === 'existing') {
        deletedExistingPaths.push(itemToDelete.path);
    } else if (itemToDelete.type === 'new') {
        URL.revokeObjectURL(itemToDelete.url); // Release blob URL
    }

    imagesList.splice(currentIdx, 1);

    if (currentIdx >= imagesList.length) {
        currentIdx = imagesList.length - 1;
    }

    renderGallery();
}

function updateHiddenInputs() {
    // Sync deleted files
    const deletedContainer = document.getElementById('deleted-images-container');
    if (deletedContainer) {
        deletedContainer.innerHTML = '';
        deletedExistingPaths.forEach(path => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'deleted_images[]';
            input.value = path;
            deletedContainer.appendChild(input);
        });
    }

    // Sync newly added files to form multiple input
    const formFileInput = document.getElementById('form-image-files');
    if (formFileInput) {
        const dt = new DataTransfer();
        imagesList.forEach(item => {
            if (item.type === 'new') {
                dt.items.add(item.file);
            }
        });
        formFileInput.files = dt.files;
    }
}

function resetViewer() {
    scale = 1;
    document.querySelectorAll('.viewer-img').forEach(img => img.style.transform = 'scale(1)');
    
    const brightnessInput = document.getElementById('slider-brightness');
    const contrastInput = document.getElementById('slider-contrast');
    if (brightnessInput) brightnessInput.value = 100;
    if (contrastInput) contrastInput.value = 100;

    updateFilters();
    if (imagesList.length > 0) {
        currentIdx = 0;
        renderGallery();
    }
}

// ICD-10 search and bridging logic
function initIcdSearch() {
    const input = document.getElementById('diagnosis_search');
    const dropdown = document.getElementById('icd-dropdown');
    if (!input || !dropdown) return;

    input.addEventListener('focus', function() {
        filterIcdCodes(input.value);
    });

    input.addEventListener('input', function() {
        filterIcdCodes(input.value);
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Support keyboard navigation (Up/Down/Enter)
    input.addEventListener('keydown', function(e) {
        const items = dropdown.querySelectorAll('.icd-item');
        if (items.length === 0) return;

        let activeIdx = -1;
        items.forEach((item, idx) => {
            if (item.classList.contains('bg-indigo-50/50') || item.classList.contains('bg-gray-100')) {
                activeIdx = idx;
            }
        });

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            const nextIdx = (activeIdx + 1) % items.length;
            setActiveItem(items, nextIdx);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            const prevIdx = (activeIdx - 1 + items.length) % items.length;
            setActiveItem(items, prevIdx);
        } else if (e.key === 'Enter') {
            if (activeIdx !== -1) {
                e.preventDefault();
                items[activeIdx].click();
            }
        }
    });
}

function setActiveItem(items, index) {
    items.forEach(item => {
        item.classList.remove('bg-indigo-50/50');
        item.classList.remove('bg-gray-100');
    });
    items[index].classList.add('bg-indigo-50/50');
    items[index].scrollIntoView({ block: 'nearest' });
}

function filterIcdCodes(query) {
    const dropdown = document.getElementById('icd-dropdown');
    const input = document.getElementById('diagnosis_search');
    if (!dropdown || !input) return;

    dropdown.innerHTML = '';
    const q = query.toLowerCase().trim();

    // Filter codes based on query
    const filtered = icdCodes.filter(item => {
        return item.code.toLowerCase().includes(q) || item.name.toLowerCase().includes(q);
    });

    if (filtered.length === 0) {
        dropdown.classList.add('hidden');
        return;
    }

    dropdown.classList.remove('hidden');

    filtered.forEach((item, idx) => {
        const itemEl = document.createElement('div');
        itemEl.className = 'icd-item px-5 py-3 hover:bg-indigo-50/50 cursor-pointer text-xs font-semibold text-gray-800 transition flex items-center justify-between border-b border-gray-100/30 last:border-0';
        
        itemEl.innerHTML = `
            <div class="flex items-center gap-2">
                <span class="px-2 py-1 bg-indigo-50 text-indigo-700 rounded-md font-bold text-[10px] uppercase">${item.code}</span>
                <span class="text-gray-700">${item.name}</span>
            </div>
            <i class="fas fa-chevron-right text-gray-300 text-[10px]"></i>
        `;

        itemEl.addEventListener('click', function() {
            input.value = `[${item.code}] ${item.name}`;
            dropdown.classList.add('hidden');
            input.blur();
        });

        dropdown.appendChild(itemEl);
    });
}

function initGallery() {
    renderGallery();
    initIcdSearch();
    
    const brightnessInput = document.getElementById('slider-brightness');
    const contrastInput = document.getElementById('slider-contrast');
    if (brightnessInput) brightnessInput.addEventListener('input', updateFilters);
    if (contrastInput) contrastInput.addEventListener('input', updateFilters);
    
    updateFilters();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initGallery);
} else {
    initGallery();
}

window.updateFilters = updateFilters;
window.resetViewer = resetViewer;
window.deleteActiveImage = deleteActiveImage;
window.triggerFileInput = triggerFileInput;
window.selectImage = selectImage;
window.prevImage = prevImage;
window.nextImage = nextImage;
</script>
@endpush

@endsection
