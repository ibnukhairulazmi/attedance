<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Middleware\AuthStudent;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Throttle login terpisah dari RateLimiter manual di controller sebagai
    // lapisan pertahanan kedua terhadap flood request (login sudah dibatasi
    // per akun+IP di controller, ini membatasi total request per IP).
    Route::prefix('auth')->name('api.auth.')->middleware('throttle:20,1')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->name('login');
    });

    Route::middleware(AuthStudent::class)->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me'])->name('api.auth.me');
        // Sengaja tidak ada endpoint logout mandiri untuk mahasiswa:
        // jika mahasiswa bisa logout sendiri, HP yang sama bisa dipakai
        // login akun lain untuk titip-absen. Reset akun/device hanya lewat
        // administrator.

        Route::prefix('attendance')->name('api.attendance.')->group(function () {
            // Scan dibatasi per-token; wajar satu mahasiswa hanya scan
            // beberapa kali per menit (percobaan ulang saat GPS meleset),
            // bukan puluhan kali. Ini melindungi DB dari beban tidak wajar
            // saat banyak mahasiswa scan bersamaan di awal sesi.
            Route::post('/scan', [AttendanceController::class, 'scan'])
                ->middleware('throttle:10,1')
                ->name('scan');
            Route::get('/today',    [AttendanceController::class, 'today'])
                ->middleware('throttle:30,1')
                ->name('today');
            Route::get('/history',  [AttendanceController::class, 'history'])
                ->middleware('throttle:30,1')
                ->name('history');
        });
    });
});
