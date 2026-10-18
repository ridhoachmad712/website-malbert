<?php

namespace App\Support;

use App\Models\Pengaturan;

/**
 * Bagian-bagian Beranda yang bisa diurutkan, disembunyikan, dan diberi judul oleh admin.
 * Bagian baru yang belum ada di simpanan admin otomatis ditambahkan di akhir dengan nilai bawaan.
 */
class BagianBeranda
{
    public const DAFTAR = [
        'berita' => ['nama' => 'Berita dan pengumuman', 'label' => 'Kabar sekolah', 'judul' => 'Berita terbaru'],
        'sambutan' => ['nama' => 'Sambutan kepala sekolah', 'label' => 'Sambutan', 'judul' => 'Sambutan kepala sekolah'],
        'guru' => ['nama' => 'Guru dan tenaga kependidikan', 'label' => 'Pendidik kami', 'judul' => 'Guru dan tenaga kependidikan'],
        'agenda' => ['nama' => 'Agenda terdekat', 'label' => 'Agenda', 'judul' => 'Kegiatan yang akan datang'],
        'program' => ['nama' => 'Program TEBAK AKSI', 'label' => 'Program unggulan', 'judul' => 'TEBAK AKSI'],
        'prestasi' => ['nama' => 'Prestasi terbaru', 'label' => 'Prestasi', 'judul' => 'Prestasi terbaru'],
        'galeri' => ['nama' => 'Galeri kegiatan', 'label' => 'Galeri', 'judul' => 'Dokumentasi kegiatan'],
        'ajakan' => ['nama' => 'Ajakan penutup', 'label' => '', 'judul' => ''],
    ];

    /** @return array<int, array{kunci: string, nama: string, tampil: bool, label: string, judul: string}> urut sesuai pilihan admin */
    public static function semua(): array
    {
        $tersimpan = collect(Pengaturan::daftar('beranda_bagian'))
            ->filter(fn ($item) => isset(self::DAFTAR[$item['kunci'] ?? null]))
            ->keyBy('kunci');

        $urutan = $tersimpan->keys()->merge(array_keys(self::DAFTAR))->unique();

        return $urutan->map(function ($kunci) use ($tersimpan) {
            $bawaan = self::DAFTAR[$kunci];
            $simpan = $tersimpan->get($kunci, []);

            return [
                'kunci' => $kunci,
                'nama' => $bawaan['nama'],
                'tampil' => filter_var($simpan['tampil'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'label' => filled($simpan['label'] ?? null) ? $simpan['label'] : $bawaan['label'],
                'judul' => filled($simpan['judul'] ?? null) ? $simpan['judul'] : $bawaan['judul'],
            ];
        })->values()->all();
    }

    /** @return array<string, array> bagian yang ditampilkan, urut dan diberi kunci */
    public static function tampil(): array
    {
        return collect(self::semua())->filter(fn ($bagian) => $bagian['tampil'])->keyBy('kunci')->all();
    }
}
