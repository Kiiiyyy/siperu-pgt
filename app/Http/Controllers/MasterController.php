<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class MasterController extends Controller
{
    private function ensureIsAdmin()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak! Halaman ini khusus untuk Admin.');
        }
    }

    public function index(Request $request)
    {
        $this->ensureIsAdmin();

        $category = $request->get('category', 'ruangan');
        $rooms = Room::orderBy('nama_ruangan', 'asc')->get();
        $users = User::orderBy('nama', 'asc')->get();

        $editData = null;
        if ($request->filled('edit')) {
            if ($category === 'ruangan') {
                $editData = Room::find($request->edit);
            } else {
                $editData = User::find($request->edit);
            }
        }

        return view('admin.master', compact('category', 'rooms', 'users', 'editData'));
    }

    // ==================== CRUD DATA RUANGAN ====================

    public function storeRoom(Request $request)
    {
        $this->ensureIsAdmin();
        $request->validate([
            'nama_ruangan' => 'required|string|max:255',
            'kapasitas' => 'required|numeric|min:1',
            'status' => 'required|in:Tersedia,Perbaikan', // 🔥 Hapus 'Digunakan'
        ]);
        Room::create($request->all());
        return redirect('/admin/master?category=ruangan')->with('success', 'Data Ruangan berhasil ditambahkan!');
    }

    public function updateRoom(Request $request, $id)
    {
        $this->ensureIsAdmin();
        $request->validate([
            'nama_ruangan' => 'required|string|max:255',
            'kapasitas' => 'required|numeric|min:1',
            'status' => 'required|in:Tersedia,Perbaikan', // 🔥 Hapus 'Digunakan'
        ]);
        $room = Room::findOrFail($id);
        $room->update($request->all());
        return redirect('/admin/master?category=ruangan')->with('success', 'Data Ruangan berhasil diperbarui!');
    }

    public function destroyRoom($id)
    {
        $this->ensureIsAdmin();
        $room = Room::findOrFail($id);
        $room->delete();
        return redirect('/admin/master?category=ruangan')->with('success', 'Data Ruangan berhasil dihapus!');
    }

    // ==================== CRUD DATA AKUN USER ====================

    public function storeUser(Request $request)
    {
        $this->ensureIsAdmin();
        $request->validate([
            'nim' => 'required|string|unique:users,nim',
            'nama' => 'required|string|max:255',
            'jurusan' => 'required_if:role,mahasiswa|nullable|string',
            'kelas' => 'required|string',
            'password' => 'required|string|min:4',
            'role' => 'required|in:admin,dosen,mahasiswa', // Balik ke 3 role utama
            'no_hp' => ['required', 'regex:/^628[0-9]{8,12}$/'],
        ], [
            'no_hp.regex' => 'Format nomor handphone master wajib diawali dengan kode 628 (contoh: 6288213503918).',
        ]);

        User::create([
            'nim' => $request->nim,
            'nama' => $request->nama,
            'jurusan' => $request->role === 'mahasiswa' ? $request->jurusan : null,
            'kelas' => $request->kelas,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'no_hp' => $request->role === 'dosen' ? $request->no_hp : null,
            // 🔥 Logika Extended Role: Jika dia dosen dan checkbox dicentang, set true (1)
            'is_approval_admin' => ($request->role === 'dosen' && $request->has('is_approval_admin')) ? true : false,
        ]);

        return redirect('/admin/master?category=user')->with('success', 'Akun User baru berhasil didaftarkan!');
    }

    public function updateUser(Request $request, $id)
    {
        $this->ensureIsAdmin();
        $request->validate([
            'nim' => 'required|string|unique:users,nim,' . $id,
            'nama' => 'required|string|max:255',
            'role' => 'required|in:admin,dosen,mahasiswa',
            // 🔥 SUNTIK PADA KOLOM NO HP DATA MASTER USER
            'no_hp' => ['required', 'regex:/^628[0-9]{8,12}$/'],
        ], [
            'no_hp.regex' => 'Format nomor handphone master wajib diawali dengan kode 628 (contoh: 6288213503918).',
        ]);

        $user = User::findOrFail($id);
        $data = [
            'nim' => $request->nim,
            'nama' => $request->nama,
            'jurusan' => $request->role === 'mahasiswa' ? $request->jurusan : null,
            'kelas' => $request->kelas,
            'role' => $request->role,
            'no_hp' => $request->role === 'dosen' ? $request->no_hp : null,
            // 🔥 Logika Extended Role saat update
            'is_approval_admin' => ($request->role === 'dosen' && $request->has('is_approval_admin')) ? true : false,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return redirect('/admin/master?category=user')->with('success', 'Data Akun User berhasil diperbarui!');
    }

    public function destroyUser($id)
    {
        $this->ensureIsAdmin();
        $user = User::findOrFail($id);
        if ($user->id === Auth::id()) {
            return redirect('/admin/master?category=user')->with('error', 'Anda tidak bisa menghapus akun Anda sendiri!');
        }
        $user->delete();
        return redirect('/admin/master?category=user')->with('success', 'Akun User berhasil dihapus!');
    }
}
