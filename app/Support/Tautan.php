<?php

namespace App\Support;

use App\Models\Halaman;
use App\Models\Pengaturan;

/**
 * Tautan yang diatur admin (menu, tombol, tautan kaki halaman).
 * Bentuk datanya: ['jenis' => 'internal'|'url', 'tujuan' => kode halaman, 'url' => alamat luar, 'tab_baru' => bool].
 */
class Tautan
{
    /** Halaman tetap website yang bisa dipilih sebagai tujuan. */
    public const TETAP = [
        'beranda' => 'Beranda',
        'profil' => 'Profil: identitas dan sambutan',
        'guru' => 'Guru dan tenaga kependidikan',
        'berita' => 'Berita (semua)',
        'pengumuman' => 'Pengumuman',
        'agenda' => 'Agenda',
        'galeri' => 'Galeri',
        'prestasi' => 'Prestasi',
        'unduhan' => 'Unduhan',
        'transparansi' => 'Transparansi anggaran',
        'ekskul' => 'Ekstrakurikuler',
        'sarana' => 'Sarana dan prasarana',
        'alumni' => 'Alumni',
        'tebakaksi' => 'Program TEBAK AKSI',
        'spmb' => 'SPMB',
        'kontak' => 'Kontak',
    ];

    /** Kode khusus untuk submenu: diganti otomatis dengan semua halaman profil yang terbit. */
    public const OTOMATIS_PROFIL = 'otomatis:profil';

    /** @return array<string, string> pilihan tujuan untuk isian admin */
    public static function pilihan(bool $denganOtomatis = false): array
    {
        $halaman = Halaman::orderBy('grup')->orderBy('urutan')->orderBy('judul')->get(['judul', 'slug', 'terbit'])
            ->mapWithKeys(fn ($item) => ['halaman:'.$item->slug => 'Halaman: '.$item->judul.($item->terbit ? '' : ' (belum tampil)')])
            ->all();

        $otomatis = $denganOtomatis ? [self::OTOMATIS_PROFIL => 'Semua halaman profil (otomatis)'] : [];

        return $otomatis + self::TETAP + $halaman;
    }

    /** Alamat dari data tautan; null bila tujuannya tidak ada lagi atau belum diisi. */
    public static function url(?array $tautan): ?string
    {
        if (! $tautan) {
            return null;
        }

        if (($tautan['jenis'] ?? 'internal') === 'url') {
            $url = trim((string) ($tautan['url'] ?? ''));

            return preg_match('~^(https?://|mailto:|tel:)~i', $url) ? $url : null;
        }

        return self::urlInternal($tautan['tujuan'] ?? null);
    }

    public static function urlInternal(?string $tujuan): ?string
    {
        if (blank($tujuan)) {
            return null;
        }

        if (str_starts_with($tujuan, 'halaman:')) {
            $slug = substr($tujuan, 8);

            return Halaman::terbit()->where('slug', $slug)->exists() ? route('halaman', $slug) : null;
        }

        return match ($tujuan) {
            'pengumuman' => route('berita', ['kategori' => 'pengumuman']),
            // Saat SPMB dimatikan admin, semua tautan ke halaman SPMB ikut disembunyikan
            'spmb' => Pengaturan::aktif('spmb_tampil') ? route('spmb') : null,
            'beranda', 'profil', 'guru', 'berita', 'agenda', 'galeri', 'prestasi', 'unduhan', 'tebakaksi', 'kontak',
            'transparansi', 'ekskul', 'sarana', 'alumni' => route($tujuan),
            default => null,
        };
    }

    /** Dibuka di tab baru? Bawaannya ya untuk alamat luar, tidak untuk halaman website sendiri. */
    public static function keluar(?array $tautan): bool
    {
        return (bool) ($tautan['tab_baru'] ?? (($tautan['jenis'] ?? 'internal') === 'url'));
    }

    /** Atribut HTML untuk membuka tautan luar di tab baru dengan aman. */
    public static function atribut(?array $tautan): string
    {
        return self::keluar($tautan) ? 'target="_blank" rel="noopener"' : '';
    }
}
