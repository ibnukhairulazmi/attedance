<?php

namespace Database\Seeders;

use App\Models\Jadwal;
use App\Models\PresensiSession;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PresensiSessionSeeder extends Seeder
{
    /**
     * Berapa minggu ke belakang riwayat sesi yang dibuat untuk tiap jadwal.
     * 4 minggu x hari yang sama = 4 sesi historis per baris jadwal.
     */
    private const JUMLAH_MINGGU = 4;

    /**
     * Koordinat kampus (contoh: area Semarang). GANTI dengan koordinat
     * kampus sebenarnya sebelum dipakai di lingkungan produksi — nilai ini
     * hanya untuk kebutuhan data contoh/testing.
     */
    private const KAMPUS_LAT = -6.9739;
    private const KAMPUS_LNG = 110.4203;

    private const HARI_KE_ISO = [
        'Senin'  => 1,
        'Selasa' => 2,
        'Rabu'   => 3,
        'Kamis'  => 4,
        'Jumat'  => 5,
        'Sabtu'  => 6,
        'Minggu' => 7,
    ];

    /**
     * Cari tanggal aktual (masa lalu) untuk kemunculan ke-`$mingguKe` dari
     * suatu nama hari (mis. "Rabu"), dihitung mundur dari hari ini.
     * $mingguKe = 1 -> kemunculan paling baru (hari ini jika kebetulan sama).
     */
    private function tanggalUntukMinggu(string $hari, int $mingguKe): Carbon
    {
        $targetIso = self::HARI_KE_ISO[$hari] ?? 1;
        $hariIni   = Carbon::today();

        $selisihHari     = ($hariIni->dayOfWeekIso - $targetIso + 7) % 7;
        $kemunculanTerbaru = $hariIni->copy()->subDays($selisihHari);

        return $kemunculanTerbaru->subWeeks($mingguKe - 1);
    }

    /**
     * Beri sedikit variasi koordinat kelas per jadwal (mensimulasikan
     * ruangan yang berbeda-beda), supaya tidak semua sesi tepat di titik
     * yang identik. Variasi kecil (~puluhan meter), deterministik
     * berdasarkan id jadwal supaya konsisten tiap kali seeder dijalankan.
     */
    private function koordinatRuangan(int $jadwalId): array
    {
        mt_srand($jadwalId);
        $offsetMeter = mt_rand(-80, 80);
        mt_srand(); // kembalikan seed acak normal untuk pemakaian random lain

        $deltaLat = $offsetMeter / 111_320;
        $deltaLng = $offsetMeter / (111_320 * cos(deg2rad(self::KAMPUS_LAT)));

        return [
            'lat' => self::KAMPUS_LAT + $deltaLat,
            'lng' => self::KAMPUS_LNG + $deltaLng,
        ];
    }

    public function run(): void
    {
        $jadwalSemua = Jadwal::all();
        $totalDibuat = 0;

        foreach ($jadwalSemua as $jadwal) {
            $koordinat = $this->koordinatRuangan($jadwal->id);

            for ($mingguKe = 1; $mingguKe <= self::JUMLAH_MINGGU; $mingguKe++) {
                $tanggal = $this->tanggalUntukMinggu($jadwal->hari, $mingguKe);

                // Jangan buat sesi untuk tanggal yang masih di masa depan
                // (bisa terjadi kalau hari ini persis hari jadwal tapi
                // jam kelasnya belum lewat saat seeder dijalankan).
                if ($tanggal->isFuture()) {
                    continue;
                }

                $qrExpiredAt = Carbon::parse($tanggal->format('Y-m-d') . ' ' . $jadwal->jam_mulai)
                    ->addMinutes((int) config('attendance.qr_refresh_minutes', 15));

                PresensiSession::updateOrCreate(
                    [
                        'jadwal_id' => $jadwal->id,
                        'tanggal'   => $tanggal->format('Y-m-d'),
                    ],
                    [
                        // Token acak unik — sesi ini historis/sudah selesai,
                        // jadi tidak perlu valid secara HMAC seperti QR yang
                        // benar-benar dipakai scan (lihat QrCodeService).
                        'qr_token'        => 'seed_' . Str::random(58),
                        'qr_expired_at'   => $qrExpiredAt,
                        'kelas_latitude'  => $koordinat['lat'],
                        'kelas_longitude' => $koordinat['lng'],
                        'radius_meter'    => 10,
                        // Semua sesi yang di-generate di sini adalah
                        // pertemuan masa lalu -> dianggap sudah ditutup dosen.
                        'status'          => 'selesai',
                    ]
                );

                $totalDibuat++;
            }
        }

        $this->command?->info("PresensiSessionSeeder: {$totalDibuat} sesi presensi berhasil dibuat.");
    }
}
