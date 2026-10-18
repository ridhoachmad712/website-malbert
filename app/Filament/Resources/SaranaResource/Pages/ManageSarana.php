<?php

namespace App\Filament\Resources\SaranaResource\Pages;

use App\Filament\Resources\SaranaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSarana extends ManageRecords
{
    protected static string $resource = SaranaResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Tambah sarana')];
    }
}
