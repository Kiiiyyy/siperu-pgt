<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Room;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // =========================================================================
        // 1. DATA MASTER USER (AKUN AKADEMIK)
        // =========================================================================

        // Akun Administrator Sistem Pusat
        User::create([
            'nim' => 'admin123',
            'nama' => 'Admin SIPERU Pusat',
            'jurusan' => 'Teknologi Informasi',
            'kelas' => 'Staff',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_approval_admin' => false,
            'no_hp' => '6281111111111',
        ]);

        // Akun Dosen 1: Murni Dosen Pembina / Terkait Kegiatan Bimbingan
        User::create([
            'nim' => 'dosen123',
            'nama' => 'Dr. Aris Sudarsono',
            'jurusan' => 'Teknologi Informasi',
            'kelas' => 'Dosen Tetap',
            'password' => Hash::make('password123'),
            'role' => 'dosen',
            'is_approval_admin' => false, // Tidak punya wewenang kunci ruangan final
            'no_hp' => '6281234567890',  // Silakan ganti dengan nomor aktif untuk simulasi WA
        ]);

        // Akun Dosen 2: Extended Role (Otoritas Ruangan / Kepala Sarpras Kampus)
        User::create([
            'nim' => 'adminruang123',
            'nama' => 'Budi Santoso, M.T.',
            'jurusan' => 'Teknologi Informasi',
            'kelas' => 'Kepala Unit Sarpras',
            'password' => Hash::make('password123'),
            'role' => 'dosen',
            'is_approval_admin' => true,  // 🔥 AKTIF: Memiliki wewenang hak akses tahap 2
            'no_hp' => '6289876543210',  // Silakan ganti dengan nomor aktif untuk simulasi WA
        ]);

        // Akun Mahasiswa 1
        User::create([
            'nim' => '202401001',
            'nama' => 'Ahmad Rifai',
            'jurusan' => 'Teknologi Informasi',
            'kelas' => 'TI-3A',
            'password' => Hash::make('password123'),
            'role' => 'mahasiswa',
            'is_approval_admin' => false,
            'no_hp' => '6285555555555',
        ]);

        // Akun Mahasiswa 2: Disediakan khusus untuk simulasi kompetisi/bentrok proposal
        User::create([
            'nim' => '202401045',
            'nama' => 'Muhammad Dzaky Ramadhani',
            'jurusan' => 'Teknologi Informasi',
            'kelas' => 'TI-3B',
            'password' => Hash::make('password123'),
            'role' => 'mahasiswa',
            'is_approval_admin' => false,
            'no_hp' => '6289999999999',
        ]);


        // =========================================================================
        // 2. DATA MASTER FASILITAS RUANGAN KAMPUS
        // =========================================================================

        Room::create([
            'nama_ruangan' => 'Laboratorium Komputer 1',
            'kapasitas' => 30,
            'status' => 'Tersedia',
        ]);

        Room::create([
            'nama_ruangan' => 'Aula Utama Kampus',
            'kapasitas' => 150,
            'status' => 'Tersedia',
        ]);

        Room::create([
            'nama_ruangan' => 'Ruang Teori 102',
            'kapasitas' => 40,
            'status' => 'Perbaikan', // Masuk daftar cek pencekalan transaksi otomatis
        ]);
    }
}