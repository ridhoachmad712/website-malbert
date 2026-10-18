<?php

namespace App\Filament\Support;

use App\Models\Album;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Penghapusan yang mencegah data hilang tanpa sengaja. Database juga menolaknya (restrictOnDelete);
 * kelas ini memberi penjelasan yang bisa dipahami admin.
 */
class HapusAman
{
    public static function alasan(Model $record): ?string
    {
        return match (true) {
            $record instanceof Album => ($jumlah = $record->foto()->count())
                ? "Album ini masih berisi {$jumlah} foto. Hapus fotonya terlebih dahulu, atau matikan \"Tampilkan\" agar album tidak muncul di website." : null,
            $record instanceof User => match (true) {
                $record->id === auth()->id() => 'Anda tidak dapat menghapus akun sendiri.',
                User::where('aktif', true)->whereKeyNot($record->id)->doesntExist() => 'Harus ada paling sedikit satu admin aktif.',
                default => null,
            },
            default => null,
        };
    }

    public static function aksi(): DeleteAction
    {
        return DeleteAction::make()->before(function (DeleteAction $action, Model $record) {
            if ($alasan = static::alasan($record)) {
                Notification::make()->warning()->title('Tidak dapat dihapus')->body($alasan)->persistent()->send();
                $action->cancel();
            }
        });
    }

    public static function aksiMassal(): DeleteBulkAction
    {
        return DeleteBulkAction::make()->action(function (Collection $records) {
            [$ditahan, $boleh] = $records->partition(fn (Model $record) => static::alasan($record) !== null);
            $boleh->each->delete();

            if ($ditahan->isNotEmpty()) {
                Notification::make()->warning()->persistent()
                    ->title($boleh->count().' dihapus, '.$ditahan->count().' tidak dapat dihapus')
                    ->body(static::alasan($ditahan->first()))->send();
            } else {
                Notification::make()->success()->title($boleh->count().' data dihapus')->send();
            }
        });
    }
}
