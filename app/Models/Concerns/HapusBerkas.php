<?php

namespace App\Models\Concerns;

use App\Support\Foto;
use Illuminate\Support\Facades\Storage;

/**
 * Menghapus berkas unggahan dari penyimpanan saat datanya dihapus atau berkasnya diganti,
 * agar ruang hosting tidak penuh oleh foto yang sudah tidak dipakai.
 * Model mendaftarkan kolomnya di properti $kolomBerkas.
 */
trait HapusBerkas
{
    protected static function bootHapusBerkas(): void
    {
        static::updated(function ($model) {
            foreach ($model->kolomBerkas as $kolom) {
                $lama = $model->getOriginal($kolom);
                if ($model->wasChanged($kolom) && filled($lama)) {
                    Foto::hapus($lama);
                }
            }
        });

        static::deleted(function ($model) {
            foreach ($model->kolomBerkas as $kolom) {
                if (filled($model->{$kolom})) {
                    Foto::hapus($model->{$kolom});
                }
            }
        });
    }

    public function urlBerkas(string $kolom): ?string
    {
        return filled($this->{$kolom}) ? Storage::disk('public')->url($this->{$kolom}) : null;
    }

    /** Versi kecil foto untuk kartu dan galeri (lihat App\Support\Foto). */
    public function urlKecil(string $kolom): ?string
    {
        return Foto::kecil($this->{$kolom});
    }
}
