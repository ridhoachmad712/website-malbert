<?php

namespace Tests\Feature;

use App\Filament\Resources\EkstrakurikulerResource\Pages\CreateEkstrakurikuler;
use App\Models\Alumni;
use App\Models\Berita;
use App\Models\DokumenAnggaran;
use App\Models\Ekstrakurikuler;
use App\Models\Guru;
use App\Models\Sarana;
use App\Models\User;
use App\Services\SimpanFoto;
use App\Support\Foto;
use Database\Seeders\DataAwalSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class FiturTambahanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DataAwalSeeder::class);
    }

    public function test_halaman_baru_terbuka_saat_masih_kosong(): void
    {
        foreach (['/transparansi', '/ekstrakurikuler', '/sarana', '/alumni', '/cari', '/cari?q=ab'] as $alamat) {
            $this->get($alamat)->assertOk();
        }
        $this->get('/transparansi')->assertSee('Dokumen anggaran belum diunggah');
    }

    public function test_transparansi_menampilkan_dokumen_per_tahun_dan_ringkasan(): void
    {
        Storage::fake('public');
        $dokumen = fn (array $data) => DokumenAnggaran::create($data + ['sumber_dana' => 'bosp_reguler', 'berkas' => 'anggaran/contoh.pdf', 'terbit' => true]);

        $dokumen(['tahun' => 2026, 'jenis' => 'rkas', 'judul' => 'RKAS 2026', 'penerimaan' => 300000000]);
        $dokumen(['tahun' => 2026, 'jenis' => 'realisasi_tahap1', 'judul' => 'Realisasi Tahap 1 2026', 'penerimaan' => 150000000, 'belanja' => 140000000]);
        $dokumen(['tahun' => 2025, 'jenis' => 'realisasi_tahunan', 'judul' => 'Realisasi Tahunan 2025', 'penerimaan' => 280000000, 'belanja' => 280000000]);
        $dokumen(['tahun' => 2026, 'jenis' => 'lainnya', 'judul' => 'Dokumen Tersembunyi', 'terbit' => false]);

        $this->get('/transparansi')->assertOk()
            ->assertSeeInOrder(['RKAS 2026', 'Realisasi Tahap 1 2026'])
            ->assertSee('Rp300.000.000')->assertSee('Rp140.000.000')->assertSee('Rp10.000.000')
            ->assertDontSee('Realisasi Tahunan 2025')->assertDontSee('Dokumen Tersembunyi')
            ->assertSee(route('transparansi', ['tahun' => 2025]));

        $this->get('/transparansi?tahun=2025')->assertSee('Realisasi Tahunan 2025')->assertDontSee('RKAS 2026');
    }

    public function test_foto_unggahan_punya_versi_kecil_yang_ikut_terhapus(): void
    {
        Storage::fake('public');

        $path = SimpanFoto::simpan(UploadedFile::fake()->image('ruang.jpg', 2000, 1500), 'guru');
        $kecil = Foto::pathKecil($path);
        Storage::disk('public')->assertExists($kecil);
        [$lebar] = getimagesize(Storage::disk('public')->path($kecil));
        $this->assertSame(Foto::SISI_KECIL, $lebar);

        $guru = Guru::create(['nama' => 'Guru Berfoto', 'foto' => $path]);
        $this->assertStringEndsWith('-kecil.webp', $guru->urlKecil('foto'));

        $guru->delete();
        Storage::disk('public')->assertMissing($path);
        Storage::disk('public')->assertMissing($kecil);
    }

    public function test_foto_sarana_yang_dilepas_ikut_terhapus(): void
    {
        Storage::fake('public');
        $satu = SimpanFoto::simpan(UploadedFile::fake()->image('a.jpg', 800, 600), 'sarana');
        $dua = SimpanFoto::simpan(UploadedFile::fake()->image('b.jpg', 800, 600), 'sarana');

        $sarana = Sarana::create(['nama' => 'Perpustakaan', 'foto' => [$satu, $dua]]);
        $this->get('/sarana')->assertSee('Perpustakaan')->assertSee('2 foto');

        $sarana->update(['foto' => [$dua]]);
        Storage::disk('public')->assertMissing($satu);
        Storage::disk('public')->assertExists($dua);
    }

    public function test_ekstrakurikuler_tampil_dan_tersembunyi(): void
    {
        $pramuka = Ekstrakurikuler::create(['nama' => 'Pramuka', 'jadwal' => 'Jumat, 14.00', 'pembina' => 'Pembina Contoh']);
        $tari = Ekstrakurikuler::create(['nama' => 'Seni Tari', 'tampil' => false]);

        $this->get('/ekstrakurikuler')->assertSee('Pramuka')->assertDontSee('Seni Tari');
        $this->get($pramuka->url())->assertOk()->assertSee('Jumat, 14.00')->assertSee('Pembina Contoh');
        $this->get($tari->url())->assertNotFound();
    }

    public function test_admin_tidak_bisa_menampilkan_ekskul_berfoto_sebelum_diperiksa(): void
    {
        Storage::fake('public');
        $this->actingAs(User::first());

        Livewire::test(CreateEkstrakurikuler::class)
            ->fillForm(['nama' => 'Futsal', 'foto' => UploadedFile::fake()->image('futsal.jpg'), 'tampil' => true, 'foto_diperiksa' => false])
            ->call('create')
            ->assertHasFormErrors(['foto_diperiksa' => 'accepted']);
    }

    public function test_kiriman_alumni_menunggu_diperiksa_dan_kontak_tidak_tampil(): void
    {
        $this->post('/alumni', [
            'nama' => 'Alumni Contoh', 'tahun_lulus' => 2015, 'kegiatan' => 'Guru SD',
            'cerita' => 'Rajin membaca, ya.', 'kontak' => '081299990000', 'setuju_tampil' => '1',
        ])->assertRedirect(route('alumni'))->assertSessionHas('terkirim');

        $alumni = Alumni::first();
        $this->assertSame('menunggu', $alumni->status);
        $this->assertSame('formulir', $alumni->sumber);
        $this->get('/alumni')->assertDontSee('Alumni Contoh');

        $alumni->update(['status' => 'tampil']);
        $this->get('/alumni')->assertSee('Alumni Contoh')->assertSee('Lulus 2015')->assertDontSee('081299990000');

        $this->post('/alumni', ['nama' => '', 'tahun_lulus' => 1800])->assertSessionHasErrors(['nama', 'tahun_lulus']);
    }

    public function test_pencarian_menemukan_konten_terbit_saja(): void
    {
        Berita::create(['judul' => 'Jadwal Pembagian Rapor', 'isi' => '<p>Isi</p>', 'status' => 'terbit', 'terbit_pada' => now()->subDay()]);
        Berita::create(['judul' => 'Rapor Rahasia Draf', 'isi' => '<p>Isi</p>', 'status' => 'draf']);
        Guru::create(['nama' => 'Guru Rapor Tersembunyi', 'tampil' => false]);

        $this->get('/cari?q=rapor')->assertOk()
            ->assertSee('Jadwal Pembagian Rapor')
            ->assertDontSee('Rapor Rahasia Draf')
            ->assertDontSee('Guru Rapor Tersembunyi');

        $this->get('/cari?q=tidakadasamasekali')->assertSee('Tidak ada hasil');
    }

    public function test_pratinjau_draf_hanya_untuk_admin_dan_tidak_diindeks(): void
    {
        $draf = Berita::create(['judul' => 'Draf Pratinjau', 'isi' => '<p>Isi draf</p>', 'status' => 'draf']);

        $this->get(route('berita.pratinjau', $draf->slug))->assertRedirect();
        $this->get($draf->url())->assertNotFound();

        $this->actingAs(User::first())->get(route('berita.pratinjau', $draf->slug))->assertOk()
            ->assertSee('Isi draf')->assertSee('masih draf')->assertSee('noindex', false);
    }

    public function test_berita_terbit_memuat_data_terstruktur(): void
    {
        $berita = Berita::create(['judul' => 'Lomba Membaca', 'isi' => '<p>Isi</p>', 'status' => 'terbit', 'terbit_pada' => now()->subHour(), 'penulis' => 'Ibu Guru']);

        $this->get($berita->url())->assertSee('"@type":"NewsArticle"', false)->assertSee('"name":"Ibu Guru"', false);
    }
}
