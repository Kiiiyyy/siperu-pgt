<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'judul_pengajuan',     // Kolom baru Fase 1
        'user_id',
        'lecturer_id',         // Kolom baru Fase 1 (Dosen Terkait)
        'approval_admin_id',   // Kolom baru Fase 1 (Approval Admin)
        'room_id',
        'waktu_mulai',
        'waktu_selesai',
        'dokumen_proposal',
        'nomor_whatsapp',
        'status_izin',
    ];

    // Relasi balik ke User (Mahasiswa yang mengajukan)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Dosen Terkait yang dipilih
    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    // Relasi ke Approval Admin yang dimintai persetujuan
    public function approvalAdmin()
    {
        return $this->belongsTo(User::class, 'approval_admin_id');
    }

    // Relasi balik ke Room
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}
