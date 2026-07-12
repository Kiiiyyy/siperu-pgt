@extends('layouts.app')

@section('title', 'Edit Peminjaman - SIPERU PGT')
@section('page_title', 'Edit Peminjaman')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
        <h3 class="text-sm font-bold text-siperu-blue uppercase tracking-wide">Ubah Formulir Peminjaman</h3>
        <p class="text-xs text-gray-500 mt-0.5">Ubah data pengajuan peminjaman ruangan yang masih dalam antrean pending.</p>
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

    <form action="{{ route('reservation.update', $reservation->id) }}" method="POST" enctype="multipart/form-data" class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <div>
                <label for="judul_pengajuan" class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-2">Judul Agenda / Pengajuan Peminjaman</label>
                <input type="text" name="judul_pengajuan" id="judul_pengajuan" value="{{ old('judul_pengajuan', $reservation->judul_pengajuan) }}" required class="w-full text-xs px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-siperu-yellow">
            </div>

            <div>
                <label for="room_id" class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-2">Pilih Ruangan Kuliah</label>
                <select name="room_id" id="room_id" required class="w-full text-xs px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-siperu-yellow">
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}" {{ old('room_id', $reservation->room_id) == $room->id ? 'selected' : '' }}>{{ $room->nama_ruangan }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="tanggal" class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-2">Tanggal Peminjaman</label>
                <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', date('Y-m-d', strtotime($reservation->waktu_mulai))) }}" required class="w-full text-xs px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-siperu-yellow">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="waktu_mulai" class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-2">Jam Mulai</label>
                    <input type="time" name="waktu_mulai" id="waktu_mulai" value="{{ old('waktu_mulai', date('H:i', strtotime($reservation->waktu_mulai))) }}" required class="w-full text-xs px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-siperu-yellow">
                </div>
                <div>
                    <label for="waktu_selesai" class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-2">Jam Selesai</label>
                    <input type="time" name="waktu_selesai" id="waktu_selesai" value="{{ old('waktu_selesai', date('H:i', strtotime($reservation->waktu_selesai))) }}" required class="w-full text-xs px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-siperu-yellow">
                </div>
            </div>
        </div>

        <div class="space-y-4 flex flex-col justify-between">
            <div class="space-y-4">
                <div>
                    <label for="lecturer_id" class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-2">Dosen Terkait / Pembina</label>
                    <select name="lecturer_id" id="lecturer_id" required class="w-full text-xs px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-siperu-yellow">
                        @foreach($lecturers as $lecturer)
                            <option value="{{ $lecturer->id }}" {{ old('lecturer_id', $reservation->lecturer_id) == $lecturer->id ? 'selected' : '' }}>{{ $lecturer->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="approval_admin_id" class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-2">Approval Admin</label>
                    <select name="approval_admin_id" id="approval_admin_id" required class="w-full text-xs px-3 py-2.5 bg-purple-50/30 border border-purple-200 rounded-xl focus:outline-none focus:border-siperu-yellow">
                        @foreach($approvalAdmins as $aa)
                            <option value="{{ $aa->id }}" {{ old('approval_admin_id', $reservation->approval_admin_id) == $aa->id ? 'selected' : '' }}>{{ $aa->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="nomor_whatsapp" class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-2">No. WhatsApp Aktif</label>
                    <input type="text" name="nomor_whatsapp" id="nomor_whatsapp" value="{{ old('nomor_whatsapp', $reservation->nomor_whatsapp) }}" required class="w-full text-xs px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-siperu-yellow">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-2">Dokumen Proposal (Biarkan kosong jika tidak diganti)</label>
                    <input type="file" name="dokumen_proposal" accept="application/pdf" class="w-full text-xs px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-siperu-blue file:text-white cursor-pointer">
                </div>
            </div>

            <div class="pt-4 md:pt-0 flex justify-end gap-2">
                <a href="{{ route('reservation.history') }}" class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold text-xs uppercase tracking-widest rounded-xl transition">Batal</a>
                <button type="submit" class="px-6 py-3 bg-siperu-blue hover:bg-opacity-90 text-white font-bold text-xs uppercase tracking-widest rounded-xl shadow-md transition">Simpan Perubahan</button>
            </div>
        </div>
    </form>
</div>
@endsection
