@extends('layouts.app')

@section('title', 'Persetujuan Terpadu - SIPERU PGT')
@section('page_title', 'Halaman Persetujuan Terpadu')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- Notifikasi Sistem Mandiri (Menggunakan SVG Clean) -->
    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 text-xs font-semibold rounded-xl shadow-sm flex items-center gap-2">
            <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('info'))
        <div class="p-4 bg-blue-50 border border-blue-200 text-blue-700 text-xs font-semibold rounded-xl shadow-sm flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('info') }}</span>
        </div>
    @endif
    @if($errors->has('alasan_ditolak') || $errors->has('alasan_ditarik'))
        <div class="p-4 bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-xl shadow-sm flex items-center gap-2">
            <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span>Berkas gagal diproses. Pastikan seluruh formulir alasan telah terisi dengan benar.</span>
        </div>
    @endif

    <!-- ==================== EXECUTIVE STATS GRID ==================== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="block text-[10px] uppercase font-bold tracking-wider text-gray-400">Antrean Pembina</span>
                <span class="text-2xl font-black text-gray-800 mt-1 block">{{ $dosenReservations->where('status_izin', 'Pending')->count() }} <span class="text-xs font-medium text-gray-400">Berkas</span></span>
            </div>
            <div class="w-10 h-10 bg-gray-50 text-gray-500 rounded-xl flex items-center justify-center border border-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
        </div>
        @if(auth()->user()->is_approval_admin)
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="block text-[10px] uppercase font-bold tracking-wider text-gray-400">Disposisi Otoritas</span>
                <span class="text-2xl font-black text-gray-800 mt-1 block">{{ $adminReservations->where('status_izin', 'Disetujui Dosen')->count() }} <span class="text-xs font-medium text-gray-400">Berkas</span></span>
            </div>
            <div class="w-10 h-10 bg-gray-50 text-gray-500 rounded-xl flex items-center justify-center border border-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            </div>
        </div>
        @endif
    </div>

    <!-- ==================== NAVIGASI TAB MODERN ==================== -->
    <div class="flex gap-2 border-b border-gray-200 pb-px">
        <button onclick="switchTab('dosen-pembina')" id="btn-tab-dosen" class="px-5 py-2.5 text-xs font-bold uppercase tracking-wider rounded-t-xl transition border-t border-x bg-white border-gray-200 text-blue-600 focus:outline-none">
            Sebagai Dosen Pembina
        </button>
        @if(auth()->user()->is_approval_admin)
            <button onclick="switchTab('approval-admin')" id="btn-tab-admin" class="px-5 py-2.5 text-xs font-bold uppercase tracking-wider rounded-t-xl transition border-t border-x bg-gray-100 border-transparent text-gray-400 hover:text-gray-600 focus:outline-none">
                Sebagai Otoritas Ruangan
            </button>
        @endif
    </div>

    <!-- ==================== PANEL 1: DOSEN PEMBINA ==================== -->
    <div id="panel-dosen" class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
        <div class="overflow-x-auto border border-gray-100 rounded-xl bg-gray-50/30 p-1">
            <table class="min-w-full bg-white rounded-lg overflow-hidden divide-y divide-gray-100">
                <thead class="bg-gray-50/80 text-gray-400 text-[9px] font-bold uppercase tracking-widest text-center">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-6 py-3 text-left">Agenda & Mahasiswa</th>
                        <th class="px-6 py-3 text-left">Fasilitas Ruang</th>
                        <th class="px-6 py-3">Waktu Kegiatan</th>
                        <th class="px-6 py-3">Status Progressive</th>
                        <th class="px-6 py-3">Tindakan Eksekusi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs text-gray-700 text-center font-medium">
                    @forelse($dosenReservations as $index => $item)
                        <tr class="hover:bg-gray-50/40 transition">
                            <td class="px-4 py-4 text-gray-400 font-bold">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 text-left">
                                <div class="font-bold text-gray-800 leading-snug">{{ $item->judul_pengajuan }}</div>
                                <div class="text-[10px] text-gray-400 font-normal mt-0.5">{{ $item->user->nama }} | NIM: {{ $item->user->nim }}</div>
                            </td>
                            <td class="px-6 py-4 text-left font-bold text-gray-600">{{ $item->room->nama_ruangan }}</td>
                            <td class="px-6 py-4 font-semibold text-gray-500">
                                {{ date('d-m-Y', strtotime($item->waktu_mulai)) }}
                                <span class="block text-[10px] font-normal text-gray-400 mt-0.5">{{ date('H:i', strtotime($item->waktu_mulai)) }} - {{ date('H:i', strtotime($item->waktu_selesai)) }} WIB</span>
                            </td>
                            
                            <!-- 🔥 PROGRESSIVE STATUS TRACKER SISI DOSEN -->
                            <td class="px-6 py-4">
                                <div class="flex flex-col items-center justify-center w-full max-w-[140px] mx-auto space-y-2">
                                    @if($item->status_izin !== 'Ditolak' && !str_contains($item->status_izin, 'Ditolak'))
                                        <!-- Micro Line Tracker -->
                                        <div class="flex items-center justify-between w-full relative px-1 h-3">
                                            <div class="absolute inset-x-0 top-1/2 -translate-y-1/2 h-0.5 {{ $item->status_izin == 'Disetujui' ? 'bg-green-200' : ($item->status_izin == 'Disetujui Dosen' ? 'bg-purple-200' : 'bg-gray-200') }} z-0"></div>
                                            
                                            <!-- Node 1: Pembina -->
                                            <div class="w-3.5 h-3.5 rounded-full z-10 flex items-center justify-center text-[8px] {{ $item->status_izin == 'Pending' ? 'bg-blue-600 ring-4 ring-blue-100 text-white' : 'bg-green-600 text-white' }}">
                                                @if($item->status_izin != 'Pending')
                                                    <svg class="w-2 h-2 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                @endif
                                            </div>

                                            <!-- Node 2: Otoritas Admin -->
                                            <div class="w-3.5 h-3.5 rounded-full z-10 flex items-center justify-center text-[8px] {{ $item->status_izin == 'Disetujui' ? 'bg-green-600 text-white' : ($item->status_izin == 'Disetujui Dosen' ? 'bg-purple-600 ring-4 ring-purple-100 animate-pulse text-white' : 'bg-gray-300') }}">
                                                @if($item->status_izin == 'Disetujui')
                                                    <svg class="w-2 h-2 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <!-- Subtext Status Label -->
                                        <div class="text-[9px] font-bold uppercase tracking-wider text-center">
                                            @if($item->status_izin == 'Pending')
                                                <span class="text-blue-600">Menunggu Pembina</span>
                                            @elseif($item->status_izin == 'Disetujui Dosen')
                                                <span class="text-purple-600">Proses Otoritas</span>
                                            @elseif($item->status_izin == 'Disetujui')
                                                <span class="text-green-600">Disetujui Final</span>
                                            @endif
                                        </div>
                                    @else
                                        <!-- Kondisi Ditolak -->
                                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-widest bg-red-50 text-red-600 border border-red-100">
                                            Ditolak
                                        </span>
                                        @if($item->alasan_ditolak)
                                            <span class="text-[9px] text-gray-400 font-normal italic max-w-[120px] truncate block mt-0.5" title="{{ $item->alasan_ditolak }}">
                                                "{{ $item->alasan_ditolak }}"
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" onclick="openReviewModal(this)"
                                            data-judul="{{ $item->judul_pengajuan }}"
                                            data-mhs="{{ $item->user->nama }} (NIM: {{ $item->user->nim }} / Kelas: {{ $item->user->kelas }})"
                                            data-ruangan="{{ $item->room->nama_ruangan }} (Kapasitas: {{ $item->room->kapasitas }} Kursi)"
                                            data-tanggal="{{ date('d-m-Y', strtotime($item->waktu_mulai)) }}"
                                            data-jam="{{ date('H:i', strtotime($item->waktu_mulai)) }} - {{ date('H:i', strtotime($item->waktu_selesai)) }} WIB"
                                            data-proposal="{{ $item->dokumen_proposal ? asset($item->dokumen_proposal) : '' }}"
                                            data-catatan="{{ $item->alasan_ditolak ?? 'Tidak ada catatan khusus.' }}"
                                            class="inline-flex items-center justify-center gap-1 text-[10px] font-bold uppercase tracking-wider bg-gray-50 text-gray-600 hover:bg-blue-50 hover:text-blue-700 hover:border-blue-300 px-2.5 py-1.5 rounded-lg border border-gray-200 transition shadow-sm">
                                        Detail
                                    </button>

                                    @if($item->status_izin === 'Pending')
                                        <form action="{{ route('reservation.approve', $item->id) }}" method="POST" onsubmit="return confirm('Kunci slot & berikan rekomendasi berkas?')" class="m-0">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center justify-center gap-1 text-[10px] font-bold uppercase tracking-wider bg-gray-50 text-gray-600 hover:bg-green-50 hover:text-green-700 hover:border-green-300 px-2.5 py-1.5 rounded-lg border border-gray-200 transition shadow-sm">
                                                Setuju
                                            </button>
                                        </form>
                                        <button onclick="openRejectModal('{{ $item->id }}')" class="inline-flex items-center justify-center gap-1 text-[10px] font-bold uppercase tracking-wider bg-gray-50 text-gray-600 hover:bg-red-50 hover:text-red-700 hover:border-red-300 px-2.5 py-1.5 rounded-lg border border-gray-200 transition shadow-sm">
                                            Tolak
                                        </button>
                                    @elseif($item->status_izin === 'Disetujui Dosen')
                                        <button type="button" onclick="openCancelModal('{{ $item->id }}')" class="inline-flex items-center justify-center gap-1 text-[10px] font-bold uppercase tracking-wider bg-gray-50 text-gray-600 hover:bg-amber-50 hover:text-amber-700 hover:border-amber-300 px-2.5 py-1.5 rounded-lg border border-gray-200 transition shadow-sm">
                                            Tarik
                                        </button>
                                    @else
                                        <span class="text-gray-400 text-[10px] font-bold uppercase tracking-wider italic">Selesai</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400 font-medium">Belum ada antrean berkas peninjauan bimbingan masuk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ==================== 🔑 PANEL 2: OTORITAS RUANGAN ==================== -->
    @if(auth()->user()->is_approval_admin)
        <div id="panel-admin" style="display: none;" class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
            <div class="overflow-x-auto border border-gray-100 rounded-xl bg-gray-50/30 p-1">
                <table class="min-w-full bg-white rounded-lg overflow-hidden divide-y divide-gray-100">
                    <thead class="bg-gray-50/80 text-gray-400 text-[9px] font-bold uppercase tracking-widest text-center">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-6 py-3 text-left">Agenda & Mahasiswa</th>
                            <th class="px-6 py-3 text-left">Fasilitas Ruang</th>
                            <th class="px-6 py-3">Waktu Kegiatan</th>
                            <th class="px-6 py-3">Status Progressive</th>
                            <th class="px-6 py-3">Tindakan Otoritas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs text-gray-700 text-center font-medium">
                        @forelse($adminReservations as $index => $item)
                            <tr class="hover:bg-gray-50/40 transition">
                                <td class="px-4 py-4 text-gray-400 font-bold">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-left">
                                    <div class="font-bold text-gray-800 leading-snug">{{ $item->judul_pengajuan }}</div>
                                    <div class="text-[10px] text-gray-400 font-normal mt-0.5">{{ $item->user->nama }} | NIM: {{ $item->user->nim }}</div>
                                </td>
                                <td class="px-6 py-4 text-left font-bold text-gray-600">{{ $item->room->nama_ruangan }}</td>
                                <td class="px-6 py-4 font-semibold text-gray-500">{{ date('d-m-Y', strtotime($item->waktu_mulai)) }}<br><span class="text-[10px] font-normal text-gray-400 mt-0.5">{{ date('H:i', strtotime($item->waktu_mulai)) }} - {{ date('H:i', strtotime($item->waktu_selesai)) }} WIB</span></td>
                                
                                <!-- Progressive status untuk panel Otoritas Admin -->
                                <td class="px-6 py-4">
                                    <div class="flex flex-col items-center justify-center w-full max-w-[140px] mx-auto space-y-2">
                                        @if($item->status_izin !== 'Ditolak' && !str_contains($item->status_izin, 'Ditolak'))
                                            <div class="flex items-center justify-between w-full relative px-1 h-3">
                                                <div class="absolute inset-x-0 top-1/2 -translate-y-1/2 h-0.5 {{ $item->status_izin == 'Disetujui' ? 'bg-green-200' : 'bg-purple-200' }} z-0"></div>
                                                
                                                <div class="w-3.5 h-3.5 rounded-full z-10 flex items-center justify-center text-[8px] bg-green-600 text-white">
                                                    <svg class="w-2 h-2 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                </div>

                                                <div class="w-3.5 h-3.5 rounded-full z-10 flex items-center justify-center text-[8px] {{ $item->status_izin == 'Disetujui' ? 'bg-green-600 text-white' : 'bg-purple-600 ring-4 ring-purple-100 animate-pulse text-white' }}">
                                                    @if($item->status_izin == 'Disetujui')
                                                        <svg class="w-2 h-2 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-[9px] font-bold uppercase tracking-wider text-center">
                                                @if($item->status_izin == 'Disetujui Dosen')
                                                    <span class="text-purple-600">Proses Otoritas</span>
                                                @elseif($item->status_izin == 'Disetujui')
                                                    <span class="text-green-600">Disetujui Final</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-widest bg-red-50 text-red-600 border border-red-100">
                                                Ditolak
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" onclick="openReviewModal(this)"
                                                data-judul="{{ $item->judul_pengajuan }}"
                                                data-mhs="{{ $item->user->nama }} (NIM: {{ $item->user->nim }} / Kelas: {{ $item->user->kelas }})"
                                                data-ruangan="{{ $item->room->nama_ruangan }} (Kapasitas: {{ $item->room->kapasitas }} Kursi)"
                                                data-tanggal="{{ date('d-m-Y', strtotime($item->waktu_mulai)) }}"
                                                data-jam="{{ date('H:i', strtotime($item->waktu_mulai)) }} - {{ date('H:i', strtotime($item->waktu_selesai)) }} WIB"
                                                data-proposal="{{ $item->dokumen_proposal ? asset($item->dokumen_proposal) : '' }}"
                                                data-catatan="{{ $item->alasan_ditolak ?? 'Tidak ada catatan khusus.' }}"
                                                class="inline-flex items-center justify-center gap-1 text-[10px] font-bold uppercase tracking-wider bg-gray-50 text-gray-600 hover:bg-blue-50 hover:text-blue-700 hover:border-blue-300 px-2.5 py-1.5 rounded-lg border border-gray-200 transition shadow-sm">
                                            Detail
                                        </button>

                                        @if($item->status_izin === 'Disetujui Dosen')
                                            <form action="{{ route('reservation.approve', $item->id) }}" method="POST" onsubmit="return confirm('Berikan otorisasi final kunci ruangan fisik?')" class="m-0">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center justify-center gap-1 text-[10px] font-bold uppercase tracking-wider bg-gray-50 text-gray-600 hover:bg-purple-50 hover:text-purple-700 hover:border-purple-300 px-2.5 py-1.5 rounded-lg border border-gray-200 transition shadow-sm">
                                                    Otorisasi
                                                </button>
                                            </form>
                                            <button onclick="openRejectModal('{{ $item->id }}')" class="inline-flex items-center justify-center gap-1 text-[10px] font-bold uppercase tracking-wider bg-gray-50 text-gray-600 hover:bg-red-50 hover:text-red-700 hover:border-red-300 px-2.5 py-1.5 rounded-lg border border-gray-200 transition shadow-sm">
                                                Tolak
                                            </button>
                                        @else
                                            <span class="text-gray-400 text-[10px] font-bold uppercase tracking-wider italic">Selesai</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400 font-medium">Belum ada antrean berkas disposisi ruangan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>

