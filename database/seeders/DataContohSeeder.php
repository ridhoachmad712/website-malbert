<?php

namespace Database\Seeders;

use App\Models\Agenda;
use App\Models\Berita;
use App\Models\Guru;
use App\Models\Halaman;
use App\Models\Pengaturan;
use App\Models\Prestasi;
use Illuminate\Database\Seeder;

/**
 * Data contoh untuk pengembangan dan uji coba. Semua nama dan isi rekaan, bukan data asli sekolah.
 * Tidak pernah dijalankan di server produksi.
 */
class DataContohSeeder extends Seeder
{
    public function run(): void
    {
        Pengaturan::simpan([
            'npsn' => '00000000',
            'akreditasi' => 'A',
            'tahun_berdiri' => '1984',
            'alamat' => 'Jl. Contoh Alamat No. 1',
            'kecamatan' => 'Contoh',
            'kode_pos' => '90000',
            'telepon' => '(0411) 000000',
            'email' => 'sekolah@contoh.test',
            'whatsapp' => '081200000000',
            'jumlah_rombel' => '18',
            'jumlah_murid' => '540',
            'kepsek_nama' => 'Nama Kepala Sekolah, S.Pd., M.Pd.',
            'kepsek_sambutan_singkat' => 'Selamat datang di website sekolah kami. Melalui website ini, kami berbagi kabar kegiatan, prestasi, dan informasi penting bagi orang tua dan masyarakat.',
            'kepsek_sambutan' => '<p>Selamat datang di website sekolah kami. Melalui website ini, kami berbagi kabar kegiatan, prestasi, dan informasi penting bagi orang tua dan masyarakat.</p><p>Kami mengajak orang tua untuk terus bekerja sama mendampingi anak belajar di rumah.</p>',
        ]);

        Halaman::where('slug', 'visi-misi')->update([
            'terbit' => true,
            'isi' => '<h2>Visi</h2><p>Terwujudnya murid yang gemar membaca, berkarakter, dan berprestasi.</p><h2>Misi</h2><ol><li>Membiasakan membaca setiap hari.</li><li>Menanamkan sikap santun dan disiplin.</li><li>Mengembangkan bakat murid melalui kegiatan sekolah.</li></ol>',
        ]);
        Halaman::where('slug', 'sejarah')->update([
            'terbit' => true,
            'isi' => '<p>Teks contoh. Sejarah sekolah akan diisi oleh admin.</p>',
        ]);

        $guru = [
            ['nama' => 'Nama Kepala Sekolah, S.Pd., M.Pd.', 'jabatan' => 'Kepala Sekolah', 'kelompok' => 'pimpinan', 'motto' => 'Belajar sepanjang hayat.'],
            ['nama' => 'Guru Contoh Satu, S.Pd.', 'jabatan' => 'Guru Kelas I', 'kelompok' => 'guru'],
            ['nama' => 'Guru Contoh Dua, S.Pd.', 'jabatan' => 'Guru Kelas II', 'kelompok' => 'guru', 'motto' => 'Setiap anak bisa.'],
            ['nama' => 'Guru Contoh Tiga, S.Pd.', 'jabatan' => 'Guru Kelas III', 'kelompok' => 'guru'],
            ['nama' => 'Guru Contoh Empat, S.Pd.I.', 'jabatan' => 'Guru Pendidikan Agama Islam', 'kelompok' => 'guru'],
            ['nama' => 'Tendik Contoh, A.Md.', 'jabatan' => 'Operator Sekolah', 'kelompok' => 'tendik'],
        ];
        foreach ($guru as $i => $data) {
            Guru::firstOrCreate(['nama' => $data['nama']], $data + ['urutan' => $i, 'profil' => 'Profil contoh. Diisi oleh admin.']);
        }

        $berita = [
            ['judul' => 'Libur Semester Ganjil dan Jadwal Masuk Kembali', 'kategori' => 'pengumuman', 'disematkan' => true],
            ['judul' => 'Murid Kelas IV Mengunjungi Perpustakaan Kota', 'kategori' => 'kegiatan'],
            ['judul' => 'Membiasakan Anak Membaca di Rumah', 'kategori' => 'artikel', 'penulis' => 'Guru Contoh Dua, S.Pd. (Guru Kelas II)'],
            ['judul' => 'Peringatan Hari Guru Nasional di Sekolah', 'kategori' => 'kegiatan'],
        ];
        foreach ($berita as $i => $data) {
            Berita::firstOrCreate(['judul' => $data['judul']], $data + [
                'isi' => '<p>Ini adalah teks contoh untuk uji coba tampilan berita. Isi sebenarnya ditulis oleh admin sekolah.</p><p>Paragraf kedua contoh.</p>',
                'status' => 'terbit',
                'terbit_pada' => now()->subDays($i * 3 + 1),
            ]);
        }

        $agenda = [
            ['judul' => 'Asesmen Sumatif Akhir Semester', 'mulai' => today()->addDays(10), 'selesai' => today()->addDays(15), 'waktu' => '07.30–10.00 WITA'],
            ['judul' => 'Pembagian Rapor', 'mulai' => today()->addDays(20), 'tempat' => 'Ruang kelas masing-masing'],
            ['judul' => 'Libur Semester Ganjil', 'mulai' => today()->addDays(22), 'selesai' => today()->addDays(35), 'libur' => true],
        ];
        foreach ($agenda as $data) {
            Agenda::firstOrCreate(['judul' => $data['judul']], $data);
        }

        Prestasi::firstOrCreate(['judul' => 'Lomba Bercerita Tingkat Kota'], [
            'peraih' => 'Murid Contoh (Kelas V)', 'peringkat' => 'Juara 2', 'tingkat' => 'kota',
            'penyelenggara' => 'Penyelenggara Contoh', 'tanggal' => today()->subMonth(),
        ]);
    }
}
