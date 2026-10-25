<?php

namespace App\Services;

use App\Models\Jadwal;
use App\Models\PresensiSession;
use Carbon\Carbon;
use Illuminate\Support\Str;

class QrCodeService
{
    public function generateToken(int $jadwalId, string $tglSesi): string
    {
        $uuid      = Str::uuid()->toString();
        $timestamp = now()->timestamp;
        $secret    = config('attendance.qr_secret_key');
        $payload   = "{$uuid}.{$jadwalId}.{$tglSesi}.{$timestamp}";
        $hmac      = hash_hmac('sha256', $payload, $secret);
        return base64_encode("{$payload}|{$hmac}");
    }

    public function verifyToken(string $rawToken): bool
    {
        try {
            $decoded = base64_decode($rawToken, strict: true);
            if (! $decoded) return false;
            [$payload, $hmac] = explode('|', $decoded, 2);
            $secret   = config('attendance.qr_secret_key');
            $expected = hash_hmac('sha256', $payload, $secret);
            return hash_equals($expected, $hmac);
        } catch (\Throwable) {
            return false;
        }
    }

    public function createSession(Jadwal $jadwal): PresensiSession
    {
        PresensiSession::where('jadwal_id', $jadwal->id)
            ->where('tanggal', today())
            ->where('status', 'aktif')
            ->update(['status' => 'expired']);

        $token = $this->generateToken($jadwal->id, today()->toDateString());

        return PresensiSession::create([
            'jadwal_id'       => $jadwal->id,
            'tanggal'         => today(),
            'qr_token'        => $token,
            'qr_expired_at'   => now()->addMinutes(15),
            'kelas_latitude'  => config('attendance.school_lat'),
            'kelas_longitude' => config('attendance.school_lng'),
            'status'          => 'aktif',
        ]);
    }

    public function generateQrImage(string $token): string
    {
        $deepLink = config('app.url') . '/scan?token=' . urlencode($token);
        return \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size(300)
            ->errorCorrection('H')
            ->generate($deepLink);
    }
}
