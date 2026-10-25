<?php

namespace App\Console\Commands;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

/**
 * Command sekali-jalan untuk migrasi password lama (plain-text) menjadi
 * bcrypt hash, menyusul perbaikan bug perbandingan password di
 * AuthController & LoginRequest (sebelumnya memakai perbandingan string
 * langsung, bukan Hash::check()).
 *
 * Aman dijalankan berkali-kali: baris yang passwordnya sudah berupa
 * hash bcrypt akan dilewati (tidak di-hash dua kali).
 *
 * Jalankan sekali setelah deploy perbaikan:
 *   php artisan attendance:rehash-passwords
 */
class RehashLegacyPasswords extends Command
{
    protected $signature   = 'attendance:rehash-passwords';
    protected $description = 'Hash ulang password mahasiswa & dosen yang masih tersimpan plain-text.';

    public function handle(): int
    {
        $studentCount = 0;
        foreach (Mahasiswa::all() as $student) {
            if (! $this->isBcryptHash($student->password)) {
                $student->update(['password' => Hash::make($student->password)]);
                $studentCount++;
            }
        }

        $dosenCount = 0;
        foreach (Dosen::all() as $dosen) {
            if (! $this->isBcryptHash($dosen->password)) {
                $dosen->update(['password' => Hash::make($dosen->password)]);
                $dosenCount++;
            }
        }

        $this->info("Selesai. {$studentCount} password mahasiswa dan {$dosenCount} password dosen berhasil di-hash ulang.");

        return self::SUCCESS;
    }

    private function isBcryptHash(?string $value): bool
    {
        return $value !== null && str_starts_with($value, '$2y$');
    }
}
