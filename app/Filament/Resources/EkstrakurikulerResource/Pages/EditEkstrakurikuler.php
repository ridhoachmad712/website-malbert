<?php

namespace App\Filament\Resources\EkstrakurikulerResource\Pages;

use App\Filament\Resources\EkstrakurikulerResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditEkstrakurikuler extends EditRecord
{
    protected static string $resource = EkstrakurikulerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('lihat')->label('Lihat di website')->icon(Heroicon::OutlinedEye)->color('gray')
                ->url(fn () => $this->record->url(), shouldOpenInNewTab: true),
            DeleteAction::make(),
        ];
    }
}
