@extends('layouts.app')

@section('title', 'Upload Scan Radiologi')
@section('page-title', 'Hasil Radiologi')
@section('page-subtitle', 'Unggah hasil rontgen/CT-Scan/MRI dalam format JPG/PNG')

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
                    <a href="{{ route('radiology.index') }}" class="text-gray-500 hover:text-tni-600 transition-colors">Hasil Radiologi</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                    <span class="text-tni-700 font-medium font-bold">Upload Scan Baru</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Main Card -->
    <div class="bg-white rounded-[2.5rem] shadow-2xl border border-gray-100 overflow-hidden">
        <!-- Banner -->
        <div class="bg-gradient-to-r from-tni-900 via-tni-800 to-tni-700 p-8 text-white relative">
            <div class="absolute top-0 right-0 p-8 opacity-10">
                <i class="fas fa-x-ray text-8xl"></i>
            </div>
            <div class="relative z-10">
                <h2 class="text-2xl font-bold">Unggah Hasil Radiologi</h2>
                <p class="text-tni-100 opacity-90 text-sm mt-1">Pilih pasien dan unggah file gambar JPG/PNG. Sistem akan menampilkan preview gambar.</p>
            </div>
        </div>

        <form action="{{ route('radiology.store') }}" method="POST" enctype="multipart/form-data" class="p-10 space-y-8">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                <!-- Left Panel: Form Info -->
                <div class="space-y-6">
                    <section class="space-y-5">
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest border-b pb-3 flex items-center">
                            <i class="fas fa-user-injured mr-2 text-tni-600"></i> Identitas Pasien
                        </h3>
                        
                        <div>
                            <label for="patient_id" class="block text-xs font-bold text-gray-500 uppercase mb-2">Pilih Pasien <span class="text-red-500">*</span></label>
                            <select id="patient_id" name="patient_id" required 
                                class="w-full px-5 py-4 bg-gray-50 border-gray-200 rounded-2xl text-sm focus:ring-tni-500 focus:border-tni-500 transition-all font-bold text-gray-700">
                                <option value="">-- Cari & Pilih Pasien --</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" 
                                        data-phone="{{ $patient->phone }}"
                                        data-email="{{ $patient->email }}"
                                        {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->name }} ({{ $patient->patient_code }}) - HP: {{ $patient->phone }}
                                    </option>
                                @endforeach
                            </select>
                            @error('patient_id') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <!-- Dynamic Patient Contact Detail & Bridging Panel -->
                        <div id="patient-bridging-panel" class="p-5 bg-gradient-to-br from-indigo-50/50 to-purple-50/30 border border-indigo-100/70 rounded-2xl text-xs space-y-4">
                            <div class="flex items-center justify-between border-b border-indigo-100/50 pb-2">
                                <span class="font-bold text-indigo-900 uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fas fa-address-book text-indigo-600"></i> Detail Kontak Pasien
                                </span>
                                <span class="px-2 py-0.5 bg-indigo-100 text-indigo-800 rounded-full text-[9px] font-bold uppercase" id="bridging-status">Belum Dipilih</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">No. WhatsApp</span>
                                    <span id="bridging-phone" class="font-bold text-gray-400 italic text-sm flex items-center gap-1.5">Belum tersedia</span>
                                </div>
                                <div class="space-y-1">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Email (Gmail)</span>
                                    <span id="bridging-email" class="font-bold text-gray-400 italic text-sm flex items-center gap-1.5">Belum tersedia</span>
                                </div>
                            </div>
                            <div class="pt-2 flex justify-end">
                                <a id="bridging-btn" href="{{ route('patients.create') }}" class="inline-flex items-center gap-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-[10px] font-bold uppercase tracking-wider transition shadow-sm">
                                    <i class="fas fa-plus"></i> Tambah Pasien Baru
                                </a>
                            </div>
                        </div>
                    </section>

                    <section class="space-y-5">
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest border-b pb-3 flex items-center">
                            <i class="fas fa-file-image mr-2 text-tni-600"></i> File Medis (JPG/PNG)
                        </h3>
                        <div>
    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">File Gambar <span class="text-red-500">*</span></label>
    <input type="file" id="image_file" name="image_files[]" accept="image/*" multiple required class="w-full px-5 py-4 bg-gray-50 border-gray-200 rounded-2xl text-sm focus:ring-tni-500 focus:border-tni-500" />
    @error('image_file') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
