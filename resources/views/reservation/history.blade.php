@extends('layouts.app')

@section('title', 'Riwayat Peminjaman - SIPERU PGT')
@section('page_title', 'Riwayat Peminjaman')

@section('content')
<div class="max-w-5xl mx-auto bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-6">

    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-sm font-bold text-siperu-blue uppercase tracking-wide">Daftar Riwayat Peminjaman Anda</h3>
            <p class="text-xs text-gray-400 mt-0.5">Pantau status persetujuan pengajuan ruangan kuliah Anda secara real-time.</p>
        </div>

        <button class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-semibold border border-gray-200 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            <span>Filter</span>
        </button>
    </div>

    <div class="overflow-x-auto border border-gray-100 rounded-xl shadow-inner bg-gray-50 p-1">
        <table class="min-w-full bg-white rounded-lg overflow-hidden divide-y divide-gray-100">
            <thead class="bg-gray-50 text-gray-500 text-[11px] font-bold uppercase tracking-wider text-center">
                <tr>
                    <th class="px-4 py-3">No</th>
                    <th class="px-6 py-3 text-left">Ruangan</th>
                    <th class="px-6 py-3">Tanggal</th>
                    <th class="px-6 py-3">Jam (Mulai - Selesai)</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Aksi</th> </tr>
            </thead>

            <tbody class="divide-y divide-gray-100 text-xs text-gray-700 text-center font-medium">
                @forelse($reservations as $index => $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td>{{ $index + 1 }}</td>
                        <td class="px-6 py-4 text-left font-bold text-siperu-blue">
                            <div>{{ $item->room->nama_ruangan }}</div>
                            <div class="text-[10px] text-gray-400 font-medium tracking-normal mt-0.5">📋 {{ $item->judul_pengajuan ?? 'Tanpa Judul' }}</div>
                        </td>
                        <td class="px-6 py-4 text-gray-500">{{ date('d-m-Y', strtotime($item->waktu_mulai)) }}</td>
                        <td class="px-6 py-4 tracking-wide bg-gray-50/50 font-semibold text-gray-600">
                            {{ date('H:i', strtotime($item->waktu_mulai)) }} - {{ date('H:i', strtotime($item->waktu_selesai)) }} WIB
                        </td>
                        <!-- 🔥 KELOMPOK BARU: VISUAL TIMELINE STEPPER PROGRESS FLOW MAHASISWA -->
                        <!-- 🔥 TAMPILAN ULTRA-CLEAN MICRO PROGRESS TRACKER MAHASISWA -->
                        <td class="px-6 py-4 vertical-align-middle">
                            <div class="flex flex-col items-center justify-center w-full max-w-[150px] mx-auto space-y-2">

                                @if($item->status_izin !== 'Ditolak' && !str_contains($item->status_izin, 'Ditolak'))
                                    <!-- 1. Sleek Line Tracker Graph -->
                                    <div class="flex items-center justify-between w-full relative px-1 h-3">
                                        <!-- Background Line Track -->
                                        <div class="absolute inset-x-0 top-1/2 -translate-y-1/2 h-0.5 {{ $item->status_izin == 'Disetujui' ? 'bg-green-200' : ($item->status_izin == 'Disetujui Dosen' ? 'bg-purple-200' : 'bg-gray-200') }} z-0 transition-colors duration-300"></div>

                                        <!-- Bulatan Node 1: Dosen Pembina -->
                                        <div class="w-3.5 h-3.5 rounded-full z-10 flex items-center justify-center text-[8px] transition-all {{ $item->status_izin == 'Pending' ? 'bg-blue-600 ring-4 ring-blue-100 text-white' : 'bg-green-600 text-white' }}">
                                            @if($item->status_izin != 'Pending')
                                                <svg class="w-2 h-2 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            @endif
                                        </div>

                                        <!-- Bulatan Node 2: Otoritas Admin Ruangan -->
                                        <div class="w-3.5 h-3.5 rounded-full z-10 flex items-center justify-center text-[8px] transition-all {{ $item->status_izin == 'Disetujui' ? 'bg-green-600 text-white' : ($item->status_izin == 'Disetujui Dosen' ? 'bg-purple-600 ring-4 ring-purple-100 animate-pulse text-white' : 'bg-gray-300 text-transparent') }}">
                                            @if($item->status_izin == 'Disetujui')
                                                <svg class="w-2 h-2 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- 2. Minimalist Single Status Text -->
                                    <div class="text-[10px] font-bold uppercase tracking-wider">
                                        @if($item->status_izin == 'Pending')
                                            <span class="text-blue-600">Menunggu Pembina</span>
                                        @elseif($item->status_izin == 'Disetujui Dosen')
                                            <span class="text-purple-600">Proses Otoritas Admin</span>
                                        @elseif($item->status_izin == 'Disetujui')
                                            <span class="text-green-600">Disetujui Final</span>
                                        @endif
                                    </div>
                                @else
                                    <!-- 3. Jalur Terputus: Tampilan Clean Khusus Data Ditolak/Gugur -->
                                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-red-50 text-red-600 border border-red-100">
                                        Ditolak
                                    </span>
                                    @if($item->alasan_ditolak)
                                        <span class="text-[10px] text-gray-400 font-normal italic max-w-[140px] truncate text-center block mt-0.5" title="{{ $item->alasan_ditolak }}">
                                            "{{ $item->alasan_ditolak }}"
                                        </span>
                                    @endif
                                @endif

                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-1.5">

                                <!-- 1. Tombol Detail (Sudah ada) -->
                                <button type="button"
                                    onclick="openDetailModal(this)"
                                    data-judul="{{ $item->judul_pengajuan }}"
                                    data-ruangan="🏢 {{ $item->room->nama_ruangan }}"
                                    data-dosen="👤 {{ $item->lecturer->nama ?? '-' }}"
                                    data-tanggal="📅 {{ date('d-m-Y', strtotime($item->waktu_mulai)) }}"
                                    data-jam="⏰ {{ date('H:i', strtotime($item->waktu_mulai)) }} - {{ date('H:i', strtotime($item->waktu_selesai)) }} WIB"
                                    data-status="{{ $item->status_izin }}"
                                    data-catatan="{{ $item->alasan_ditolak ?? 'Tidak ada catatan khusus dari dosen.' }}"
                                    data-proposal="{{ $item->dokumen_proposal ? asset($item->dokumen_proposal) : '' }}"
                                    class="text-[10px] font-bold uppercase tracking-wider bg-gray-50 text-gray-600 hover:bg-gray-600 hover:text-white px-2.5 py-1.5 rounded-lg border border-gray-200 transition">
                                    Detail
                                </button>

                                @if($item->status_izin === 'Pending')
                                    <!-- 2. Tombol Edit (Biru Serasi) -->
                                    <a href="{{ route('reservation.edit', $item->id) }}"
                                    class="text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white px-2.5 py-1.5 rounded-lg border border-blue-200 transition">
                                        Edit
                                    </a>

                                    <!-- 3. Tombol Hapus (Merah Lembut Serasi) -->
                                    <form action="{{ route('reservation.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan pengajuan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-[10px] font-bold uppercase tracking-wider bg-red-50 text-red-600 hover:bg-red-600 hover:text-white px-2.5 py-1.5 rounded-lg border border-red-200 transition">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400 font-medium">Belum ada riwayat.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pt-2 flex justify-end">
        {!! $reservations->links() !!}
    </div>

</div>

<!-- ==================== 🔥 TAILWIND MODAL BOX: DETAIL PEMINJAMAN ULTRA CLEAN ==================== -->
<div id="modal-detail-mhs" style="display: none;" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4 transition-all duration-200 animate-fadeIn">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-2xl max-w-md w-full overflow-hidden transform scale-95 transition-transform duration-200">

        <!-- Header Modal -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
            <div>
                <h4 class="text-xs font-black text-gray-800 uppercase tracking-widest">📋 Rincian Berkas Pengajuan</h4>
                <p class="text-[10px] text-gray-400 mt-0.5">Informasi validasi pelacakan alur birokrasi ruang kuliah.</p>
            </div>
            <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600 font-bold text-sm focus:outline-none">✕</button>
        </div>

        <!-- Body Konten Detail -->
        <div class="p-6 space-y-4 text-xs text-gray-600">
            <!-- Agenda Judul -->
            <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                <span class="block text-[9px] uppercase font-bold tracking-wider text-gray-400 mb-0.5">Judul Agenda / Kegiatan :</span>
                <span id="dt-judul" class="font-bold text-gray-800 text-sm leading-snug">Nama Agenda</span>
            </div>

            <!-- Grid Data 2 Kolom -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <span class="block text-[9px] uppercase font-bold tracking-wider text-gray-400 mb-0.5">Lokasi Fasilitas :</span>
                    <span id="dt-ruangan" class="font-semibold text-gray-700 block">🏢 Ruangan</span>
                </div>
                <div>
                    <span class="block text-[9px] uppercase font-bold tracking-wider text-gray-400 mb-0.5">Dosen Pembimbing :</span>
                    <span id="dt-dosen" class="font-semibold text-gray-700 block">👤 Nama Dosen</span>
                </div>
                <div>
                    <span class="block text-[9px] uppercase font-bold tracking-wider text-gray-400 mb-0.5">Tanggal Pelaksanaan :</span>
                    <span id="dt-tanggal" class="font-semibold text-gray-700 block">📅 Tanggal</span>
                </div>
                <div>
                    <span class="block text-[9px] uppercase font-bold tracking-wider text-gray-400 mb-0.5">Durasi Waktu :</span>
                    <span id="dt-jam" class="font-semibold text-gray-700 block">⏰ Jam</span>
                </div>
            </div>

            <!-- Catatan/Alasan Log Kerja -->
            <div id="wrapper-catatan" class="p-3 rounded-xl border">
                <span class="block text-[9px] uppercase font-bold tracking-wider text-gray-400 mb-0.5">Catatan Validasi / Alasan Kampus :</span>
                <p id="dt-catatan" class="text-gray-600 font-medium italic leading-relaxed">Isi alasan...</p>
            </div>

            <!-- Download File Proposal Dokumen -->
            <div id="wrapper-proposal" class="pt-2">
                <a id="dt-proposal-link" href="#" target="_blank" class="w-full inline-flex items-center justify-center gap-1.5 bg-red-50 hover:bg-red-100 text-red-600 font-bold text-[10px] uppercase tracking-wider py-2 rounded-xl border border-red-200 shadow-sm transition">
                    📄 Buka Lampiran Berkas Proposal (PDF)
                </a>
            </div>
        </div>

        <!-- Footer Penutup -->
        <div class="px-6 py-3.5 bg-gray-50 border-t border-gray-100 flex justify-end">
            <button type="button" onclick="closeDetailModal()" class="px-4 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold text-[10px] uppercase rounded-lg shadow-sm transition">Tutup Detail</button>
        </div>
    </div>
</div>

<!-- ==================== JAVASCRIPT LOGIC POPULATE DATA ==================== -->
<script>
function openDetailModal(button) {
    const modal = document.getElementById('modal-detail-mhs');

    // Tarik seluruh data atribut dari button yang diklik
    const judul = button.getAttribute('data-judul');
    const ruangan = button.getAttribute('data-ruangan');
    const dosen = button.getAttribute('data-dosen');
    const tanggal = button.getAttribute('data-tanggal');
    const jam = button.getAttribute('data-jam');
    const status = button.getAttribute('data-status');
    const catatan = button.getAttribute('data-catatan');
    const proposal = button.getAttribute('data-proposal');

    // Suntikkan isi text ke dalam elemen modal box
    document.getElementById('dt-judul').innerText = judul;
    document.getElementById('dt-ruangan').innerText = ruangan;
    document.getElementById('dt-dosen').innerText = dosen;
    document.getElementById('dt-tanggal').innerText = tanggal;
    document.getElementById('dt-jam').innerText = jam;
    document.getElementById('dt-catatan').innerText = catatan;

    // Kustomisasi warna kotak catatan berdasarkan status izinnya pak
    const wrapperCatatan = document.getElementById('wrapper-catatan');
    if (status.includes('Ditolak') || status === 'Ditolak') {
        wrapperCatatan.className = "p-3 rounded-xl border border-red-100 bg-red-50/50 text-red-700";
    } else if (status === 'Disetujui') {
        wrapperCatatan.className = "p-3 rounded-xl border border-green-100 bg-green-50/50 text-green-700";
    } else {
        wrapperCatatan.className = "p-3 rounded-xl border border-gray-100 bg-gray-50 text-gray-600";
    }

    // Cek ketersediaan file lampiran proposal mhs
    const wrapperProposal = document.getElementById('wrapper-proposal');
    if (proposal) {
        document.getElementById('dt-proposal-link').href = proposal;
        wrapperProposal.style.display = 'block';
    } else {
        wrapperProposal.style.display = 'none';
    }

    // Tampilkan modal secara flex
    modal.style.display = 'flex';
}

function closeDetailModal() {
    document.getElementById('modal-detail-mhs').style.display = 'none';
}
</script>
@endsection
