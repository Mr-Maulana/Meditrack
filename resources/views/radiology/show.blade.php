@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white dark:bg-gray-900 rounded-2xl shadow-lg">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4">
        Hasil Radiologi – {{ $result->patient->name ?? 'Pasien Tidak Diketahui' }}
    </h1>

    {{-- Patient & Meta Info --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
            <p class="text-gray-600 dark:text-gray-300"><strong>Nama Pasien:</strong> {{ $result->patient->name }}</p>
            <p class="text-gray-600 dark:text-gray-300"><strong>Kode Pasien:</strong> {{ $result->patient->patient_code }}</p>
            <p class="text-gray-600 dark:text-gray-300"><strong>Status:</strong> {{ ucfirst($result->status) }}</p>
        </div>
        <div class="text-right">
            <p class="text-gray-600 dark:text-gray-300"><strong>Dikirim via:</strong> {{ $result->sent_via ?? 'Belum' }}</p>
            @if($result->sent_at)
                <p class="text-gray-600 dark:text-gray-300"><strong>Waktu kirim:</strong> {{ $result->sent_at->format('d M Y H:i') }}</p>
            @endif
        </div>
    </div>

    {{-- Main Scan Images --}}
    @if($result->image_paths && count($result->image_paths) > 0)
    <div id="carousel-container" class="relative w-full max-w-xl mx-auto mt-4">
        <button type="button" id="prevBtn" class="absolute left-0 top-1/2 -translate-y-1/2 z-20 bg-gray-800 text-white rounded-full p-2 opacity-70 hover:opacity-100" title="Previous">
            <i class="fas fa-chevron-left"></i>
        </button>
        <img id="carousel-image" src="" alt="Preview" class="w-full h-auto object-contain rounded-xl shadow" />
        <button type="button" id="nextBtn" class="absolute right-0 top-1/2 -translate-y-1/2 z-20 bg-gray-800 text-white rounded-full p-2 opacity-70 hover:opacity-100" title="Next">
            <i class="fas fa-chevron-right"></i>
        </button>
        <div id="carousel-indicator" class="absolute bottom-2 left-1/2 -translate-x-1/2 text-sm text-white bg-black/50 px-2 py-1 rounded">0 / 0</div>
    </div>
@endif

@push('scripts')
<script>
let imageData = [];
let currentIdx = 0;
const carouselContainer = document.getElementById('carousel-container');
const carouselImg = document.getElementById('carousel-image');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
const indicator = document.getElementById('carousel-indicator');

@php
    $paths = $result->image_paths ?? [];
@endphp
@if(count($paths) > 0)
imageData = {!! json_encode(collect($paths)->map(fn($p) => asset('storage/'.$p))->values()) !!};
@endif

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
updateCarousel();
</script>
@endpush

    {{-- Diagnosis & Reading Result (visible to doctor) --}}
    @if($result->diagnosis)
        <div class="mb-4">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-1">Diagnosis</h2>
            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $result->diagnosis }}</p>
        </div>
    @endif
    @if($result->reading_result)
        <div class="mb-4">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-1">Hasil Baca Dokter</h2>
            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $result->reading_result }}</p>
        </div>
    @endif

    {{-- Share Link (public report) --}}
    <div class="mb-6">
        <p class="text-gray-600 dark:text-gray-300">
            <strong>Link Public:</strong>
            <a href="{{ route('radiology.public-report', $result->share_token) }}" class="text-blue-600 hover:underline" target="_blank">
                {{ route('radiology.public-report', $result->share_token) }}
            </a>
        </p>
    </div>

    {{-- Action Buttons for sending via Email / WhatsApp --}}
    <div class="flex gap-4">
        @if(auth()->user()->isAdmin())
            <form method="POST" action="{{ route('radiology.destroy', $result->id) }}" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow" onclick="return confirm('Yakin ingin menghapus laporan ini?');">
                    <i class="fas fa-trash-alt mr-2"></i> Hapus Laporan
                </button>
            </form>
        @endif
        <form method="POST" action="{{ route('radiology.send', ['id' => $result->id, 'channel' => 'email']) }}">
            @csrf
            <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow">
                <i class="fas fa-envelope mr-2"></i> Kirim ke Email
            </button>
        </form>
        <form method="POST" action="{{ route('radiology.send', ['id' => $result->id, 'channel' => 'whatsapp']) }}">
            @csrf
            <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow">
                <i class="fab fa-whatsapp mr-2"></i> Kirim ke WhatsApp
            </button>
        </form>
        @if($result->status === 'completed')
            <a href="{{ route('radiology.public-report.pdf', $result->share_token) }}" target="_blank" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow inline-flex items-center">
                <i class="fas fa-print mr-2"></i> Cetak Laporan PDF
            </a>
        @endif
        <a href="{{ route('radiology.index') }}" class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg shadow">
            Kembali ke Daftar
        </a>
    </div>
</div>
@endsection
