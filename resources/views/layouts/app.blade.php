<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MediTrack - Sistem Antar Obat')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        @media print {
            .no-print,
            .sidebar,
            #mobileSidebar,
            #sidebarOverlay,
            .overlay {
                display: none !important;
            }
            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
            }
            .main-content > nav,
            .main-content > footer {
                display: none !important;
            }
            .main-content > main {
                padding: 0 !important;
                background: #fff !important;
            }
            body {
                font-family: Arial, Helvetica, sans-serif !important;
                font-size: 10pt !important;
                color: #111 !important;
                background: #fff !important;
            }
            .report-document {
                width: 100% !important;
                margin: 0 auto !important;
                padding: 0 !important;
            }
            .report-letterhead {
                page-break-after: avoid;
            }
            .overflow-x-auto {
                overflow-x: visible !important;
            }
            .report-table {
                width: 100% !important;
                border-collapse: collapse !important;
                margin-top: 0 !important;
                table-layout: fixed !important;
                page-break-inside: auto !important;
            }
            .report-table thead {
                display: table-header-group !important;
            }
            .report-table tr {
                page-break-inside: avoid !important;
            }
            .report-table th,
            .report-table td {
                border: 1px solid #999 !important;
                padding: 6px !important;
                word-wrap: break-word !important;
                hyphens: auto !important;
                vertical-align: top !important;
            }
            .report-table th {
                background: #f4f4f4 !important;
                color: #111 !important;
                font-size: 9pt !important;
            }
            .report-table td {
                background: #fff !important;
                color: #111 !important;
                font-size: 9pt !important;
            }
            .print\:bg-gray-100 { background: #f4f4f4 !important; }
            .print\:bg-gray-200 { background: #e5e7eb !important; }
            .print\:shadow-none { box-shadow: none !important; }
            .bg-white { background: #fff !important; }
            .shadow-xl, .shadow-2xl, .shadow-sm { box-shadow: none !important; }
            .rounded-3xl,
            .rounded-[2.5rem],
            .rounded-2xl {
                border-radius: 6px !important;
            }
            .report-stats {
                display: none !important;
            }
            .report-letterhead {
                position: relative;
                padding-top: 1rem;
            }
            .report-letterhead .report-meta {
                position: absolute;
                top: 0;
                right: 0;
                text-align: right;
                font-size: 8pt;
                line-height: 1.2;
            }
            .report-letterhead .report-meta .px-3 {
                padding: 6px 8px !important;
            }
            .page-break {
                page-break-after: always;
            }
        }

        @page {
            size: A4 portrait;
            margin: 15mm;
        }
        
        .sidebar {
            transition: all 0.3s ease;
            width: 260px;
        }
        
        .sidebar.collapsed {
            width: 88px; /* Slightly wider to prevent padding overflow */
        }
        
        .sidebar.collapsed .nav-text {
            display: none;
        }
        
        .sidebar.collapsed .logo-text {
            display: none;
        }
        
        .sidebar.collapsed .toggle-btn .expand {
            display: none;
        }
        
        .sidebar.collapsed .toggle-btn .collapse {
            display: block;
        }
        
        .sidebar.collapsed .user-info {
            display: none;
        }

        .sidebar.collapsed .submenu-icon {
            display: none;
        }

        .sidebar.collapsed .submenu {
            display: none !important;
        }

        .sidebar.collapsed .nav-item {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        .sidebar.collapsed .nav-item > div {
            justify-content: center;
            margin: 0;
        }
        
        .toggle-btn .collapse {
            display: none;
        }
        
        .main-content {
            transition: all 0.3s ease;
            margin-left: 260px;
            width: calc(100% - 260px);
        }
        
        .main-content.expanded {
            margin-left: 88px;
            width: calc(100% - 88px);
        }
        
        .mobile-sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        
        .mobile-sidebar.open {
            transform: translateX(0);
        }
        
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 40;
        }
        
        .overlay.active {
            display: block;
        }
        
        .hamburger-btn {
            display: none;
        }
        
        @media (max-width: 1024px) {
            .sidebar {
                display: none;
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .main-content.expanded {
                margin-left: 0;
                width: 100%;
            }
            
            .hamburger-btn {
                display: block;
            }
            
            .desktop-toggle {
                display: none;
            }
        }
        
        .nav-item {
            transition: all 0.2s ease;
            white-space: nowrap;
            overflow: hidden;
        }
        
        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        
        .submenu.open {
            max-height: 500px;
        }
        
        .submenu-item {
            padding-left: 3rem;
        }
        
        .print-label {
            width: 80mm !important;
            max-width: 80mm !important;
            font-family: 'Courier New', monospace;
            font-size: 9pt;
        }

        .leaflet-control-locate a {
            background-color: #3b82f6 !important;
            color: white !important;
        }

        .leaflet-control-locate.active a {
            background-color: #10b981 !important;
        }

        .leaflet-routing-container {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            max-height: 400px;
            overflow-y: auto;
        }

        .leaflet-routing-alt {
            max-height: 300px !important;
        }
    </style>
    
    @yield('styles')
</head>
<body class="bg-[#f8fafc] text-gray-800 font-sans antialiased selection:bg-tni-500 selection:text-white">
    <!-- Overlay untuk mobile -->
    <div id="overlay" class="overlay" onclick="toggleMobileSidebar()"></div>

    @if(Auth::check())
    <!-- Desktop Sidebar -->
    <div class="sidebar no-print fixed left-0 top-0 h-screen bg-tni-800 shadow-2xl z-30 hidden lg:block border-r border-tni-700" id="desktopSidebar">
        <div class="h-full flex flex-col">
            <!-- Logo & Toggle -->
            <div class="p-5 border-b border-tni-700/50 flex items-center justify-between bg-tni-900/30">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-gold-300 via-tni-500 to-gold-600 p-[1.5px] rounded-xl shadow-lg shadow-gold-500/20 hover:scale-105 hover:shadow-gold-500/40 transition-all duration-300">
                        <div class="w-full h-full bg-tni-950 rounded-[10px] flex items-center justify-center overflow-hidden">
                            <img src="{{ asset('images/logo.png') }}" alt="MediTrack Logo" class="w-full h-full object-cover">
                        </div>
                    </div>
                    <div class="logo-text">
                        <h2 class="text-xl font-bold text-white tracking-tight leading-tight">MediTrack</h2>
                        <p class="text-[10px] text-gold-400 font-semibold tracking-wider uppercase">Rumkit TK III IM 07.01 Lhokseumawe</p>
                    </div>
                </div>
                
                <!-- Desktop Toggle Button -->
                <button type="button" onclick="toggleDesktopSidebar()" class="toggle-btn p-2 rounded-lg hover:bg-tni-700 text-tni-200 transition-colors">
                    <i class="fas fa-chevron-left expand"></i>
                    <i class="fas fa-chevron-right collapse"></i>
                </button>
            </div>
            
            <!-- Navigation -->
            <div class="flex-1 overflow-y-auto py-4 custom-scrollbar">
                @include('partials.navigation')
            </div>
            
            <!-- User Info -->
            <div class="p-4 border-t border-tni-700/50 bg-tni-900/30">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-tni-600 rounded-full flex items-center justify-center text-gold-400 font-bold shadow-inner border border-tni-500 overflow-hidden">
                        @if(auth()->user()->profile_photo)
                            <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="Profile" class="w-full h-full object-cover">
                        @else
                            {{ substr(auth()->user()->name, 0, 1) }}
                        @endif
                    </div>
                    <div class="user-info">
                        <div class="text-sm font-medium text-white">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-tni-300 capitalize flex items-center mt-0.5">
                            <span class="w-2 h-2 rounded-full bg-gold-500 mr-1.5 animate-pulse"></span>
                            {{ auth()->user()->role }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Sidebar -->
    <div class="mobile-sidebar no-print fixed left-0 top-0 h-screen w-64 bg-tni-800 shadow-2xl z-50 lg:hidden transform -translate-x-full transition-transform duration-300" id="mobileSidebar">
        <div class="h-full flex flex-col">
            <!-- Mobile Header -->
            <div class="p-5 border-b border-tni-700/50 flex items-center justify-between bg-tni-900/50">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-gold-300 via-tni-500 to-gold-600 p-[1.5px] rounded-xl shadow-lg shadow-gold-500/20">
                        <div class="w-full h-full bg-tni-950 rounded-[10px] flex items-center justify-center overflow-hidden">
                            <img src="{{ asset('images/logo.png') }}" alt="MediTrack Logo" class="w-full h-full object-cover">
                        </div>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-white leading-tight">MediTrack</h2>
                        <p class="text-[9px] text-gold-400 font-bold uppercase tracking-widest">Rumkit TK III IM</p>
                    </div>
                </div>
                <button type="button" onclick="toggleMobileSidebar()" class="w-8 h-8 flex items-center justify-center text-tni-300 hover:text-white hover:bg-tni-700 rounded-full transition-all">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            
            <!-- Mobile Navigation -->
            <div class="flex-1 overflow-y-auto py-4 px-2">
                @include('partials.navigation')
            </div>
            
            <!-- Mobile User Info -->
            <div class="p-5 border-t border-tni-700/50 bg-tni-900/30">
                <div class="flex items-center space-x-3">
                    <div class="w-11 h-11 bg-tni-700 rounded-full flex items-center justify-center text-gold-400 font-bold shadow-inner border border-tni-600 overflow-hidden">
                        @if(auth()->user()->profile_photo)
                            <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="Profile" class="w-full h-full object-cover">
                        @else
                            {{ substr(auth()->user()->name, 0, 1) }}
                        @endif
                    </div>
                    <div>
                        <div class="text-sm font-bold text-white">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-tni-400 font-bold uppercase flex items-center">
                            <span class="w-1.5 h-1.5 rounded-full bg-gold-500 mr-1.5"></span>
                            {{ auth()->user()->role }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Overlay -->
    <div class="overlay no-print fixed inset-0 bg-tni-900/60 backdrop-blur-sm z-40 hidden" id="sidebarOverlay" onclick="toggleMobileSidebar()"></div>

    <!-- Main Content -->
    <div class="main-content min-h-screen flex flex-col" id="mainContent">
        <!-- Top Navigation -->
        <nav class="no-print bg-white shadow-sm border-b border-gray-100 sticky top-0 z-20 transition-all duration-300">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <!-- Mobile Hamburger Button -->
                        <button type="button" onclick="toggleMobileSidebar()" class="hamburger-btn text-gray-500 hover:text-gray-700 mr-4">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        
                        <!-- Desktop Toggle Button -->
                        <button type="button" onclick="toggleDesktopSidebar()" class="desktop-toggle text-gray-500 hover:text-gray-700 mr-4">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        
                        <div class="ml-2">
                            <h1 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                            <p class="text-xs text-gray-500 hidden md:block">
                                @yield('page-subtitle', 'Sistem Manajemen Pengantaran Obat')
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <div class="relative">
                            <button type="button" onclick="toggleNotifications()" class="text-gray-500 hover:text-gray-700 relative p-2 rounded-full hover:bg-gray-100 transition-colors">
                                <i class="fas fa-bell text-xl"></i>
                                @if(($unreadCount ?? 0) > 0)
                                <span class="absolute top-1 right-1 w-4 h-4 bg-red-600 text-white text-[10px] rounded-full flex items-center justify-center font-bold">
                                    {{ ($unreadCount ?? 0) > 9 ? '9+' : $unreadCount }}
                                </span>
                                @endif
                            </button>
                            
                            <!-- Notifications Dropdown -->
                            <div id="notificationsMenu" class="hidden absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 py-0 z-50 overflow-hidden transform transition-all">
                                <div class="px-5 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                                    <h3 class="text-sm font-bold text-gray-800">Notifikasi</h3>
                                    @if(($unreadCount ?? 0) > 0)
                                    <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-[10px] text-blue-600 font-bold hover:underline uppercase tracking-wider">Tandai Dibaca</button>
                                    </form>
                                    @endif
                                </div>
                                <div class="max-h-[400px] overflow-y-auto">
                                    @forelse(($notifications ?? collect()) as $notification)
                                    <a href="{{ route('notifications.mark-as-read', $notification->id) }}" class="block px-5 py-4 hover:bg-gray-50 border-b border-gray-100 transition-colors {{ $notification->unread() ? 'bg-blue-50/30' : '' }}">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 bg-{{ $notification->data['color'] ?? 'blue' }}-100 text-{{ $notification->data['color'] ?? 'blue' }}-600 rounded-xl flex items-center justify-center shadow-sm">
                                                    <i class="{{ $notification->data['icon'] ?? 'fas fa-info-circle' }} text-sm"></i>
                                                </div>
                                            </div>
                                            <div class="ml-3 flex-1">
                                                <div class="flex justify-between items-start">
                                                    <p class="text-xs font-bold text-gray-900">{{ $notification->data['title'] ?? 'Notifikasi' }}</p>
                                                    <span class="text-[9px] text-gray-400 font-medium whitespace-nowrap ml-2">{{ $notification->created_at->diffForHumans() }}</span>
                                                </div>
                                                <p class="text-[11px] text-gray-600 mt-1 leading-relaxed">{{ $notification->data['message'] ?? '' }}</p>
                                            </div>
                                        </div>
                                    </a>
                                    @empty
                                    <div class="px-5 py-10 text-center">
                                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-bell-slash text-2xl text-gray-200"></i>
                                        </div>
                                        <p class="text-sm text-gray-500 font-medium">Tidak ada notifikasi baru</p>
                                    </div>
                                    @endforelse
                                </div>
                                @if(($notifications ?? collect())->count() > 0)
                                <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 text-center">
                                    <a href="#" class="text-xs text-tni-700 font-bold hover:text-black transition-colors uppercase tracking-widest">
                                        Lihat Semua
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- User Menu -->
                        <div class="relative">
                            <button type="button" onclick="toggleUserMenu()" class="flex items-center space-x-3 focus:outline-none p-1 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="w-9 h-9 bg-tni-700 rounded-full flex items-center justify-center text-gold-400 shadow border border-tni-600 font-bold overflow-hidden">
                                    @if(auth()->user()->profile_photo)
                                        <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="Profile" class="w-full h-full object-cover">
                                    @else
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    @endif
                                </div>
                                <div class="hidden md:flex flex-col items-start">
                                    <span class="text-sm font-semibold text-gray-800 leading-tight">{{ auth()->user()->name }}</span>
                                    <span class="text-[10px] text-gray-500 leading-tight capitalize">{{ auth()->user()->role }}</span>
                                </div>
                                <i class="fas fa-chevron-down text-gray-400 text-xs ml-1"></i>
                            </button>
                            
                            <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                <div class="px-4 py-3 border-b border-gray-200">
                                    <div class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</div>
                                    <div class="text-xs text-gray-500">{{ auth()->user()->email }}</div>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i> Profil
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog mr-2"></i> Pengaturan
                                </a>
                                <div class="border-t border-gray-100"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-gray-50">
            <!-- Flash Messages -->
            @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
            @endif
            
            @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
            @endif
            
            @if(session('print'))
            <div class="mb-4 p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded flex justify-between items-center">
                <span>Data pasien berhasil disimpan! Cetak label untuk ditempel pada obat.</span>
                <a href="{{ route('patients.print', session('patient_id') ?? '') }}" target="_blank" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded no-print">
                    <i class="fas fa-print mr-2"></i> Cetak Label
                </a>
            </div>
            @endif

            @yield('content')
        </main>
        
        <!-- Footer -->
        <footer class="no-print bg-white border-t border-gray-200 py-4 px-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-sm text-gray-600">
                    &copy; {{ date('Y') }} MediTrack - Sistem Antar Obat
                </div>
                <div class="mt-2 md:mt-0">
                    <span class="text-xs text-gray-500">
                        Version 1.0.0 | 
                        <span id="currentTime"></span>
                    </span>
                </div>
            </div>
        </footer>
    </div>
    @else
    <!-- Guest Layout -->
    @yield('content')
    @endif

    <script>
        // Sidebar state
        let sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        
        // Initialize sidebar state
        document.addEventListener('DOMContentLoaded', function() {
            // Set initial state
            if (sidebarCollapsed) {
                collapseDesktopSidebar();
            } else {
                expandDesktopSidebar();
            }
            
            // Update current time
            updateCurrentTime();
            setInterval(updateCurrentTime, 60000);
            
            // Check active menu
            highlightActiveMenu();
            bindMobileNavLinks();
        });
        
        function bindMobileNavLinks() {
            document.querySelectorAll('#mobileSidebar a.nav-item').forEach(link => {
                link.addEventListener('click', () => {
                    const sidebar = document.getElementById('mobileSidebar');
                    const overlay = document.getElementById('sidebarOverlay');
                    if (sidebar && overlay && sidebar.classList.contains('open')) {
                        sidebar.classList.remove('open');
                        overlay.classList.remove('active');
                        document.body.style.overflow = '';
                    }
                });
            });
        }
        
        function toggleDesktopSidebar() {
            if (sidebarCollapsed) {
                expandDesktopSidebar();
            } else {
                collapseDesktopSidebar();
            }
            sidebarCollapsed = !sidebarCollapsed;
            localStorage.setItem('sidebarCollapsed', sidebarCollapsed);
        }
        
        function collapseDesktopSidebar() {
            const sidebar = document.getElementById('desktopSidebar');
            const mainContent = document.getElementById('mainContent');
            if (sidebar && mainContent) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
                
                // Automatically close all open submenus when sidebar is collapsed
                document.querySelectorAll('.submenu.open').forEach(menu => {
                    menu.classList.remove('open');
                });
            }
        }
        
        function expandDesktopSidebar() {
            const sidebar = document.getElementById('desktopSidebar');
            const mainContent = document.getElementById('mainContent');
            if (sidebar && mainContent) {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('expanded');
            }
        }
        
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('mobileSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (sidebar && overlay) {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('active');
                document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
            }
        }
        
        function toggleUserMenu() {
            const userMenu = document.getElementById('userMenu');
            if (userMenu) {
                userMenu.classList.toggle('hidden');
            }
        }
        
        function toggleNotifications() {
            const notificationsMenu = document.getElementById('notificationsMenu');
            if (notificationsMenu) {
                notificationsMenu.classList.toggle('hidden');
            }
        }
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.getElementById('userMenu');
            const userMenuButton = document.querySelector('button[onclick="toggleUserMenu()"]');
            const notificationsMenu = document.getElementById('notificationsMenu');
            const notificationsButton = document.querySelector('button[onclick="toggleNotifications()"]');
            
            if (userMenu && userMenuButton && !userMenu.contains(event.target) && !userMenuButton.contains(event.target)) {
                userMenu.classList.add('hidden');
            }
            
            if (notificationsMenu && notificationsButton && !notificationsMenu.contains(event.target) && !notificationsButton.contains(event.target)) {
                notificationsMenu.classList.add('hidden');
            }
        });
        
        // Auto print for label pages
        @if(request()->has('print') || (isset($print) && $print))
        window.onload = function() {
            window.print();
        };
        @endif
        
        // Dynamic form for medications
        function addMedicationField() {
            const container = document.getElementById('medications-container');
            const index = container.children.length;
            
            const html = `
                <div class="border border-gray-300 rounded p-4 mb-4 medication-item">
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="font-medium">Obat ${index + 1}</h4>
                        <button type="button" onclick="removeMedicationField(this)" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Obat</label>
                            <input type="text" name="medications[${index}][name]" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dosis</label>
                            <input type="text" name="medications[${index}][dosage]" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="contoh: 500mg, 1 tablet">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Frekuensi</label>
                            <input type="text" name="medications[${index}][frequency]" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="contoh: 3x1, 1x1">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Durasi</label>
                            <input type="text" name="medications[${index}][duration]" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="contoh: 7 hari, 30 hari">
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Instruksi Khusus</label>
                        <textarea name="medications[${index}][instructions]" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="contoh: Sesudah makan, sebelum tidur"></textarea>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', html);
        }
        
        function removeMedicationField(button) {
            if (document.querySelectorAll('.medication-item').length > 1) {
                button.closest('.medication-item').remove();
                // Reindex remaining items
                document.querySelectorAll('.medication-item').forEach((item, index) => {
                    item.querySelector('h4').textContent = `Obat ${index + 1}`;
                    // Update input names
                    const inputs = item.querySelectorAll('input, textarea');
                    inputs.forEach(input => {
                        const name = input.getAttribute('name');
                        if (name) {
                            const newName = name.replace(/medications\[\d+\]/, `medications[${index}]`);
                            input.setAttribute('name', newName);
                        }
                    });
                });
            }
        }
        
        function updateCurrentTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', { 
                hour: '2-digit', 
                minute: '2-digit',
                hour12: false 
            });
            const dateString = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            document.getElementById('currentTime').textContent = 
                `${dateString} ${timeString}`;
        }
        
        function highlightActiveMenu() {
            const currentPath = window.location.pathname;
            const navItems = document.querySelectorAll('.nav-item');
            
            navItems.forEach(item => {
                item.classList.remove('active');
                const href = item.getAttribute('href');
                if (href && currentPath.includes(href.replace(/^.*\/\/[^\/]+/, ''))) {
                    item.classList.add('active');
                }
            });
        }
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(event) {
            // Ctrl + B to toggle sidebar
            if (event.ctrlKey && event.key === 'b') {
                event.preventDefault();
                toggleDesktopSidebar();
            }
            
            // Esc to close mobile sidebar
            if (event.key === 'Escape') {
                const mobileSidebar = document.getElementById('mobileSidebar');
                if (mobileSidebar && mobileSidebar.classList.contains('open')) {
                    toggleMobileSidebar();
                }
            }
        });
        
        // Auto resize sidebar on window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                // Close mobile sidebar on desktop
                const mobileSidebar = document.getElementById('mobileSidebar');
                const overlay = document.getElementById('sidebarOverlay');
                if (mobileSidebar && overlay) {
                    mobileSidebar.classList.remove('open');
                    overlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html>
