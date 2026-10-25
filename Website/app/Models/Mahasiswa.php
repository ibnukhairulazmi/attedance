<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswa';

    protected $fillable = [
        'kelas_id',
        'nama',
        'nim',
        'email',
        'password',
        'device_id',
        'device_token',
        'is_locked',
        'api_token',
        'api_token_expires_at',
    ];

    protected $hidden = ['password', 'api_token'];

    protected function casts(): array
    {
        return [
            'is_locked'             => 'boolean',
            'api_token_expires_at'  => 'datetime',
        ];
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'mahasiswa_id');
    }

    public function tugasPengumpulan(): HasMany
    {
        return $this->hasMany(TugasPengumpulan::class, 'mahasiswa_id');
    }

    public function nilaiMahasiswa(): HasMany
    {
        return $this->hasMany(NilaiMahasiswa::class, 'mahasiswa_id');
    }

    public function generateApiToken(): string
    {
        $token = Str::random(60);
        $this->update([
            'api_token'             => hash('sha256', $token),
            'api_token_expires_at'  => now()->addDays(
                (int) config('attendance.api_token_ttl_days', 30)
            ),
        ]);
        return $token;
    }
}
