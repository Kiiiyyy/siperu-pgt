<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'room_id',
        'waktu_mulai',
        'waktu_selesai',
        'dokumen_proposal',
        'nomor_whatsapp',
        'status_izin',
    ];

    // Relasi balik ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi balik ke Room
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
