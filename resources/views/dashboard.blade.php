@extends('layouts.app')

@section('title', 'Dashboard Jadwal - SIPERU PGT')
@section('page_title', 'Dashboard / Jadwal Ruangan')

@section('styles')
<style>
    /* 🔥 ANTI-CUTOFF RESOLUTION: Memaksa teks event FullCalendar membungkus rapi ke bawah */
    .fc-event {
        padding: 2px 6px !important;
        border-radius: 6px !important;
        font-size: 10px !important;
        font-weight: 700 !important;
        cursor: pointer !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
    }
    .fc-event-title {
        white-space: normal !important;
        word-break: break-word !important;
        line-height: 1.3 !important;
    }
    .fc-daygrid-event {
        margin-top: 2px !important;
        white-space: normal !important;
    }
    .fc-theme-standard td, .fc-theme-standard th {
        border-color: #F3F4F6 !important;
    }
    .fc-col-header-cell {
        background-color: #F9FAFB !important;
        padding: 8px 0 !important;
        font-size: 11px !important;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6B7280 !important;
    }
</style>
@endsection

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h2 class="text-xl font-bold text-gray-800 tracking-tight">
            Welcome{{ Auth::check() ? ', ' . Auth::user()->nama : ' to SIPERU PGT' }}!
        </h2>
        <p class="text-xs text-gray-400 mt-0.5">Periksa ketersediaan ruangan sebelum melakukan pengajuan.</p>
    </div>

    @auth
        @if(Auth::user()->role == 'mahasiswa')
        <a href="{{ route('reservation.create') }}" class="px-5 py-2.5 bg-siperu-blue hover:bg-opacity-90 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition flex items-center gap-2">
            <span>Pinjam Sekarang</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
        </a>
        @endif
    @endauth
</div>

<div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-6">

    <div class="max-w-xs">
        <label for="filter-ruangan" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Filter Berdasarkan Ruangan</label>
        <select id="filter-ruangan" class="w-full text-xs px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-siperu-blue text-gray-700 font-bold uppercase tracking-wide">
            <option value="">Tampilkan Semua Ruangan</option>
            @foreach($rooms as $room)
                <option value="{{ $room->id }}">{{ $room->nama_ruangan }}</option>
            @endforeach
        </select>
    </div>

    <div class="p-1 bg-gray-50 rounded-2xl border border-gray-100">
        <div id="calendar-siperu" class="bg-white p-4 rounded-xl min-h-[550px] text-xs"></div>
    </div>
</div>

<div id="modal-event-detail" style="display: none;" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4 transition-all duration-200">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-2xl max-w-sm w-full overflow-hidden transform scale-95 transition-transform duration-200">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
            <div>
                <h4 class="text-xs font-bold text-gray-800 uppercase tracking-widest">Informasi Slot Ruangan</h4>
                <p class="text-[9px] text-gray-400 mt-0.5">Status ketersediaan terverifikasi sistem.</p>
            </div>
            <button onclick="closeEventModal()" class="text-gray-400 hover:text-gray-600 font-bold text-sm focus:outline-none">✕</button>
        </div>
        <div class="p-6 space-y-4 text-xs text-gray-600">
            <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                <span class="block text-[9px] uppercase font-bold tracking-wider text-gray-400 mb-0.5">Agenda Kegiatan:</span>
                <span id="md-agenda" class="font-bold text-gray-800 text-sm leading-snug">-</span>
            </div>
            <div class="space-y-2">
                <div>
                    <span class="block text-[9px] uppercase font-bold tracking-wider text-gray-400 mb-0.5">Penanggung Jawab:</span>
                    <span id="md-pemohon" class="font-semibold text-gray-700 block">-</span>
                </div>
                <div>
                    <span class="block text-[9px] uppercase font-bold tracking-wider text-gray-400 mb-0.5">Lokasi Ruangan:</span>
                    <span id="md-ruangan" class="font-semibold text-gray-700 block">-</span>
                </div>
                <div>
                    <span class="block text-[9px] uppercase font-bold tracking-wider text-gray-400 mb-0.5">Waktu Pelaksanaan:</span>
                    <span id="md-waktu" class="font-semibold text-gray-700 block">-</span>
                </div>
                <div class="pt-2">
                    <span class="block text-[9px] uppercase font-bold tracking-wider text-gray-400 mb-1">Tahapan Otoritas:</span>
                    <span id="md-status" class="inline-block px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider border">-</span>
                </div>
            </div>
        </div>
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-end">
            <button type="button" onclick="closeEventModal()" class="px-4 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold text-[10px] uppercase rounded-lg transition">Tutup</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar-siperu');
        const filterDropdown = document.getElementById('filter-ruangan');

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            locale: 'id',
            themeSystem: 'standard',
            height: 'auto',
            dayMaxEvents: true, // Membatasi jumlah baris per sel agar tidak merusak grid tanggal
            events: function(info, successCallback, failureCallback) {
                const roomId = filterDropdown.value;
                fetch(`/api/calendar-events?room_id=${roomId}`)
                    .then(response => response.json())
                    .then(data => successCallback(data))
                    .catch(error => failureCallback(error));
            },
            // 🔥 MODAL OVERRIDE: Menangkap data extendedProps untuk dimasukkan ke modal box kustom
            eventClick: function(info) {
                const props = info.event.extendedProps;
                
                document.getElementById('md-agenda').innerText = props.agenda;
                document.getElementById('md-pemohon').innerText = props.pemohon;
                document.getElementById('md-ruangan').innerText = props.ruangan;
                document.getElementById('md-waktu').innerText = props.waktu;
                
                const statusEl = document.getElementById('md-status');
                statusEl.innerText = props.status === 'Disetujui Dosen' ? 'Proses Otoritas' : props.status;

                // Modifikasi warna badge status di dalam modal secara progresif
                if (props.status === 'Pending') {
                    statusEl.className = "inline-block px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-blue-50 text-blue-600 border border-blue-100";
                } else if (props.status === 'Disetujui Dosen') {
                    statusEl.className = "inline-block px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-purple-50 text-purple-600 border border-purple-100";
                } else if (props.status === 'Disetujui') {
                    statusEl.className = "inline-block px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-green-50 text-green-600 border border-green-100";
                }

                document.getElementById('modal-event-detail').style.display = 'flex';
            }
        });

        calendar.render();

        filterDropdown.addEventListener('change', function() {
            calendar.refetchEvents();
        });
    });

    function closeEventModal() {
        document.getElementById('modal-event-detail').style.display = 'none';
    }
</script>
@endsection