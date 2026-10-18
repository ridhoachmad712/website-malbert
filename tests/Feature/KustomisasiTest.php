<?php

namespace Tests\Feature;

use App\Filament\Pages\MenuSitus;
use App\Filament\Pages\TampilanBeranda;
use App\Models\Guru;
use App\Models\Halaman;
use App\Models\Pengaturan;
use App\Models\User;
use App\Support\BagianBeranda;
use App\Support\Menu;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KustomisasiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->actingAs(User::first());
    }

    public function test_menu_bawaan_menampilkan_halaman_profil_otomatis_dan_menyembunyikan_spmb(): void
    {
        $menu = Menu::utama();

        $this->assertSame(['Beranda', 'Profil', 'Informasi', 'TEBAK AKSI', 'Kontak'], $menu->pluck('label')->all());
        $profil = collect($menu->firstWhere('label', 'Profil')['anak'])->pluck('label');
        $this->assertTrue($profil->contains('Visi dan Misi'));
        $this->assertFalse($profil->contains('Struktur Organisasi'), 'Halaman yang belum terbit tidak boleh muncul di menu');

        Pengaturan::simpan(['spmb_tampil' => true]);
        $this->assertContains('SPMB', Menu::utama()->pluck('label')->all());
    }

    public function test_admin_menambah_menu_tautan_luar_grup_dan_menyembunyikan_menu(): void
    {
        Livewire::test(MenuSitus::class)
            ->set('data.menu_utama', [
                ['label' => 'Beranda', 'bentuk' => 'tautan', 'jenis' => 'internal', 'tujuan' => 'beranda', 'tab_baru' => false, 'tampil' => true, 'anak' => []],
                ['label' => 'Rapor Daring', 'bentuk' => 'tautan', 'jenis' => 'url', 'url' => 'https://rapor.contoh.test', 'tab_baru' => true, 'tampil' => true, 'anak' => []],
                ['label' => 'Layanan', 'bentuk' => 'grup', 'tampil' => true, 'anak' => [
                    ['label' => 'Unduhan', 'jenis' => 'internal', 'tujuan' => 'unduhan', 'tab_baru' => false],
                    ['label' => 'Visi kami', 'jenis' => 'internal', 'tujuan' => 'halaman:visi-misi', 'tab_baru' => false],
                ]],
                ['label' => 'Kontak', 'bentuk' => 'tautan', 'jenis' => 'internal', 'tujuan' => 'kontak', 'tab_baru' => false, 'tampil' => false, 'anak' => []],
            ])
            ->set('data.kepala_tombol_tampil', true)
            ->set('data.kepala_tombol_label', 'Daftar SPMB')
            ->set('data.kepala_tombol_jenis', 'url')
            ->set('data.kepala_tombol_url', 'https://spmb.contoh.test')
            ->set('data.kepala_tombol_tab_baru', true)
            ->call('simpan')
            ->assertHasNoErrors();

        $halaman = $this->get('/')->assertOk();
        $halaman->assertSee('Rapor Daring')->assertSee('https://rapor.contoh.test')
            ->assertSee('Layanan')->assertSee(route('halaman', 'visi-misi'))
            ->assertSee('Daftar SPMB')->assertSee('https://spmb.contoh.test');
        $this->assertNotContains('Kontak', Menu::utama()->pluck('label')->all());
        $this->assertTrue(Menu::utama()->firstWhere('label', 'Rapor Daring')['keluar']);
    }

    public function test_tautan_ke_halaman_yang_disembunyikan_tidak_tampil(): void
    {
        Pengaturan::simpan(['menu_utama' => [
            ['label' => 'Sejarah Kami', 'bentuk' => 'tautan', 'jenis' => 'internal', 'tujuan' => 'halaman:sejarah', 'tampil' => true],
        ]]);
        $this->assertSame(['Sejarah Kami'], Menu::utama()->pluck('label')->all());

        Halaman::where('slug', 'sejarah')->update(['terbit' => false]);
        $this->assertSame([], Menu::utama()->pluck('label')->all());
    }

    public function test_tautan_luar_hanya_menerima_alamat_web(): void
    {
        Pengaturan::simpan(['kaki_tautan' => [
            ['label' => 'Aman', 'jenis' => 'url', 'url' => 'https://contoh.test'],
            ['label' => 'Berbahaya', 'jenis' => 'url', 'url' => 'javascript:alert(1)'],
        ]]);

        $this->assertSame(['Aman'], collect(Menu::kaki())->pluck('label')->all());
        $this->get('/')->assertDontSee('javascript:alert', false);
    }

    public function test_admin_mengurutkan_menyembunyikan_dan_mengganti_judul_bagian_beranda(): void
    {
        $bagian = collect(BagianBeranda::semua())->keyBy('kunci');
        $urutan = [
            [...$bagian['guru'], 'judul' => 'Kenalan dengan guru kami'],
            $bagian['berita'],
            [...$bagian['sambutan'], 'tampil' => false],
            ...$bagian->except(['guru', 'berita', 'sambutan'])->values(),
        ];

        Livewire::test(TampilanBeranda::class)
            ->set('data.beranda_bagian', $urutan)
            ->set('data.beranda_tombol1_label', 'Info SPMB')
            ->set('data.beranda_tombol1_jenis', 'url')
            ->set('data.beranda_tombol1_url', 'https://spmb.contoh.test')
            ->set('data.ajakan_judul', 'Mari berkunjung ke sekolah')
            ->call('simpan')
            ->assertHasNoErrors();

        $this->get('/')->assertOk()
            ->assertSeeInOrder(['Kenalan dengan guru kami', 'Berita terbaru'])
            ->assertDontSee('Baca sambutan lengkap')
            ->assertSee('Info SPMB')->assertSee('https://spmb.contoh.test')
            ->assertSee('Mari berkunjung ke sekolah');
    }

    public function test_bagian_baru_tetap_muncul_walau_simpanan_admin_lama(): void
    {
        Pengaturan::simpan(['beranda_bagian' => [['kunci' => 'berita', 'tampil' => true, 'label' => '', 'judul' => '']]]);

        $this->assertEqualsCanonicalizing(array_keys(BagianBeranda::DAFTAR), collect(BagianBeranda::semua())->pluck('kunci')->all());
        $this->assertSame('berita', BagianBeranda::semua()[0]['kunci']);
        $this->assertSame('Berita terbaru', BagianBeranda::semua()[0]['judul']);
    }

    public function test_beranda_tanpa_foto_hero_tetap_rapi_dan_bagian_kosong_dilewati(): void
    {
        Guru::query()->delete();

        $this->get('/')->assertOk()
            ->assertDontSee('lintasan-guru', false)
            ->assertSee('Belajar dengan gembira');
    }
}
