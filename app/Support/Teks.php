<?php

namespace App\Support;

use Illuminate\Support\Str;

class Teks
{
    /**
     * Isi dari editor admin yang siap ditampilkan: dibersihkan dari skrip, lalu paragraf pendek yang seluruhnya tebal
     * (misalnya "<p><strong>Visi</strong></p>") dijadikan judul kecil. Admin sering menebalkan teks alih-alih memakai
     * tombol judul, dan tanpa ini judulnya terlihat sama dengan isi.
     */
    public static function isiRapi(?string $html): string
    {
        $bersih = Str::sanitizeHtml((string) $html);

        return preg_replace('~<p>\s*<(strong|b)>([^<]{1,80})</\1>\s*(?:<br\s*/?>)?\s*</p>~u', '<h2>$2</h2>', $bersih);
    }

    /** Huruf awal nama sebagai pengganti foto, misalnya "SR" untuk "Siti Rahmah, S.Pd.". */
    public static function inisial(?string $nama): string
    {
        $nama = preg_replace('/[^\p{L}\s]/u', '', explode(',', (string) $nama)[0]);

        return mb_strtoupper(collect(preg_split('/\s+/', trim($nama)))->filter()->take(2)->map(fn ($kata) => mb_substr($kata, 0, 1))->implode(''));
    }
}
