<?php

namespace App\Services;

use App\Models\Presensi;
use App\Models\PresensiSession;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    public function __construct(
        private readonly QrCodeService        $qrService,
        private readonly GpsValidationService $gpsService
    ) {}

    public function processScan(
        $student,
        string    $qrToken,
        float     $latitude,
        float     $longitude,
        string    $deviceId,
        string    $ipAddress,
        ?float    $accuracy = null,
    ): array {

        if ($student->device_id && $student->device_id !== $deviceId) {
            return $this->fail('Akun ini terdaftar di perangkat lain. Hubungi administrator.');
        }

        if (! $this->gpsService->isValidCoordinate($latitude, $longitude)) {
            return $this->fail('Koordinat GPS tidak valid.');
        }

        // Accuracy GPS hanya menjadi soft warning, BUKAN pemblokir presensi.
        // Yang menentukan fraud atau bukan adalah distance (radius), bukan accuracy sinyal.
        $minAccuracy     = (int) config('attendance.min_gps_accuracy', 150);
        $accuracyWarning = null;
        if ($accuracy !== null && $accuracy > $minAccuracy) {
            $accuracyWarning = "Sinyal GPS lemah ({$accuracy}m), hasil presensi mungkin kurang presisi.";
        }

        $session = PresensiSession::where('qr_token', $qrToken)
            ->with('jadwal.kelas')
            ->first();

        if (! $session) {
            return $this->fail('QR Code tidak dikenali. Minta dosen menampilkan ulang.');
        }

        if ($session->isExpired()) {
            return $this->fail('QR Code sudah kadaluwarsa atau sesi sudah ditutup.');
        }

        if (! $this->qrService->verifyToken($qrToken)) {
            return $this->fail('QR Code tidak valid atau telah dimanipulasi.');
        }

        if ($student->kelas_id !== $session->jadwal->kelas_id) {
            return $this->fail('Anda bukan bagian dari kelas ini.');
        }

        $existing = Presensi::where('sesi_presensi_id', $session->id)
            ->where('mahasiswa_id', $student->id)
            ->first();

        if ($existing && $existing->status === 'hadir') {
            return $this->fail('Anda sudah melakukan presensi untuk sesi ini.');
        }

        $gps = $this->gpsService->isWithinRadius(
            schoolLat: $session->kelas_latitude,
            schoolLng: $session->kelas_longitude,
            studentLat: $latitude,
            studentLng: $longitude,
            maxDistanceMeters: $session->radius_meter,
        );

        if (! $gps['valid']) {
            return $this->fail(
                "Anda berada {$gps['distance']} meter dari kelas. "
                    . "Batas maksimal {$session->radius_meter} meter."
            );
        }

        $attendance = DB::transaction(function () use (
            $session,
            $student,
            $latitude,
            $longitude,
            $gps,
            $accuracy,
            $ipAddress,
            $deviceId,
            $existing
        ) {
            if (! $student->device_id) {
                $student->update(['device_id' => $deviceId]);
            }

            $data = [
                'status'         => 'hadir',
                'scan_latitude'  => $latitude,
                'scan_longitude' => $longitude,
                'jarak_meter'    => $gps['distance'],
                'gps_accuracy'   => $accuracy,   // simpan untuk keperluan audit
                'scan_time'      => now(),
                'ip_address'     => $ipAddress,
            ];

            if ($existing) {
                $existing->update($data);
                return $existing->fresh();
            }

            return Presensi::create(array_merge($data, [
                'sesi_presensi_id' => $session->id,
                'mahasiswa_id'     => $student->id,
            ]));
        });

        $message = 'Presensi berhasil dicatat!';
        if ($accuracyWarning) {
            $message .= " ⚠️ {$accuracyWarning}";
        }

        return [
            'success' => true,
            'message' => $message,
            'data'    => [
                'student_name'    => $student->nama,
                'subject'         => $session->jadwal?->matakuliah?->nama_mk ?? '-',
                'class'           => $session->jadwal?->kelas?->nama_kelas ?? '-',
                'scan_time'       => $attendance->scan_time ? $attendance->scan_time->format('H:i:s') : now()->format('H:i:s'),
                'distance_meters' => $gps['distance'],
                'gps_accuracy'    => $accuracy,
                'status'          => 'hadir',
            ],
        ];
    }

    public function initializeSessionAttendances(PresensiSession $session): void
    {
        $students = $session->jadwal->kelas->mahasiswa;
        $records = $students->map(fn($s) => [
            'sesi_presensi_id' => $session->id,
            'mahasiswa_id'     => $s->id,
            'status'           => 'alpha',
            'created_at'       => now()->toDateTimeString(),
            'updated_at'       => now()->toDateTimeString(),
        ])->toArray();

        Presensi::upsert(
            $records,
            ['sesi_presensi_id', 'mahasiswa_id'],
            ['status']
        );
    }

    public function monthlyReport(int $classId, int $year, int $month): array
    {
        return Presensi::query()
            ->with('mahasiswa', 'sesi.jadwal')
            ->whereHas('sesi.jadwal', fn($q) => $q->where('kelas_id', $classId))
            ->whereHas(
                'sesi',
                fn($q) =>
                $q->whereYear('tanggal', $year)
                    ->whereMonth('tanggal', $month)
            )
            ->get()
            ->groupBy('mahasiswa_id')
            ->map(function ($records) {
                $counts = $records->countBy('status');
                $hadir  = $counts->get('hadir', 0);
                $total  = $records->count();
                return [
                    'student' => $records->first()->mahasiswa,
                    'hadir'   => $hadir,
                    'izin'    => $counts->get('izin',  0),
                    'sakit'   => $counts->get('sakit', 0),
                    'alpha'   => $counts->get('alpha', 0),
                    'total'   => $total,
                    'persen'  => $total > 0 ? round($hadir / $total * 100, 1) : 0,
                ];
            })
            ->values()
            ->toArray();
    }

    private function fail(string $message): array
    {
        return ['success' => false, 'message' => $message, 'data' => null];
    }
}
