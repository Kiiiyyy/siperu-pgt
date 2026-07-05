<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'nama_ruangan',
        'kapasitas',
        'status',
    ];

    // Relasi ke Reservation: 1 Ruangan bisa memiliki banyak jadwal reservasi
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
