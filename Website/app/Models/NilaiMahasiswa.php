<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NilaiMahasiswa extends Model
{
    use HasFactory;

    protected $table = 'nilai_mahasiswa';

    protected $fillable = [
        'jadwal_id',
        'mahasiswa_id',
        'nilai_uts',
        'nilai_uas',
    ];

    protected function casts(): array
    {
        return [
            'nilai_uts' => 'float',
            'nilai_uas' => 'float',
        ];
    }

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }
}
