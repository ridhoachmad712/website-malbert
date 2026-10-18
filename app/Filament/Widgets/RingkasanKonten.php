<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AgendaResource;
use App\Filament\Resources\BeritaResource;
use App\Filament\Resources\PesanResource;
use App\Models\Agenda;
use App\Models\Berita;
use App\Models\Pesan;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RingkasanKonten extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $terakhir = Berita::terbit()->latest('terbit_pada')->first();

        return [
            Stat::make('Pesan baru', Pesan::baru()->count())
                ->description('Dari formulir Kontak')->url(PesanResource::getUrl()),
            Stat::make('Berita terbit bulan ini', Berita::terbit()->where('terbit_pada', '>=', now()->startOfMonth())->count())
                ->description($terakhir ? 'Terakhir: '.$terakhir->terbit_pada->diffForHumans() : 'Belum ada berita terbit')
                ->url(BeritaResource::getUrl()),
            Stat::make('Draf berita', Berita::where('status', 'draf')->count())
                ->description('Belum tampil di website')->url(BeritaResource::getUrl()),
            Stat::make('Agenda mendatang', Agenda::mendatang()->count())
                ->description('Termasuk hari libur')->url(AgendaResource::getUrl()),
        ];
    }
}
