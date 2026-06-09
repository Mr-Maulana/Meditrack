@props([
    'documentLabel' => 'Dokumen Resmi',
    'documentCode' => 'MT/REP/' . date('Ymd'),
    'reportTitle',
])

<div class="report-letterhead hidden print:block mb-8 border-b-2 border-black pb-6 relative">
    <div class="report-meta absolute top-0 right-0 text-right">
        <div class="px-3 py-2 bg-gray-100 rounded-lg border border-gray-200 inline-block">
            <p class="text-[8px] font-black text-gray-500 uppercase tracking-[0.18em] mb-1">{{ $documentLabel }}</p>
            <p class="text-xs font-black text-tni-900">{{ $documentCode }}</p>
            <p class="text-[9px] text-gray-500 mt-1">Dicetak: {{ now()->format('d/m/Y H:i') }} WIB</p>
        </div>
    </div>
    <div class="flex items-start gap-4">
        <div class="w-16 h-16 flex-shrink-0">
            <img src="{{ asset('images/logo.png') }}" alt="MediTrack Logo" class="w-full h-full object-contain">
        </div>
        <div>
            <h1 class="text-lg font-black uppercase tracking-tight leading-snug text-tni-900">
                MediTrack
            </h1>
            <h1 class="text-base font-bold uppercase tracking-tight leading-snug text-tni-900">
                Rumah Sakit TK III IM 07.01 Lhokseumawe
            </h1>
            <p class="text-xs text-gray-500 mt-1 font-medium">
                Jl. Sultan Iskandar Muda No. 1, Kec. Banda Sakti, Kota Lhokseumawe, Aceh 24311
            </p>
        </div>
    </div>
    <div class="text-center mt-6">
        <h2 class="text-xl font-black uppercase border-b border-black inline-block pb-1 mb-1">{{ $reportTitle }}</h2>
        <p class="text-[10px] font-bold text-gray-600">
            Periode Data: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}
        </p>
    </div>
</div>
