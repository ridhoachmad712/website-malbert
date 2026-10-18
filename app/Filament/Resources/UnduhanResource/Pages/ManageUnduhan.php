<?php

namespace App\Filament\Resources\UnduhanResource\Pages;

use App\Filament\Resources\UnduhanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageUnduhan extends ManageRecords
{
    protected static string $resource = UnduhanResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Tambah berkas')->mutateDataUsing(fn (array $data) => UnduhanResource::lengkapiData($data))];
    }
}
