<?php

namespace Tests\Feature;

use App\Models\Agenda;
use App\Models\Album;
use App\Models\Berita;
use App\Models\Guru;
use App\Models\Halaman;
use App\Models\Pengaturan;
use App\Models\Pesan;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class HalamanPublikTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-10-18 08:00:00');
    }

    public function test_semua_halaman_publik_terbuka_saat_website_masih_kosong(): void
    {
        foreach (['/', '/profil', '/profil/guru', '/berita', '/agenda', '/galeri', '/prestasi', '/unduhan', '/tebak-aksi', '/spmb', '/kontak', '/sitemap.xml'] as $alamat) {
            $this->get($alamat)->assertOk();
        }

        $this->get('/')->assertSee('Belum ada berita yang terbit');
        $this->get('/galeri')->assertSee('Belum ada album foto');
    }

    public function test_semua_halaman_publik_terbuka_dengan_data_contoh(): void
    {
        $this->seed(DatabaseSeeder::class);
        $berita = Berita::terbit()->first();
        $guru = Guru::first();

        foreach (['/', '/profil', '/profil/guru', '/profil/visi-misi', '/profil/kebijakan-privasi', '/berita', '/berita?kategori=pengumuman', '/agenda', '/prestasi', '/tebak-aksi', '/spmb', '/kontak', $berita->url(), $guru->url()] as $alamat) {
            $this->get($alamat)->assertOk();
        }

        $this->get('/')->assertSee('Libur Semester Ganjil dan Jadwal Masuk Kembali')->assertSee('Asesmen Sumatif Akhir Semester');
    }

    public function test_draf_berita_terjadwal_dan_halaman_tersembunyi_tidak_tampil(): void
    {
        $draf = Berita::create(['judul' => 'Berita Draf', 'isi' => '<p>Isi</p>', 'status' => 'draf']);
        $terjadwal = Berita::create(['judul' => 'Berita Terjadwal', 'isi' => '<p>Isi</p>', 'status' => 'terbit', 'terbit_pada' => now()->addDay()]);
        $halaman = Halaman::create(['judul' => 'Rahasia', 'isi' => '<p>Isi</p>', 'terbit' => false]);
        $guru = Guru::create(['nama' => 'Guru Tersembunyi', 'tampil' => false]);
        $album = Album::create(['judul' => 'Album Belum Diperiksa', 'terbit' => false]);

        $this->get($draf->url())->assertNotFound();
        $this->get($terjadwal->url())->assertNotFound();
        $this->get($halaman->url())->assertNotFound();
        $this->get($guru->url())->assertNotFound();
        $this->get($album->url())->assertNotFound();
        $this->get('/berita')->assertDontSee('Berita Draf')->assertDontSee('Berita Terjadwal');

        Carbon::setTestNow(now()->addDays(2));
        $this->get($terjadwal->url())->assertOk();
    }

    public function test_pengumuman_disematkan_berhenti_setelah_tanggal_berakhir(): void
    {
        Berita::create([
            'judul' => 'Pengumuman Lama', 'isi' => '<p>Isi</p>', 'kategori' => 'pengumuman', 'status' => 'terbit',
            'terbit_pada' => now()->subDays(5), 'disematkan' => true, 'sematkan_sampai' => now()->subDay(),
        ]);

        $this->assertSame(0, Berita::disematkan()->count());
        $this->get('/')->assertDontSee('Pengumuman penting');
    }

    public function test_isi_berita_dibersihkan_dari_skrip(): void
    {
        $berita = Berita::create([
            'judul' => 'Uji Skrip', 'status' => 'terbit', 'terbit_pada' => now()->subHour(),
            'isi' => '<p>Aman</p><script>alert("x")</script><img src="x" onerror="alert(1)">',
        ]);

        $this->get($berita->url())->assertOk()->assertSee('Aman')->assertDontSee('alert("x")', false)->assertDontSee('onerror', false);
    }

    public function test_nama_penulis_tampil_dan_bawaan_admin_sekolah(): void
    {
        $dariGuru = Berita::create(['judul' => 'Tulisan Guru', 'isi' => '<p>Isi</p>', 'status' => 'terbit', 'terbit_pada' => now(), 'penulis' => 'Ibu Guru Contoh']);
        $dariAdmin = Berita::create(['judul' => 'Tulisan Admin', 'isi' => '<p>Isi</p>', 'status' => 'terbit', 'terbit_pada' => now()]);

        $this->get($dariGuru->url())->assertSee('Ibu Guru Contoh');
        $this->get($dariAdmin->url())->assertSee('Admin Sekolah');
    }

    public function test_menu_spmb_hanya_tampil_bila_diaktifkan_admin(): void
    {
        $this->get('/')->assertDontSee('href="'.route('spmb').'"', false);

        Pengaturan::simpan(['spmb_tampil' => true, 'spmb_status' => 'dibuka', 'spmb_portal_url' => 'https://spmb.contoh.test']);

        $this->get('/')->assertSee('href="'.route('spmb').'"', false)->assertSee('Info penerimaan murid baru');
        $this->get('/spmb')->assertSee('Daftar di portal SPMB')->assertSee('https://spmb.contoh.test');
    }

    public function test_agenda_yang_sudah_lewat_tidak_termasuk_mendatang(): void
    {
        Agenda::create(['judul' => 'Sudah Lewat', 'mulai' => now()->subDays(3)]);
        Agenda::create(['judul' => 'Sedang Berlangsung', 'mulai' => now()->subDay(), 'selesai' => now()->addDay()]);
        Agenda::create(['judul' => 'Akan Datang', 'mulai' => now()->addDays(3)]);

        $this->assertSame(['Sedang Berlangsung', 'Akan Datang'], Agenda::mendatang()->pluck('judul')->all());
        $this->assertSame(['Sudah Lewat'], Agenda::lewat()->pluck('judul')->all());
    }

    public function test_formulir_kontak_menyimpan_pesan_dan_menolak_spam(): void
    {
        $this->post('/kontak', ['kategori' => 'pertanyaan', 'isi' => 'Kapan jadwal pembagian rapor semester ini?'])
            ->assertRedirect(route('kontak'))->assertSessionHas('terkirim');
        $this->assertSame(1, Pesan::where('status', 'baru')->count());

        // Isian jebakan terisi: tampak berhasil bagi pengirim, tetapi tidak disimpan
        $this->post('/kontak', ['kategori' => 'saran', 'isi' => 'Pesan dari program spam otomatis', 'situs' => 'http://spam.test']);
        $this->assertSame(1, Pesan::count());

        $this->post('/kontak', ['kategori' => 'tidak-ada', 'isi' => 'pendek'])->assertSessionHasErrors(['kategori', 'isi']);
    }

    public function test_formulir_kontak_dibatasi_jumlah_kiriman(): void
    {
        foreach (range(1, 5) as $i) {
            $this->post('/kontak', ['kategori' => 'saran', 'isi' => "Saran nomor {$i} untuk sekolah."]);
        }

        $this->post('/kontak', ['kategori' => 'saran', 'isi' => 'Saran keenam untuk sekolah.'])->assertStatus(429);
    }

    public function test_halaman_publik_memakai_header_keamanan(): void
    {
        $this->get('/')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Content-Security-Policy');
    }
}
