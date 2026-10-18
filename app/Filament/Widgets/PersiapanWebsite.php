<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\IdentitasSekolah;
use App\Filament\Resources\GuruResource;
use App\Filament\Resources\HalamanResource;
use App\Models\Guru;
use App\Models\Halaman;
use App\Models\Pengaturan;
use Filament\Widgets\Widget;

/** Daftar isian dasar yang belum lengkap. Hilang sendiri bila semuanya sudah diisi. */
class PersiapanWebsite extends Widget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.persiapan-website';

    public static function canView(): bool
    {
        return count(static::belumLengkap()) > 0;
    }

    /** @return array<int, array{teks: string, url: string}> */
    public static function belumLengkap(): array
    {
        $identitas = IdentitasSekolah::getUrl();
        $daftar = [
            ['ada' => filled(Pengaturan::ambil('logo_sekolah')), 'teks' => 'Unggah logo sekolah', 'url' => $identitas],
            ['ada' => filled(Pengaturan::ambil('npsn')), 'teks' => 'Isi NPSN dan data pokok sekolah', 'url' => $identitas],
            ['ada' => filled(Pengaturan::ambil('alamat')), 'teks' => 'Isi alamat sekolah', 'url' => $identitas],
            ['ada' => filled(Pengaturan::ambil('telepon')) || filled(Pengaturan::ambil('whatsapp')) || filled(Pengaturan::ambil('email')), 'teks' => 'Isi paling sedikit satu kontak resmi sekolah', 'url' => $identitas],
            ['ada' => filled(Pengaturan::ambil('peta_sematan')), 'teks' => 'Pasang peta lokasi sekolah', 'url' => $identitas],
            ['ada' => filled(Pengaturan::ambil('kepsek_nama')) && filled(Pengaturan::ambil('kepsek_sambutan')), 'teks' => 'Isi nama dan sambutan kepala sekolah', 'url' => $identitas],
            ['ada' => Halaman::terbit()->whereNotNull('isi')->where('isi', '!=', '')->where('slug', 'visi-misi')->exists(), 'teks' => 'Tulis visi dan misi sekolah', 'url' => HalamanResource::getUrl()],
            ['ada' => Guru::where('tampil', true)->exists(), 'teks' => 'Tambahkan data guru dan tenaga kependidikan', 'url' => GuruResource::getUrl('create')],
        ];

        return array_values(array_filter($daftar, fn ($item) => ! $item['ada']));
    }
}
