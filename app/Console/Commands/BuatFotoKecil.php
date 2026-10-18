<?php

namespace App\Console\Commands;

use App\Support\Foto;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Throwable;

/** Membuat versi kecil (WebP) untuk foto lama yang diunggah sebelum fitur ini ada. Aman dijalankan berulang. */
#[Signature('foto:buat-kecil {--ulang : Buat ulang walaupun versi kecil sudah ada}')]
#[Description('Membuat versi kecil WebP untuk semua foto unggahan')]
class BuatFotoKecil extends Command
{
    public function handle(): int
    {
        $disk = Storage::disk('public');
        $dibuat = 0;

        foreach ($disk->allFiles() as $path) {
            if (! preg_match('/\.(jpe?g|png)$/i', $path) || str_starts_with($path, 'unduhan/') || str_starts_with($path, 'anggaran/')) {
                continue;
            }
            if (! $this->option('ulang') && $disk->exists(Foto::pathKecil($path))) {
                continue;
            }

            try {
                Foto::buatKecil($path);
                $dibuat++;
            } catch (Throwable $e) {
                $this->warn("Lewati {$path}: {$e->getMessage()}");
            }
        }

        $this->info("{$dibuat} versi kecil dibuat.");

        return self::SUCCESS;
    }
}
