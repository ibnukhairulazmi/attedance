<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        Log::info('LOGIN REQUEST', [
            'nim_or_email' => $request->input('nim_or_email'),
            'device_id'    => $request->input('device_id'),
            'ip'           => $request->ip(),
        ]);

        $request->validate([
            'nim_or_email' => 'required|string',
            'password'     => 'required|string',
            'device_id'    => 'required|string|max:255',
        ]);

        $throttleKey = Str::transliterate(
            Str::lower((string) $request->input('nim_or_email')) . '|' . $request->ip()
        );

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return response()->json([
                'success' => false,
                'message' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ], 429);
        }

        $student = Mahasiswa::where('nim', $request->nim_or_email)
            ->orWhere('email', $request->nim_or_email)
            ->first();

        if (! $student || ! Hash::check($request->password, $student->password)) {
            RateLimiter::hit($throttleKey, 60);

            return response()->json([
                'success' => false,
                'message' => 'NIM/Email atau password salah.',
            ], 401);
        }

        RateLimiter::clear($throttleKey);

        if ($student->is_locked) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda dinonaktifkan. Hubungi administrator.',
            ], 403);
        }

        if ($student->device_id && $student->device_id !== $request->device_id) {
            return response()->json([
                'success' => false,
                'message' => 'Akun ini sudah terdaftar di perangkat lain. '
                    . 'Hubungi administrator untuk mereset perangkat.',
            ], 403);
        }

        if (! $student->device_id) {
            $student->update(['device_id' => $request->device_id]);
            $student->refresh();
        }

        $rawToken = $student->generateApiToken();
        $student->load('kelas');

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data'    => [
                'token'   => $rawToken,
                'student' => $this->formatStudent($student),
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $student = $request->user();
        $student->load('kelas');

        return response()->json([
            'success' => true,
            'data'    => $this->formatStudent($student),
        ]);
    }

    private function formatStudent($student): array
    {
        return [
            'id'    => $student->id,
            'nama'  => $student->nama,
            'nim'   => $student->nim,
            'email' => $student->email,
            'kelas' => $student->kelas->nama_kelas ?? '',
            'angkatan' => '',
        ];
    }
}
