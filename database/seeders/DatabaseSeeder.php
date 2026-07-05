<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Room;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Data Akun User (Admin, Dosen, Mahasiswa)
        User::create([
            'nim' => 'admin123',
            'nama' => 'Admin SIPERU',
            'jurusan' => 'Teknologi Informasi',
            'kelas' => 'Staff',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        User::create([
            'nim' => 'dosen123',
            'nama' => 'Dr. Aris Sudarsono',
            'jurusan' => 'Teknologi Informasi',
            'kelas' => 'Dosen Tetap',
            'password' => Hash::make('password123'),
            'role' => 'dosen',
        ]);

        User::create([
            'nim' => '202401001',
            'nama' => 'Ahmad Rifai',
            'jurusan' => 'Teknologi Informasi',
            'kelas' => 'TI-3A',
            'password' => Hash::make('password123'),
            'role' => 'mahasiswa',
        ]);

        // 2. Seed Data Ruangan awal
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
            'status' => 'Perbaikan',
        ]);
    }
}
