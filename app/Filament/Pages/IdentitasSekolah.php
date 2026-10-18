<?php

namespace App\Filament\Pages;

use App\Filament\Support\HalamanPengaturan;
use App\Filament\Support\Isian;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class IdentitasSekolah extends HalamanPengaturan
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static string|UnitEnum|null $navigationGroup = 'Profil Sekolah';

    protected static ?string $navigationLabel = 'Identitas & Kontak';

    protected static ?string $title = 'Identitas Sekolah dan Kontak';

    protected static ?string $slug = 'identitas-sekolah';

    protected static ?int $navigationSort = 1;

    protected static array $kunci = [
        'nama_sekolah', 'nama_singkat', 'npsn', 'status_sekolah', 'akreditasi', 'kurikulum', 'tahun_berdiri',
        'jumlah_rombel', 'jumlah_murid', 'tautan_data_resmi', 'logo_sekolah',
        'alamat', 'kelurahan', 'kecamatan', 'kota', 'provinsi', 'kode_pos', 'telepon', 'email', 'whatsapp', 'jam_layanan',
        'tautan_peta', 'peta_sematan',
        'medsos_instagram', 'medsos_facebook', 'medsos_youtube', 'medsos_tiktok',
        'kepsek_nama', 'kepsek_nip', 'kepsek_foto', 'kepsek_sambutan_singkat', 'kepsek_sambutan',
    ];

    protected static array $berkas = ['logo_sekolah', 'kepsek_foto'];

    protected static string $pesanTersimpan = 'Identitas sekolah disimpan';

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->components([
            Tabs::make('identitas')->persistTabInQueryString()->tabs([
                Tab::make('Identitas')->icon(Heroicon::OutlinedIdentification)->schema([
                    Section::make('Nama dan data pokok')->description('Tampil di halaman Profil, kepala dan kaki halaman, serta hasil pencarian Google.')->columns(2)->schema([
                        TextInput::make('nama_sekolah')->label('Nama resmi sekolah')->required()->maxLength(150)->columnSpanFull(),
                        TextInput::make('nama_singkat')->label('Nama singkat')->required()->maxLength(40)->helperText('Dipakai di tempat sempit, misalnya kepala halaman di HP.'),
                        TextInput::make('npsn')->label('NPSN')->maxLength(12),
                        TextInput::make('status_sekolah')->label('Status')->maxLength(30),
                        TextInput::make('akreditasi')->label('Akreditasi')->maxLength(30)->placeholder('Contoh: A'),
                        TextInput::make('kurikulum')->label('Kurikulum')->maxLength(60),
                        TextInput::make('tahun_berdiri')->label('Tahun berdiri')->maxLength(10),
                        TextInput::make('jumlah_rombel')->label('Jumlah rombongan belajar')->numeric()->minValue(0)->helperText('Angka saja. Kosongkan bila tidak ingin ditampilkan.'),
                        TextInput::make('jumlah_murid')->label('Jumlah murid')->numeric()->minValue(0)->helperText('Angka saja, tanpa nama murid. Kosongkan bila tidak ingin ditampilkan.'),
                        TextInput::make('tautan_data_resmi')->label('Tautan data resmi sekolah')->url()->maxLength(255)->columnSpanFull()
                            ->helperText('Misalnya halaman sekolah di situs data referensi Kemendikdasmen.'),
                    ]),
                    Section::make('Logo')->schema([
                        Isian::foto('logo_sekolah', 'pengaturan')->label('Logo sekolah')->maxSize(2048)
                            ->helperText('Tampil di kepala halaman dan ikon tab browser. Sebaiknya berbentuk persegi dengan latar polos.'),
                    ]),
                ]),

                Tab::make('Alamat & Kontak')->icon(Heroicon::OutlinedMapPin)->schema([
                    Section::make('Alamat')->columns(2)->schema([
                        TextInput::make('alamat')->label('Jalan')->maxLength(200)->columnSpanFull(),
                        TextInput::make('kelurahan')->maxLength(60),
                        TextInput::make('kecamatan')->maxLength(60),
                        TextInput::make('kota')->label('Kota/Kabupaten')->maxLength(60),
                        TextInput::make('provinsi')->maxLength(60),
                        TextInput::make('kode_pos')->label('Kode pos')->maxLength(10),
                    ]),
                    Section::make('Kontak')->description('Hanya kontak resmi sekolah. Jangan memakai nomor pribadi guru.')->columns(2)->schema([
                        TextInput::make('telepon')->tel()->maxLength(30),
                        TextInput::make('email')->label('Surel')->email()->maxLength(120),
                        TextInput::make('whatsapp')->label('WhatsApp sekolah')->tel()->maxLength(20)->placeholder('08xxxxxxxxxx'),
                        TextInput::make('jam_layanan')->label('Jam layanan')->maxLength(80),
                    ]),
                    Section::make('Peta')->schema([
                        TextInput::make('tautan_peta')->label('Tautan Google Maps')->url()->maxLength(500)
                            ->helperText('Buka lokasi sekolah di Google Maps, ketuk Bagikan, lalu salin tautannya. Dipakai untuk tombol "Buka di Google Maps".'),
                        Textarea::make('peta_sematan')->label('Kode sematan peta')->rows(3)->maxLength(2000)
                            ->helperText('Di Google Maps: Bagikan, Sematkan peta, Salin HTML, lalu tempel di sini. Hanya alamat peta dari Google yang dipakai.')
                            ->rule(fn () => function (string $attribute, mixed $value, \Closure $fail) {
                                if (filled($value) && self::alamatSematan($value) === '') {
                                    $fail('Kode sematan tidak dikenali. Salin ulang dari Google Maps: Bagikan, Sematkan peta, Salin HTML.');
                                }
                            })
                            ->dehydrateStateUsing(fn (?string $state) => self::alamatSematan($state)),
                    ]),
                ]),

                Tab::make('Media Sosial')->icon(Heroicon::OutlinedShare)->schema([
                    Section::make()->description('Tampil di kaki halaman dan halaman Kontak. Kosongkan yang tidak dipakai.')->columns(2)->schema([
                        TextInput::make('medsos_instagram')->label('Instagram')->url()->placeholder('https://instagram.com/akun-sekolah'),
                        TextInput::make('medsos_facebook')->label('Facebook')->url()->placeholder('https://facebook.com/akun-sekolah'),
                        TextInput::make('medsos_youtube')->label('YouTube')->url()->placeholder('https://youtube.com/@kanal-sekolah'),
                        TextInput::make('medsos_tiktok')->label('TikTok')->url()->placeholder('https://tiktok.com/@akun-sekolah'),
                    ]),
                ]),

                Tab::make('Kepala Sekolah')->icon(Heroicon::OutlinedUserCircle)->schema([
                    Section::make()->columns(2)->schema([
                        TextInput::make('kepsek_nama')->label('Nama kepala sekolah')->maxLength(150)->helperText('Lengkap dengan gelar.'),
                        TextInput::make('kepsek_nip')->label('NIP')->maxLength(30),
                        Isian::foto('kepsek_foto', 'pengaturan')->label('Foto kepala sekolah')->columnSpanFull(),
                        Textarea::make('kepsek_sambutan_singkat')->label('Sambutan singkat (Beranda)')->rows(3)->maxLength(400)->columnSpanFull()
                            ->helperText('Dua atau tiga kalimat. Tampil di Beranda dengan tautan ke sambutan lengkap.'),
                        Isian::teks('kepsek_sambutan')->label('Sambutan lengkap (halaman Profil)')->columnSpanFull(),
                    ]),
                ]),
            ]),
        ]);
    }

    protected function aksiTambahan(): array
    {
        return [Action::make('lihat')->label('Lihat halaman Profil')->color('gray')->url(route('profil'), shouldOpenInNewTab: true)];
    }

    /** Mengambil alamat peta dari kode sematan Google; selain alamat Google Maps ditolak agar tidak ada sematan sembarangan. */
    public static function alamatSematan(?string $kode): string
    {
        if (blank($kode)) {
            return '';
        }

        preg_match('~src="([^"]+)"~', $kode, $cocok);
        $alamat = html_entity_decode($cocok[1] ?? trim($kode));

        return preg_match('~^https://(www\.)?google\.com/maps/embed\?~', $alamat) ? $alamat : '';
    }
}
