<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenggunaResource\Pages\ManagePengguna;
use App\Filament\Support\HapusAman;
use App\Models\User;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Password;
use UnitEnum;

/** Akun admin website. Semua akun punya hak yang sama; tidak ada peran lain. */
class PenggunaResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $slug = 'pengguna';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $modelLabel = 'admin';

    protected static ?string $pluralModelLabel = 'Akun Admin';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            TextInput::make('name')->label('Nama')->required()->maxLength(120),
            TextInput::make('email')->label('Surel')->email()->required()->unique(ignoreRecord: true)->maxLength(150),
            TextInput::make('password')->label('Kata sandi')->password()->revealable()
                ->rule(Password::min(10)->letters()->numbers())
                ->required(fn (string $operation) => $operation === 'create')
                ->dehydrated(fn (?string $state) => filled($state))
                ->helperText(fn (string $operation) => $operation === 'edit'
                    ? 'Kosongkan bila tidak diganti. Paling sedikit 10 karakter, berisi huruf dan angka.'
                    : 'Paling sedikit 10 karakter, berisi huruf dan angka. Minta admin baru menggantinya setelah masuk pertama kali.'),
            Toggle::make('aktif')->label('Boleh masuk')->default(true)
                ->disabled(fn (?User $record) => $record?->id === auth()->id())
                ->helperText('Matikan untuk guru yang tidak lagi bertugas sebagai admin.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->weight('bold')->searchable()->description(fn (User $record) => $record->email),
                IconColumn::make('aktif')->boolean(),
                IconColumn::make('dua_langkah')->label('Verifikasi dua langkah')->boolean()
                    ->state(fn (User $record) => filled($record->app_authentication_secret)),
                TextColumn::make('created_at')->label('Dibuat')->date('d/m/Y'),
            ])
            ->recordActions([EditAction::make(), HapusAman::aksi()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManagePengguna::route('/')];
    }
}
