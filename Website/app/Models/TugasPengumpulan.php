<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TugasPengumpulan extends Model
{
    use HasFactory;

    protected $table = 'tugas_pengumpulan';

    protected $fillable = [
        'tugas_id',
        'mahasiswa_id',
        'file_path',
        'link_url',
        'catatan_mahasiswa',
        'waktu_kumpul',
        'status',
        'nilai',
        'catatan_dosen',
    ];

    protected function casts(): array
    {
        return [
            'waktu_kumpul' => 'datetime',
            'nilai'        => 'float',
        ];
    }

    public function tugas(): BelongsTo
    {
        return $this->belongsTo(Tugas::class, 'tugas_id');
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }
}
