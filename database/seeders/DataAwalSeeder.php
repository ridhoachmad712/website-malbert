<?php

namespace Database\Seeders;

use App\Models\Halaman;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

/** Data awal yang dibutuhkan website: akun admin dan kerangka halaman profil (isinya diisi admin). */
class DataAwalSeeder extends Seeder
{
    public function run(): void
    {
        $sandi = env('ADMIN_PASSWORD');
        if (app()->isProduction() && (blank($sandi) || strlen($sandi) < 10 || in_array($sandi, ['password', 'ganti-dengan-kata-sandi-kuat'], true))) {
            throw new RuntimeException('Isi ADMIN_PASSWORD di .env dengan kata sandi kuat (minimal 10 karakter) sebelum menjalankan seeder di server.');
        }

        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@sdimalbert.test')],
            ['name' => 'Admin Website', 'password' => $sandi ?: 'password', 'aktif' => true],
        );

        $halaman = [
            ['judul' => 'Sejarah', 'slug' => 'sejarah', 'ringkasan' => 'Perjalanan sekolah dari awal berdiri hingga sekarang.'],
            ['judul' => 'Visi dan Misi', 'slug' => 'visi-misi', 'ringkasan' => 'Arah dan cita-cita sekolah.'],
            ['judul' => 'Struktur Organisasi', 'slug' => 'struktur-organisasi', 'ringkasan' => 'Susunan pimpinan, guru, dan tenaga kependidikan.'],
            ['judul' => 'Sarana dan Prasarana', 'slug' => 'sarana-prasarana', 'ringkasan' => 'Ruang belajar dan fasilitas pendukung di sekolah.'],
        ];
        foreach ($halaman as $i => $data) {
            Halaman::firstOrCreate(['slug' => $data['slug']], $data + ['grup' => 'profil', 'urutan' => $i + 1, 'isi' => '', 'terbit' => false]);
        }

        Halaman::firstOrCreate(['slug' => 'kebijakan-privasi'], [
            'judul' => 'Kebijakan Privasi',
            'grup' => 'lainnya',
            'ringkasan' => 'Cara sekolah menjaga data dan foto murid di website ini.',
            'terbit' => true,
            'isi' => self::KEBIJAKAN_PRIVASI,
        ]);
    }

    /** Teks awal kebijakan privasi; admin dapat menyesuaikannya. */
    public const KEBIJAKAN_PRIVASI = <<<'HTML'
<p>Website ini dikelola oleh admin sekolah untuk memberikan informasi kepada orang tua, calon murid, dan masyarakat. Kami menjaga data pribadi murid sesuai Undang-Undang Nomor 27 Tahun 2022 tentang Pelindungan Data Pribadi.</p>
<h2>Data yang tidak kami tampilkan</h2>
<ul><li>Data pribadi murid seperti NISN, tanggal lahir, alamat rumah, nilai, dan catatan perkembangan.</li><li>Nomor telepon pribadi guru dan orang tua.</li></ul>
<h2>Foto kegiatan</h2>
<p>Foto murid hanya ditampilkan setelah orang tua atau wali memberikan izin kepada sekolah. Admin memeriksa setiap foto sebelum ditampilkan. Keterangan foto tidak menyebutkan nama murid, dan data lokasi pada berkas foto dihapus otomatis saat diunggah.</p>
<p>Nama murid hanya dapat tampil pada halaman Prestasi, setelah diperiksa admin dan mendapat persetujuan orang tua.</p>
<h2>Pesan dari formulir Kontak</h2>
<p>Pesan yang dikirim melalui formulir Kontak hanya dibaca oleh admin sekolah dan tidak ditampilkan di website. Nama dan kontak boleh dikosongkan.</p>
<h2>Pendaftaran murid baru</h2>
<p>Pendaftaran murid baru dilakukan melalui portal resmi Dinas Pendidikan Kota Makassar. Website ini tidak menerima atau menyimpan data pendaftar.</p>
<h2>Meminta foto diturunkan</h2>
<p>Orang tua atau wali yang ingin foto anaknya diturunkan dari website dapat menghubungi sekolah melalui halaman Kontak atau datang langsung ke sekolah. Kami akan menurunkan foto tersebut paling lambat dua hari kerja.</p>
HTML;
}
