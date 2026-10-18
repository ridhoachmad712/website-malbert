<?php

namespace App\Filament\Pages;

use App\Filament\Support\HalamanPengaturan;
use App\Filament\Support\Isian;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/** Menu utama, tombol ajakan di kepala halaman, dan isi kaki halaman. */
class MenuSitus extends HalamanPengaturan
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBars3;

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Menu & Tombol';

    protected static ?string $title = 'Menu, Tombol, dan Kaki Halaman';

    protected static ?string $slug = 'menu';

    protected static ?int $navigationSort = 2;

    protected static array $kunci = [
        'menu_utama',
        'kepala_tombol_tampil', 'kepala_tombol_label', 'kepala_tombol_jenis', 'kepala_tombol_tujuan', 'kepala_tombol_url', 'kepala_tombol_tab_baru',
        'kaki_deskripsi', 'kaki_tautan',
    ];

    protected static array $daftar = ['menu_utama', 'kaki_tautan'];

    protected static array $saklar = ['kepala_tombol_tampil', 'kepala_tombol_tab_baru'];

    protected static string $pesanTersimpan = 'Menu disimpan';

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->components([
            Tabs::make('menu')->persistTabInQueryString()->tabs([
                Tab::make('Menu utama')->icon(Heroicon::OutlinedBars3)->schema([
                    Repeater::make('menu_utama')->hiddenLabel()
                        ->schema([
                            Grid::make(3)->schema([
                                TextInput::make('label')->label('Tulisan di menu')->required()->maxLength(30),
                                Radio::make('bentuk')->label('Bentuk')->inline()->default('tautan')->live()
                                    ->options(['tautan' => 'Tautan langsung', 'grup' => 'Menu tarik-turun']),
                                Toggle::make('tampil')->label('Tampilkan')->default(true)->inline(false),
                            ]),
                            Grid::make(2)->visible(fn (Get $get) => $get('bentuk') !== 'grup')
                                ->schema(Isian::tautan()),
                            Repeater::make('anak')->label('Isi menu tarik-turun')
                                ->visible(fn (Get $get) => $get('bentuk') === 'grup')
                                ->schema([
                                    TextInput::make('label')->label('Tulisan')->required()->maxLength(40),
                                    ...Isian::tautan(denganOtomatis: true),
                                ])
                                ->columns(2)->reorderable()->collapsible()->defaultItems(1)->maxItems(12)
                                ->itemLabel(fn (array $state) => $state['label'] ?? null)
                                ->addActionLabel('Tambah isi menu')
                                ->helperText('Pilihan "Semua halaman profil (otomatis)" menampilkan setiap halaman profil yang terbit, sehingga halaman baru langsung muncul di menu.'),
                        ])
                        ->reorderable()->collapsible()->collapsed()->minItems(1)->maxItems(8)
                        ->itemLabel(fn (array $state) => ($state['label'] ?? 'Menu baru')
                            .(($state['bentuk'] ?? '') === 'grup' ? ' (tarik-turun)' : '')
                            .(filter_var($state['tampil'] ?? true, FILTER_VALIDATE_BOOLEAN) ? '' : ' — disembunyikan'))
                        ->addActionLabel('Tambah menu')
                        ->helperText('Seret untuk mengubah urutan. Sebaiknya paling banyak 7 menu agar tetap muat di layar laptop. Menu yang tujuannya sudah tidak ada (misalnya halaman yang disembunyikan) tidak ditampilkan.'),
                ]),

                Tab::make('Tombol kepala halaman')->icon(Heroicon::OutlinedCursorArrowRays)->schema([
                    Section::make()->description('Satu tombol berwarna aksen di ujung kanan menu, misalnya "Daftar SPMB" atau "Hubungi kami". Tampil di semua halaman.')
                        ->columns(2)->schema([
                            Toggle::make('kepala_tombol_tampil')->label('Tampilkan tombol')->live()->columnSpanFull(),
                            TextInput::make('kepala_tombol_label')->label('Tulisan tombol')->maxLength(30)
                                ->required(fn (Get $get) => $get('kepala_tombol_tampil')),
                            ...Isian::tautan('kepala_tombol_', wajib: false),
                        ]),
                ]),

                Tab::make('Kaki halaman')->icon(Heroicon::OutlinedQueueList)->schema([
                    Section::make()->schema([
                        Textarea::make('kaki_deskripsi')->label('Kalimat singkat tentang sekolah')->rows(2)->maxLength(300)
                            ->helperText('Tampil di bawah nama sekolah pada kaki halaman. Boleh dikosongkan.'),
                        Repeater::make('kaki_tautan')->label('Tautan di kaki halaman')
                            ->schema([
                                TextInput::make('label')->label('Tulisan')->required()->maxLength(40),
                                ...Isian::tautan(),
                            ])
                            ->columns(2)->reorderable()->collapsible()->defaultItems(0)->maxItems(10)
                            ->itemLabel(fn (array $state) => $state['label'] ?? null)
                            ->addActionLabel('Tambah tautan'),
                    ]),
                ]),
            ]),
        ]);
    }

    protected function aksiTambahan(): array
    {
        return [Action::make('lihat')->label('Lihat website')->color('gray')->url(route('beranda'), shouldOpenInNewTab: true)];
    }
}
