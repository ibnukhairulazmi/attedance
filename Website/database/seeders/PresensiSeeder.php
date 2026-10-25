<?php

namespace Database\Seeders;

use App\Models\Presensi;
use App\Models\PresensiSession;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PresensiSeeder extends Seeder
{
    /**
     * Distribusi status kehadiran (dalam persen, total harus 100).
     * Dibuat cukup realistis: mayoritas hadir, sebagian kecil izin/sakit/alpha.
     */
    private const PELUANG_HADIR = 82;
    private const PELUANG_IZIN  = 6;
    private const PELUANG_SAKIT = 5;
    // Sisanya (7%) -> alpha

    private function acakStatus(): string
    {
        $r = mt_rand(1, 100);

        return match (true) {
            $r <= self::PELUANG_HADIR                                    => 'hadir',
            $r <= self::PELUANG_HADIR + self::PELUANG_IZIN               => 'izin',
            $r <= self::PELUANG_HADIR + self::PELUANG_IZIN + self::PELUANG_SAKIT => 'sakit',
            default                                                      => 'alpha',
        };
    }

    /**
     * Hasilkan titik scan acak di sekitar koordinat kelas, dalam radius
     * yang ditentukan sesi (default 10 meter) — mensimulasikan mahasiswa
     * yang benar-benar berada di dalam kelas saat scan.
     */
    private function titikScanAcak(float $lat, float $lng, int $radiusMeter): array
    {
        $jarak   = mt_rand(1, max(1, $radiusMeter - 1));
        $bearing = deg2rad(mt_rand(0, 359));

        $deltaLat = ($jarak * cos($bearing)) / 111_320;
        $deltaLng = ($jarak * sin($bearing)) / (111_320 * cos(deg2rad($lat)));

        return [
            'lat'   => $lat + $deltaLat,
            'lng'   => $lng + $deltaLng,
            'jarak' => $jarak,
        ];
    }

    /** IP publik contoh (format ISP Indonesia), hanya untuk data dummy. */
    private function ipAcak(): string
    {
        return '182.253.' . mt_rand(0, 255) . '.' . mt_rand(1, 254);
    }

    public function run(): void
    {
        $sesiSemua = PresensiSession::with('jadwal.kelas.mahasiswa')->get();
        $totalDibuat = 0;

        foreach ($sesiSemua as $sesi) {
            $mahasiswaSekelas = $sesi->jadwal->kelas->mahasiswa;

            foreach ($mahasiswaSekelas as $mhs) {
                $status = $this->acakStatus();

                $data = [
                    'status'         => $status,
                    'scan_latitude'  => null,
                    'scan_longitude' => null,
                    'jarak_meter'    => null,
                    'scan_time'      => null,
                    'ip_address'     => null,
                    'device_id'      => 'seed-device-' . $mhs->id,
                ];

                if ($status === 'hadir') {
                    $titik = $this->titikScanAcak(
                        (float) $sesi->kelas_latitude,
                        (float) $sesi->kelas_longitude,
                        (int) $sesi->radius_meter
                    );

                    $scanTime = Carbon::parse(
                        $sesi->tanggal->format('Y-m-d') . ' ' . $sesi->jadwal->jam_mulai
                    )->addMinutes(mt_rand(0, 12));

                    $data = array_merge($data, [
                        'scan_latitude'  => $titik['lat'],
                        'scan_longitude' => $titik['lng'],
                        'jarak_meter'    => $titik['jarak'],
                        'scan_time'      => $scanTime,
                        'ip_address'     => $this->ipAcak(),
                    ]);
                }

                Presensi::updateOrCreate(
                    [
                        'sesi_presensi_id' => $sesi->id,
                        'mahasiswa_id'     => $mhs->id,
                    ],
                    $data
                );

                $totalDibuat++;
            }
        }

        $this->command?->info("PresensiSeeder: {$totalDibuat} data presensi berhasil dibuat.");
    }
}
