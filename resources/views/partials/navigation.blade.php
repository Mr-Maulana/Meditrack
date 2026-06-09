<nav class="space-y-1 px-2">
    <!-- Dashboard -->
    <a href="{{ route('dashboard') }}" 
       class="nav-item flex items-center space-x-3 px-3 py-3 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-tni-900 border-l-4 border-gold-500 text-gold-400 font-medium shadow-md shadow-tni-900/50' : 'text-tni-200 hover:bg-tni-700/50 hover:text-white transition-all duration-200' }}">
        <i class="fas fa-tachometer-alt w-5 text-center"></i>
        <span class="nav-text">Dashboard</span>
    </a>
    
    <!-- ADMIN NAVIGATION -->
    @if(auth()->user()->isAdmin())
        <!-- Patients -->
        <div class="space-y-1">
            <button type="button" onclick="toggleSubmenu(this)" 
                    class="nav-item flex items-center justify-between w-full space-x-3 px-3 py-3 rounded-lg {{ request()->routeIs('patients.*') ? 'bg-tni-900 border-l-4 border-gold-500 text-gold-400 font-medium shadow-md shadow-tni-900/50' : 'text-tni-200 hover:bg-tni-700/50 hover:text-white transition-all duration-200' }}">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-users w-5 text-center"></i>
                    <span class="nav-text">Data Pasien</span>
                </div>
                <i class="fas fa-chevron-down submenu-icon text-sm"></i>
            </button>
            
            <!-- Patient Submenu -->
            <div class="submenu ml-4 {{ request()->routeIs('patients.*') ? 'open' : '' }}">
                <a href="{{ route('patients.index') }}" 
                   class="nav-item submenu-item block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('patients.index') ? 'text-gold-400 font-semibold' : 'text-tni-300 hover:text-gold-300 transition-colors' }}">
                    <i class="fas fa-list mr-2 text-xs"></i> Daftar Pasien
                </a>
                <a href="{{ route('patients.create') }}" 
                   class="nav-item submenu-item block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('patients.create') ? 'text-gold-400 font-semibold' : 'text-tni-300 hover:text-gold-300 transition-colors' }}">
                    <i class="fas fa-plus mr-2 text-xs"></i> Tambah Pasien
                </a>
            </div>
        </div>

        <!-- Prescriptions -->
        <div class="space-y-1">
            <button type="button" onclick="toggleSubmenu(this)" 
                    class="nav-item flex items-center justify-between w-full space-x-3 px-3 py-3 rounded-lg {{ request()->routeIs('prescriptions.*') ? 'bg-tni-900 border-l-4 border-gold-500 text-gold-400 font-medium shadow-md shadow-tni-900/50' : 'text-tni-200 hover:bg-tni-700/50 hover:text-white transition-all duration-200' }}">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-prescription-bottle-medical w-5 text-center"></i>
                    <span class="nav-text">Resep Obat</span>
                </div>
                <i class="fas fa-chevron-down submenu-icon text-sm"></i>
            </button>
            
            <!-- Prescription Submenu -->
            <div class="submenu ml-4 {{ request()->routeIs('prescriptions.*') ? 'open' : '' }}">
                <a href="{{ route('prescriptions.index') }}" 
                   class="nav-item submenu-item block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('prescriptions.index') ? 'text-gold-400 font-semibold' : 'text-tni-300 hover:text-gold-300 transition-colors' }}">
                    <i class="fas fa-list mr-2 text-xs"></i> Daftar Resep
                </a>
                <a href="{{ route('prescriptions.create') }}" 
                   class="nav-item submenu-item block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('prescriptions.create') ? 'text-gold-400 font-semibold' : 'text-tni-300 hover:text-gold-300 transition-colors' }}">
                    <i class="fas fa-plus mr-2 text-xs"></i> Buat Resep Baru
                </a>
            </div>
        </div>
        
        <!-- Deliveries -->
        <div class="space-y-1">
            <button type="button" onclick="toggleSubmenu(this)" 
                    class="nav-item flex items-center justify-between w-full space-x-3 px-3 py-3 rounded-lg {{ request()->routeIs('deliveries.*') ? 'bg-tni-900 border-l-4 border-gold-500 text-gold-400 font-medium shadow-md shadow-tni-900/50' : 'text-tni-200 hover:bg-tni-700/50 hover:text-white transition-all duration-200' }}">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-truck-medical w-5 text-center"></i>
                    <span class="nav-text">Pengantaran</span>
                </div>
                <i class="fas fa-chevron-down submenu-icon text-sm"></i>
            </button>
            
            <!-- Delivery Submenu -->
            <div class="submenu ml-4 {{ request()->routeIs('deliveries.*') ? 'open' : '' }}">
                <a href="{{ route('deliveries.index') }}" 
                   class="nav-item submenu-item block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('deliveries.index') ? 'text-gold-400 font-semibold' : 'text-tni-300 hover:text-gold-300 transition-colors' }}">
                    <i class="fas fa-list mr-2 text-xs"></i> Daftar Pengantaran
                </a>
                <a href="{{ route('deliveries.create') }}" 
                   class="nav-item submenu-item block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('deliveries.create') ? 'text-gold-400 font-semibold' : 'text-tni-300 hover:text-gold-300 transition-colors' }}">
                    <i class="fas fa-plus mr-2 text-xs"></i> Buat Pengantaran
                </a>
            </div>
        </div>
        
        <!-- Reports -->
        <div class="space-y-1">
            <button type="button" onclick="toggleSubmenu(this)" 
                    class="nav-item flex items-center justify-between w-full space-x-3 px-3 py-3 rounded-lg {{ request()->routeIs('reports.*') ? 'bg-tni-900 border-l-4 border-gold-500 text-gold-400 font-medium shadow-md shadow-tni-900/50' : 'text-tni-200 hover:bg-tni-700/50 hover:text-white transition-all duration-200' }}">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-chart-bar w-5 text-center"></i>
                    <span class="nav-text">Laporan</span>
                </div>
                <i class="fas fa-chevron-down submenu-icon text-sm"></i>
            </button>
            
            <!-- Reports Submenu -->
            <div class="submenu ml-4 {{ request()->routeIs('reports.*') ? 'open' : '' }}">
                <a href="{{ route('reports.index') }}" 
                   class="nav-item submenu-item block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.index') ? 'text-gold-400 font-semibold' : 'text-tni-300 hover:text-gold-300 transition-colors' }}">
                    <i class="fas fa-file-alt mr-2 text-xs"></i> Generator Laporan
                </a>
            </div>
        </div>

        <!-- User Management -->
        <a href="{{ route('users.index') }}" 
           class="nav-item flex items-center space-x-3 px-3 py-3 rounded-lg {{ request()->routeIs('users.*') ? 'bg-tni-900 border-l-4 border-gold-500 text-gold-400 font-medium shadow-md shadow-tni-900/50' : 'text-tni-200 hover:bg-tni-700/50 hover:text-white transition-all duration-200' }}">
            <i class="fas fa-user-cog w-5 text-center"></i>
            <span class="nav-text">Kelola User</span>
        </a>
    @endif

    <!-- APOTEKER NAVIGATION -->
    @if(auth()->user()->isApoteker())
        <!-- Patients -->
        <div class="space-y-1">
            <button type="button" onclick="toggleSubmenu(this)" 
                    class="nav-item flex items-center justify-between w-full space-x-3 px-3 py-3 rounded-lg {{ request()->routeIs('patients.*') ? 'bg-tni-900 border-l-4 border-gold-500 text-gold-400 font-medium shadow-md shadow-tni-900/50' : 'text-tni-200 hover:bg-tni-700/50 hover:text-white transition-all duration-200' }}">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-users w-5 text-center"></i>
                    <span class="nav-text">Data Pasien</span>
                </div>
                <i class="fas fa-chevron-down submenu-icon text-sm"></i>
            </button>
            
            <!-- Patient Submenu -->
            <div class="submenu ml-4 {{ request()->routeIs('patients.*') ? 'open' : '' }}">
                <a href="{{ route('patients.index') }}" 
                   class="nav-item submenu-item block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('patients.index') ? 'text-gold-400 font-semibold' : 'text-tni-300 hover:text-gold-300 transition-colors' }}">
                    <i class="fas fa-list mr-2 text-xs"></i> Daftar Pasien
                </a>
                <a href="{{ route('patients.create') }}" 
                   class="nav-item submenu-item block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('patients.create') ? 'text-gold-400 font-semibold' : 'text-tni-300 hover:text-gold-300 transition-colors' }}">
                    <i class="fas fa-plus mr-2 text-xs"></i> Tambah Pasien
                </a>
            </div>
        </div>

        <!-- Prescriptions -->
        <div class="space-y-1">
            <button type="button" onclick="toggleSubmenu(this)" 
                    class="nav-item flex items-center justify-between w-full space-x-3 px-3 py-3 rounded-lg {{ request()->routeIs('prescriptions.*') ? 'bg-tni-900 border-l-4 border-gold-500 text-gold-400 font-medium shadow-md shadow-tni-900/50' : 'text-tni-200 hover:bg-tni-700/50 hover:text-white transition-all duration-200' }}">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-prescription-bottle-medical w-5 text-center"></i>
                    <span class="nav-text">Resep Obat</span>
                </div>
                <i class="fas fa-chevron-down submenu-icon text-sm"></i>
            </button>
            
            <!-- Prescription Submenu -->
            <div class="submenu ml-4 {{ request()->routeIs('prescriptions.*') ? 'open' : '' }}">
                <a href="{{ route('prescriptions.index') }}" 
                   class="nav-item submenu-item block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('prescriptions.index') ? 'text-gold-400 font-semibold' : 'text-tni-300 hover:text-gold-300 transition-colors' }}">
                    <i class="fas fa-list mr-2 text-xs"></i> Daftar Resep
                </a>
                <a href="{{ route('prescriptions.create') }}" 
                   class="nav-item submenu-item block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('prescriptions.create') ? 'text-gold-400 font-semibold' : 'text-tni-300 hover:text-gold-300 transition-colors' }}">
                    <i class="fas fa-plus mr-2 text-xs"></i> Buat Resep Baru
                </a>
            </div>
        </div>
        
        <!-- Deliveries -->
        <div class="space-y-1">
            <button type="button" onclick="toggleSubmenu(this)" 
                    class="nav-item flex items-center justify-between w-full space-x-3 px-3 py-3 rounded-lg {{ request()->routeIs('deliveries.*') ? 'bg-tni-900 border-l-4 border-gold-500 text-gold-400 font-medium shadow-md shadow-tni-900/50' : 'text-tni-200 hover:bg-tni-700/50 hover:text-white transition-all duration-200' }}">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-truck-medical w-5 text-center"></i>
                    <span class="nav-text">Pengantaran</span>
                </div>
                <i class="fas fa-chevron-down submenu-icon text-sm"></i>
            </button>
            
            <!-- Delivery Submenu -->
            <div class="submenu ml-4 {{ request()->routeIs('deliveries.*') ? 'open' : '' }}">
                <a href="{{ route('deliveries.index') }}" 
                   class="nav-item submenu-item block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('deliveries.index') ? 'text-gold-400 font-semibold' : 'text-tni-300 hover:text-gold-300 transition-colors' }}">
                    <i class="fas fa-list mr-2 text-xs"></i> Daftar Pengantaran
                </a>
                <a href="{{ route('deliveries.create') }}" 
                   class="nav-item submenu-item block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('deliveries.create') ? 'text-gold-400 font-semibold' : 'text-tni-300 hover:text-gold-300 transition-colors' }}">
                    <i class="fas fa-plus mr-2 text-xs"></i> Buat Pengantaran
                </a>
            </div>
        </div>
        
        <!-- Reports -->
        <div class="space-y-1">
            <button type="button" onclick="toggleSubmenu(this)" 
                    class="nav-item flex items-center justify-between w-full space-x-3 px-3 py-3 rounded-lg {{ request()->routeIs('reports.*') ? 'bg-tni-900 border-l-4 border-gold-500 text-gold-400 font-medium shadow-md shadow-tni-900/50' : 'text-tni-200 hover:bg-tni-700/50 hover:text-white transition-all duration-200' }}">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-chart-bar w-5 text-center"></i>
                    <span class="nav-text">Laporan</span>
                </div>
                <i class="fas fa-chevron-down submenu-icon text-sm"></i>
            </button>
            
            <!-- Reports Submenu -->
            <div class="submenu ml-4 {{ request()->routeIs('reports.*') ? 'open' : '' }}">
                <a href="{{ route('reports.index') }}" 
                   class="nav-item submenu-item block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.index') ? 'text-gold-400 font-semibold' : 'text-tni-300 hover:text-gold-300 transition-colors' }}">
                    <i class="fas fa-file-alt mr-2 text-xs"></i> Laporan Pengantaran
                </a>
            </div>
        </div>
    @endif

    <!-- KURIR NAVIGATION -->
    @if(auth()->user()->isKurir())
        <!-- Delivery Process -->
        <div class="space-y-1">
            <button type="button" onclick="toggleSubmenu(this)" 
                    class="nav-item flex items-center justify-between w-full space-x-3 px-3 py-3 rounded-lg {{ request()->routeIs('delivery-process.*') || request()->routeIs('my-deliveries*') ? 'bg-tni-900 border-l-4 border-gold-500 text-gold-400 font-medium shadow-md shadow-tni-900/50' : 'text-tni-200 hover:bg-tni-700/50 hover:text-white transition-all duration-200' }}">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-truck-fast w-5 text-center"></i>
                    <span class="nav-text">Pengantaran</span>
                </div>
                <i class="fas fa-chevron-down submenu-icon text-sm"></i>
            </button>
            
            <!-- Delivery Process Submenu -->
            <div class="submenu ml-4 {{ request()->routeIs('delivery-process.*') || request()->routeIs('my-deliveries*') ? 'open' : '' }}">
                <a href="{{ route('delivery-process.index') }}" 
                   class="nav-item submenu-item block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('delivery-process.index') ? 'text-gold-400 font-semibold' : 'text-tni-300 hover:text-gold-300 transition-colors' }}">
                    <i class="fas fa-map-marked-alt mr-2 text-xs"></i> Mulai Pengantaran
                </a>
                <a href="{{ route('my-deliveries') }}" 
                   class="nav-item submenu-item block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('my-deliveries*') ? 'text-gold-400 font-semibold' : 'text-tni-300 hover:text-gold-300 transition-colors' }}">
                    <i class="fas fa-history mr-2 text-xs"></i> Riwayat Saya
                </a>
            </div>
        </div>
    @endif
    
    <!-- User Section -->
    <div class="pt-4 border-t border-tni-700/50">
        <a href="{{ route('profile.edit') }}" 
           class="nav-item flex items-center space-x-3 px-3 py-3 rounded-lg {{ request()->routeIs('profile.*') ? 'bg-tni-900 border-l-4 border-gold-500 text-gold-400 font-medium shadow-md shadow-tni-900/50' : 'text-tni-200 hover:bg-tni-700/50 hover:text-white transition-all duration-200' }}">
            <i class="fas fa-user-edit w-5 text-center"></i>
            <span class="nav-text">Edit Profile</span>
        </a>
        
        <form method="POST" action="{{ route('logout') }}" class="inline w-full">
            @csrf
            <button type="submit" 
                    class="nav-item flex items-center space-x-3 px-3 py-3 rounded-lg w-full text-left text-tni-200 hover:bg-tni-700/50 hover:text-white transition-all duration-200">
                <i class="fas fa-sign-out-alt w-5 text-center"></i>
                <span class="nav-text">Logout</span>
            </button>
        </form>
    </div>