<!-- ==================== NEW TAILWIND MODAL BOX: LECTURER QUICK REVIEW ==================== -->
<div id="modal-review-dosen" style="display: none;" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4 transition-all duration-200 animate-fadeIn">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-2xl max-w-md w-full overflow-hidden transform scale-95 transition-transform duration-200">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
            <div>
                <h4 class="text-xs font-bold text-gray-800 uppercase tracking-widest">Tinjau Kelengkapan Berkas</h4>
                <p class="text-[10px] text-gray-400 mt-0.5">Rincian proposal data transaksi mahasiswa PGT.</p>
            </div>
            <button onclick="closeReviewModal()" class="text-gray-400 hover:text-gray-600 font-bold text-sm focus:outline-none">✕</button>
        </div>
        <div class="p-6 space-y-4 text-xs text-gray-600">
            <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                <span class="block text-[9px] uppercase font-bold tracking-wider text-gray-400 mb-0.5">Judul Agenda Pengajuan:</span>
                <span id="rv-judul" class="font-bold text-gray-800 text-sm leading-snug">Nama Agenda</span>
            </div>
            <div class="space-y-2.5">
                <div>
                    <span class="block text-[9px] uppercase font-bold tracking-wider text-gray-400 mb-0.5">Identitas Mahasiswa:</span>
                    <span id="rv-mhs" class="font-semibold text-gray-700 block">Nama Mhs</span>
                </div>
                <div>
                    <span class="block text-[9px] uppercase font-bold tracking-wider text-gray-400 mb-0.5">Target Obyek Ruangan:</span>
                    <span id="rv-ruangan" class="font-semibold text-gray-700 block">Nama Ruangan</span>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <span class="block text-[9px] uppercase font-bold tracking-wider text-gray-400 mb-0.5">Tanggal Pelaksanaan:</span>
                        <span id="rv-tanggal" class="font-semibold text-gray-700 block">Tanggal</span>
                    </div>
                    <div>
                        <span class="block text-[9px] uppercase font-bold tracking-wider text-gray-400 mb-0.5">Durasi Penggunaan:</span>
                        <span id="rv-jam" class="font-semibold text-gray-700 block">Jam</span>
                    </div>
                </div>
                <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
                    <span class="block text-[9px] uppercase font-bold tracking-wider text-gray-400 mb-0.5">Catatan Log Berkas saat ini:</span>
                    <p id="rv-catatan" class="text-gray-500 italic font-medium leading-relaxed">Catatan...</p>
                </div>
            </div>
            <div id="rv-wrapper-proposal">
                <a id="rv-proposal-link" href="#" target="_blank" class="w-full inline-flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-600 font-bold text-[10px] uppercase tracking-wider py-2 rounded-xl border border-red-200 transition shadow-sm">
                    Buka Lampiran Proposal (PDF)
                </a>
            </div>
        </div>
        <div class="px-6 py-3.5 bg-gray-50 border-t border-gray-100 flex justify-end">
            <button type="button" onclick="closeReviewModal()" class="px-4 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold text-[10px] uppercase rounded-lg transition">Selesai Meninjau</button>
        </div>
    </div>
