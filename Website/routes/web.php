<?php

use App\Http\Controllers\Web\AssignmentController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\GradebookController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\Web\SessionController;
use App\Http\Middleware\CheckScheduleTime;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

require __DIR__ . '/auth.php';

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::prefix('sessions')->name('sessions.')->group(function () {

        Route::post('/jadwal/{jadwal}/start', [SessionController::class, 'start'])
            ->name('start')
            ->middleware(CheckScheduleTime::class);

        Route::get('/{session}',           [SessionController::class, 'show'])
            ->name('show');
        Route::post('/{session}/refresh',  [SessionController::class, 'refresh'])
            ->name('refresh');
        Route::post('/{session}/close',    [SessionController::class, 'close'])
            ->name('close');
        Route::post('/{session}/override', [SessionController::class, 'overrideAttendance'])
            ->name('override');
        Route::get('/{session}/report',    [SessionController::class, 'report'])->name('report');
        Route::get('/{session}/export/{format}', [SessionController::class, 'exportSession'])
            ->name('export')
            ->where('format', 'excel|pdf');
    });

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/{format}', [ReportController::class, 'export'])
        ->name('reports.export')
        ->where('format', 'excel|pdf');

    // Manajemen Tugas (Assignments)
    Route::prefix('assignments')->name('assignments.')->group(function () {
        Route::get('/', [AssignmentController::class, 'index'])->name('index');
        Route::get('/create', [AssignmentController::class, 'create'])->name('create');
        Route::post('/', [AssignmentController::class, 'store'])->name('store');
        Route::get('/{assignment}', [AssignmentController::class, 'show'])->name('show');
        Route::delete('/{assignment}', [AssignmentController::class, 'destroy'])->name('destroy');
        Route::post('/submissions/{submission}/grade', [AssignmentController::class, 'grade'])->name('grade');
    });

    // Buku Nilai (Gradebook)
    Route::prefix('gradebook')->name('gradebook.')->group(function () {
        Route::get('/', [GradebookController::class, 'index'])->name('index');
        Route::post('/jadwal/{jadwal}/weights', [GradebookController::class, 'updateWeights'])->name('weights');
        Route::post('/jadwal/{jadwal}/scores', [GradebookController::class, 'updateScores'])->name('scores');
    });
});

