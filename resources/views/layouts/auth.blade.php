<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MediTrack - Login')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        tni: {
                            50: '#f1f8f1',
                            100: '#e0f0e0',
                            200: '#c2e1c2',
                            300: '#94c994',
                            400: '#61aa61',
                            500: '#3e8e3e',
                            600: '#2d722d',
                            700: '#255b25',
                            800: '#1e481e',
                            900: '#1a3c1a',
                            950: '#0d210d',
                        },
                        gold: {
                            50: '#fffcf2',
                            100: '#fef8e1',
                            200: '#fdeeb3',
                            300: '#fcdf7a',
                            400: '#fbc93c',
                            500: '#f9ac13',
                            600: '#e1890b',
                            700: '#bb660c',
                            800: '#984f11',
                            900: '#7d4111',
                            950: '#482106',
                        },
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(rgba(26, 46, 26, 0.9), rgba(13, 23, 13, 0.95)), url('https://www.tniad.mil.id/wp-content/uploads/2023/05/RSPAD-Gatot-Soebroto.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
        }
        
        .auth-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-radius: 3rem;
            box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="selection:bg-gold-500 selection:text-white">
    <div class="w-full max-w-md mx-4 animate-fade-in">
        <div class="auth-card p-10 overflow-hidden relative">
            <!-- Decorative Elements -->
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gold-400/10 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-tni-800/10 rounded-full blur-2xl"></div>
            
            <!-- Logo/Header -->
            <div class="text-center mb-10 relative z-10">
                <div class="w-20 h-20 bg-gradient-to-br from-gold-300 via-tni-500 to-gold-600 p-[2.5px] rounded-2xl shadow-2xl shadow-gold-500/30 hover:rotate-3 transition-all duration-500 mx-auto mb-6">
                    <div class="w-full h-full bg-tni-950 rounded-[13px] flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('images/logo.png') }}" alt="MediTrack Logo" class="w-full h-full object-cover">
                    </div>
                </div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">MediTrack</h1>
                <div class="flex items-center justify-center gap-2 mt-2">
                    <span class="h-px w-8 bg-gold-500"></span>
                    <p class="text-[10px] font-bold text-gold-600 uppercase tracking-[0.3em]">Rumkit TK III IM 07.01 Lhokseumawe</p>
                    <span class="h-px w-8 bg-gold-500"></span>
                </div>
            </div>
            
            <!-- Content -->
            <div class="relative z-10">
                @yield('content')
            </div>
            
            <!-- Footer -->
            <div class="mt-10 pt-8 border-t border-gray-100 text-center relative z-10">
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest">
                    &copy; {{ date('Y') }} Rumkit TK III IM 07.01 Lhokseumawe • MediTrack v1.0
                </p>
            </div>
        </div>
    </div>
</body>
</html>