@extends('layouts.app')

@section('title', 'Dashboard Jadwal - SIPERU PGT')
@section('page_title', 'Dashboard / Jadwal Ruangan')

@section('content')
<!-- Baris Header Konten -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h2 class="text-xl font-bold text-siperu-blue">
            Welcome{{ Auth::check() ? ', ' . Auth::user()->nama : ' to SIPERU PGT' }}!
        </h2>
        <p class="text-xs text-gray-500">Periksa ketersediaan ruangan sebelum melakukan pengajuan.</p>
    </div>

    @auth
        @if(Auth::user()->role == 'mahasiswa')
        <a href="{{ route('reservation.create') }}" class="px-5 py-2.5 bg-siperu-blue hover:bg-opacity-90 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition flex items-center gap-2">
            <span>Pinjam Sekarang</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        </a>
        @endif
    @endauth
</div>

<!-- Grid Utama -->
<div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-6">

    <!-- Filter Dropdown Ruangan Dinamis dari Database -->
    <div class="max-w-xs">
        <label for="filter-ruangan" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Filter Berdasarkan Ruangan</label>
        <select id="filter-ruangan" class="w-full text-xs px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-siperu-yellow text-gray-700 font-medium">
            <option value="">-- Tampilkan Semua Ruangan --</option>
            @foreach($rooms as $room)
                <option value="{{ $room->id }}">{{ $room->nama_ruangan }}</option>
            @endforeach
        </select>
    </div>

    <!-- Container Utama Kalender Asli FullCalendar -->
    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 shadow-inner">
        <div id="calendar-siperu" class="bg-white p-4 rounded-xl shadow-sm min-h-[500px]"></div>
    </div>

</div>
@endsection

@section('scripts')
<!-- Inject Library FullCalendar v6 CDN Resmi (Pure Vanilla JS) -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar-siperu');
        const filterDropdown = document.getElementById('filter-ruangan');

        // Inisialisasi konfigurasi dasar FullCalendar sesuai visual grid wireframe
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            locale: 'id', // Set bahasa ke Indonesia agar ramah sidang
            themeSystem: 'standard',
            height: 'auto',
            // Ambil data event langsung dari endpoint API internal Laravel kita
            // ... (bagian atas aman)
            events: function(info, successCallback, failureCallback) {
                const roomId = filterDropdown.value;
                // Ganti ->then menjadi .then dan ->catch menjadi .catch
                fetch(`/api/calendar-events?room_id=${roomId}`)
                    .then(response => response.json())
                    .then(data => successCallback(data))
                    .catch(error => failureCallback(error));
            },
            eventClick: function(info) {
                // Interaksi manis minimalis saat event jadwal diklik
                alert('Detail Jadwal: ' + info.event.title);
            }
        });

        // Render kalender saat halaman pertama kali dibuka
        calendar.render();

        // Efek Re-fetch Otomatis pas Dropdown Filter Ruangan diganti oleh User
        filterDropdown.addEventListener('change', function() {
            calendar.refetchEvents();
        });
    });
</script>
@endsection
