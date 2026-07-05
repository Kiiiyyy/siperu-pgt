<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIPERU PGT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-siperu-yellow min-h-screen flex items-center justify-center p-4 relative overflow-hidden font-sans">

    <div class="absolute -top-20 -left-20 w-64 h-64 rounded-full bg-white/20 blur-sm pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-10 w-96 h-96 rounded-full bg-siperu-blue/10 blur-md pointer-events-none"></div>
    <div class="absolute top-1/4 right-10 w-12 h-12 rounded-full border-4 border-white/30 pointer-events-none"></div>

    <div class="w-full max-w-4xl bg-white rounded-2xl shadow-2xl flex overflow-hidden min-h-[480px] relative z-10">

        <div class="hidden md:flex w-1/2 bg-siperu-blue items-center justify-center p-12 relative overflow-hidden">
            <div class="absolute top-10 right-10 w-24 h-24 rounded-full bg-white/5 pointer-events-none"></div>
            <div class="absolute bottom-[-40px] left-[-20px] w-40 h-40 rounded-full bg-siperu-yellow/10 pointer-events-none"></div>

            <div class="text-center z-10">
                <img src="{{ asset('images/logo-yellow.png') }}" alt="Logo SIPERU PGT" class="w-64 h-auto mx-auto mb-4 drop-shadow-lg" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'text-siperu-yellow font-bold text-3xl tracking-widest mb-2\'>SIPERU</div><div class=\'text-white/70 text-xs tracking-wider uppercase\'>Politeknik Gajah Tunggal</div><p class=\'text-white/50 text-[10px] mt-4\'>(Pindahkan file logo ke public/images/logo-yellow.png)</p>'">
            </div>
        </div>

        <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center bg-gray-50 relative">


            <div class="mb-8">
                <h2 class="text-2xl font-black text-siperu-blue uppercase tracking-wider">Sign In</h2>
                <p class="text-sm text-gray-500 mt-1">Sistem Peminjaman Ruangan Politeknik Gajah Tunggal</p>
            </div>

            @if ($errors->any())
                <div class="mb-5 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-r-md shadow-sm">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        <span class="font-medium">{{ $errors->first() }}</span>
                    </div>
                </div>
            @endif

            <form action="{{ url('/login') }}" method="POST" class="space-y-5 relative z-10">
                @csrf

                <div>
                    <label for="nim" class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Nomor Induk Mahasiswa / Employee ID</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </span>
                        <input type="text"
                               name="nim"
                               id="nim"
                               value="{{ old('nim') }}"
                               placeholder="Masukkan NIM atau Username"
                               required
                               class="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-siperu-yellow focus:ring-2 focus:ring-siperu-yellow/20 text-gray-800 transition duration-200 shadow-sm">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Password Secure</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </span>
                        <input type="password"
                               name="password"
                               id="password"
                               placeholder="••••••••"
                               required
                               class="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-siperu-yellow focus:ring-2 focus:ring-siperu-yellow/20 text-gray-800 transition duration-200 shadow-sm">
                    </div>
                </div>

                <div class="flex justify-center pt-4">
                    <button type="submit"
                            class="w-full py-3.5 bg-siperu-blue hover:bg-opacity-90 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition duration-200 uppercase text-sm tracking-widest border-2 border-transparent focus:ring-4 focus:ring-siperu-blue/30">
                        Sign In System
                    </button>
                </div>
            </form>
        </div>

    </div>

</body>
</html>
