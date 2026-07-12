<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Reservation;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    // Menampilkan halaman utama web dengan data filter ruangan
    public function index()
    {
        $rooms = Room::all(); 
        return view('dashboard', compact('rooms'));
    }

    // 🔥 REFACTORING: Sinkronisasi Status Berjenjang + Perbaikan Bug Filter Dropdown
    public function getKalenderKetersediaan(Request $request)
    {
        // Hubungkan relasi room dan user untuk keperluan informasi detail di kalender
        $query = Reservation::with(['room', 'user']);

        // 🎯 FIX BUG: Filter berdasarkan dropdown ruangan jika dipilih oleh user
        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        // Ambil seluruh jadwal aktif yang masuk ke dalam alur pemblokiran fasilitas fisik
        $reservations = $query->whereIn('status_izin', ['Pending', 'Disetujui Dosen', 'Disetujui'])->get();

        $events = [];
        foreach ($reservations as $res) {
            // Skema Default
            $bgColor = '#F3F4F6';
            $borderColor = '#D1D5DB';
            $textColor = '#374151';

            // 🎨 Peta Warna Kompetisi Proposal (Minimalis Pastel)
            if ($res->status_izin === 'Pending') {
                $bgColor = '#EFF6FF';      // Blue 50
                $borderColor = '#BFDBFE';  // Blue 200
                $textColor = '#1D4ED8';    // Blue 700
            } elseif ($res->status_izin === 'Disetujui Dosen') {
                $bgColor = '#F5F3FF';      // Purple 50
                $borderColor = '#DDD6FE';  // Purple 200
                $textColor = '#6D28D9';    // Purple 700
            } elseif ($res->status_izin === 'Disetujui') {
                $bgColor = '#F0FDF4';      // Green 50
                $borderColor = '#BBF7D0';  // Green 200
                $textColor = '#15803D';    // Green 700
            }

            $events[] = [
                'id' => $res->id,
                'title' => '[' . $res->room->nama_ruangan . '] ' . $res->judul_pengajuan,
                'start' => $res->waktu_mulai,
                'end' => $res->waktu_selesai,
                'backgroundColor' => $bgColor,
                'borderColor' => $borderColor,
                'textColor' => $textColor,
                // Kirim meta data tambahan ke front-end untuk kebutuhan laci informasi detail
                'extendedProps' => [
                    'agenda' => $res->judul_pengajuan,
                    'pemohon' => $res->user->nama ?? '-',
                    'ruangan' => $res->room->nama_ruangan,
                    'status' => $res->status_izin,
                    'waktu' => date('d-m-Y', strtotime($res->waktu_mulai)) . ' (' . date('H:i', strtotime($res->waktu_mulai)) . ' - ' . date('H:i', strtotime($res->waktu_selesai)) . ' WIB)'
                ]
            ];
        }

        return response()->json($events);
    }
}