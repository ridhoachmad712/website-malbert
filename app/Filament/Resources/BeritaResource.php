<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BeritaResource\Pages;
use App\Filament\Support\Isian;
use App\Models\Berita;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class BeritaResource extends Resource
{
    protected static ?string $model = Berita::class;

    protected static ?string $slug = 'berita';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?string $modelLabel = 'berita';

    protected static ?string $pluralModelLabel = 'Berita & Pengumuman';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            Group::make()->columnSpan(['lg' => 2])->schema([
                Section::make()->schema([
                    TextInput::make('judul')->required()->maxLength(200),
                    Textarea::make('ringkasan')->rows(2)->maxLength(300)
                        ->helperText('Satu atau dua kalimat untuk kartu berita dan pratinjau saat dibagikan ke WhatsApp. Bila kosong, diambil dari awal isi.'),
                    Isian::teks('isi')->required(),
                ]),
                Section::make('Gambar utama')->schema([
                    Isian::foto('gambar', 'berita')->hiddenLabel()->live(),
                    TextInput::make('keterangan_gambar')->label('Keterangan gambar')->maxLength(200)
                        ->helperText('Sebutkan kegiatannya. Jangan menuliskan nama murid pada keterangan foto.'),
                ]),
            ]),
            Group::make()->columnSpan(['lg' => 1])->schema([
                Section::make('Terbit')->schema([
                    Radio::make('status')->options(Berita::STATUS)->default('draf')->required()->inline()->live()
                        ->helperText('Draf tidak tampil di website.'),
                    DateTimePicker::make('terbit_pada')->label('Tanggal terbit')->native(false)->seconds(false)->displayFormat('d/m/Y H.i')
                        ->helperText('Kosongkan untuk terbit sekarang. Bila diisi tanggal yang akan datang, berita tampil otomatis pada waktunya.'),
                    Isian::fotoDiperiksa()
                        ->accepted(fn (Get $get) => $get('status') === 'terbit' && filled($get('gambar')))
                        ->validationMessages(['accepted' => 'Periksa foto terlebih dahulu sebelum menerbitkan berita yang bergambar.']),
                ]),
                Section::make('Keterangan')->schema([
                    Select::make('kategori')->options(Berita::KATEGORI)->default('kegiatan')->required()->native(false)->live(),
                    TextInput::make('penulis')->label('Nama penulis')->maxLength(120)
                        ->helperText('Isi bila tulisan berasal dari guru lain, misalnya "Siti Rahmah, S.Pd. (Guru Kelas IV)". Bila kosong, tertulis Admin Sekolah.'),
                    Toggle::make('disematkan')->label('Sematkan di Beranda')->live()
                        ->helperText('Untuk pengumuman penting. Tampil paling atas di Beranda.'),
                    DatePicker::make('sematkan_sampai')->label('Sematkan sampai')->native(false)->displayFormat('d/m/Y')
                        ->visible(fn (Get $get) => $get('disematkan'))
                        ->helperText('Setelah tanggal ini, pengumuman tidak lagi disematkan. Kosongkan bila tanpa batas.'),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('gambar')->label('')->disk('public')->imageWidth(64)->imageHeight(44),
                TextColumn::make('judul')->searchable()->weight('bold')->wrap()
                    ->description(fn (Berita $record) => $record->namaPenulis()),
                TextColumn::make('kategori')->badge()->color('gray')->formatStateUsing(fn ($state) => Berita::KATEGORI[$state] ?? $state),
                TextColumn::make('status')->badge()
                    ->state(fn (Berita $record) => match (true) {
                        $record->status !== 'terbit' => 'Draf',
                        $record->terbit_pada?->isFuture() => 'Terjadwal',
                        default => 'Terbit',
                    })
                    ->color(fn (string $state) => match ($state) {
                        'Terbit' => 'success',
                        'Terjadwal' => 'info',
                        default => 'gray',
                    }),
                IconColumn::make('disematkan')->label('Disematkan')->boolean()->trueIcon(Heroicon::OutlinedMapPin)->falseIcon(null),
                TextColumn::make('terbit_pada')->label('Terbit')->dateTime('d/m/Y H.i')->sortable()->placeholder('Belum'),
            ])
            ->filters([
                SelectFilter::make('kategori')->options(Berita::KATEGORI),
                SelectFilter::make('status')->options(Berita::STATUS),
            ])
            ->recordActions([
                Action::make('lihat')->icon(Heroicon::OutlinedEye)->color('gray')
                    ->url(fn (Berita $record) => $record->url(), shouldOpenInNewTab: true)
                    ->visible(fn (Berita $record) => $record->status === 'terbit'),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->emptyStateHeading('Belum ada berita')
            ->emptyStateDescription('Tulis kabar kegiatan atau pengumuman pertama melalui tombol Buat berita.');
    }

    /** Waktu terbit diisi saat pertama kali diterbitkan, bila admin tidak menjadwalkannya. */
    public static function siapkanData(array $data): array
    {
        if (($data['status'] ?? null) === 'terbit' && blank($data['terbit_pada'] ?? null)) {
            $data['terbit_pada'] = now();
        }
        if (! ($data['disematkan'] ?? false)) {
            $data['sematkan_sampai'] = null;
        }

        return $data;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBerita::route('/'),
            'create' => Pages\CreateBerita::route('/buat'),
            'edit' => Pages\EditBerita::route('/{record}/ubah'),
        ];
    }
}