</div>

<!-- ==================== TAILWIND POP-UP MODAL BOX: ALASAN REJECT ==================== -->
<div id="modal-reject" style="display: none;" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4 transition-all duration-200 animate-fadeIn">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-xl max-w-md w-full overflow-hidden transform scale-95 transition-transform duration-200">
        <div class="px-6 py-4 bg-red-50 border-b border-red-100">
            <h4 class="text-xs font-bold text-red-700 uppercase tracking-widest">Formulir Alasan Penolakan</h4>
            <p class="text-[11px] text-gray-500 mt-0.5">Berikan alasan logis mengapa berkas mahasiswa ini digugurkan/ditolak.</p>
        </div>
        <form id="form-reject-action" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label for="input-alasan" class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1.5">Alasan Penolakan Resmi :</label>
                <textarea name="alasan_ditolak" id="input-alasan" placeholder="Masukkan alasan penolakan berkas..." required rows="3" class="w-full text-xs p-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-red-500"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-gray-100 text-gray-500 font-bold text-[10px] uppercase rounded-lg transition">Batal</button>
                <button type="submit" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white font-bold text-[10px] uppercase rounded-lg shadow-md transition">Eksekusi Tolak</button>
            </div>
        </form>
    </div>
</div>

<!-- ==================== TAILWIND POP-UP MODAL BOX: ALASAN TARIK REKOMENDASI ==================== -->
<div id="modal-cancel" style="display: none;" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4 transition-all duration-200 animate-fadeIn">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-xl max-w-md w-full overflow-hidden transform scale-95 transition-transform duration-200">
        <div class="px-6 py-4 bg-amber-50 border-b border-amber-100">
            <h4 class="text-xs font-bold text-amber-700 uppercase tracking-widest">Catatan Penarikan Berkas</h4>
            <p class="text-[11px] text-gray-500 mt-0.5">Berikan catatan khusus kepada mahasiswa mengapa rekomendasi ini ditarik kembali.</p>
        </div>
        <form id="form-cancel-action" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label for="input-alasan-cancel" class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1.5">Alasan / Catatan Penarikan :</label>
                <textarea name="alasan_ditarik" id="input-alasan-cancel" placeholder="Masukkan alasan penarikan wewenang..." required rows="3" class="w-full text-xs p-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-amber-500"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeCancelModal()" class="px-4 py-2 bg-gray-100 text-gray-500 font-bold text-[10px] uppercase rounded-lg transition">Batal</button>
                <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-[10px] uppercase rounded-lg shadow-md transition">Tarik Rekomendasi</button>
            </div>
        </form>
    </div>
