<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Laporan Radiologi - MediTrack</title>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #f8fafc;
        }
        .report-card {
            border-radius: 2rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        }
        .chat-widget {
            border-radius: 1.5rem;
        }
    </style>
</head>
<body class="selection:bg-indigo-500 selection:text-white pb-24">

    <!-- Top Navigation Header -->
    <header class="bg-slate-900 text-white py-6 shadow-lg">
        <div class="max-w-4xl mx-auto px-6 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-lg text-white">
                    <i class="fas fa-x-ray"></i>
                </div>
                <div>
                    <h1 class="text-base font-black leading-tight tracking-tight">MediTrack Portal</h1>
                    <p class="text-[9px] text-amber-400 font-bold uppercase tracking-widest">Rumkit TK III IM Lhokseumawe</p>
                </div>
            </div>
            <span class="px-3 py-1 bg-green-500/20 text-green-400 border border-green-500/30 rounded-full text-[9px] font-bold uppercase tracking-wider">
                Laporan Valid
            </span>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-4xl mx-auto px-6 mt-8 grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left Panel: The Medical Report (Lg: 8 cols) -->
        <div class="lg:col-span-8 bg-white report-card p-6 md:p-8 border border-gray-100 space-y-8">
            <!-- Header Letterhead -->
            <div class="text-center border-b pb-6">
                <h2 class="text-lg font-black text-gray-800">RUMAH SAKIT TNI AD TK III IM 07.01</h2>
                <p class="text-xs text-gray-400 uppercase tracking-widest font-bold mt-1">Instalasi Radiologi & Pencitraan Medis</p>
                <div class="w-16 h-1 bg-slate-900 mx-auto mt-4 rounded-full"></div>
                <div class="mt-4 flex justify-center">
                    <a href="{{ route('radiology.public-report.pdf', $result->share_token) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-md">
                        <i class="fas fa-file-pdf text-red-400"></i> Unduh PDF Laporan Resmi
                    </a>
                </div>
            </div>

            <!-- Patient Info Card -->
            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-1.5">
                    <i class="fas fa-id-card text-slate-500"></i> Data Rekam Medis Pasien
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs font-bold text-slate-500">
                    <div>
                        <span class="text-gray-400 uppercase tracking-wider block text-[9px]">Nama Lengkap</span>
                        <span class="text-slate-800 text-sm font-black">{{ $result->patient->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 uppercase tracking-wider block text-[9px]">No. Rekam Medis</span>
                        <span class="text-slate-800 text-sm font-black">{{ $result->patient->patient_code }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 uppercase tracking-wider block text-[9px]">Jenis Kelamin</span>
                        <span class="text-slate-800 font-medium">{{ $result->patient->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 uppercase tracking-wider block text-[9px]">Tanggal Pemeriksaan</span>
                        <span class="text-slate-800 font-medium">{{ $result->created_at->format('d F Y, H:i') }} WIB</span>
                    </div>
                </div>
            </div>

            <!-- Scan Viewport -->
            @if($result->image_paths && count($result->image_paths) > 0)
                @foreach($result->image_paths as $imgPath)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $imgPath) }}" alt="Gambar Scan" class="w-full max-h-[800px] object-contain rounded-lg shadow" />
                    </div>
                @endforeach
            @endif
<div class="flex justify-end mt-4">
    <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow"><i class="fas fa-print mr-2"></i> Cetak Laporan</button>
</div>
<!-- Doctor findings -->
            <div class="grid grid-cols-1 gap-6 border-t pt-8">
                <div class="space-y-2">
                    <h4 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-notes-medical text-indigo-600"></i> Diagnosis Klinis
                    </h4>
                    <div class="p-5 bg-indigo-50/50 border border-indigo-100 rounded-2xl text-sm text-indigo-950 font-black">
                        {{ $result->diagnosis }}
                    </div>
                </div>

                <div class="space-y-2">
                    <h4 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-stethoscope text-indigo-600"></i> Hasil Ekspertise Dokter Sp.Rad
                    </h4>
                    <div class="p-6 bg-slate-50 border border-slate-100 rounded-2xl text-sm text-slate-700 font-medium whitespace-pre-line leading-relaxed">
                        {{ $result->reading_result }}
                    </div>
                    <div class="text-right text-[10px] text-slate-400 font-bold pr-2">
                        Dokter Penanggung Jawab: <span class="text-slate-700">{{ $result->doctor->name ?? 'Dokter Spesialis Radiologi' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel: Chat widget with Hospital (Lg: 4 cols) -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white chat-widget border border-gray-100 shadow-xl overflow-hidden flex flex-col h-[500px]">
                <!-- Chat widget header -->
                <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 p-4 text-white flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm shadow-inner">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold leading-tight">Hubungi Operator Radiologi</h4>
                        <p class="text-[8px] text-emerald-100 flex items-center gap-1 font-bold mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block animate-pulse"></span> aktif (tanya jawab hasil)
                        </p>
                    </div>
                </div>

                <!-- Chat history display -->
                <div class="flex-1 p-4 overflow-y-auto bg-slate-50 space-y-3" id="patient-portal-chat-body">
                    <!-- Default greeting -->
                    <div class="flex justify-start">
                        <div class="max-w-[85%] rounded-xl px-3 py-1.5 bg-white text-gray-800 text-[11px] shadow-sm rounded-tl-none font-medium leading-relaxed">
                            Halo {{ $result->patient->name }}. Jika ada yang ingin ditanyakan mengenai hasil pemeriksaan radiologi Anda, silakan ketik pesan di bawah ini. Tim medis kami akan segera membantu.
                        </div>
                    </div>

                    @foreach($result->messages->where('channel', 'whatsapp') as $msg)
                        @php $isPatient = $msg->sender_type === 'patient'; @endphp
                        <div class="flex {{ $isPatient ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[85%] rounded-xl px-3 py-1.5 text-[11px] shadow-sm font-medium {{
                                $isPatient 
                                    ? 'bg-[#dcf8c6] text-gray-800 rounded-tr-none' 
                                    : 'bg-white text-gray-800 rounded-tl-none'
                            }}">
                                <p class="leading-relaxed whitespace-pre-wrap">{{ $msg->message_text }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Chat input form -->
                <div class="p-3 bg-white border-t border-gray-100">
                    <form onsubmit="sendPatientPortalMessage(event)" class="flex gap-2 items-center">
                        <input type="text" id="patient-portal-msg-input" placeholder="Tulis pesan Anda di sini..." required
                            class="flex-1 px-4 py-2 bg-gray-50 border-gray-200 rounded-xl text-xs focus:ring-emerald-500 focus:border-emerald-500 font-medium">
                        <button type="submit" id="patient-portal-send-btn" class="w-8 h-8 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center transition shadow-md">
                            <i class="fas fa-paper-plane text-[10px]"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            <p class="text-[10px] text-center text-gray-400 font-bold px-4">
                <i class="fas fa-shield-alt"></i> Sambungan diamankan. Seluruh data medis terenkripsi dan tercatat secara resmi.
            </p>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // scroll chat to bottom
            const chatBody = document.getElementById('patient-portal-chat-body');
            chatBody.scrollTop = chatBody.scrollHeight;
        });

        function sendPatientPortalMessage(e) {
            e.preventDefault();
            const input = document.getElementById('patient-portal-msg-input');
            const text = input.value.trim();
            const sendBtn = document.getElementById('patient-portal-send-btn');
            
            if (!text) return;

            // Disable input while sending
            input.disabled = true;
            sendBtn.disabled = true;

            const formData = new FormData();
            formData.append('message_text', text);
            formData.append('channel', 'whatsapp');

            fetch(`/radiology/chat/{{ $result->id }}/send`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Append bubble to portal chat body
                    const chatBody = document.getElementById('patient-portal-chat-body');
                    const bubbleHtml = `
                        <div class="flex justify-end animate-fade-in">
                            <div class="max-w-[85%] rounded-xl px-3 py-1.5 bg-[#dcf8c6] text-gray-800 text-[11px] shadow-sm rounded-tr-none font-medium leading-relaxed">
                                <p class="leading-relaxed whitespace-pre-wrap">${data.message.message_text}</p>
                            </div>
                        </div>
                    `;
                    chatBody.insertAdjacentHTML('beforeend', bubbleHtml);
                    chatBody.scrollTop = chatBody.scrollHeight;
                    input.value = '';
                }
            })
            .catch(err => {
                console.error("Failed to send message:", err);
            })
            .finally(() => {
                input.disabled = false;
                sendBtn.disabled = false;
                input.focus();
            });
        }
    </script>
</body>
</html>
