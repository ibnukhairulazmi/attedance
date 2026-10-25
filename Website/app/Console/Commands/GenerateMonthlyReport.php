<?php

namespace App\Console\Commands;

use App\Models\Kelas;
use App\Services\AttendanceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateMonthlyReport extends Command
{
    protected $signature   = 'attendance:monthly-report {--year=} {--month=}';
    protected $description = 'Generate rekap presensi bulanan untuk semua kelas.';

    public function __construct(
        private readonly AttendanceService $attendanceService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $year = $this->option('year') !== null
            ? (int) $this->option('year')
            : (now()->month === 1 ? now()->subMonth()->year : now()->year);
        $month = $this->option('month') !== null
            ? (int) $this->option('month')
            : now()->subMonth()->month;

        $classes = Kelas::all();
        $this->info("Generating rekap {$month}/{$year} untuk {$classes->count()} kelas...");

        $bar = $this->output->createProgressBar($classes->count());
        $bar->start();

        foreach ($classes as $class) {
            $report = $this->attendanceService->monthlyReport($class->id, $year, $month);
            $filename = "reports/{$year}-{$month}-kelas-{$class->id}.json";
            Storage::put($filename, json_encode([
                'class'     => $class->nama_kelas,
                'year'      => $year,
                'month'     => $month,
                'generated' => now()->toISOString(),
                'data'      => $report,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Rekap bulanan berhasil dibuat!');

        return self::SUCCESS;
    }
}
