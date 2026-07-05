@extends('layouts.app')

@section('title', 'Peminjaman Baru - SIPERU PGT')
@section('page_title', 'Peminjaman Baru')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
        <h3 class="text-sm font-bold text-siperu-blue uppercase tracking-wide">Formulir Peminjaman Ruangan</h3>
        <p class="text-xs text-gray-500 mt-0.5">Isi data di bawah ini secara lengkap untuk mengajukan izin penggunaan ruangan.</p>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-red-50 border-b border-red-200 text-red-700 text-xs font-semibold">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('reservation.store') }}" method="POST" enctype="multipart/form-data" class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        @csrf

        <div class="space-y-4">
            <div>
                <label for="room_id" class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-2">Pilih Ruangan Kuliah</label>
                <select name="room_id" id="room_id" required class="w-full text-xs px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-siperu-yellow">
                    <option value="">-- Pilih Ruangan Tersedia --</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>{{ $room->nama_ruangan }} (Kapasitas: {{ $room->kapasitas }} Orang)</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="tanggal" class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-2">Tanggal Peminjaman</label>
                <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal') }}" required class="w-full text-xs px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-siperu-yellow">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="waktu_mulai" class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-2">Jam Mulai</label>
                    <input type="time" name="waktu_mulai" id="waktu_mulai" value="{{ old('waktu_mulai') }}" required class="w-full text-xs px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-siperu-yellow">
                </div>
                <div>
                    <label for="waktu_selesai" class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-2">Jam Selesai</label>
                    <input type="time" name="waktu_selesai" id="waktu_selesai" value="{{ old('waktu_selesai') }}" required class="w-full text-xs px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-siperu-yellow">
                </div>
            </div>
        </div>

        <div class="space-y-4 flex flex-col justify-between">
            <div class="space-y-4">
                <div>
                    <label for="nomor_whatsapp" class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-2">No. WhatsApp Aktif (Untuk Notifikasi)</label>
                    <input type="text" name="nomor_whatsapp" id="nomor_whatsapp" value="{{ old('nomor_whatsapp') }}" placeholder="Contoh: 08123456789" required class="w-full text-xs px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-siperu-yellow">
                </div>

                <div>
                    <label for="dokumen_proposal" class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-2">Upload Dokumen Proposal (Format .PDF, Max 1MB)</label>
                    <input type="file" name="dokumen_proposal" id="dokumen_proposal" accept="application/pdf" required class="w-full text-xs px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-siperu-blue file:text-white hover:file:bg-opacity-90 cursor-pointer">
                </div>
            </div>

            <div class="pt-4 md:pt-0 flex justify-end">
                <button type="submit" class="w-full md:w-auto px-6 py-3 bg-siperu-blue hover:bg-opacity-90 text-white font-bold text-xs uppercase tracking-widest rounded-xl shadow-md transition">
                    Ajukan Sekarang
                </button>
            </div>
        </div>

    </form>
</div>
@endsection
