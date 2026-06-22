@extends('layouts.app')

@section('title', 'Simulasi Chat Radiologi')
@section('page-title', 'Hasil Radiologi')
@section('page-subtitle', 'Pusat Simulasi Komunikasi Pasien (WhatsApp / Gmail)')

@section('content')
<div class="space-y-6 animate-fade-in pb-12">
    <!-- Split Screen Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 items-start">
        
        <!-- LEFT WORKSPACE (Xl: 7 cols): Hospital Dashboard Chat -->
        <div class="xl:col-span-7 bg-white rounded-[2.5rem] shadow-xl border border-gray-100 flex flex-col h-[750px] overflow-hidden">
            <!-- Header -->
            <div class="p-6 bg-gradient-to-r from-tni-900 to-tni-850 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-tni-800 text-gold-400 flex items-center justify-center text-lg">
                        <i class="fas fa-hospital-user"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm">Dashboard Chat Operator</h3>
                        <p class="text-[10px] text-tni-200">Kanal komunikasi hasil radiologi meditrack</p>
                    </div>
                </div>

                <!-- Select patient thread -->
                <div class="flex items-center gap-2">
                    <select id="chat-thread-selector" onchange="changeActiveThread()" 
                        class="bg-tni-800 border-tni-700 text-white rounded-xl text-xs py-2 px-3 focus:ring-gold-500 font-bold max-w-[200px]">
                        <option value="">-- Pilih Sesi Pasien --</option>
                        @foreach($results as $res)
                            <option value="{{ $res->id }}" {{ request('result_id') == $res->id ? 'selected' : '' }}>
                                {{ $res->patient->name }} ({{ $res->patient->patient_code }})
                            </option>
                        @endforeach
                        @if($completedResults->count() > 0)
                            <optgroup label="Buat Sesi Baru (Ekspertise Selesai)">
                                @foreach($completedResults as $res)
                                    <option value="{{ $res->id }}" {{ request('result_id') == $res->id ? 'selected' : '' }}>
                                        [BARU] {{ $res->patient->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif
                    </select>
                </div>
            </div>

            <!-- Active Patient Sub-Header & Bridging Controls -->
            <div id="active-patient-header" class="hidden px-6 py-4 bg-gradient-to-r from-indigo-50 to-purple-50/50 border-b border-indigo-100/50 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-sm font-bold shadow-md">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div>
                        <h4 id="active-patient-name" class="font-bold text-xs text-gray-800">-</h4>
                        <div class="flex items-center gap-3 text-[10px] text-gray-500 font-semibold mt-0.5">
                            <span class="flex items-center gap-1"><i class="fab fa-whatsapp text-emerald-600"></i> <span id="active-patient-phone">-</span></span>
                            <span>&bull;</span>
                            <span class="flex items-center gap-1"><i class="far fa-envelope text-blue-600"></i> <span id="active-patient-email">-</span></span>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center gap-2">
                    <!-- Bridge to Edit Patient Profile -->
                    <a id="active-edit-patient-btn" href="#" class="px-3 py-1.5 bg-white border border-gray-200 hover:border-indigo-300 text-indigo-700 hover:text-indigo-900 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-all shadow-sm flex items-center gap-1">
                        <i class="fas fa-user-edit text-[9px]"></i> Edit Profil Pasien
                    </a>
                    <!-- Bridge to Update Radiology Result/Scan -->
                    <a id="active-update-scan-btn" href="#" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[10px] font-bold uppercase tracking-wider transition-all shadow-sm flex items-center gap-1">
                        <i class="fas fa-x-ray text-[9px]"></i> Update Hasil Scan
                    </a>
                </div>
            </div>

            <!-- Chat Area Dashboard -->
            <div id="dashboard-chat-body" class="flex-1 p-6 overflow-y-auto bg-gray-50/50 flex flex-col gap-4">
                <!-- Select thread notice -->
                <div id="no-thread-notice" class="flex flex-col items-center justify-center h-full text-center p-10">
                    <div class="w-16 h-16 bg-gray-150 rounded-full flex items-center justify-center text-gray-400 text-2xl mb-4">
                        <i class="far fa-comments"></i>
                    </div>
                    <h4 class="font-bold text-gray-700">Pilih Sesi Percakapan</h4>
                    <p class="text-xs text-gray-400 max-w-xs mt-1">Silakan pilih nama pasien dari dropdown di kanan atas untuk memulai simulasi pengiriman laporan dan tanya jawab.</p>
                </div>

                <!-- Messages container -->
                <div id="dashboard-msg-list" class="space-y-4 hidden flex-1">
                    <!-- Loaded dynamically -->
                </div>
            </div>

            <!-- Input area Dashboard -->
            <div id="dashboard-chat-footer" class="p-4 bg-white border-t border-gray-100 hidden">
                <form id="dashboard-send-form" onsubmit="sendChatMessage(event)" class="flex flex-wrap gap-3 items-center">
                    <div class="flex gap-2 items-center flex-1 min-w-[200px]">
                        <select id="msg-channel" class="bg-gray-50 border border-gray-200 rounded-xl text-xs p-3 font-bold text-gray-700 focus:ring-tni-500">
                            <option value="whatsapp">WhatsApp</option>
                            <option value="email">Gmail</option>
                        </select>
                        
                        <input type="text" id="dashboard-msg-input" required placeholder="Ketik pesan balasan untuk pasien..." 
                            class="flex-1 px-4 py-3 bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-tni-500 focus:border-tni-500 transition-all font-medium">
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <label id="wa-web-checkbox-container" class="flex items-center gap-1.5 text-[10px] text-gray-500 font-bold cursor-pointer">
                            <input type="checkbox" id="wa-web-redirect" checked class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            Hubungkan WA Web
                        </label>
                        
                        <button type="submit" id="dashboard-send-btn" class="px-6 py-3 bg-tni-800 hover:bg-black text-white rounded-xl font-bold transition flex items-center justify-center gap-1.5 shadow-md shadow-tni-100">
                            <i class="fas fa-paper-plane text-xs"></i> Kirim
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- RIGHT WORKSPACE (Xl: 5 cols): Patient Mobile Phone Simulator -->
        <div class="xl:col-span-5 flex justify-center">
            <!-- Smartphone Shell -->
            <div class="relative w-[360px] h-[720px] bg-slate-900 rounded-[3rem] p-3 shadow-2xl border-4 border-slate-800 flex flex-col overflow-hidden ring-8 ring-slate-950">
                <!-- Camera Notch -->
                <div class="absolute top-4 left-1/2 -translate-x-1/2 w-28 h-5 bg-black rounded-full z-50 flex items-center justify-center">
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-900 border border-slate-800"></span>
                </div>

                <!-- Screen Display -->
                <div class="w-full h-full bg-[#efeae2] rounded-[2.5rem] overflow-hidden flex flex-col relative" id="phone-screen">
                    <!-- Status Bar -->
                    <div class="bg-slate-950 text-white px-6 py-2 pt-5 flex justify-between items-center text-[10px] z-40">
                        <span class="font-bold">15:45</span>
                        <div class="flex items-center gap-1.5">
                            <i class="fas fa-signal"></i>
                            <i class="fas fa-wifi"></i>
                            <i class="fas fa-battery-three-quarters"></i>
                        </div>
                    </div>

                    <!-- PHONE INTERFACES -->
                    
                    <!-- 1. WHATSAPP MOCK INTERFACE -->
                    <div id="mock-whatsapp" class="flex flex-col h-full hidden">
                        <!-- Header -->
                        <div class="bg-[#075e54] text-white px-4 py-3 flex items-center gap-2 pt-2 shadow-md z-30">
                            <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-600 font-bold flex items-center justify-center text-sm">
                                R
                            </div>
                            <div class="flex-1">
                                <h4 class="text-xs font-bold text-white leading-tight">Rumkit TK III IM Lhokseumawe</h4>
                                <p class="text-[9px] text-emerald-100 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block animate-pulse"></span> online
                                </p>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-slate-200">
                                <i class="fas fa-video"></i>
                                <i class="fas fa-phone"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>

                        <!-- Chat bubble body -->
                        <div class="flex-1 p-3 overflow-y-auto bg-[#efeae2] space-y-3 pt-4" id="wa-bubbles-container" style="background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); background-size: cover;">
                            <!-- Dynamic messages -->
                        </div>

                        <!-- WhatsApp Typing Status -->
                        <div id="wa-typing-indicator" class="hidden px-4 py-1.5 bg-white/70 text-[10px] text-gray-500 font-bold italic z-20 border-t border-gray-100">
                            Rumkit TK III IM mengetik...
                        </div>

                        <!-- Chat input mock -->
                        <div class="p-2 bg-[#efeae2] border-t border-gray-200/50 flex gap-2 items-center">
                            <div class="flex-1 bg-white rounded-full py-2 px-4 flex items-center gap-2 shadow-sm border text-[11px] text-gray-400">
                                <i class="far fa-laugh-beam"></i>
                                <span>Ketik pesan...</span>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-[#075e54] text-white flex items-center justify-center shadow-md">
                                <i class="fas fa-microphone text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <!-- 2. GMAIL MOCK INTERFACE -->
                    <div id="mock-gmail" class="flex flex-col h-full bg-[#f6f8fc] hidden">
                        <!-- Header -->
                        <div class="bg-white px-4 py-3 border-b flex justify-between items-center z-30 pt-2 shadow-sm">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-arrow-left text-sm text-gray-600"></i>
                                <span class="font-bold text-xs text-gray-800">Detail Email</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-gray-500">
                                <i class="far fa-star"></i>
                                <i class="fas fa-reply"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>

                        <!-- Email body -->
                        <div class="flex-1 p-4 overflow-y-auto space-y-4" id="gmail-email-container">
                            <!-- Dynamic emails list -->
                        </div>
                    </div>
                </div>

                <!-- Simulate patient response control floating button -->
                <button type="button" id="trigger-reply-btn" onclick="triggerSimulatedPatientReply()" 
                    class="absolute bottom-6 right-6 w-12 h-12 bg-gold-500 hover:bg-gold-600 text-black rounded-full flex items-center justify-center shadow-2xl z-50 border border-gold-400 cursor-pointer group hover:scale-105 active:scale-95 transition-all">
                    <i class="fas fa-robot text-lg group-hover:rotate-12 transition-transform"></i>
                    <!-- tooltip -->
                    <span class="absolute right-14 bg-black/90 text-white text-[9px] font-bold py-1.5 px-3 rounded-lg whitespace-nowrap shadow-xl opacity-0 group-hover:opacity-100 transition-opacity">
                        Simulasikan Balasan Pasien
                    </span>
                </button>
            </div>
        </div>

    </div>
</div>

<!-- Sound files (using standard browser synthesized beep as fallback to keep it error-free) -->
<script>
let activeResultId = null;

document.addEventListener('DOMContentLoaded', function() {
    // Check if result_id is in query string
    const selector = document.getElementById('chat-thread-selector');
    if (selector.value) {
        changeActiveThread();
    }
});

function changeActiveThread() {
    const selector = document.getElementById('chat-thread-selector');
    const resultId = selector.value;
    activeResultId = resultId;

    if (!resultId) {
        document.getElementById('no-thread-notice').classList.remove('hidden');
        document.getElementById('dashboard-msg-list').classList.add('hidden');
        document.getElementById('dashboard-chat-footer').classList.add('hidden');
        document.getElementById('mock-whatsapp').classList.add('hidden');
        document.getElementById('mock-gmail').classList.add('hidden');
        document.getElementById('active-patient-header').classList.add('hidden');
        return;
    }

    // Show Chat Area
    document.getElementById('no-thread-notice').classList.add('hidden');
    document.getElementById('dashboard-msg-list').classList.remove('hidden');
    document.getElementById('dashboard-chat-footer').classList.remove('hidden');

    loadChatHistory(resultId);
}

function loadChatHistory(resultId) {
    const msgList = document.getElementById('dashboard-msg-list');
    msgList.innerHTML = '<div class="text-center py-6 text-xs text-gray-400 font-bold uppercase tracking-wider"><i class="fas fa-spinner fa-spin mr-1"></i> Memuat chat...</div>';

    fetch(`/radiology/chat/${resultId}/history`)
        .then(res => res.json())
        .then(data => {
            renderChatHistory(data);
        })
        .catch(err => {
            console.error("Failed to load chat history:", err);
            msgList.innerHTML = '<div class="text-center py-6 text-xs text-red-500 font-bold uppercase tracking-wider"><i class="fas fa-exclamation-triangle mr-1"></i> Gagal memuat percakapan</div>';
        });
}

function renderChatHistory(data) {
    const result = data.result;
    const messages = data.messages;
    const patient = result.patient;
    const channelSelect = document.getElementById('msg-channel');
    
    // Auto-detect channel based on last message or default to whatsapp
    let activeChannel = 'whatsapp';
    if (messages.length > 0) {
        activeChannel = messages[messages.length - 1].channel;
    }
    channelSelect.value = activeChannel;

    // Show active header and populate contact info + bridging links
    const activeHeader = document.getElementById('active-patient-header');
    activeHeader.classList.remove('hidden');
    
    document.getElementById('active-patient-name').textContent = patient.name + ' (' + (patient.patient_code || 'NO-CODE') + ')';
    document.getElementById('active-patient-phone').textContent = patient.phone || '-';
    document.getElementById('active-patient-email').textContent = patient.email || '-';
    
    document.getElementById('active-edit-patient-btn').href = `/patients/${patient.id}/edit`;
    document.getElementById('active-update-scan-btn').href = `/radiology/${result.id}/edit`;

    // Toggle WA Web checkbox visibility
    const checkboxContainer = document.getElementById('wa-web-checkbox-container');
    if (activeChannel === 'whatsapp') {
        checkboxContainer.style.display = 'flex';
    } else {
        checkboxContainer.style.display = 'none';
    }

    // Render Dashboard (Left Panel)
    const dashboardContainer = document.getElementById('dashboard-msg-list');
    dashboardContainer.innerHTML = '';

    // Render Mobile Screens (Right Panel)
    const whatsappContainer = document.getElementById('wa-bubbles-container');
    const gmailContainer = document.getElementById('gmail-email-container');
    whatsappContainer.innerHTML = '';
    gmailContainer.innerHTML = '';

    messages.forEach(msg => {
        appendMessageToDashboard(msg);
        appendMessageToMobile(msg);
    });

    // Toggle Mobile interface view
    toggleMobileInterface(activeChannel);

    // Scroll
    scrollChatToBottom();
}

function toggleMobileInterface(channel) {
    if (channel === 'whatsapp') {
        document.getElementById('mock-whatsapp').classList.remove('hidden');
        document.getElementById('mock-gmail').classList.add('hidden');
        document.getElementById('phone-screen').style.backgroundColor = '#efeae2';
    } else {
        document.getElementById('mock-whatsapp').classList.add('hidden');
        document.getElementById('mock-gmail').classList.remove('hidden');
        document.getElementById('phone-screen').style.backgroundColor = '#f6f8fc';
    }
}

// Bind event for channel change to toggle mobile screens and checkbox
document.getElementById('msg-channel').addEventListener('change', function(e) {
    const channel = e.target.value;
    toggleMobileInterface(channel);
    
    const checkboxContainer = document.getElementById('wa-web-checkbox-container');
    if (channel === 'whatsapp') {
        checkboxContainer.style.display = 'flex';
    } else {
        checkboxContainer.style.display = 'none';
    }
});

function appendMessageToDashboard(msg) {
    const container = document.getElementById('dashboard-msg-list');
    const isPatient = msg.sender_type === 'patient';
    
    const time = new Date(msg.created_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'});
    const channelIcon = msg.channel === 'whatsapp' ? 'fab fa-whatsapp text-emerald-500' : 'far fa-envelope text-blue-500';

    const bubbleHtml = `
        <div class="flex ${isPatient ? 'justify-start' : 'justify-end'} animate-fade-in">
            <div class="max-w-[70%] rounded-2xl p-4 shadow-sm border ${
                isPatient 
                    ? 'bg-white border-gray-100 text-gray-800 rounded-tl-none' 
                    : 'bg-tni-800 border-tni-700 text-white rounded-tr-none'
            }">
                <div class="flex items-center gap-1.5 text-[10px] font-bold ${isPatient ? 'text-gray-400' : 'text-tni-300'} mb-1">
                    <span>${isPatient ? 'Pasien' : (msg.sender ? msg.sender.name : 'Operator')}</span>
                    <span>&bull;</span>
                    <i class="${channelIcon}"></i>
                    <span>${time}</span>
                </div>
                <p class="text-sm font-medium leading-relaxed whitespace-pre-wrap">${msg.message_text}</p>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', bubbleHtml);
}

function appendMessageToMobile(msg) {
    if (msg.channel === 'whatsapp') {
        const container = document.getElementById('wa-bubbles-container');
        const isPatient = msg.sender_type === 'patient';
        const time = new Date(msg.created_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'});

        const waBubble = `
            <div class="flex ${isPatient ? 'justify-start' : 'justify-end'}">
                <div class="relative max-w-[80%] rounded-xl px-3 py-1.5 text-xs shadow-sm ${
                    isPatient 
                        ? 'bg-white text-gray-800 rounded-tl-none' 
                        : 'bg-[#dcf8c6] text-gray-800 rounded-tr-none'
                }">
                    <p class="leading-relaxed whitespace-pre-wrap font-medium pb-2 pr-6">${msg.message_text}</p>
                    <span class="absolute bottom-0.5 right-2 text-[8px] text-gray-400 font-bold">${time}</span>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', waBubble);
    } else {
        const container = document.getElementById('gmail-email-container');
        const isPatient = msg.sender_type === 'patient';
        const time = new Date(msg.created_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'short'}) + ' ' + new Date(msg.created_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'});

        const gmailMail = `
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex flex-col gap-2">
                <div class="flex justify-between items-start border-b pb-2">
                    <div>
                        <h5 class="text-xs font-bold text-gray-800">${isPatient ? 'Pasien' : 'Rumkit TK III IM Lhokseumawe'}</h5>
                        <p class="text-[9px] text-gray-400">${isPatient ? 'patient@gmail.com' : 'radiology@meditrack.id'}</p>
                    </div>
                    <span class="text-[8px] text-gray-400 font-bold">${time}</span>
                </div>
                <p class="text-[11px] text-gray-600 font-medium whitespace-pre-wrap leading-relaxed">${msg.message_text}</p>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', gmailMail);
    }
}

function scrollChatToBottom() {
    const dashboard = document.getElementById('dashboard-chat-body');
    const wa = document.getElementById('wa-bubbles-container');
    const gmail = document.getElementById('gmail-email-container');
    
    dashboard.scrollTop = dashboard.scrollHeight;
    wa.scrollTop = wa.scrollHeight;
    gmail.scrollTop = gmail.scrollHeight;
}

// Send chat message
function sendChatMessage(e) {
    e.preventDefault();
    if (!activeResultId) return;

    const input = document.getElementById('dashboard-msg-input');
    const channel = document.getElementById('msg-channel').value;
    const text = input.value.trim();

    if (!text) return;

    // Disable input
    input.disabled = true;
    document.getElementById('dashboard-send-btn').disabled = true;

    const formData = new FormData();
    formData.append('message_text', text);
    formData.append('channel', channel);

    fetch(`/radiology/chat/${activeResultId}/send`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            appendMessageToDashboard(data.message);
            appendMessageToMobile(data.message);
            scrollChatToBottom();
            
            // If WhatsApp Web redirect is checked, open it in a new window/tab
            if (channel === 'whatsapp' && document.getElementById('wa-web-redirect').checked && data.whatsapp_url) {
                window.open(data.whatsapp_url, '_blank');
            }

            input.value = '';
            
            // Trigger auto reply after 3 seconds
            setTimeout(function() {
                autoPatientReply(channel);
            }, 2500);
        }
    })
    .catch(err => {
        console.error("Error sending message:", err);
    })
    .finally(() => {
        input.disabled = false;
        document.getElementById('dashboard-send-btn').disabled = false;
        input.focus();
    });
}

function autoPatientReply(channel) {
    if (!activeResultId) return;

    // Show typing status on mobile screen
    if (channel === 'whatsapp') {
        document.getElementById('wa-typing-indicator').classList.remove('hidden');
    }

    const formData = new FormData();
    formData.append('channel', channel);

    fetch(`/radiology/chat/${activeResultId}/simulate`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            appendMessageToDashboard(data.message);
            appendMessageToMobile(data.message);
            
            // Play notification tone
            playBeep();
            
            scrollChatToBottom();
        }
    })
    .catch(err => {
        console.error("Error simulation:", err);
    })
    .finally(() => {
        if (channel === 'whatsapp') {
            document.getElementById('wa-typing-indicator').classList.add('hidden');
        }
    });
}

// Manual trigger for simulated reply
function triggerSimulatedPatientReply() {
    if (!activeResultId) {
        alert("Pilih sesi percakapan terlebih dahulu!");
        return;
    }
    const channel = document.getElementById('msg-channel').value;
    autoPatientReply(channel);
}

// Generate web audio API beep sound (error-free, no external asset needed)
function playBeep() {
    try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioCtx.createOscillator();
        const gainNode = audioCtx.createGain();

        oscillator.connect(gainNode);
        gainNode.connect(audioCtx.destination);

        oscillator.type = 'sine';
        oscillator.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5 note
        gainNode.gain.setValueAtTime(0.1, audioCtx.currentTime);

        oscillator.start();
        
        // play double chime
        setTimeout(() => {
            oscillator.frequency.setValueAtTime(880, audioCtx.currentTime); // A5 note
        }, 120);
        
        setTimeout(() => {
            oscillator.stop();
        }, 250);
    } catch (e) {
        console.warn("Audio Context blocked or unsupported");
    }
}
</script>
@endsection
