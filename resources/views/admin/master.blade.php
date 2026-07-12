@extends('layouts.app')

@section('title', 'Data Master - SIPERU PGT')
@section('page_title', 'Mengelola Data Master')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    @if(session('success'))
        <div class="p-4 bg-green-100 border-l-4 border-green-500 text-green-700 text-xs font-bold rounded shadow-sm">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div class="flex gap-2 border-b border-gray-200 pb-px">
        <a href="?category=ruangan" class="px-5 py-2.5 text-xs font-bold uppercase tracking-wider rounded-t-xl transition border-t border-x {{ $category === 'ruangan' ? 'bg-white border-gray-200 text-siperu-blue' : 'bg-gray-100 border-transparent text-gray-400 hover:text-gray-600' }}">
            🏢 Manajemen Data Ruangan
        </a>
        <a href="?category=user" class="px-5 py-2.5 text-xs font-bold uppercase tracking-wider rounded-t-xl transition border-t border-x {{ $category === 'user' ? 'bg-white border-gray-200 text-siperu-blue' : 'bg-gray-100 border-transparent text-gray-400 hover:text-gray-600' }}">
            👤 Manajemen Akun User
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5 space-y-4">

            @if($category === 'ruangan')
                <h3 class="text-xs font-bold text-siperu-blue uppercase tracking-wider border-b pb-2">
                    {{ $editData ? 'Edit Data Lama Ruangan' : 'Formulir Entitas Ruangan Baru' }}
                </h3>

                <form action="{{ $editData ? route('admin.room.update', $editData->id) : route('admin.room.store') }}" method="POST" class="space-y-4">
                    @csrf
                    @if($editData) @method('PUT') @endif

                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Nama Ruangan</label>
                        <input type="text" name="nama_ruangan" required value="{{ $editData ? $editData->nama_ruangan : old('nama_ruangan') }}" placeholder="Contoh: Lab Komputer 3" class="w-full text-xs px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-siperu-yellow">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Kapasitas (Orang)</label>
                        <input type="number" name="kapasitas" required value="{{ $editData ? $editData->kapasitas : old('kapasitas') }}" placeholder="30" class="w-full text-xs px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-siperu-yellow">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Status Ruangan</label>
                        <select name="status" class="w-full text-xs px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-siperu-yellow">
                            <option value="Tersedia" {{ ($editData && $editData->status == 'Tersedia') ? 'selected' : '' }}>Tersedia</option>
                            <option value="Perbaikan" {{ ($editData && $editData->status == 'Perbaikan') ? 'selected' : '' }}>Perbaikan</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-siperu-blue hover:bg-opacity-90 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow">
                        {{ $editData ? 'Perbarui Data Ruangan' : 'Simpan Data Ruangan' }}
                    </button>

                    @if($editData)
                        <a href="?category=ruangan" class="block text-center w-full py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold text-xs uppercase tracking-wider rounded-xl transition mt-2">Batal</a>
                    @endif
                </form>

            @else
                <h3 class="text-xs font-bold text-siperu-blue uppercase tracking-wider border-b pb-2">
                    {{ $editData ? 'Edit Data Akun User' : 'Formulir Registrasi Akun Baru' }}
                </h3>

                <form action="{{ $editData ? route('admin.user.update', $editData->id) : route('admin.user.store') }}" method="POST" class="space-y-3">
                    @csrf
                    @if($editData) @method('PUT') @endif

                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Role</label>
                        <select name="role" id="role_select" onchange="adjustFormFields()" class="w-full text-xs px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-siperu-yellow">
                            <option value="mahasiswa" {{ ($editData && $editData->role == 'mahasiswa') ? 'selected' : '' }}>Mahasiswa</option>
                            <option value="dosen" {{ ($editData && $editData->role == 'dosen') ? 'selected' : '' }}>Dosen</option>
                            <option value="admin" {{ ($editData && $editData->role == 'admin') ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>

                    <div id="wrapper_checkbox_approval" style="display: none;" class="p-3 bg-purple-50 border border-purple-100 rounded-xl flex items-center gap-3">
                        <input type="checkbox" name="is_approval_admin" id="is_approval_admin" value="1" {{ ($editData && $editData->is_approval_admin) ? 'checked' : '' }} class="rounded border-purple-300 text-purple-600 focus:ring-purple-500">
                        <div>
                            <label for="is_approval_admin" class="block text-xs font-bold text-purple-700">Beri Wewenang Approval Admin</label>
                            <span class="text-[10px] text-purple-500 block">Dosen ini dapat memberikan persetujuan/peminjaman ruangan.</span>
                        </div>
                    </div>
                    <div>
                        <label id="nim_label" class="block text-[10px] font-bold text-gray-500 uppercase mb-1">NIM / Username Login</label>
                        <input type="text" name="nim" required value="{{ $editData ? $editData->nim : old('nim') }}" placeholder="202401002" class="w-full text-xs px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-siperu-yellow">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Nama Lengkap</label>
                        <input type="text" name="nama" required value="{{ $editData ? $editData->nama : old('nama') }}" placeholder="Budi Dermawan" class="w-full text-xs px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-siperu-yellow">
                    </div>
                    <div id="wrapper_jurusan">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Jurusan</label>
                        <input type="text" name="jurusan" id="input_jurusan" value="{{ $editData ? $editData->jurusan : old('jurusan') }}" placeholder="Teknologi Informasi" class="w-full text-xs px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-siperu-yellow">
                    </div>
                    <div>
                        <label id="kelas_label" class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Kelas / Jabatan</label>
                        <input type="text" name="kelas" required value="{{ $editData ? $editData->kelas : old('kelas') }}" placeholder="TI-3B" class="w-full text-xs px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-siperu-yellow">
                    </div>
                    <div id="wrapper_no_hp" style="display: none;">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Nomor WhatsApp Aktif</label>
                        <input type="text" name="no_hp" id="input_no_hp" value="{{ $editData ? $editData->no_hp : old('no_hp') }}" placeholder="628123456789" class="w-full text-xs px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-siperu-yellow">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Password Akses {{ $editData ? '(Kosongkan jika tak diubah)' : '' }}</label>
                        <input type="password" name="password" {{ $editData ? '' : 'required' }} placeholder="••••" class="w-full text-xs px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-siperu-yellow">
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-siperu-blue hover:bg-opacity-90 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow">
                        {{ $editData ? 'Perbarui Akun User' : 'Daftarkan Akun User' }}
                    </button>

                    @if($editData)
                        <a href="?category=user" class="block text-center w-full py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold text-xs uppercase tracking-wider rounded-xl transition mt-2">Batal</a>
                    @endif
                </form>

                <script>
                    function adjustFormFields() {
                        const role = document.getElementById('role_select').value;
                        const nimLabel = document.getElementById('nim_label');
                        const kelasLabel = document.getElementById('kelas_label');
                        const wrapperJurusan = document.getElementById('wrapper_jurusan');
                        const inputJurusan = document.getElementById('input_jurusan');
                        const wrapperNoHp = document.getElementById('wrapper_no_hp');
                        const inputNoHp = document.getElementById('input_no_hp');
                        const wrapperCheckboxApproval = document.getElementById('wrapper_checkbox_approval');

                        if (role === 'mahasiswa') {
                            nimLabel.innerText = "NIM / Username Login";
                            kelasLabel.innerText = "Kelas / Prodi";
                            wrapperJurusan.style.display = "block";
                            inputJurusan.required = true;
                            wrapperNoHp.style.display = "none";
                            inputNoHp.required = false;
                            wrapperCheckboxApproval.style.display = "none"; // Sembunyikan checkbox
                        } else if (role === 'dosen') {
                            nimLabel.innerText = "NIDN / Username Login";
                            kelasLabel.innerText = "Jabatan Dosen";
                            wrapperJurusan.style.display = "none";
                            inputJurusan.required = false;
                            wrapperNoHp.style.display = "block";
                            inputNoHp.required = true;
                            wrapperCheckboxApproval.style.display = "flex"; // TAMPILKAN CHECKBOX UNTUK DOSEN
                        } else { // admin
                            nimLabel.innerText = "Username Login Admin";
                            kelasLabel.innerText = "Jabatan / Divisi";
                            wrapperJurusan.style.display = "none";
                            inputJurusan.required = false;
                            wrapperNoHp.style.display = "none";
                            inputNoHp.required = false;
                            wrapperCheckboxApproval.style.display = "none"; // Sembunyikan checkbox
                        }
                    }
                    document.addEventListener("DOMContentLoaded", adjustFormFields);
                </script>
            @endif
        </div>

        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">
                Daftar Tabel Master: {{ $category === 'ruangan' ? 'Seluruh Ruangan PGT' : 'Akun Terdaftar' }}
            </h3>

            <div class="overflow-x-auto border border-gray-100 rounded-xl bg-gray-50 p-1">
                @if($category === 'ruangan')
                    <table class="min-w-full bg-white rounded-lg overflow-hidden divide-y divide-gray-100 text-xs text-center font-medium">
                        <thead class="bg-gray-50 text-gray-400 text-[10px] font-bold uppercase tracking-wider">
                            <tr>
                                <th class="py-2 text-left pl-4">Nama</th>
                                <th>Kapasitas</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($rooms as $room)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="py-3 font-bold text-siperu-blue text-left pl-4">{{ $room->nama_ruangan }}</td>
                                    <td>{{ $room->kapasitas }} Kursi</td>
                                    <td>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase {{ $room->status === 'Tersedia' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $room->status }}
                                        </span>
                                    </td>
                                    <td class="px-2">
                                        <div class="flex items-center justify-center gap-3">
                                            <a href="?category=ruangan&edit={{ $room->id }}" class="text-blue-500 hover:text-blue-700 text-[10px] font-bold uppercase tracking-wider">Edit</a>
                                            <form action="{{ route('admin.room.destroy', $room->id) }}" method="POST" onsubmit="return confirm('Konfirmasi Hapus? Klik Batal untuk kembali ke tabel.')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 text-[10px] font-bold uppercase tracking-wider">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <table class="min-w-full bg-white rounded-lg overflow-hidden divide-y divide-gray-100 text-xs text-center font-medium">
                        <thead class="bg-gray-50 text-gray-400 text-[10px] font-bold uppercase tracking-wider">
                            <tr>
                                <th class="py-2 text-left pl-4">Nama / NIM</th>
                                <th>Role</th>
                                <th>No WA / Kontak</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($users as $user)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="py-3 text-left pl-4">
                                        <div class="font-bold text-gray-800">{{ $user->nama }}</div>
                                        <div class="text-[10px] text-gray-400">{{ $user->nim }}</div>
                                    </td>
                                    <td>
                                        <div class="flex flex-col gap-1 items-center">
                                            <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase {{ $user->role === 'admin' ? 'bg-red-100 text-red-700' : ($user->role === 'dosen' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600') }}">
                                                {{ $user->role }}
                                            </span>

                                            @if($user->role === 'dosen' && $user->is_approval_admin)
                                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-purple-100 text-purple-700 border border-purple-200 shadow-sm animate-pulse">
                                                    + Approval Admin
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-gray-500 text-[11px]">
                                        @if($user->no_hp)
                                            <span class="text-green-600 font-bold">🟢 {{ $user->no_hp }}</span>
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                        <br><small>{{ $user->kelas }} {{ $user->jurusan ? '- '.$user->jurusan : '' }}</small>
                                    </td>
                                    <td class="px-2">
                                        <div class="flex items-center justify-center gap-3">
                                            <a href="?category=user&edit={{ $user->id }}" class="text-blue-500 hover:text-blue-700 text-[10px] font-bold uppercase tracking-wider">Edit</a>
                                            <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Konfirmasi Hapus? Klik Batal untuk kembali ke tabel.')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 text-[10px] font-bold uppercase tracking-wider">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