</nav>

<style>
    .submenu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
    }

    .submenu.open {
        max-height: 999px;
        transition: max-height 0.3s ease-in;
    }

    .submenu-icon {
        transition: transform 0.3s ease;
    }

    .submenu-icon.rotated {
        transform: rotate(180deg);
    }
</style>

<script>
    // Toggle submenu
    function toggleSubmenu(element) {
        const submenu = element.nextElementSibling;
        if (submenu && submenu.classList.contains('submenu')) {
            const isOpen = submenu.classList.toggle('open');
            submenu.style.maxHeight = isOpen ? `${submenu.scrollHeight}px` : '0';

            const icon = element.querySelector('.submenu-icon');
            if (icon) {
                icon.classList.toggle('rotated', isOpen);
            }
        }
    }
    
    // Auto-open submenu on page load if needed
    document.addEventListener('DOMContentLoaded', function() {
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.nav-item');
        
        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href && currentPath.includes(href.replace(/^.*\/\/[^\/]+/, ''))) {
                // Open parent submenu if exists (only within div wrappers, not matching the main nav.space-y-1)
                const submenu = link.closest('div.space-y-1')?.querySelector('.submenu');
                if (submenu) {
                    submenu.classList.add('open');
                    submenu.style.maxHeight = `${submenu.scrollHeight}px`;

                    const button = submenu.previousElementSibling;
                    const icon = button?.querySelector('.submenu-icon');
                    if (icon) {
                        icon.classList.add('rotated');
                    }
                }
            }
        });
    });
</script>

