@extends('layouts.app')

@section('title', 'Cetak Laporan - SIPERU PGT')
@section('page_title', 'Cetak Laporan PDF')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

    <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
        <h3 class="text-sm font-bold text-siperu-blue uppercase tracking-wide">Filter Data Laporan Peminjaman</h3>
        <p class="text-xs text-gray-500 mt-0.5">Tentukan parameter bulan, tahun, atau ruangan untuk mencetak dokumen PDF resmi.</p>
    </div>

    <form action="{{ route('reservation.export_pdf') }}" method="GET" class="p-6 space-y-4">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="room_id" class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-2">Ruangan Kuliah</label>
                <select name="room_id" id="room_id" class="w-full text-xs px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-siperu-yellow">
                    <option value="">-- Semua Ruangan --</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}">{{ $room->nama_ruangan }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="bulan" class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-2">Pilih Bulan</label>
                <select name="bulan" id="bulan" class="w-full text-xs px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-siperu-yellow">
                    <option value="">-- Semua Bulan --</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="tahun" class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-2">Pilih Tahun</label>
                <select name="tahun" id="tahun" class="w-full text-xs px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-siperu-yellow">
                    <option value="">-- Semua Tahun --</option>
                    <option value="2026">2026</option>
                    <option value="2027">2027</option>
                </select>
            </div>
        </div>

        <div class="pt-4 flex justify-end">
            <button type="submit" class="flex items-center gap-2 px-6 py-3 bg-siperu-blue hover:bg-opacity-90 text-white font-bold text-xs uppercase tracking-widest rounded-xl shadow-md transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Cetak Laporan</span>
            </button>
        </div>

    </form>
</div>
@endsection
