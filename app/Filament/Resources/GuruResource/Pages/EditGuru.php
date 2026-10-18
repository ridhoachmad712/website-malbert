<?php

namespace App\Filament\Resources\GuruResource\Pages;

use App\Filament\Resources\GuruResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditGuru extends EditRecord
{
    protected static string $resource = GuruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('lihat')->label('Lihat di website')->icon(Heroicon::OutlinedEye)->color('gray')
                ->url(fn () => $this->record->url(), shouldOpenInNewTab: true),
            DeleteAction::make(),
        ];
    }
}
