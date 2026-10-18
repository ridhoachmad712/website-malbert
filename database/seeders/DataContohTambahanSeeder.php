<?php

namespace Database\Seeders;

use App\Models\Alumni;
use App\Models\DokumenAnggaran;
use App\Models\Ekstrakurikuler;
use App\Models\Sarana;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/** Data contoh rekaan untuk transparansi, ekstrakurikuler, sarana, dan alumni. Tidak pernah dijalankan di produksi. */
class DataContohTambahanSeeder extends Seeder
{
    public function run(): void
    {
        $berkas = 'anggaran/contoh-dokumen.pdf';
        if (! Storage::disk('public')->exists($berkas)) {
            Storage::disk('public')->put($berkas, "%PDF-1.4\n% Berkas contoh untuk uji coba, bukan dokumen asli.\n");
        }

        $tahun = now()->year;
        $dokumen = [
            ['jenis' => 'rkas', 'judul' => "RKAS BOSP Reguler Tahun {$tahun}", 'penerimaan' => 312000000],
            ['jenis' => 'realisasi_tahap1', 'judul' => "Laporan Realisasi BOSP Reguler Tahap 1 Tahun {$tahun}", 'periode' => "Januari–Juni {$tahun}", 'penerimaan' => 156000000, 'belanja' => 151250000],
        ];
        foreach ($dokumen as $data) {
            DokumenAnggaran::firstOrCreate(['judul' => $data['judul']], $data + ['tahun' => $tahun, 'sumber_dana' => 'bosp_reguler', 'berkas' => $berkas, 'nama_berkas' => 'contoh-dokumen.pdf']);
        }

        foreach ([
            ['nama' => 'Pramuka', 'ringkasan' => 'Melatih kemandirian, kerja sama, dan cinta alam.', 'jadwal' => 'Jumat, 14.00–15.30', 'pembina' => 'Pembina Contoh', 'peserta' => 'Kelas III–VI'],
            ['nama' => 'Seni Tari Daerah', 'ringkasan' => 'Mengenal dan melestarikan tari daerah Sulawesi Selatan.', 'jadwal' => 'Rabu, 14.00–15.00', 'peserta' => 'Kelas II–VI'],
            ['nama' => 'Klub Membaca', 'ringkasan' => 'Membaca bersama dan bercerita, lanjutan kegiatan Teras Baca.', 'jadwal' => 'Kamis, 13.30–14.30', 'peserta' => 'Semua kelas'],
        ] as $i => $data) {
            Ekstrakurikuler::firstOrCreate(['nama' => $data['nama']], $data + ['urutan' => $i]);
        }

        foreach ([
            ['nama' => 'Ruang kelas', 'jumlah' => '18 ruang', 'keterangan' => 'Ruang kelas dengan meja dan kursi murid, papan tulis, dan pojok baca.'],
            ['nama' => 'Perpustakaan', 'jumlah' => '1 ruang', 'keterangan' => 'Koleksi buku bacaan anak dan buku pelajaran.'],
            ['nama' => 'Teras Baca', 'jumlah' => 'Di setiap kelas', 'keterangan' => 'Tempat murid membaca 15 menit setiap hari dalam program TEBAK AKSI.'],
        ] as $i => $data) {
            Sarana::firstOrCreate(['nama' => $data['nama']], $data + ['urutan' => $i]);
        }

        Alumni::firstOrCreate(['nama' => 'Alumni Contoh'], [
            'tahun_lulus' => 2012, 'kegiatan' => 'Guru sekolah dasar', 'cerita' => 'Teruslah membaca setiap hari. Dari buku, kita bisa ke mana saja.',
            'sumber' => 'admin', 'status' => 'tampil', 'setuju_tampil' => true,
        ]);
    }
}
