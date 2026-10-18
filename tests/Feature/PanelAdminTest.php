<?php

namespace Tests\Feature;

use App\Filament\Pages\IdentitasSekolah;
use App\Filament\Pages\Spmb;
use App\Filament\Pages\TampilanBeranda;
use App\Filament\Resources\AlbumResource\Pages\CreateAlbum;
use App\Filament\Resources\AlbumResource\Pages\EditAlbum;
use App\Filament\Resources\BeritaResource\Pages\CreateBerita;
use App\Filament\Resources\GuruResource\Pages\CreateGuru;
use App\Filament\Support\HapusAman;
use App\Models\Album;
use App\Models\Berita;
use App\Models\CatatanAktivitas;
use App\Models\Foto;
use App\Models\Guru;
use App\Models\Pengaturan;
use App\Models\User;
use App\Services\CadanganData;
use App\Support\BagianBeranda;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PanelAdminTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->admin = User::first();
        $this->actingAs($this->admin);
    }

    public function test_semua_halaman_admin_terbuka(): void
    {
        $alamat = [
            '/admin', '/admin/berita', '/admin/berita/buat', '/admin/agenda', '/admin/galeri', '/admin/galeri/buat', '/admin/prestasi',
            '/admin/unduhan', '/admin/pesan', '/admin/identitas-sekolah', '/admin/halaman', '/admin/halaman/buat', '/admin/guru',
            '/admin/guru/buat', '/admin/program-tebak-aksi', '/admin/spmb', '/admin/tampilan-beranda', '/admin/pengguna',
            '/admin/cadangan', '/admin/menu', '/admin/transparansi', '/admin/ekstrakurikuler', '/admin/ekstrakurikuler/buat', '/admin/sarana', '/admin/alumni', '/admin/teks-pengantar', '/admin/catatan-aktivitas', '/admin/profile',
            '/admin/berita/'.Berita::first()->slug.'/ubah', '/admin/guru/'.Guru::first()->slug.'/ubah', '/admin/halaman/sejarah/ubah',
        ];

        foreach ($alamat as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_tamu_dan_admin_nonaktif_tidak_bisa_masuk_panel(): void
    {
        auth()->logout();
        $this->get('/admin')->assertRedirect('/admin/login');

        $nonaktif = User::create(['name' => 'Bekas Admin', 'email' => 'bekas@contoh.test', 'password' => 'rahasia12345', 'aktif' => false]);
        $this->actingAs($nonaktif)->get('/admin')->assertForbidden();
    }

    public function test_admin_menulis_berita_dengan_nama_penulis_guru(): void
    {
        Livewire::test(CreateBerita::class)
            ->fillForm([
                'judul' => 'Kunjungan ke Perpustakaan Daerah',
                'isi' => '<p>Murid kelas V berkunjung ke perpustakaan.</p>',
                'kategori' => 'kegiatan',
                'penulis' => 'Guru Contoh, S.Pd.',
                'status' => 'terbit',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $berita = Berita::where('judul', 'Kunjungan ke Perpustakaan Daerah')->first();
        $this->assertSame('kunjungan-ke-perpustakaan-daerah', $berita->slug);
        $this->assertNotNull($berita->terbit_pada);
        $this->assertSame($this->admin->id, $berita->user_id);
        $this->get($berita->url())->assertOk()->assertSee('Guru Contoh, S.Pd.');
        $this->assertTrue(CatatanAktivitas::where('aksi', 'menambah')->where('judul', $berita->judul)->exists());
    }

    public function test_berita_bergambar_tidak_bisa_terbit_sebelum_foto_diperiksa(): void
    {
        Storage::fake('public');

        Livewire::test(CreateBerita::class)
            ->fillForm([
                'judul' => 'Berita Bergambar',
                'isi' => '<p>Isi</p>',
                'kategori' => 'kegiatan',
                'status' => 'terbit',
                'gambar' => UploadedFile::fake()->image('kegiatan.jpg', 2400, 1600),
                'foto_diperiksa' => false,
            ])
            ->call('create')
            ->assertHasFormErrors(['foto_diperiksa' => 'accepted']);

        $this->assertFalse(Berita::where('judul', 'Berita Bergambar')->exists());
    }

    public function test_foto_unggahan_dikecilkan(): void
    {
        Storage::fake('public');

        Livewire::test(CreateGuru::class)
            ->fillForm([
                'nama' => 'Guru Berfoto, S.Pd.',
                'kelompok' => 'guru',
                'foto' => UploadedFile::fake()->image('foto.jpg', 3000, 4000),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $path = Guru::where('nama', 'Guru Berfoto, S.Pd.')->value('foto');
        Storage::disk('public')->assertExists($path);
        [$lebar, $tinggi] = getimagesize(Storage::disk('public')->path($path));
        $this->assertLessThanOrEqual(1600, max($lebar, $tinggi));
    }

    public function test_album_menyimpan_banyak_foto_dan_tidak_bisa_dihapus_selagi_berisi(): void
    {
        Storage::fake('public');

        Livewire::test(CreateAlbum::class)
            ->fillForm([
                'judul' => 'Hari Guru 2026',
                'tanggal' => '2026-11-25',
                'foto_baru' => [UploadedFile::fake()->image('a.jpg', 800, 600), UploadedFile::fake()->image('b.jpg', 800, 600)],
                'foto_diperiksa' => true,
                'terbit' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $album = Album::where('judul', 'Hari Guru 2026')->first();
        $this->assertSame(2, $album->foto()->count());
        $this->get($album->url())->assertOk()->assertSee('2 foto');

        Livewire::test(EditAlbum::class, ['record' => $album->getRouteKey()])
            ->callAction('delete');
        $this->assertModelExists($album);

        // Foto dihapus: berkasnya ikut terhapus dari penyimpanan
        $foto = $album->foto()->first();
        $foto->delete();
        Storage::disk('public')->assertMissing($foto->path);
    }

    public function test_album_tidak_bisa_tampil_sebelum_foto_diperiksa(): void
    {
        Livewire::test(CreateAlbum::class)
            ->fillForm(['judul' => 'Album Tanpa Periksa', 'foto_diperiksa' => false, 'terbit' => true])
            ->call('create')
            ->assertHasFormErrors(['foto_diperiksa' => 'accepted']);
    }

    public function test_admin_mengubah_identitas_dan_peta_hanya_menerima_google_maps(): void
    {
        Livewire::test(IdentitasSekolah::class)
            ->set('data.npsn', '40312345')
            ->set('data.peta_sematan', '<iframe src="https://www.google.com/maps/embed?pb=!1m18!contoh" width="600"></iframe>')
            ->call('simpan')
            ->assertHasNoErrors();

        $this->assertSame('40312345', Pengaturan::ambil('npsn'));
        $this->assertSame('https://www.google.com/maps/embed?pb=!1m18!contoh', Pengaturan::ambil('peta_sematan'));

        Livewire::test(IdentitasSekolah::class)
            ->set('data.peta_sematan', '<iframe src="https://situs-lain.test/peta"></iframe>')
            ->call('simpan')
            ->assertHasErrors(['data.peta_sematan']);
    }

    public function test_admin_mengganti_warna_dan_mematikan_bagian_beranda(): void
    {
        Livewire::test(TampilanBeranda::class)
            ->set('data.tema_warna', 'biru')
            ->set('data.beranda_bagian', collect(BagianBeranda::semua())
                ->map(fn ($bagian) => $bagian['kunci'] === 'agenda' ? [...$bagian, 'tampil' => false] : $bagian)->all())
            ->call('simpan')
            ->assertHasNoErrors();

        $this->get('/')->assertSee('--aksen-700:#1d4ed8', false)->assertDontSee('Kegiatan yang akan datang');
    }

    public function test_admin_mengisi_spmb(): void
    {
        Livewire::test(Spmb::class)
            ->set('data.spmb_tampil', true)
            ->set('data.spmb_jadwal', [['kegiatan' => 'Pendaftaran daring', 'tanggal' => '2–6 Juni 2027']])
            ->call('simpan')
            ->assertHasNoErrors();

        $this->get('/spmb')->assertSee('Pendaftaran daring')->assertSee('2–6 Juni 2027');
    }

    public function test_admin_tidak_bisa_menghapus_akun_sendiri(): void
    {
        $this->assertSame('Anda tidak dapat menghapus akun sendiri.', HapusAman::alasan($this->admin));
    }

    public function test_unduh_cadangan_data(): void
    {
        $this->assertStringContainsString('CREATE TABLE', (function () {
            ob_start();
            CadanganData::tulisSql(function (string $teks) {
                echo $teks;
            });

            return ob_get_clean();
        })());
    }
}