</div>

<script>
// Logic Quick Review Modal Dosen
function openReviewModal(button) {
    const modal = document.getElementById('modal-review-dosen');
    document.getElementById('rv-judul').innerText = button.getAttribute('data-judul');
    document.getElementById('rv-mhs').innerText = button.getAttribute('data-mhs');
    document.getElementById('rv-ruangan').innerText = button.getAttribute('data-ruangan');
    document.getElementById('rv-tanggal').innerText = button.getAttribute('data-tanggal');
    document.getElementById('rv-jam').innerText = button.getAttribute('data-jam');
    document.getElementById('rv-catatan').innerText = button.getAttribute('data-catatan');

    const proposal = button.getAttribute('data-proposal');
    if (proposal) {
        document.getElementById('rv-proposal-link').href = proposal;
        document.getElementById('rv-wrapper-proposal').style.display = 'block';
    } else {
        document.getElementById('rv-wrapper-proposal').style.display = 'none';
    }
    modal.style.display = 'flex';
}

function closeReviewModal() {
    document.getElementById('modal-review-dosen').style.display = 'none';
}

// Logic Pop-up Form Reject & Cancel
function openRejectModal(id) {
    const form = document.getElementById('form-reject-action');
    form.action = `/dosen/persetujuan/${id}/reject`;
    document.getElementById('modal-reject').style.display = 'flex';
}
function closeRejectModal() { document.getElementById('modal-reject').style.display = 'none'; }

