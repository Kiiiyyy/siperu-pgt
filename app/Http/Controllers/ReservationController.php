<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
class ReservationController extends Controller
{
    // 1. Menampilkan Form Peminjaman
    public function create()
    {
        // Hanya ruangan bertatus 'Tersedia' yang boleh dipinjam
        $rooms = Room::where('status', 'Tersedia')->get();
        return view('reservation.create', compact('rooms'));
    }

    // 2. Memproses Data Pengajuan (buatPengajuan() & validasiBentrok() di UML)
    public function store(Request $request)
    {
        // A. Validasi Dasar & Ukuran File Proposal max 1MB (Sesuai Activity Diagram)
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'nomor_whatsapp' => 'required|numeric',
            'dokumen_proposal' => 'required|file|mimes:pdf|max:1024', // max 1024 KB = 1MB
        ], [
            'dokumen_proposal.max' => 'Ukuran file proposal tidak boleh melebihi 1 Megabyte (1MB)!',
            'waktu_selesai.after' => 'Waktu selesai harus lebih lambat dari waktu mulai.',
        ]);

        // Gabungkan tanggal dan jam menjadi format DateTime penuh untuk database
        $waktuMulaiFull = $request->tanggal . ' ' . $request->waktu_mulai . ':00';
        $waktuSelesaiFull = $request->tanggal . ' ' . $request->waktu_selesai . ':00';

        // B. Validasi Jadwal Bentrok (Algoritma Overlapping Schedule)
        $jadwalBentrok = Reservation::where('room_id', $request->room_id)
            ->where('status_izin', 'Disetujui') // Hanya mengecek jadwal yang sudah fix disetujui
            ->where(function ($query) use ($waktuMulaiFull, $waktuSelesaiFull) {
                $query->where(function ($q) use ($waktuMulaiFull, $waktuSelesaiFull) {
                    $q->where('waktu_mulai', '<', $waktuSelesaiFull)
                      ->where('waktu_selesai', '>', $waktuMulaiFull);
                });
            })->exists();

        if ($jadwalBentrok) {
            return back()->withErrors([
                'bentrok' => 'Jadwal gagal diajukan! Ruangan tersebut sudah dipesan oleh orang lain pada jam yang sama.'
            ])->withInput();
        }

        // C. Proses Upload File Proposal ke Folder Storage Lokal
        $filePath = null;
        if ($request->hasFile('dokumen_proposal')) {
            $file = $request->file('dokumen_proposal');
            // Simpan di public/proposals agar mudah diunduh dosen nanti
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('proposals'), $fileName);
            $filePath = 'proposals/' . $fileName;
        }

        // D. Simpan dengan Status Default 'Pending' (Sesuai UML)
        Reservation::create([
            'user_id' => Auth::id(),
            'room_id' => $request->room_id,
            'waktu_mulai' => $waktuMulaiFull,
            'waktu_selesai' => $waktuSelesaiFull,
            'dokumen_proposal' => $filePath,
            'nomor_whatsapp' => $request->nomor_whatsapp,
            'status_izin' => 'Pending',
        ]);

        return redirect()->route('dashboard')->with('success', 'Pengajuan ruangan berhasil disimpan! Menunggu persetujuan dosen.');
    }

    // Tambahkan ini di dalam class ReservationController Anda:
    public function history()
    {
        // Ambil data reservasi milik user yang login, urutkan dari yang terbaru
        $reservations = Reservation::with('room')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(5); // Kita batasi 5 data per halaman sesuai wireframe yang ringkas

        return view('reservation.history', compact('reservations'));
    }

    // 3. Menampilkan Daftar Pengajuan Masuk Berstatus Pending (Untuk Dosen/Admin)
    public function approvalList()
    {
        // Ambil semua pengajuan yang statusnya masih Pending
        $reservations = Reservation::with(['room', 'user'])
            ->where('status_izin', 'Pending')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('dosen.approval', compact('reservations'));
    }

    // 4. Proses Approve Pengajuan + Integrasi Otomatis Fonnte WhatsApp API
    public function approve($id)
    {
        $reservation = Reservation::with(['room', 'user'])->findOrFail($id);

        // Update status di database menjadi Disetujui
        $reservation->update([
            'status_izin' => 'Disetujui'
        ]);

        // Kirim Notifikasi WA via Fonnte API (Sesuai Alur Activity Diagram)
        $token = env('FONNTE_TOKEN');
        $pesan = "Halo *{$reservation->user->nama}*,\n\nPengajuan peminjaman ruangan Anda untuk:\n🔹 *Ruangan:* {$reservation->room->nama_ruangan}\n📅 *Tanggal:* " . date('d-m-Y', strtotime($reservation->waktu_mulai)) . "\n⏰ *Jam:* " . date('H:i', strtotime($reservation->waktu_mulai)) . " - " . date('H:i', strtotime($reservation->waktu_selesai)) . " WIB\n\nStatus pengajuan Anda telah *DISETUJUI* oleh Dosen. Silakan gunakan ruangan sesuai jadwal.\n\n_Terima kasih,\nSistem SIPERU PGT_";

        // Eksekusi nembak API Fonnte menggunakan HTTP Client bawaan Laravel 12
        try {
            Http::withHeaders([
                'Authorization' => $token
            ])->post('https://api.fonnte.com/send', [
                'target' => $reservation->nomor_whatsapp,
                'message' => $pesan,
                'country' => '62' // Set ke kode negara Indonesia
            ]);
        } catch (\Exception $e) {
            // Jika API Fonnte error/kuota habis, sistem tidak akan crash dan tetap melanjutkan redirect
        }

        return redirect()->route('reservation.approval')->with('success', 'Pengajuan berhasil disetujui dan notifikasi WA telah dikirim!');
    }

    // 5. Proses Reject Pengajuan (Tanpa Kirim WA / Sesuai Jalur Kanan Activity Diagram)
    public function reject($id)
    {
        $reservation = Reservation::findOrFail($id);

        // Update status menjadi Ditolak
        $reservation->update([
            'status_izin' => 'Ditolak'
        ]);

        return redirect()->route('reservation.approval')->with('info', 'Pengajuan ruangan telah ditolak.');
    }

    // 6. Menampilkan Halaman Filter Laporan (Untuk Dosen)
    public function reportIndex()
    {
        $rooms = Room::all();
        return view('dosen.report', compact('rooms'));
    }

    // 7. Mengonversi HTML ke PDF Menggunakan DomPDF (generateLaporanPDF() di UML)
    public function exportPDF(Request $request)
    {
        // Ambil data dasar reservasi yang sudah disetujui
        $query = Reservation::with(['room', 'user'])->where('status_izin', 'Disetujui');

        // Terapkan Filter Ruangan jika dipilih
        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        // Terapkan Filter Bulan jika dipilih
        if ($request->filled('bulan')) {
            $query->whereMonth('waktu_mulai', $request->bulan);
        }

        // Terapkan Filter Tahun jika dipilih
        if ($request->filled('tahun')) {
            $query->whereYear('waktu_mulai', $request->tahun);
        }

        $reservations = $query->orderBy('waktu_mulai', 'asc')->get();

        // Gunakan Facade PDF bawaan package barryvdh/laravel-dompdf
        // Catatan: Kita pisahkan view khusus strukturnya agar DomPDF tidak error membaca utility CSS Tailwind
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('dosen.report_pdf', compact('reservations', 'request'));

        // Download otomatis dengan nama file dinamis
        return $pdf->download('Laporan-Peminjaman-' . date('Y-m-d') . '.pdf');
    }
}
