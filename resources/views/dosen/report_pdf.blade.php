<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Peminjaman Ruangan SIPERU PGT</title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; color: #333; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #0A3981; padding-bottom: 10px; }
        .header h2 { margin: 0; color: #0A3981; text-transform: uppercase; font-size: 18px; }
        .header p { margin: 4px 0 0 0; color: #555; font-size: 11px; }
        table { wwidth: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { bg-color: #F3C31B; color: #0A3981; font-weight: bold; font-size: 11px; text-transform: uppercase; }
        .text-left { text-align: left; }
        .footer { margin-top: 40px; text-align: right; font-size: 11px; color: #666; }
    </style>
</head>
<body>

    <div class="header">
        <h2>Sistem Peminjaman Ruangan (SIPERU)</h2>
        <h2>Politeknik Gajah Tunggal</h2>
        <p>Dokumen Laporan Resmi Rekapitulasi Penggunaan Ruangan Kuliah</p>
    </div>

    <p style="margin-bottom: 15px;"><strong>Tanggal Cetak:</strong> {{ date('d-m-Y H:i') }} WIB</p>

    <table wwidth="100%">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th class="text-left" style="width: 35%;">Nama Ruangan</th>
                <th style="width: 30%;">Nama Peminjam</th>
                <th style="width: 30%;">Waktu Penggunaan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reservations as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left" style="font-weight: bold; color: #0A3981;">{{ $item->room->nama_ruangan }}</td>
                    <td>{{ $item->user->nama }}<br><small style="color:#777;">({{ $item->user->nim }})</small></td>
                    <td>
                        {{ date('d-m-Y', strtotime($item->waktu_mulai)) }}<br>
                        <small style="font-weight: bold;">{{ date('H:i', strtotime($item->waktu_mulai)) }} - {{ date('H:i', strtotime($item->waktu_selesai)) }} WIB</small>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="padding: 20px; color: #999;">Tidak ada data peminjaman yang disetujui pada parameter filter tersebut.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak secara otomatis oleh Sistem SIPERU PGT</p>
    </div>

</body>
</html>
