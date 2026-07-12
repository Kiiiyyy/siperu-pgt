<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIPERU PGT')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')
</head>
<body class="bg-gray-50 font-sans min-h-screen flex text-gray-800 antialiased">

    <!-- ==================== SIDEBAR COMPONENT ==================== -->
    <aside class="w-64 bg-siperu-yellow flex flex-col justify-between fixed h-screen z-20 border-r border-siperu-blue/5">
        <div>
            <!-- Identity Header Platform -->
            <div class="p-6 flex items-center gap-3 border-b border-siperu-blue/10">
                <div class="w-9 h-9 bg-siperu-blue rounded-xl flex items-center justify-center text-white font-bold text-sm overflow-hidden shadow-sm">
                    <img src="{{ asset('images/logo-yellow.png') }}" alt="S" class="w-full h-full object-cover" onerror="this.onerror=null; this.parentElement.innerHTML='SP'">
                </div>
                <div>
                    <h1 class="text-xs font-black text-siperu-blue tracking-widest uppercase leading-none">SIPERU PGT</h1>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-siperu-blue/60 mt-0.5 block">Manajemen Ruang</span>
                </div>
            </div>

            <!-- Dynamic Floating Navigation List -->
            <nav class="p-4 space-y-1">
                <!-- Public Accessible Dashboard Schedule -->
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition duration-200 group {{ request()->routeIs('dashboard') ? 'bg-siperu-blue text-white shadow-md shadow-siperu-blue/10' : 'text-siperu-blue hover:bg-white/20' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Jadwal Ruangan
                </a>

                @auth
                    <!-- ==================== JALUR OTORISASI MAHASISWA ==================== -->
                    @if(Auth::user()->role == 'mahasiswa')
                        <div class="pt-3 pb-1 px-4 text-[9px] font-black uppercase tracking-widest text-siperu-blue/40">Fasilitas Berkas</div>
                        
                        <a href="{{ route('reservation.create') }}" 
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition duration-200 group {{ request()->routeIs('reservation.create') ? 'bg-siperu-blue text-white shadow-md shadow-siperu-blue/10' : 'text-siperu-blue hover:bg-white/20' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                            Peminjaman Baru
                        </a>
                    @endif

                    <!-- ==================== MAHASISWA & DOSEN MULTIACCESS ==================== -->
                    @if(Auth::user()->role == 'mahasiswa' || Auth::user()->role == 'dosen')
                        <a href="{{ route('reservation.history') }}" 
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition duration-200 group {{ request()->routeIs('reservation.history') ? 'bg-siperu-blue text-white shadow-md shadow-siperu-blue/10' : 'text-siperu-blue hover:bg-white/20' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            Riwayat Transaksi
                        </a>
                    @endif

                    <!-- ==================== JALUR OTORISASI DOSEN MANAJEMEN ==================== -->
                    @if(Auth::user()->role == 'dosen')
                        <div class="pt-3 pb-1 px-4 text-[9px] font-black uppercase tracking-widest text-siperu-blue/40">Otoritas Obyek</div>
                        
                        <a href="{{ route('reservation.approval') }}" 
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition duration-200 group {{ request()->routeIs('reservation.approval') ? 'bg-siperu-blue text-white shadow-md shadow-siperu-blue/10' : 'text-siperu-blue hover:bg-white/20' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Persetujuan Terpadu
                        </a>
                        
                        <a href="{{ route('reservation.report') }}" 
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition duration-200 group {{ request()->routeIs('reservation.report') ? 'bg-siperu-blue text-white shadow-md shadow-siperu-blue/10' : 'text-siperu-blue hover:bg-white/20' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a4 4 0 00-4-4H3m18 10v-2a4 4 0 00-4-4h-3m-6 0a4 4 0 100-8 4 4 0 000 8zm8 0a4 4 0 100-8 4 4 0 000 8z"></path></svg>
                            Laporan Peminjaman
                        </a>
                    @endif

                    <!-- ==================== JALUR OTORISASI MASTER ADMIN ==================== -->
                    @if(Auth::user()->role == 'admin')
                        <div class="pt-3 pb-1 px-4 text-[9px] font-black uppercase tracking-widest text-siperu-blue/40">Konfigurasi Inti</div>
                        
                        <a href="{{ route('admin.master') }}" 
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition duration-200 group {{ request()->routeIs('admin.master') ? 'bg-siperu-blue text-white shadow-md shadow-siperu-blue/10' : 'text-siperu-blue hover:bg-white/20' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.58 4 8 4s8-2.21 8-4V7M4 7c0 2.21 3.58 4 8 4s8-2.21 8-4M4 7c0-2.21 3.58-4 8-4s8 2.21 8 4"></path></svg>
                            Data Master
                        </a>
                    @endif
                @endauth
            </nav>
        </div>

        <!-- Exit Session Mechanism Footer -->
        @auth
        <div class="p-4 border-t border-siperu-blue/10">
            <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-siperu-blue text-white text-[10px] font-black rounded-xl hover:bg-opacity-95 transition-all uppercase tracking-widest shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Keluar Sistem
                </button>
            </form>
        </div>
        @endauth
    </aside>

    <!-- ==================== MAIN CONTENT SECTION CONTAINER ==================== -->
    <div class="flex-1 ml-64 flex flex-col min-h-screen">
        
        <!-- Header Bar Dashboard -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-10 shadow-sm shadow-gray-100/40">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-widest">
                @yield('page_title', 'Halaman Utama')
            </div>

            <!-- Identity User Node Info -->
            <div class="flex items-center gap-3">
                @auth
                    <span class="text-[10px] font-bold text-gray-500 bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-lg">
                        {{ Auth::user()->nama }} &middot; <span class="text-gray-400 font-semibold uppercase">{{ Auth::user()->role }}</span>
                    </span>
                    <div class="w-8 h-8 rounded-xl bg-siperu-blue text-white flex items-center justify-center font-black text-xs shadow-sm uppercase">
                        {{ substr(Auth::user()->nama, 0, 1) }}
                    </div>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2 bg-siperu-blue text-white text-xs font-bold rounded-xl hover:bg-opacity-90 transition uppercase tracking-wider shadow-sm">
                        Sign In System
                    </a>
                @endauth
            </div>
        </header>

        <!-- Dynamic Main Content Grid View -->
        <main class="p-8 flex-1">
            @yield('content')
        </main>
    </div>

    @yield('scripts')
</body>
</html>