function openCancelModal(id) {
    const form = document.getElementById('form-cancel-action');
    form.action = `/dosen/persetujuan/${id}/cancel-recommendation`;
    document.getElementById('modal-cancel').style.display = 'flex';
}
function closeCancelModal() { document.getElementById('modal-cancel').style.display = 'none'; }

// Logic Tab Switch
function switchTab(roleTab) {
    const btnDosen = document.getElementById('btn-tab-dosen');
    const btnAdmin = document.getElementById('btn-tab-admin');
    const panelDosen = document.getElementById('panel-dosen');
    const panelAdmin = document.getElementById('panel-admin');

    if (roleTab === 'dosen-pembina') {
        panelDosen.style.display = 'block';
        btnDosen.className = "px-5 py-2.5 text-xs font-bold uppercase tracking-wider rounded-t-xl transition border-t border-x bg-white border-gray-200 text-blue-600 focus:outline-none";
        if (panelAdmin) {
            panelAdmin.style.display = 'none';
            btnAdmin.className = "px-5 py-2.5 text-xs font-bold uppercase tracking-wider rounded-t-xl transition border-t border-x bg-gray-100 border-transparent text-gray-400 hover:text-gray-600 focus:outline-none";
        }
    } else if (roleTab === 'approval-admin') {
        panelDosen.style.display = 'none';
        btnDosen.className = "px-5 py-2.5 text-xs font-bold uppercase tracking-wider rounded-t-xl transition border-t border-x bg-gray-100 border-transparent text-gray-400 hover:text-gray-600 focus:outline-none";
        panelAdmin.style.display = 'block';
        btnAdmin.className = "px-5 py-2.5 text-xs font-bold uppercase tracking-wider rounded-t-xl transition border-t border-x bg-white border-gray-200 text-purple-700 focus:outline-none";
    }
}
</script>
@endsection