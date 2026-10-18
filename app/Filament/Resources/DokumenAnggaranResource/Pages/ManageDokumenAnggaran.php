<?php

namespace App\Filament\Resources\DokumenAnggaranResource\Pages;

use App\Filament\Resources\DokumenAnggaranResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageDokumenAnggaran extends ManageRecords
{
    protected static string $resource = DokumenAnggaranResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Tambah dokumen')->mutateDataUsing(fn (array $data) => DokumenAnggaranResource::lengkapiData($data))];
    }
}
