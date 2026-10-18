<?php

namespace App\Support;

use App\Models\Halaman;
use App\Models\Pengaturan;
use Illuminate\Support\Collection;

/**
 * Menyusun menu utama dari pengaturan admin. Butir yang dimatikan, tautan yang tujuannya sudah tidak ada,
 * dan menu induk tanpa isi dilewati agar tidak ada tautan mati di website.
 */
class Menu
{
    /** @return Collection<int, array{label: string, url: ?string, keluar: bool, eksternal: bool, aktif: bool, anak: array}> */
    public static function utama(): Collection
    {
        return collect(Pengaturan::daftar('menu_utama'))
            ->filter(fn ($item) => filter_var($item['tampil'] ?? true, FILTER_VALIDATE_BOOLEAN) && filled($item['label'] ?? null))
            ->map(function ($item) {
                $anak = collect($item['anak'] ?? [])
                    ->flatMap(fn ($sub) => ($sub['tujuan'] ?? null) === Tautan::OTOMATIS_PROFIL && ($sub['jenis'] ?? 'internal') === 'internal'
                        ? self::halamanProfil()
                        : [self::butir($sub)])
                    ->filter(fn ($sub) => $sub && $sub['url'])
                    ->values()->all();

                if (($item['bentuk'] ?? 'tautan') === 'grup') {
                    return $anak ? ['label' => $item['label'], 'url' => null, 'keluar' => false, 'eksternal' => false, 'aktif' => collect($anak)->contains('aktif', true), 'anak' => $anak] : null;
                }

                return self::butir($item);
            })
            ->filter(fn ($item) => $item && ($item['url'] || $item['anak']))
            ->values();
    }

    /** Tombol ajakan di kepala halaman, atau null bila dimatikan. */
    public static function tombolKepala(): ?array
    {
        if (! Pengaturan::aktif('kepala_tombol_tampil')) {
            return null;
        }

        $butir = self::butir(Pengaturan::tautan('kepala_tombol_'));

        return $butir && $butir['url'] ? $butir : null;
    }

    /** @return array<int, array> tautan kaki halaman */
    public static function kaki(): array
    {
        return collect(Pengaturan::daftar('kaki_tautan'))->map(fn ($item) => self::butir($item))
            ->filter(fn ($item) => $item && $item['url'])->values()->all();
    }

    protected static function butir(array $item): ?array
    {
        if (blank($item['label'] ?? null)) {
            return null;
        }

        $url = Tautan::url($item);

        return [
            'label' => $item['label'],
            'url' => $url,
            'keluar' => Tautan::keluar($item),
            'eksternal' => ($item['jenis'] ?? 'internal') === 'url',
            'aktif' => $url !== null && self::sedangDibuka($url),
            'anak' => [],
        ];
    }

    protected static function halamanProfil(): array
    {
        return Halaman::terbit()->where('grup', 'profil')->orderBy('urutan')->orderBy('judul')->get(['judul', 'slug'])
            ->map(fn ($halaman) => self::butir(['label' => $halaman->judul, 'jenis' => 'internal', 'tujuan' => 'halaman:'.$halaman->slug]))
            ->all();
    }

    protected static function sedangDibuka(string $url): bool
    {
        if (request()->fullUrl() === $url) {
            return true;
        }

        $jalur = parse_url($url, PHP_URL_PATH) ?: '/';
        $sekarang = '/'.ltrim(request()->path(), '/');

        return parse_url($url, PHP_URL_HOST) === request()->getHost()
            && ! parse_url($url, PHP_URL_QUERY)
            && $jalur !== '/'
            && ($sekarang === $jalur || str_starts_with($sekarang, rtrim($jalur, '/').'/'))
            && ! ($jalur === '/berita' && request()->query('kategori'))
            && ! ($jalur === '/profil' && request()->is('profil/*'));
    }
}
