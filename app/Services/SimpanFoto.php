<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

/**
 * Menyimpan foto unggahan setelah dikecilkan agar hemat ruang hosting.
 * Foto dikodekan ulang sebagai JPG, sehingga data EXIF (termasuk lokasi GPS) ikut terbuang.
 */
class SimpanFoto
{
    public const MAKS_KB = 8192;

    /** Sisi terpanjang foto biasa, dalam piksel. */
    public const SISI_BAWAAN = 1600;

    /** Foto hero tampil selebar layar, jadi disimpan lebih besar. */
    public const SISI_HERO = 2400;

    public static function simpan(UploadedFile $file, string $folder, int $sisiTerpanjang = self::SISI_BAWAAN): string
    {
        $path = trim($folder, '/').'/'.now()->format('Y/m').'/'.Str::uuid().'.jpg';

        $gambar = Image::decode($file->getRealPath())->scaleDown($sisiTerpanjang, $sisiTerpanjang);
        Storage::disk('public')->put($path, (string) $gambar->encodeUsingFileExtension('jpg', quality: 80));

        return $path;
    }
}
