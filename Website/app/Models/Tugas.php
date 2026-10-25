<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tugas extends Model
{
    use HasFactory;

    protected $table = 'tugas';

    protected $fillable = [
        'jadwal_id',
        'judul',
        'deskripsi',
        'file_petunjuk',
        'deadline',
        'max_score',
        'tipe_pengumpulan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'deadline'  => 'datetime',
            'is_active' => 'boolean',
            'max_score' => 'integer',
        ];
    }

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }

    public function pengumpulan(): HasMany
    {
        return $this->hasMany(TugasPengumpulan::class, 'tugas_id');
    }

    public function isPastDeadline(): bool
    {
        return now()->isAfter($this->deadline);
    }
}