</div>

                    </section>                    
                </div>

                <!-- Right Panel: Previews & Fallback -->
                <div class="space-y-6 flex flex-col justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest border-b pb-3 flex items-center">
                            <i class="fas fa-image mr-2 text-tni-600"></i> Visualisasi & Preview Scan
                        </h3>

                        <!-- Canvas Preview Container (Carousel) -->
                        <div id="carousel-container" class="relative w-full max-w-xl mx-auto mt-4 hidden">
                            <button type="button" id="prevBtn" class="absolute left-0 top-1/2 -translate-y-1/2 z-20 bg-gray-800 text-white rounded-full p-2 opacity-70 hover:opacity-100" title="Previous">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <img id="carousel-image" src="" alt="Preview" class="w-full h-auto object-contain rounded-xl shadow" />
                            <button type="button" id="nextBtn" class="absolute right-0 top-1/2 -translate-y-1/2 z-20 bg-gray-800 text-white rounded-full p-2 opacity-70 hover:opacity-100" title="Next">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                            <div id="carousel-indicator" class="absolute bottom-2 left-1/2 -translate-x-1/2 text-sm text-white bg-black/50 px-2 py-1 rounded">0 / 0</div>
                        </div>

                        <!-- Manual Preview Option -->
                        <div id="manual-preview-section" class="hidden mt-6 space-y-4">
                            <div id="manual-upload-note" class="p-4 bg-amber-50 border border-amber-100 rounded-2xl text-xs text-amber-800 font-bold space-y-1">
                                <p class="flex items-center gap-1 text-amber-900">
                                    <i class="fas fa-exclamation-triangle"></i> Gambar scan tidak dapat di-render otomatis
                                </p>
                                <p class="text-gray-600 font-medium">Jika tidak ada preview otomatis, unggah gambar preview (JPG/PNG) di bawah ini untuk laporan WhatsApp/Gmail.</p>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Unggah Preview Gambar Manual (JPG/PNG)</label>
                                <input type="file" id="preview_image" name="preview_image" accept="image/*"
                                    class="w-full px-5 py-4 bg-gray-50 border-gray-200 border rounded-2xl text-sm focus:ring-tni-500 focus:border-tni-500">
                                @error('preview_image') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Hidden field to send base64 data to server -->
                    <input type="hidden" id="preview_image_base64" name="preview_image_base64">
                        <div id="selected-image-preview-container" class="mt-4 hidden"></div>
                            <img id="selected-image-preview" src="#" alt="Selected Image Preview" class="max-w-full rounded-xl hidden" />
                        </div>

                    <!-- Actions -->
                    <div class="pt-6 border-t mt-6 flex flex-col sm:flex-row justify-end gap-3">
                        <a href="{{ route('radiology.index') }}" class="px-8 py-4 text-sm font-bold text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest text-center">
                            Batal
                        </a>
                        <button type="submit" class="px-12 py-4 bg-tni-800 hover:bg-black text-white rounded-[1.5rem] transition-all shadow-xl shadow-tni-200 font-bold uppercase tracking-widest flex items-center justify-center gap-2">
                            <i class="fas fa-cloud-upload-alt"></i> Simpan Hasil Scan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

    <script>
    // Dynamic Patient Details panel updater
    function updatePatientBridgingInfo() {
        const select = document.getElementById('patient_id');
        const selectedOption = select.options[select.selectedIndex];
        
        const statusEl = document.getElementById('bridging-status');
        const phoneEl = document.getElementById('bridging-phone');
        const emailEl = document.getElementById('bridging-email');
        const btnEl = document.getElementById('bridging-btn');
        
        if (selectedOption && selectedOption.value) {
            const phone = selectedOption.getAttribute('data-phone') || '-';
            const email = selectedOption.getAttribute('data-email') || '-';
            const id = selectedOption.value;
            
            statusEl.textContent = 'Ditemukan';
            statusEl.className = 'px-2 py-0.5 bg-green-100 text-green-800 rounded-full text-[9px] font-bold uppercase';
            
            phoneEl.textContent = phone;
            phoneEl.className = 'font-bold text-gray-800 text-sm flex items-center gap-1.5';
            
            emailEl.textContent = email;
            emailEl.className = 'font-bold text-gray-800 text-sm flex items-center gap-1.5';
            
            btnEl.href = `/patients/${id}/edit`;
            btnEl.innerHTML = '<i class="fas fa-edit"></i> Edit Kontak Pasien';
            btnEl.className = 'inline-flex items-center gap-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-[10px] font-bold uppercase tracking-wider transition shadow-sm';
        } else {
            statusEl.textContent = 'Belum Dipilih';
            statusEl.className = 'px-2 py-0.5 bg-indigo-100 text-indigo-800 rounded-full text-[9px] font-bold uppercase';
            
            phoneEl.textContent = 'Belum tersedia';
            phoneEl.className = 'font-bold text-gray-400 italic text-sm flex items-center gap-1.5';
            
            emailEl.textContent = 'Belum tersedia';
            emailEl.className = 'font-bold text-gray-400 italic text-sm flex items-center gap-1.5';
            
            btnEl.href = '{{ route("patients.create") }}';
            btnEl.innerHTML = '<i class="fas fa-plus"></i> Tambah Pasien Baru';
            btnEl.className = 'inline-flex items-center gap-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-[10px] font-bold uppercase tracking-wider transition shadow-sm';
        }
    }

    document.getElementById('patient_id').addEventListener('change', updatePatientBridgingInfo);
    // Initialize on page load
    updatePatientBridgingInfo();

        // Image file carousel preview for JPG/PNG
        const carouselContainer = document.getElementById('carousel-container');
        const carouselImg = document.getElementById('carousel-image');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const indicator = document.getElementById('carousel-indicator');
        let imageData = [];
        let currentIdx = 0;

        function updateCarousel() {
            if (imageData.length === 0) {
                carouselContainer.classList.add('hidden');
                return;
            }
            carouselContainer.classList.remove('hidden');
            carouselImg.src = imageData[currentIdx];
            indicator.textContent = `${currentIdx + 1} / ${imageData.length}`;
        }

        prevBtn.addEventListener('click', () => {
            if (imageData.length === 0) return;
            currentIdx = (currentIdx - 1 + imageData.length) % imageData.length;
            updateCarousel();
        });

        nextBtn.addEventListener('click', () => {
            if (imageData.length === 0) return;
            currentIdx = (currentIdx + 1) % imageData.length;
            updateCarousel();
        });

        document.getElementById('image_file').addEventListener('change', function(event) {
            const files = Array.from(event.target.files);
            imageData = [];
            currentIdx = 0;
            if (files.length === 0) {
                carouselContainer.classList.add('hidden');
                return;
            }
            let loaded = 0;
            files.forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imageData.push(e.target.result);
                    loaded++;
                    if (loaded === files.length) {
                        updateCarousel();
                    }
                };
                reader.readAsDataURL(file);
            });
        });

    </script>
@endsection
