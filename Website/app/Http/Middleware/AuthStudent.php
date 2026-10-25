<?php

namespace App\Http\Middleware;

use App\Models\Mahasiswa;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthStudent
{
    public function handle(Request $request, Closure $next): Response
    {
        $bearerToken = $request->bearerToken();

        if (! $bearerToken) {
            return response()->json([
                'success' => false,
                'message' => 'Token autentikasi tidak ditemukan. Silakan login ulang.',
            ], 401);
        }

        $student = Mahasiswa::where('api_token', hash('sha256', $bearerToken))->first();

        if (! $student) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid atau sudah kedaluwarsa. Silakan login ulang.',
            ], 401);
        }

        if ($student->api_token_expires_at && $student->api_token_expires_at->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi Anda telah berakhir. Silakan login ulang.',
            ], 401);
        }

        if ($student->is_locked) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda dinonaktifkan. Hubungi administrator.',
            ], 403);
        }

        $request->setUserResolver(fn() => $student);

        return $next($request);
    }
}
