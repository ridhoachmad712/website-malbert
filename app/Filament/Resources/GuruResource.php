<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuruResource\Pages;
use App\Filament\Support\Isian;
use App\Models\Guru;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class GuruResource extends Resource
{
    protected static ?string $model = Guru::class;

    protected static ?string $slug = 'guru';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup = 'Profil Sekolah';

    protected static ?string $modelLabel = 'guru/tendik';

    protected static ?string $pluralModelLabel = 'Guru & Tendik';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            Group::make()->columnSpan(['lg' => 2])->schema([
                Section::make('Data pokok')->columns(2)->schema([
                    TextInput::make('nama')->label('Nama lengkap dan gelar')->required()->maxLength(150)->columnSpanFull(),
                    TextInput::make('jabatan')->maxLength(150)->placeholder('Contoh: Guru Kelas IV'),
                    Select::make('kelompok')->options(Guru::KELOMPOK)->default('guru')->required()->native(false),
                    TextInput::make('nip')->label('NIP')->maxLength(30)->helperText('Boleh dikosongkan.'),
                    TextInput::make('motto')->maxLength(200),
                    Textarea::make('profil')->rows(4)->maxLength(2000)->columnSpanFull()
                        ->helperText('Cerita singkat: pendidikan, pengalaman, atau minat mengajar.'),
                ]),
                Section::make('Media sosial')->schema([
                    Repeater::make('media_sosial')->hiddenLabel()->schema([
                        Select::make('jenis')->options(Guru::MEDIA_SOSIAL)->required()->native(false),
                        TextInput::make('url')->label('Alamat')->url()->required()->maxLength(255)->placeholder('https://'),
                    ])->columns(2)->defaultItems(0)->maxItems(6)->reorderable()->addActionLabel('Tambah media sosial'),
                ]),
                Section::make('Tombol tautan')->description('Tombol ke halaman luar, misalnya blog, kanal belajar, atau karya guru.')->schema([
                    Repeater::make('tautan')->hiddenLabel()->schema([
                        TextInput::make('label')->label('Tulisan tombol')->required()->maxLength(40)->placeholder('Contoh: Blog belajar'),
                        TextInput::make('url')->label('Alamat')->url()->required()->maxLength(255)->placeholder('https://'),
                    ])->columns(2)->defaultItems(0)->maxItems(4)->reorderable()->addActionLabel('Tambah tombol'),
                ]),
            ]),
            Group::make()->columnSpan(['lg' => 1])->schema([
                Section::make('Foto')->schema([
                    Isian::foto('foto', 'guru')->hiddenLabel()->imageCropAspectRatio('3:4')
                        ->helperText('Foto resmi, tegak (3:4).'),
                ]),
                Section::make('Tampilan')->schema([
                    Toggle::make('tampil')->label('Tampilkan di website')->default(true),
                    TextInput::make('urutan')->numeric()->default(0)->minValue(0)->helperText('Angka kecil tampil lebih dulu.'),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('urutan')
            ->reorderable('urutan')
            ->columns([
                ImageColumn::make('foto')->label('')->disk('public')->circular()->imageSize(40),
                TextColumn::make('nama')->searchable()->weight('bold')->description(fn (Guru $record) => $record->jabatan),
                TextColumn::make('kelompok')->badge()->color('gray')->formatStateUsing(fn ($state) => Guru::KELOMPOK[$state] ?? $state),
                TextColumn::make('nip')->label('NIP')->placeholder('-')->toggleable(),
                ToggleColumn::make('tampil'),
            ])
            ->filters([SelectFilter::make('kelompok')->options(Guru::KELOMPOK)])
            ->recordActions([
                Action::make('lihat')->icon(Heroicon::OutlinedEye)->color('gray')
                    ->url(fn (Guru $record) => $record->url(), shouldOpenInNewTab: true),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('Belum ada guru dan tenaga kependidikan')
            ->emptyStateDescription('Tambahkan data melalui tombol Tambah guru/tendik. Urutan bisa diatur dengan menyeret baris.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGuru::route('/'),
            'create' => Pages\CreateGuru::route('/buat'),
            'edit' => Pages\EditGuru::route('/{record}/ubah'),
        ];
    }
}
