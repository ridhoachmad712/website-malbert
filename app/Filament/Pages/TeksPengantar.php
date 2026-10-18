<?php

namespace App\Filament\Pages;

use App\Filament\Support\HalamanPengaturan;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/** Kalimat pembuka di bawah judul halaman Transparansi, Ekstrakurikuler, Sarana, dan Alumni. */
class TeksPengantar extends HalamanPengaturan
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Teks Pengantar';

    protected static ?string $title = 'Teks Pengantar Halaman';

    protected static ?string $slug = 'teks-pengantar';

    protected static ?int $navigationSort = 3;

    protected static array $kunci = ['transparansi_pengantar', 'ekskul_pengantar', 'sarana_pengantar', 'alumni_pengantar', 'alumni_formulir_tampil'];

    protected static array $saklar = ['alumni_formulir_tampil'];

    protected static string $pesanTersimpan = 'Teks pengantar disimpan';

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->components([
            Section::make('Transparansi anggaran')->schema([
                Textarea::make('transparansi_pengantar')->hiddenLabel()->rows(4)->maxLength(800),
            ]),
            Section::make('Ekstrakurikuler')->schema([
                Textarea::make('ekskul_pengantar')->hiddenLabel()->rows(2)->maxLength(400),
            ]),
            Section::make('Sarana dan prasarana')->schema([
                Textarea::make('sarana_pengantar')->hiddenLabel()->rows(2)->maxLength(400),
            ]),
            Section::make('Alumni')->schema([
                Textarea::make('alumni_pengantar')->hiddenLabel()->rows(3)->maxLength(500),
                Toggle::make('alumni_formulir_tampil')->label('Tampilkan formulir kiriman alumni'),
            ]),
        ]);
    }
}
