<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaranaResource\Pages\ManageSarana;
use App\Models\Sarana;
use App\Services\SimpanFoto;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use UnitEnum;

class SaranaResource extends Resource
{
    protected static ?string $model = Sarana::class;

    protected static ?string $slug = 'sarana';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = 'Profil Sekolah';

    protected static ?string $modelLabel = 'sarana';

    protected static ?string $pluralModelLabel = 'Sarana & Prasarana';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('nama')->required()->maxLength(120)->placeholder('Contoh: Perpustakaan'),
            TextInput::make('jumlah')->maxLength(60)->placeholder('Contoh: 1 ruang, 2.500 buku'),
            Textarea::make('keterangan')->rows(3)->maxLength(600)->columnSpanFull(),
            FileUpload::make('foto')->label('Foto')->image()->multiple()->reorderable()->maxFiles(8)
                ->disk('public')->directory('sarana')->maxSize(SimpanFoto::MAKS_KB)->panelLayout('grid')->columnSpanFull()
                ->saveUploadedFileUsing(fn (TemporaryUploadedFile $file) => SimpanFoto::simpan($file, 'sarana'))
                ->helperText('Paling banyak 8 foto. Foto pertama menjadi sampul. Utamakan foto ruangan tanpa murid.'),
            Toggle::make('tampil')->label('Tampilkan di website')->default(true),
            TextInput::make('urutan')->numeric()->default(0)->minValue(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('urutan')
            ->reorderable('urutan')
            ->columns([
                ImageColumn::make('foto')->label('')->disk('public')->imageWidth(56)->imageHeight(40)->limit(1),
                TextColumn::make('nama')->searchable()->weight('bold')->description(fn (Sarana $record) => $record->jumlah),
                TextColumn::make('jumlah_foto')->label('Foto')->state(fn (Sarana $record) => count($record->daftarFoto()).' foto'),
                ToggleColumn::make('tampil'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->emptyStateHeading('Belum ada sarana dan prasarana')
            ->emptyStateDescription('Tambahkan ruang kelas, perpustakaan, Teras Baca, UKS, atau lapangan beserta fotonya melalui tombol Tambah sarana.');
    }

    public static function getPages(): array
    {
        return ['index' => ManageSarana::route('/')];
    }
}
