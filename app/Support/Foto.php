<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

/**
 * Alamat foto dan versi kecilnya. Setiap foto unggahan punya versi kecil WebP (lebar 640 px)
 * bernama sama dengan akhiran "-kecil.webp", dipakai di kartu dan galeri agar halaman ringan di HP.
 */
class Foto
{
    public const SISI_KECIL = 640;

    public static function url(?string $path): ?string
    {
        return filled($path) ? Storage::disk('public')->url($path) : null;
    }

    public static function pathKecil(string $path): string
    {
        return preg_replace('/\.[a-z0-9]+$/i', '', $path).'-kecil.webp';
    }

    /** Alamat versi kecil bila sudah ada; bila belum (foto lama), alamat foto aslinya. */
    public static function kecil(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        $kecil = self::pathKecil($path);

        return Storage::disk('public')->exists($kecil) ? Storage::disk('public')->url($kecil) : self::url($path);
    }

    /** Membuat versi kecil dari foto yang sudah tersimpan. */
    public static function buatKecil(string $path): void
    {
        $disk = Storage::disk('public');
        $gambar = Image::decode($disk->path($path))->scaleDown(self::SISI_KECIL, self::SISI_KECIL);
        $disk->put(self::pathKecil($path), (string) $gambar->encodeUsingFileExtension('webp', quality: 75));
    }

    /** Menghapus foto beserta versi kecilnya. */
    public static function hapus(string|array|null $path): void
    {
        foreach (array_filter((array) $path) as $item) {
            Storage::disk('public')->delete([$item, self::pathKecil($item)]);
        }
    }
}
