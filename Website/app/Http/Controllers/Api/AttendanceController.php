<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AttendanceService;
use App\Services\GpsValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    public function __construct(
        private readonly AttendanceService $attendanceService,
        private readonly GpsValidationService $gpsValidationService,
    ) {}

    public function scan(Request $request): JsonResponse
    {
        $request->validate([
            'qr_token'  => 'required|string',
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy'  => 'required|numeric|min:0',
            'device_id' => 'required|string|max:255',
        ]);

        try {
            $student = $request->user();

            // ✅ Cari session dulu sebelum log
            $session = \App\Models\PresensiSession::where('qr_token', $request->qr_token)
                ->first();

            // Log detail lokasi hanya pada level debug agar tidak menumpuk/menyimpan
            // data lokasi mahasiswa di log production.
            if (config('app.debug')) {
                Log::debug('SCAN DEBUG', [
                    'student_lat' => $request->latitude,
                    'student_lng' => $request->longitude,
                    'accuracy'    => $request->accuracy,
                    'kelas_lat'   => $session?->kelas_latitude,
                    'kelas_lng'   => $session?->kelas_longitude,
                    'radius'      => $session?->radius_meter,
                    'distance'    => $session ? $this->gpsValidationService->calculateDistance(
                        $session->kelas_latitude,
                        $session->kelas_longitude,
                        (float) $request->latitude,
                        (float) $request->longitude,
                    ) : null,
                ]);
            }

            $result = $this->attendanceService->processScan(
                student: $student,
                qrToken: $request->qr_token,
                latitude: (float) $request->latitude,
                longitude: (float) $request->longitude,
                deviceId: $request->device_id,
                ipAddress: $request->ip(),
                accuracy: (float) $request->accuracy,
            );

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'data'    => $result['data'],
            ], $result['success'] ? 200 : 422);
        } catch (\Exception $e) {
            Log::error('SCAN ERROR', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return response()->json(array_merge([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses presensi. Silakan coba lagi.',
            ], config('app.debug') ? [
                'debug_message' => $e->getMessage(),
                'file'          => $e->getFile(),
                'line'          => $e->getLine(),
            ] : []), 500);
        }
    }

    public function today(Request $request): JsonResponse
    {
        try {
            $student = $request->user();

            $todayDate = \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d');
            $attendances = $student->presensi()
                ->with(['sesi.jadwal.matakuliah'])
                ->whereHas('sesi', function ($q) use ($todayDate) {
                    $q->whereDate('tanggal', $todayDate);
                })
                ->get();

            $data = $attendances->map(fn($a) => [
                'id'       => $a->id,
                'subject'  => $a->sesi?->jadwal?->matakuliah?->nama_mk ?? '-',
                'date'     => $a->sesi?->tanggal ? $a->sesi->tanggal->isoFormat('D MMM YYYY') : '-',
                'status'   => $a->status,
                'time'     => $a->scan_time ? \Carbon\Carbon::parse($a->scan_time)->format('H:i') : '-',
                'distance' => $a->jarak_meter,
            ]);

            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);
        } catch (\Throwable $e) {
            Log::error('TODAY ATTENDANCE ERROR', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return response()->json(array_merge([
                'success' => false,
                'message' => 'Gagal mengambil data presensi hari ini.',
            ], config('app.debug') ? [
                'error' => $e->getMessage(),
                'file'  => basename($e->getFile()),
                'line'  => $e->getLine(),
            ] : []), 500);
        }
    }

    public function history(Request $request): JsonResponse
    {
        try {
            $student = $request->user();

            $paginated = $student->presensi()
                ->with('sesi.jadwal.matakuliah')
                ->orderByDesc('created_at')
                ->paginate(20);

            $data = $paginated->getCollection()->map(fn($a) => [
                'id'       => $a->id,
                'subject'  => $a->sesi?->jadwal?->matakuliah?->nama_mk ?? '-',
                'date'     => $a->sesi?->tanggal ? $a->sesi->tanggal->isoFormat('D MMM YYYY') : '-',
                'status'   => $a->status,
                'time'     => $a->scan_time ? $a->scan_time->format('H:i') : '-',
                'distance' => $a->jarak_meter,
            ]);

            return response()->json([
                'success' => true,
                'data'    => $data,
                'meta'    => [
                    'current_page' => $paginated->currentPage(),
                    'last_page'    => $paginated->lastPage(),
                    'total'        => $paginated->total(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('HISTORY ATTENDANCE ERROR', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return response()->json(array_merge([
                'success' => false,
                'message' => 'Gagal mengambil riwayat presensi.',
            ], config('app.debug') ? [
                'debug_message' => $e->getMessage(),
                'file'          => $e->getFile(),
                'line'          => $e->getLine(),
            ] : []), 500);
        }
    }
}
