<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatatanAktivitas extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'catatan_aktivitas';

    protected $fillable = ['user_id', 'aksi', 'jenis', 'subjek_id', 'judul'];

    /** Nama jenis konten yang mudah dibaca admin. */
    public const JENIS = [
        Berita::class => 'Berita',
        Halaman::class => 'Halaman',
        Agenda::class => 'Agenda',
        Album::class => 'Album galeri',
        Prestasi::class => 'Prestasi',
        Guru::class => 'Guru dan tendik',
        Unduhan::class => 'Unduhan',
        DokumenAnggaran::class => 'Dokumen anggaran',
        Ekstrakurikuler::class => 'Ekstrakurikuler',
        Sarana::class => 'Sarana',
    ];

    public static function catat(string $aksi, Model $model): void
    {
        static::create([
            'user_id' => auth()->id(),
            'aksi' => $aksi,
            'jenis' => self::JENIS[$model::class] ?? class_basename($model),
            'subjek_id' => $model->getKey(),
            'judul' => mb_substr((string) ($model->judul ?? $model->nama ?? ''), 0, 220),
        ]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
