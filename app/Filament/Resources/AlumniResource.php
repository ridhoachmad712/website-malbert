<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlumniResource\Pages\ManageAlumni;
use App\Filament\Support\Isian;
use App\Models\Alumni;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class AlumniResource extends Resource
{
    protected static ?string $model = Alumni::class;

    protected static ?string $slug = 'alumni';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?string $modelLabel = 'alumni';

    protected static ?string $pluralModelLabel = 'Alumni';

    protected static ?int $navigationSort = 7;

    public static function getNavigationBadge(): ?string
    {
        return ($jumlah = Alumni::menunggu()->count()) ? (string) $jumlah : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Kiriman alumni yang menunggu diperiksa';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Placeholder::make('info_formulir')->hiddenLabel()->columnSpanFull()
                ->visible(fn (?Alumni $record) => $record?->sumber === 'formulir')
                ->content(fn (?Alumni $record) => $record?->setuju_tampil
                    ? 'Dikirim lewat formulir alumni. Pengirim menyetujui nama dan ceritanya ditampilkan setelah diperiksa admin.'
                    : 'Dikirim lewat formulir alumni. Pengirim TIDAK menyetujui ditampilkan; simpan sebagai data saja.'),
            TextInput::make('nama')->required()->maxLength(150),
            TextInput::make('tahun_lulus')->label('Tahun lulus')->numeric()->minValue(1950)->maxValue(now()->year),
            TextInput::make('kegiatan')->label('Kegiatan sekarang')->maxLength(200)->columnSpanFull()
                ->placeholder('Contoh: Mahasiswa Pendidikan Guru SD, Universitas Negeri Makassar'),
            Textarea::make('cerita')->label('Cerita atau pesan untuk adik kelas')->rows(4)->maxLength(1500)->columnSpanFull(),
            TextInput::make('kontak')->maxLength(120)->helperText('Hanya terlihat oleh admin, tidak pernah tampil di website.'),
            Isian::foto('foto', 'alumni')->label('Foto'),
            Radio::make('status')->options(Alumni::STATUS)->default('tampil')->required()->inline()->columnSpanFull()
                ->disableOptionWhen(fn (string $value, ?Alumni $record) => $value === 'tampil' && $record?->sumber === 'formulir' && ! $record->setuju_tampil),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('foto')->label('')->disk('public')->circular()->imageSize(40),
                TextColumn::make('nama')->searchable()->weight('bold')
                    ->description(fn (Alumni $record) => collect([$record->tahun_lulus ? 'Lulus '.$record->tahun_lulus : null, $record->kegiatan])->filter()->implode(' · ')),
                TextColumn::make('sumber')->badge()->color('gray')->formatStateUsing(fn ($state) => Alumni::SUMBER[$state] ?? $state),
                TextColumn::make('status')->badge()->formatStateUsing(fn ($state) => Alumni::STATUS[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'menunggu' => 'warning',
                        'tampil' => 'success',
                        default => 'gray',
                    }),
            ])
            ->filters([SelectFilter::make('status')->options(Alumni::STATUS)])
            ->recordActions([
                Action::make('tampilkan')->label('Tampilkan')->icon(Heroicon::OutlinedCheck)->color('success')
                    ->visible(fn (Alumni $record) => $record->status === 'menunggu' && $record->setuju_tampil)
                    ->requiresConfirmation()->modalDescription('Pastikan isi cerita pantas dan tidak memuat data pribadi orang lain.')
                    ->action(fn (Alumni $record) => $record->update(['status' => 'tampil'])),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('Belum ada data alumni')
            ->emptyStateDescription('Tambahkan cerita alumni, atau tunggu kiriman dari formulir di halaman Alumni.');
    }

    public static function getPages(): array
    {
        return ['index' => ManageAlumni::route('/')];
    }
}
