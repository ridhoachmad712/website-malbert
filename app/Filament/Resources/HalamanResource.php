<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HalamanResource\Pages;
use App\Filament\Support\Isian;
use App\Models\Halaman;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class HalamanResource extends Resource
{
    protected static ?string $model = Halaman::class;

    protected static ?string $slug = 'halaman';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Profil Sekolah';

    protected static ?string $modelLabel = 'halaman';

    protected static ?string $pluralModelLabel = 'Halaman Profil';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make()->columns(2)->schema([
                TextInput::make('judul')->required()->maxLength(150),
                TextInput::make('slug')->label('Alamat halaman')->prefix('/profil/')->maxLength(160)
                    ->unique(ignoreRecord: true)->alphaDash()
                    ->dehydrateStateUsing(fn (?string $state) => filled($state) ? Str::slug($state) : null)
                    ->helperText('Kosongkan untuk dibuat otomatis dari judul. Jangan diubah setelah tautannya dibagikan.'),
                Select::make('grup')->label('Tampil di')->options(Halaman::GRUP)->default('profil')->required()->native(false),
                TextInput::make('urutan')->numeric()->default(0)->minValue(0)->helperText('Angka kecil tampil lebih dulu di menu Profil.'),
                Textarea::make('ringkasan')->rows(2)->maxLength(300)->columnSpanFull()
                    ->helperText('Tampil di bawah judul dan di hasil pencarian Google.'),
            ]),
            Section::make('Isi')->schema([
                Isian::teks('isi')->hiddenLabel(),
                Isian::foto('gambar', 'halaman')->label('Gambar')
                    ->helperText('Misalnya bagan struktur organisasi. Tampil di bawah isi, bisa diperbesar.'),
            ]),
            Toggle::make('terbit')->label('Tampilkan di website')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('urutan')
            ->reorderable('urutan')
            ->columns([
                TextColumn::make('judul')->searchable()->weight('bold')->description(fn (Halaman $record) => '/profil/'.$record->slug),
                TextColumn::make('grup')->label('Tampil di')->badge()->color('gray')
                    ->formatStateUsing(fn ($state) => $state === 'profil' ? 'Menu Profil' : 'Lainnya'),
                ToggleColumn::make('terbit')->label('Tampil'),
                TextColumn::make('updated_at')->label('Diubah')->since()->sortable(),
            ])
            ->filters([SelectFilter::make('grup')->label('Tampil di')->options(Halaman::GRUP)])
            ->recordActions([
                Action::make('lihat')->icon(Heroicon::OutlinedEye)->color('gray')
                    ->url(fn (Halaman $record) => $record->url(), shouldOpenInNewTab: true),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('Belum ada halaman')
            ->emptyStateDescription('Buat halaman Sejarah, Visi dan Misi, atau Struktur Organisasi melalui tombol Buat halaman.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHalaman::route('/'),
            'create' => Pages\CreateHalaman::route('/buat'),
            'edit' => Pages\EditHalaman::route('/{record}/ubah'),
        ];
    }
}
