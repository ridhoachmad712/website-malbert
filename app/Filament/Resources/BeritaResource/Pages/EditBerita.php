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
            Action::make('lihat')->label('Lihat di website')->icon(Heroicon::OutlinedEye)->color('gray')
                ->url(fn () => $this->record->url(), shouldOpenInNewTab: true)
                ->visible(fn () => $this->record->status === 'terbit'),
            DeleteAction::make(),
        ];
    }
}
