<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class MasterController extends Controller
{
    // Helper privat pembatas hak akses agar sesuai arsitektur Laravel 12
    private function ensureIsAdmin()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak! Halaman ini khusus untuk Admin.');
        }
    }

    // 1. Menampilkan Halaman Utama Data Master
    public function index(Request $request)
    {
        $this->ensureIsAdmin(); // Kunci pengaman aktif

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
            'status' => 'required|in:Tersedia,Digunakan,Perbaikan',
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
            'status' => 'required|in:Tersedia,Digunakan,Perbaikan',
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
            'jurusan' => 'required|string',
            'kelas' => 'required|string',
            'password' => 'required|string|min:4',
            'role' => 'required|in:admin,dosen,mahasiswa',
        ]);
        User::create([
            'nim' => $request->nim,
            'nama' => $request->nama,
            'jurusan' => $request->jurusan,
            'kelas' => $request->kelas,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);
        return redirect('/admin/master?category=user')->with('success', 'Akun User baru berhasil didaftarkan!');
    }

    public function updateUser(Request $request, $id)
    {
        $this->ensureIsAdmin();
        $request->validate([
            'nim' => 'required|string|unique:users,nim,' . $id,
            'nama' => 'required|string|max:255',
            'jurusan' => 'required|string',
            'kelas' => 'required|string',
            'role' => 'required|in:admin,dosen,mahasiswa',
        ]);
        $user = User::findOrFail($id);
        $data = [
            'nim' => $request->nim,
            'nama' => $request->nama,
            'jurusan' => $request->jurusan,
            'kelas' => $request->kelas,
            'role' => $request->role,
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