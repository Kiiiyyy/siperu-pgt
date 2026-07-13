<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    // ==================== SISI MAHASISWA ====================

    public function create()
    {
        $rooms = Room::where('status', 'Tersedia')->orderBy('nama_ruangan', 'asc')->get();
        $lecturers = User::where('role', 'dosen')->orderBy('nama', 'asc')->get();
        $approvalAdmins = User::where('role', 'dosen')->where('is_approval_admin', true)->orderBy('nama', 'asc')->get();

        return view('reservation.create', compact('rooms', 'lecturers', 'approvalAdmins'));
    }

    // Memproses Data Pengajuan (Case 4: Proteksi Jam Operasional)
    // Memproses Data Pengajuan (Senin - Jumat)
    public function store(Request $request)
    {
        $request->validate([
            'judul_pengajuan' => 'required|string|max:255',
            'room_id' => 'required|exists:rooms,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'lecturer_id' => 'required|exists:users,id',
            'approval_admin_id' => 'required|exists:users,id',
            'nomor_whatsapp' => ['required', 'regex:/^628[0-9]{8,12}$/'],
            'dokumen_proposal' => 'required|file|mimes:pdf|max:1024',
        ], [
            'dokumen_proposal.max' => 'Ukuran file proposal tidak boleh melebihi 1 Megabyte (1MB)!',
            'waktu_selesai.after' => 'Waktu selesai harus lebih lambat dari waktu mulai.',
            'nomor_whatsapp.regex' => 'Format nomor WhatsApp tidak valid! Wajib diawali dengan kode negara 628 tanpa tanda + atau spasi (contoh: 6288213503918).',
        ]);

        // 🔥 FIX BUG: Mengunci hari kerja murni Senin s/d Jumat (Sabtu [6] dan Minggu [7] diblokir)
        $dayOfWeek = date('N', strtotime($request->tanggal));
        if ($request->waktu_mulai < '07:00' || $request->waktu_selesai > '22:00' || $dayOfWeek >= 6) {
            return back()->withErrors([
                'jam_operasi' => 'Izin ditolak! Peminjaman fasilitas kampus hanya diperbolehkan pada hari Senin s/d Jumat pukul 07:00 s/d 22:00 WIB.'
            ])->withInput();
        }

        $waktuMulaiFull = $request->tanggal . ' ' . $request->waktu_mulai . ':00';
        $waktuSelesaiFull = $request->tanggal . ' ' . $request->waktu_selesai . ':00';

        // Logika anti-tabrakan jadwal terkunci
        $jadwalBentrok = Reservation::where('room_id', $request->room_id)
            ->whereIn('status_izin', ['Disetujui Dosen', 'Disetujui'])
            ->where(function ($query) use ($waktuMulaiFull, $waktuSelesaiFull) {
                $query->where('waktu_mulai', '<', $waktuSelesaiFull)
                    ->where('waktu_selesai', '>', $waktuMulaiFull);
            })->exists();

        if ($jadwalBentrok) {
            return back()->withErrors(['bentrok' => 'Jadwal gagal diajukan! Ruangan pada jam tersebut sudah resmi dikunci mahasiswa lain.'])->withInput();
        }

        $filePath = null;
        if ($request->hasFile('dokumen_proposal')) {
            $file = $request->file('dokumen_proposal');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('proposals'), $fileName);
            $filePath = 'proposals/' . $fileName;
        }

        Reservation::create([
            'judul_pengajuan' => $request->judul_pengajuan,
            'user_id' => Auth::id(),
            'lecturer_id' => $request->lecturer_id,
            'approval_admin_id' => $request->approval_admin_id,
            'room_id' => $request->room_id,
            'waktu_mulai' => $waktuMulaiFull,
            'waktu_selesai' => $waktuSelesaiFull,
            'dokumen_proposal' => $filePath,
            'nomor_whatsapp' => $request->nomor_whatsapp,
            'status_izin' => 'Pending',
        ]);

        try {
            $token = env('FONNTE_TOKEN');
            $dosenTerkait = User::find($request->lecturer_id);
            $pesan = "NOTIFIKASI SIPERU PGT\nHalo Bapak/Ibu, ada pengajuan izin ruangan baru dari Mahasiswa untuk agenda \"{$request->judul_pengajuan}\". Mohon tinjau berkas di sistem.";

            if ($dosenTerkait && $dosenTerkait->no_hp) {
                Http::timeout(3)->withHeaders(['Authorization' => $token])->post('https://api.fonnte.com/send', [
                    'target' => $dosenTerkait->no_hp, 'message' => $pesan, 'country' => '62'
                ]);
            }
        } catch (\Exception $e) {}

        return redirect()->route('dashboard')->with('success', 'Pengajuan berhasil dikirim!');
    }

    public function history()
    {
        $reservations = Reservation::with(['room', 'lecturer']) // 🔥 SUNTIK 'lecturer' DISINI
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('reservation.history', compact('reservations'));
    }

    // ==================== SISI DOSEN & ALUR ESTAFET BERJENJANG ====================

    public function approvalList()
    {
        $user = Auth::user();
        if ($user->role !== 'dosen') { abort(403); }

        $dosenReservations = Reservation::with(['room', 'user'])->where('lecturer_id', $user->id)->orderBy('created_at', 'desc')->get();

        $adminReservations = $user->is_approval_admin ? Reservation::with(['room', 'user'])
            ->where('approval_admin_id', $user->id)
            ->where('status_izin', '!=', 'Pending')
            ->orderBy('created_at', 'desc')
            ->get() : collect();

        return view('dosen.approval', compact('dosenReservations', 'adminReservations'));
    }

    // Proses Approve Terproteksi Masif (Case 1, 2, & 5 Teratasi)
    public function approve($id)
    {
        $token = env('FONNTE_TOKEN');

        // 🔥 CASE 5: Gunakan DB Transaction & Pessimistic Lock (lockForUpdate) untuk mencegah klik barengan di mili-detik yang sama
        return DB::transaction(function () use ($id, $token) {
            $reservation = Reservation::lockForUpdate()->with(['room', 'user', 'lecturer'])->findOrFail($id);

            // 🔥 CASE 2: Proteksi Status Limbo Data Master
            if ($reservation->room->status !== 'Tersedia') {
                return redirect()->route('reservation.approval')->with('info', '🚨 Pembatalan Sistem: Ruangan ini sedang disetel ke status TIDAK TERSEDIA/PERBAIKAN oleh Admin Pusat.');
            }

            // TAHAP 1: Ketukan Palu Dosen Pembina (Pending -> Disetujui Dosen)
            if ($reservation->status_izin === 'Pending') {

                // Cek ulang anti-tabrakan detik terakhir sebelum merubah status
                $sudahTerkunci = Reservation::where('room_id', $reservation->room_id)
                    ->whereIn('status_izin', ['Disetujui Dosen', 'Disetujui'])
                    ->where('waktu_mulai', '<', $reservation->waktu_selesai)
                    ->where('waktu_selesai', '>', $reservation->waktu_mulai)
                    ->exists();

                if ($sudahTerkunci) {
                    return redirect()->route('reservation.approval')->with('info', 'Gagal memproses! Slot jam pada ruangan ini sudah terlanjur dikunci oleh proposal mhs lain.');
                }

                $reservation->update(['status_izin' => 'Disetujui Dosen', 'alasan_ditolak' => null]);

                // 🔥 CASE 1: Optimasi Loop HTTP Request Fonnte dengan Timeout 3 Detik agar Browser Dosen Gak Hang/Blank
                $sainganBentroks = Reservation::where('room_id', $reservation->room_id)
                    ->where('id', '!=', $id)->where('status_izin', 'Pending')
                    ->where('waktu_mulai', '<', $reservation->waktu_selesai)
                    ->where('waktu_selesai', '>', $reservation->waktu_mulai)->get();

                foreach ($sainganBentroks as $saingan) {
                    $saingan->update(['status_izin' => 'Ditolak (Bentrok)', 'alasan_ditolak' => 'Slot waktu telah dikunci oleh kompetitor berkas lain yang disetujui Dosen Pembina terlebih dahulu.']);

                    try {
                        // Diberi timeout ketat agar loop berjalan instant tanpa nunggu delay server luar
                        Http::timeout(3)->withHeaders(['Authorization' => $token])->post('https://api.fonnte.com/send', [
                            'target' => $saingan->nomor_whatsapp,
                            'message' => "❌ *NOTIFIKASI GUGUR SIPERU PGT*\n\nMohon maaf, pengajuan agenda \"{$saingan->judul_pengajuan}\" dinyatakan *GUGUR* karena kalah cepat dapet rekomendasi Dosen Pembina di slot jam yang sama.",
                            'country' => '62'
                        ]);
                    } catch (\Exception $e) {}
                }

                // Notifikasi ke Otoritas Ruangan
                $adminApproval = User::find($reservation->approval_admin_id);
                if ($adminApproval && $adminApproval->no_hp) {
                    try {
                        Http::timeout(3)->withHeaders(['Authorization' => $token])->post('https://api.fonnte.com/send', [
                            'target' => $adminApproval->no_hp,
                            'message' => "🚨 *NOTIFIKASI OTORITAS RUANGAN*\nProposal *\"{$reservation->judul_pengajuan}\"* butuh wewenang persetujuan akhir Anda.",
                            'country' => '62'
                        ]);
                    } catch (\Exception $e) {}
                }

                return redirect()->route('reservation.approval')->with('success', 'Rekomendasi disimpan! Saingan otomatis digugurkan.');
            }

            // TAHAP 2: Ketukan Palu Approval Admin Otoritas Ruangan (Disetujui Dosen -> Disetujui Final)
            elseif ($reservation->status_izin === 'Disetujui Dosen') {
                $reservation->update(['status_izin' => 'Disetujui']);

                try {
                    Http::timeout(3)->withHeaders(['Authorization' => $token])->post('https://api.fonnte.com/send', [
                        'target' => $reservation->nomor_whatsapp,
                        'message' => "🎉 *IZIN FINAL DISOPOSISI*\nSelamat agenda *\"{$reservation->judul_pengajuan}\"* resmi mendapatkan izin penggunaan ruangan fisik.",
                        'country' => '62'
                    ]);
                } catch (\Exception $e) {}

                return redirect()->route('reservation.approval')->with('success', 'Izin final diberikan! Ruangan dikunci.');
            }

            return redirect()->route('reservation.approval');
        });
    }

    // Proses Tolak dengan Alasan Kustom dari Input Form Dosen
    public function reject(Request $request, $id)
    {
        // Validasi wajib isi alasan reject
        $request->validate([
            'alasan_ditolak' => 'required|string|max:255'
        ], [
            'alasan_ditolak.required' => 'Anda wajib memberikan alasan penolakan berkas!'
        ]);

        $reservation = Reservation::findOrFail($id);

        $reservation->update([
            'status_izin' => 'Ditolak',
            'alasan_ditolak' => $request->alasan_ditolak // Simpan ke MySQL
        ]);

        // Kirim WA pembatalan + sertakan alasan kustom dosen ke HP mahasiswa
        try {
            $token = env('FONNTE_TOKEN');
            $pesanReject = "❌ *NOTIFIKASI PENOLAKAN SIPERU PGT*\n\nHalo *{$reservation->user->nama}*,\n\nPengajuan peminjaman ruangan Anda untuk agenda *\"{$reservation->judul_pengajuan}\"* dinyatakan *DITOLAK* oleh Pihak Kampus.\n\n📌 *Alasan Penolakan:* _\"{$request->alasan_ditolak}\"_\n\nSilakan lengkapi berkas Anda atau pilih alternatif jadwal lain. Terima kasih.";

            Http::timeout(3)->withHeaders(['Authorization' => $token])->post('https://api.fonnte.com/send', [
                'target' => $reservation->nomor_whatsapp,
                'message' => $pesanReject,
                'country' => '62'
            ]);
        } catch (\Exception $e) {}

        return redirect()->route('reservation.approval')->with('info', 'Pengajuan berhasil ditolak beserta alasan resmi.');
    }

    // 🔥 CASE 3: Fitur Pembatalan Mandiri Dosen Pembina (Menarik kembali Berkas)
    // 🔥 REFACTORING UTAMA: Tarik Rekomendasi + Bangkitkan Otomatis Saingan yang Sempat Gugur (Resureksi)
    public function cancelRecommendation(Request $request, $id)
    {
        $request->validate([
            'alasan_ditarik' => 'required|string|max:255'
        ], [
            'alasan_ditarik.required' => 'Anda wajib memberikan alasan penarikan rekomendasi berkas!'
        ]);

        $reservation = Reservation::with(['user', 'room'])->findOrFail($id);

        if ($reservation->status_izin !== 'Disetujui Dosen') {
            return redirect()->route('reservation.approval')->with('info', 'Gagal! Berkas sudah diproses final oleh Otoritas Ruangan.');
        }

        // 📝 Ambil data krusial sebelum status di-mutate untuk melacak saingan yang bentrok kemarin
        $waktuMulai = $reservation->waktu_mulai;
        $waktuSelesai = $reservation->waktu_selesai;
        $roomId = $reservation->room_id;
        $token = env('FONNTE_TOKEN');

        // 1. Kembalikan berkas utama ke Pending Pembina
        $reservation->update([
            'status_izin' => 'Pending',
            'alasan_ditolak' => $request->alasan_ditarik
        ]);

        // Kirim WA Notifikasi ke Mahasiswa Utama yang dibatalkan
        try {
            $pesanCancel = "⚠️ *PENGUMUMAN: REKOMENDASI DIATUR ULANG*\n\nHalo *{$reservation->user->nama}*,\n\nPengajuan peminjaman ruangan Anda untuk agenda *\"{$reservation->judul_pengajuan}\"* saat ini *DITARIK KEMBALI* oleh Dosen Pembina untuk penyesuaian ulang.\n\n📌 *Catatan/Alasan Dosen:* _\"{$request->alasan_ditarik}\"_\n\nStatus berkas Anda kembali menjadi *Pending Pembina*. Silakan periksa sistem SIPERU PGT.";

            Http::timeout(3)->withHeaders(['Authorization' => $token])->post('https://api.fonnte.com/send', [
                'target' => $reservation->nomor_whatsapp,
                'message' => $pesanCancel,
                'country' => '62'
            ]);
        } catch (\Exception $e) {}

        // 🔥 2. LOGIKA RESUREKSI OTOMATIS: Cari semua saingan berstatus 'Ditolak (Bentrok)' di jam & ruang yang sama
        $sainganGugurs = Reservation::where('room_id', $roomId)
            ->where('status_izin', 'Ditolak (Bentrok)')
            ->where('waktu_mulai', '<', $waktuSelesai)
            ->where('waktu_selesai', '>', $waktuMulai)
            ->with('user')
            ->get();

        foreach ($sainganGugurs as $saingan) {
            // Bangkitkan kembali statusnya menjadi Pending agar muncul lagi di antrean dashboard dosen pembina mereka
            $saingan->update([
                'status_izin' => 'Pending',
                'alasan_ditolak' => null // Hapus catatan gugur lama
            ]);

            // Kirim WA Kabar Gembira ke Mahasiswa saingan: "Lu masuk ring lagi pak!"
            $pesanResusitasi = "🔄 *NOTIFIKASI SIPERU PGT: BERKAS DIAKTIFKAN KEMBALI*\n\nHalo *{$saingan->user->nama}*,\n\nAda secercah harapan! Pengajuan ruangan Anda untuk agenda *\"{$saingan->judul_pengajuan}\"* di *{$reservation->room->nama_ruangan}* yang sebelumnya gugur akibat kalah cepat, saat ini telah *DIAKTIFKAN KEMBALI* menjadi *Pending Pembina*.\n\nSebab: Rekomendasi untuk kompetitor utama Anda baru saja ditarik kembali oleh Dosen Pembina, sehingga slot waktu terbuka kembali untuk diperebutkan.\n\nBerkas Anda kini masuk kembali ke antrean peninjauan dosen. Silakan pantau sistem secara berkala.";

            try {
                Http::timeout(3)->withHeaders(['Authorization' => $token])->post('https://api.fonnte.com/send', [
                    'target' => $saingan->nomor_whatsapp,
                    'message' => $pesanResusitasi,
                    'country' => '62'
                ]);
            } catch (\Exception $e) {}
        }

        return redirect()->route('reservation.approval')->with('success', 'Rekomendasi ditarik! Seluruh mahasiswa saingan yang sempat bentrok otomatis dibangkitkan kembali ke antrean Pending.');
    }

    // ==================== REPORT & DOMPDF MANAGEMENT ====================

    // 7. Menampilkan Halaman Filter Laporan
    public function reportIndex()
    {
        $rooms = Room::all();
        return view('dosen.report', compact('rooms'));
    }

    // 8. Mengonversi HTML ke PDF Menggunakan DomPDF
    public function exportPDF(Request $request)
    {
        $query = Reservation::with(['room', 'user'])->where('status_izin', 'Disetujui');

        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }
        if ($request->filled('bulan')) {
            $query->whereMonth('waktu_mulai', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('waktu_mulai', $request->tahun);
        }

        $reservations = $query->orderBy('waktu_mulai', 'asc')->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('dosen.report_pdf', compact('reservations', 'request'));

        return $pdf->download('Laporan-Peminjaman-' . date('Y-m-d') . '.pdf');
    }

    // 9. Menampilkan Form Edit Peminjaman (Hanya Jika Masih Pending)
    public function edit($id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->user_id !== Auth::id() || $reservation->status_izin !== 'Pending') {
            abort(403, 'Akses ditolak! Anda tidak diperbolehkan mengubah pengajuan ini.');
        }

        $rooms = Room::where('status', 'Tersedia')->orderBy('nama_ruangan', 'asc')->get();
        $lecturers = User::where('role', 'dosen')->orderBy('nama', 'asc')->get();
        $approvalAdmins = User::where('role', 'dosen')->where('is_approval_admin', true)->orderBy('nama', 'asc')->get();

        return view('reservation.edit', compact('reservation', 'rooms', 'lecturers', 'approvalAdmins'));
    }

    // 10. Memproses Pembaruan Data (Dengan Validasi Ulang Anti-Tabrakan Kompetisi)
    // Memproses Pembaruan Data (Dengan Validasi Ulang Senin - Jumat)
    public function update(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->user_id !== Auth::id() || $reservation->status_izin !== 'Pending') {
            abort(403);
        }

        $request->validate([
            'judul_pengajuan' => 'required|string|max:255',
            'room_id' => 'required|exists:rooms,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'lecturer_id' => 'required|exists:users,id',
            'approval_admin_id' => 'required|exists:users,id',
            'nomor_whatsapp' => ['required', 'regex:/^628[0-9]{8,12}$/'],
        ], [
            'nomor_whatsapp.regex' => 'Format nomor WhatsApp tidak valid! Wajib diawali dengan kode negara 628 tanpa tanda + atau spasi (contoh: 6288213503918).',
        ]);

        // 🔥 SINKRONISASI PROTEKSI: Kunci hari kerja di menu update (Blokir Sabtu & Minggu)
        $dayOfWeek = date('N', strtotime($request->tanggal));
        if ($request->waktu_mulai < '07:00' || $request->waktu_selesai > '22:00' || $dayOfWeek >= 6) {
            return back()->withErrors([
                'jam_operasi' => 'Izin ditolak! Peminjaman fasilitas kampus hanya diperbolehkan pada hari Senin s/d Jumat pukul 07:00 s/d 22:00 WIB.'
            ])->withInput();
        }

        $waktuMulaiFull = $request->tanggal . ' ' . $request->waktu_mulai . ':00';
        $waktuSelesaiFull = $request->tanggal . ' ' . $request->waktu_selesai . ':00';

        $jadwalBentrok = Reservation::where('room_id', $request->room_id)
            ->where('id', '!=', $id)
            ->whereIn('status_izin', ['Disetujui Dosen', 'Disetujui'])
            ->where(function ($query) use ($waktuMulaiFull, $waktuSelesaiFull) {
                $query->where('waktu_mulai', '<', $waktuSelesaiFull)
                    ->where('waktu_selesai', '>', $waktuMulaiFull);
            })->exists();

        if ($jadwalBentrok) {
            return back()->withErrors(['bentrok' => 'Gagal memperbarui! Ruangan pada jam tersebut sudah resmi dikunci oleh pengajuan mahasiswa lain.'])->withInput();
        }

        $filePath = $reservation->dokumen_proposal;
        if ($request->hasFile('dokumen_proposal')) {
            $request->validate(['dokumen_proposal' => 'file|mimes:pdf|max:1024']);
            $file = $request->file('dokumen_proposal');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('proposals'), $fileName);
            $filePath = 'proposals/' . $fileName;
        }

        $reservation->update([
            'judul_pengajuan' => $request->judul_pengajuan,
            'room_id' => $request->room_id,
            'waktu_mulai' => $waktuMulaiFull,
            'waktu_selesai' => $waktuSelesaiFull,
            'dokumen_proposal' => $filePath,
            'lecturer_id' => $request->lecturer_id,
            'approval_admin_id' => $request->approval_admin_id,
            'nomor_whatsapp' => $request->nomor_whatsapp,
        ]);

        return redirect()->route('reservation.history')->with('success', 'Data pengajuan peminjaman berhasil diperbarui!');
    }

    // 11. Menghapus / Membatalkan Pengajuan
    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        if ($reservation->status_izin === 'Disetujui') {
            return redirect()->back()->with('error', '🚨 Gagal! Pengajuan yang sudah dalam status DISETUJUI terkunci mati.');
        }

        $reservation->delete();
        return redirect()->route('reservation.history')->with('success', 'Pengajuan peminjaman berhasil dibatalkan/dihapus.');
    }
}
