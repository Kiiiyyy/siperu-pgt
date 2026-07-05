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
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-xs text-gray-700 text-center font-medium">
                @forelse($reservations as $index => $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-4 text-gray-400 font-bold">
                            {{ $reservations->firstItem() + $index }}
                        </td>
                        <td class="px-6 py-4 text-left font-bold text-siperu-blue">
                            {{ $item->room->nama_ruangan }}
                        </td>
                        <td class="px-6 py-4 text-gray-500">
                            {{ date('d-m-Y', strtotime($item->waktu_mulai)) }}
                        </td>
                        <td class="px-6 py-4 tracking-wide bg-gray-50/50 font-semibold text-gray-600">
                            {{ date('H:i', strtotime($item->waktu_mulai)) }} - {{ date('H:i', strtotime($item->waktu_selesai)) }} WIB
                        </td>
                        <td class="px-6 py-4">
                            @if($item->status_izin == 'Disetujui')
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase bg-green-100 text-green-700 tracking-wider">
                                    Disetujui
                                </span>
                            @elseif($item->status_izin == 'Ditolak')
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase bg-red-100 text-red-700 tracking-wider">
                                    Ditolak
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase bg-yellow-100 text-yellow-700 tracking-wider animate-pulse">
                                    Pending
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-400 font-medium">
                            <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            Belum ada riwayat pengajuan peminjaman ruangan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pt-2 flex justify-end">
        {!! $reservations->links() !!}
    </div>

</div>
@endsection
