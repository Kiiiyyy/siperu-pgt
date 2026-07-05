<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Reservation;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    // MATCH SINKRON: Menampilkan halaman utama web dengan data filter ruangan
    public function index()
    {
        // Tarik data seluruh ruangan dari database agar dropdown filter tidak kosong
        $rooms = Room::all(); 
        
        return view('dashboard', compact('rooms'));
    }

    // MATCH SINKRON: Select data reservasi (Status: Disetujui & Pending)
    public function getKalenderKetersediaan()
    {
        $reservations = Reservation::with('room')
            ->whereIn('status_izin', ['Disetujui', 'Pending'])
            ->get();

        $events = [];
        foreach ($reservations as $res) {
            $events[] = [
                'title' => '[' . $res->room->nama_ruangan . '] - ' . $res->status_izin,
                'start' => $res->waktu_mulai,
                'end' => $res->waktu_selesai,
                'backgroundColor' => $res->status_izin === 'Disetujui' ? '#0A3981' : '#F3C31B',
                'borderColor' => $res->status_izin === 'Disetujui' ? '#0A3981' : '#F3C31B',
                'textColor' => $res->status_izin === 'Disetujui' ? '#FFFFFF' : '#0A3981',
            ];
        }

        return response()->json($events);
    }
}