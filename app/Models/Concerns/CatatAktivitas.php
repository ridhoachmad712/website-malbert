<?php

namespace App\Models\Concerns;

use App\Models\CatatanAktivitas;

/** Mencatat siapa yang menambah, mengubah, atau menghapus konten, untuk ditelusuri bila ada masalah. */
trait CatatAktivitas
{
    protected static function bootCatatAktivitas(): void
    {
        static::created(fn ($model) => CatatanAktivitas::catat('menambah', $model));
        static::updated(fn ($model) => CatatanAktivitas::catat($model->aksiUbah(), $model));
        static::deleted(fn ($model) => CatatanAktivitas::catat('menghapus', $model));
    }

    /** Berita yang berpindah status dicatat sebagai menerbitkan atau menurunkan, bukan sekadar mengubah. */
    protected function aksiUbah(): string
    {
        if ($this->wasChanged('status')) {
            return $this->status === 'terbit' ? 'menerbitkan' : 'menurunkan';
        }

        return 'mengubah';
    }
}
