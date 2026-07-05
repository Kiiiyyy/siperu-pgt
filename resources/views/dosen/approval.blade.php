@extends('layouts.app')

@section('title', 'Persetujuan Admin - SIPERU PGT')
@section('page_title', 'Persetujuan Admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-4">

    <div class="flex justify-between items-center bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <div>
            <h3 class="text-sm font-bold text-siperu-blue uppercase tracking-wide">Menunggu Persetujuan Admin / Dosen</h3>
            <p class="text-xs text-gray-400 mt-0.5">Periksa dokumen proposal dan kesesuaian waktu sebelum mengambil keputusan.</p>
        </div>
        <span class="px-3 py-1 bg-siperu-blue/10 text-siperu-blue text-xs font-bold rounded-lg">
            Total: {{ $reservations->count() }} Pengajuan
        </span>
    </div>

    @forelse($reservations as $item)
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 hover:border-gray-300 transition">

            <div class="flex items-center gap-4 w-full md:w-1/3">
                <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center border border-gray-200 text-gray-400 flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <div class="truncate">
                    <h4 class="text-sm font-black text-gray-800 leading-tight">{{ $item->user->nama }}</h4>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $item->user->nim }} • {{ $item->user->kelas }}</p>
                    <a href="{{ asset($item->dokumen_proposal) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-siperu-blue hover:underline mt-1 bg-gray-50 px-2 py-0.5 rounded border border-gray-100">
                        📄 Lihat Proposal PDF
                    </a>
                </div>
            </div>

            <div class="w-full md:w-1/3 border-t md:border-t-0 md:border-l border-gray-100 pt-3 md:pt-0 md:pl-6 space-y-1 text-xs">
                <p class="font-bold text-siperu-blue text-sm">{{ $item->room->nama_ruangan }}</p>
                <p class="text-gray-500 font-medium">📆 Hari/Tgl: <span class="text-gray-700 font-semibold">{{ date('d-m-Y', strtotime($item->waktu_mulai)) }}</span></p>
                <p class="text-gray-500 font-medium">⏰ Waktu: <span class="text-gray-700 font-semibold">{{ date('H:i', strtotime($item->waktu_mulai)) }} - {{ date('H:i', strtotime($item->waktu_selesai)) }} WIB</span></p>
            </div>

            <div class="w-full md:w-auto flex md:flex-col gap-2 flex-shrink-0 pt-3 md:pt-0 border-t md:border-t-0 border-gray-100">
                <form action="{{ route('reservation.approve', $item->id) }}" method="POST">
                    @csrf
                    <button type="submit" onclick="return confirm('Setujui peminjaman ruangan ini? Sistem akan mengirimkan notifikasi WA ke mahasiswa.')" class="w-full md:w-28 px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-sm transition text-center">
                        Approve
                    </button>
                </form>

                <form action="{{ route('reservation.reject', $item->id) }}" method="POST">
                    @csrf
                    <button type="submit" onclick="return confirm('Tolak pengajuan peminjaman ruangan ini?')" class="w-full md:w-28 px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-sm transition text-center">
                        Reject
                    </button>
                </form>
            </div>

        </div>
    @empty
        <div class="bg-white border border-gray-100 rounded-xl p-12 text-center text-gray-400 font-medium shadow-sm">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-sm font-semibold text-gray-500">Bersih Mantap!</p>
            <p class="text-xs text-gray-400 mt-0.5">Tidak ada pengajuan peminjaman ruangan baru yang berstatus pending saat ini.</p>
        </div>
    @endforelse

</div>
@endsection
