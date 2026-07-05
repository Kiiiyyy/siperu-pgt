<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIPERU PGT')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles') </head>
<body class="bg-gray-50 font-sans min-h-screen flex">

    <aside class="w-64 bg-siperu-yellow flex flex-col justify-between fixed h-screen z-20 border-r border-siperu-blue/10">
        <div>
            <div class="p-6 flex items-center gap-3 border-b border-siperu-blue/10">
                <div class="w-10 h-10 bg-siperu-blue rounded-full flex items-center justify-center text-white font-bold text-sm overflow-hidden">
                    <img src="{{ asset('images/logo-yellow.png') }}" alt="S" class="w-full h-full object-cover" onerror="this.onerror=null; this.parentElement.innerHTML='SP'">
                </div>
                <div>
                    <h1 class="text-sm font-black text-siperu-blue tracking-wider uppercase leading-none">SIPERU PGT</h1>
                    <span class="text-[10px] font-medium text-siperu-blue/70">Peminjaman Ruangan</span>
                </div>
            </div>

            <nav class="p-4 space-y-1">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold text-siperu-blue hover:bg-white/30 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 00-2 2z"></path></svg>
                    Jadwal Ruangan
                </a>

                @auth
                    <div class="border-t border-siperu-blue/10 my-2"></div>

                    @if(Auth::user()->role == 'mahasiswa')
                    <a href="{{ route('reservation.create') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold text-siperu-blue hover:bg-white/30 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Peminjaman Baru
                    </a>
                    @endif

                    <a href="{{ route('reservation.history') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold text-siperu-blue hover:bg-white/30 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Riwayat Peminjaman
                    </a>

                    @if(Auth::user()->role == 'dosen')
                    <div class="border-t border-siperu-blue/10 my-2"></div>
                    <a href="{{ route('reservation.approval') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold text-siperu-blue hover:bg-white/30 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Persetujuan Admin
                    </a>
                    <a href="{{ route('reservation.report') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold text-siperu-blue hover:bg-white/30 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Mencetak Laporan PDF
                    </a>
                    @endif

                    @if(Auth::user()->role == 'admin')
                    <div class="border-t border-siperu-blue/10 my-2"></div>
                    <a href="{{ route('admin.master') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold text-siperu-blue hover:bg-white/30 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.58 4 8 4s8-2.21 8-4V7M4 7c0 2.21 3.58 4 8 4s8-2.21 8-4M4 7c0-2.21 3.58-4 8-4s8 2.21 8 4m0 5c0 2.21-3.58 4-8 4s8-2.21 8-4"></path></svg>
                        Data Master
                    </a>
                    @endif
                @endauth
            </nav>
            
        </div>
        @auth
        <div class="p-4 border-t border-siperu-blue/10">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-siperu-blue text-white text-xs font-bold rounded-xl hover:bg-opacity-90 transition uppercase tracking-wider">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Keluar Sistem
                </button>
            </form>
        </div>
        @endauth
    </aside>

    <div class="flex-1 ml-64 flex flex-col min-h-screen">
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-10">
            <div class="text-sm font-bold text-gray-700 uppercase tracking-wide">
                @yield('page_title', 'Halaman Utama')
            </div>

            <div class="flex items-center gap-3">
                @auth
                    <span class="text-xs font-bold text-gray-600 bg-gray-100 px-3 py-1.5 rounded-lg border border-gray-200">
                        {{ Auth::user()->nama }} ({{ strtoupper(Auth::user()->role) }})
                    </span>
                    <div class="w-9 h-9 rounded-full bg-siperu-blue text-white flex items-center justify-center font-bold text-sm shadow-sm">
                        {{ substr(Auth::user()->nama, 0, 1) }}
                    </div>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2 bg-siperu-blue text-white text-xs font-bold rounded-xl hover:bg-opacity-90 transition uppercase tracking-wider shadow-sm">
                        Sign In System
                    </a>
                @endauth
            </div>
        </header>

        <main class="p-8 flex-1">
            @yield('content')
        </main>
    </div>

    @yield('scripts') </body>
</html>
