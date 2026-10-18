<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Membuat alamat (slug) otomatis dari judul/nama saat data dibuat, dan memastikan tidak ada yang kembar.
 * Slug tidak ikut berubah saat judul diubah agar tautan yang sudah dibagikan tetap berfungsi.
 */
trait PunyaSlug
{
    protected static function bootPunyaSlug(): void
    {
        static::saving(function ($model) {
            if (blank($model->slug)) {
                $model->slug = static::slugUnik($model->{static::$sumberSlug ?? 'judul'}, $model->getKey());
            }
        });
    }

    public static function slugUnik(?string $teks, mixed $kecuali = null): string
    {
        $dasar = Str::limit(Str::slug((string) $teks) ?: 'tanpa-judul', 150, '');
        $slug = $dasar;
        $nomor = 2;

        while (static::where('slug', $slug)->when($kecuali, fn ($query) => $query->whereKeyNot($kecuali))->exists()) {
            $slug = $dasar.'-'.$nomor++;
        }

        return $slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
