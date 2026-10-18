<?php

namespace App\Support;

class Teks
{
    /** Huruf awal nama sebagai pengganti foto, misalnya "SR" untuk "Siti Rahmah, S.Pd.". */
    public static function inisial(?string $nama): string
    {
        $nama = preg_replace('/[^\p{L}\s]/u', '', explode(',', (string) $nama)[0]);

        return mb_strtoupper(collect(preg_split('/\s+/', trim($nama)))->filter()->take(2)->map(fn ($kata) => mb_substr($kata, 0, 1))->implode(''));
    }
}
