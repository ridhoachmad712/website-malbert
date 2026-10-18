<?php

namespace App\Filament\Resources\BeritaResource\Pages;

use App\Filament\Resources\BeritaResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditBerita extends EditRecord
{
    protected static string $resource = BeritaResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return BeritaResource::siapkanData($data);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('pratinjau')->label('Pratinjau')->icon(Heroicon::OutlinedEye)->color('gray')
                ->url(fn () => route('berita.pratinjau', $this->record->slug), shouldOpenInNewTab: true)
                ->tooltip('Lihat tampilan berita di website sebelum diterbitkan. Simpan dulu perubahan terbaru.'),
            DeleteAction::make(),
        ];
    }
}
